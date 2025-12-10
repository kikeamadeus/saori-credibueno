<?php
/**
 * Servicio de asistencia (fase ENTRADA con horarios por empleado)
 * ---------------------------------------------------------------
 * Reglas actuales:
 * - Solo registra ENTRADA (un registro por día).
 * - Usa la tabla schedules para obtener la hora de entrada.
 * - Permite checar desde 1 hora antes de la hora de entrada.
 * - Calcula A / R / F:
 *      A = asistencia (mientras tenga tolerancia >= 0)
 *      R = retardo (cuando ya no hay tolerancia disponible)
 *      F = falta (si intenta checar después de 1 hora + 1 minuto)
 * - Descuenta tolerancia; puede quedar en valores negativos.
 */

require_once __DIR__ . '/../../config/bootstrap.php';

function getTodayAttendanceByRole(PDO $pdo, int $employeeId, int $roleId): array
{
    date_default_timezone_set("America/Monterrey");
    $today = date("Y-m-d");

    // Roles con vista total
    if (in_array($roleId, [1, 2, 3])) {

        $stmt = $pdo->prepare("
            SELECT 
                ar.id,
                ar.employee_id,
                CONCAT(e.names, ' ', e.surname1, ' ', e.surname2) AS employee_name,
                ar.attendance_date,
                ar.attendance_hour,
                ar.attendance_type,
                ar.source,
                e.tolerance_minutes AS remaining_minutes
            FROM attendance_records ar
            INNER JOIN employees e ON e.id = ar.employee_id
            WHERE ar.attendance_date = :today
            ORDER BY ar.attendance_hour ASC
        ");

        $stmt->execute([':today' => $today]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Rol empleado → solo su registro
    $stmt = $pdo->prepare("
        SELECT 
            ar.id,
            ar.employee_id,
            CONCAT(e.names, ' ', e.surname1, ' ', e.surname2) AS employee_name,
            ar.attendance_date,
            ar.attendance_hour,
            ar.attendance_type,
            ar.source,
            e.tolerance_minutes AS remaining_minutes
        FROM attendance_records ar
        INNER JOIN employees e ON e.id = ar.employee_id
        WHERE ar.attendance_date = :today
          AND ar.employee_id = :emp
        LIMIT 1
    ");

    $stmt->execute([
        ':today' => $today,
        ':emp'   => $employeeId
    ]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // Para homogeneidad, siempre regresar array
    return $row ? [$row] : [];
}

/**
 * Obtiene el historial de asistencia del día para todos los empleados
 */
function getTodayAttendance(PDO $pdo)
{
    date_default_timezone_set("America/Monterrey");
    $today = date("Y-m-d");

    $stmt = $pdo->prepare("
        SELECT 
            ar.id,
            ar.employee_id,
            CONCAT(e.names, ' ', e.surname1, ' ', e.surname2) AS employee_name,
            ar.attendance_date,
            ar.attendance_hour,
            ar.attendance_type,
            ar.source,
            ar.created_at,
            e.tolerance_minutes AS remaining_minutes
        FROM attendance_records ar
        INNER JOIN employees e ON e.id = ar.employee_id
        WHERE ar.attendance_date = :today
        ORDER BY ar.attendance_hour ASC
    ");

    $stmt->execute([
        ':today' => $today
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Verifica si el empleado ya tiene asistencia registrada hoy
 * (cualquier tipo distinto de NULL).
 */
function hasAttendanceToday(PDO $pdo, int $employeeId): bool
{
    $today = date('Y-m-d');

    $stmt = $pdo->prepare("
        SELECT 1
        FROM attendance_records
        WHERE employee_id = :employee_id
          AND attendance_date = :today
          AND attendance_type IS NOT NULL
        LIMIT 1
    ");

    $stmt->execute([
        ':employee_id' => $employeeId,
        ':today'       => $today,
    ]);

    return (bool) $stmt->fetchColumn();
}

/**
 * Obtiene el horario de hoy para el empleado (tabla schedules)
 */
function getTodaySchedule(PDO $pdo, int $employeeId): ?array
{
    $dayOfWeek = (int) date('N'); // 1=Lunes ... 7=Domingo

    $stmt = $pdo->prepare("
        SELECT entry_time, lunch_out_time, lunch_in_time, exit_time
        FROM schedules
        WHERE employee_id = :emp
          AND day_of_week = :day
        LIMIT 1
    ");

    $stmt->execute([
        ':emp' => $employeeId,
        ':day' => $dayOfWeek
    ]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row ?: null;
}

/**
 * Función principal para registrar asistencia (solo ENTRADA por ahora)
 */
function registerAttendance($employeeId, $source, $latitude = null, $longitude = null)
{
    $pdo = getConnectionMySql();
    date_default_timezone_set("America/Monterrey");

    $employeeId = (int) $employeeId;

    // ==================================================
    // 0) Validar que hoy no tenga ya asistencia
    // ==================================================
    if (hasAttendanceToday($pdo, $employeeId)) {
        return [
            "success" => false,
            "message" => "Ya registraste tu asistencia el día de hoy."
        ];
    }

    // ==================================================
    // 1) Obtener datos del empleado + sucursal
    // ==================================================
    $stmt = $pdo->prepare("
        SELECT 
            e.id,
            e.tolerance_minutes,
            e.id_branch,
            b.latitude AS branch_lat,
            b.longitude AS branch_lng
        FROM employees e
        LEFT JOIN branches b ON b.id = e.id_branch
        WHERE e.id = :id
        LIMIT 1
    ");
    $stmt->execute([':id' => $employeeId]);
    $emp = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$emp) {
        return ["success" => false, "message" => "Empleado no encontrado"];
    }

    // ==================================================
    // 2) Ajustar coordenadas según origen
    // ==================================================
    if ($source === 'web') {
        // Checada desde PC → usar sucursal
        $latitude  = $emp['branch_lat'];
        $longitude = $emp['branch_lng'];

        if (!$latitude || !$longitude) {
            return ["success" => false, "message" => "Sucursal sin coordenadas configuradas. Contacta a sistemas."];
        }
    } elseif ($source === 'mobile') {
        if (!$latitude || !$longitude) {
            return ["success" => false, "message" => "Se requiere GPS para registrar asistencia desde el móvil."];
        }
    } else {
        return ["success" => false, "message" => "Origen inválido para registrar asistencia."];
    }

    // ==================================================
    // 3) Hora y fecha actuales
    // ==================================================
    $now       = new DateTime();
    $hourNow   = $now->format("H:i:s");
    $dateToday = $now->format("Y-m-d");

    // ==================================================
    // 4) Obtener horario de hoy desde schedules
    // ==================================================
    $schedule = getTodaySchedule($pdo, $employeeId);

    if (!$schedule || empty($schedule['entry_time'])) {
        // No hay horario de entrada configurado para hoy
        return [
            "success" => false,
            "message" => "Hoy no tienes un horario de entrada configurado. Contacta a sistemas."
        ];
    }

    $entryTime = $schedule['entry_time']; // ej. "08:30:00"

    // ==================================================
    // 5) Validar ventana de tiempo para registrar ENTRADA
    //     - Puede checar desde 1 hora antes de su hora de entrada
    //     - Se considera FALTA si intenta checar después de (entrada + 61 min)
    // ==================================================
    $dtEntry = new DateTime("$dateToday $entryTime");

    // Hora mínima para poder registrar entrada (1 hora antes)
    $dtAllowedStart = clone $dtEntry;
    $dtAllowedStart->modify('-1 hour');
    $allowedStart = $dtAllowedStart->format('H:i:s');

    // Límite de falta = entrada + 61 minutos
    $dtLimit = clone $dtEntry;
    $dtLimit->modify('+61 minutes');
    $limitFalta = $dtLimit->format('H:i:s');

    // Demasiado temprano (antes de la ventana permitida)
    if ($hourNow < $allowedStart) {
        $horaMostrada = substr($entryTime, 0, 5); // hh:mm
        return [
            "success" => false,
            "message" => "Todavía no puedes registrar tu entrada. Tu horario de entrada es a las $horaMostrada."
        ];
    }

    // FALTA inmediata si intenta registrar después del límite
    if ($hourNow > $limitFalta) {
        return insertAttendance(
            $pdo,
            $employeeId,
            $dateToday,
            $hourNow,
            "F",
            $latitude,
            $longitude,
            $source
        );
    }

    // ==================================================
    // 6) Calcular minutos tarde respecto a la hora de entrada
    // ==================================================
    $dtNow = new DateTime("$dateToday $hourNow");

    $minutesLate = max(
        0,
        ($dtNow->getTimestamp() - $dtEntry->getTimestamp()) / 60
    );

    // ==================================================
    // 7) Determinar A o R, y actualizar tolerancia
    // ==================================================
    $type = "A"; // default asistencia

    if ($minutesLate > 0) {
        $currentTol = (int) $emp['tolerance_minutes'];

        if ($currentTol > 0) {
            // Todavía tiene tolerancia: se descuenta y sigue siendo A
            $newTol = $currentTol - $minutesLate;
            $upd = $pdo->prepare("
                UPDATE employees 
                SET tolerance_minutes = :tol 
                WHERE id = :id
            ");
            $upd->execute([
                ':tol' => $newTol,
                ':id'  => $employeeId
            ]);

            $type = "A"; // sigue siendo asistencia

        } else {
            // Ya no tiene tolerancia: es retardo
            $newTol = $currentTol - $minutesLate; // puede quedar negativo
            $upd = $pdo->prepare("
                UPDATE employees 
                SET tolerance_minutes = :tol 
                WHERE id = :id
            ");
            $upd->execute([
                ':tol' => $newTol,
                ':id'  => $employeeId
            ]);

            $type = "R";
        }
    }

    // ==================================================
    // 8) Insertar registro de entrada
    // ==================================================
    return insertAttendance(
        $pdo,
        $employeeId,
        $dateToday,
        $hourNow,
        $type,
        $latitude,
        $longitude,
        $source
    );
}

/**
 * Inserta en la base de datos el registro de asistencia
 */
function insertAttendance($pdo, $employeeId, $date, $hour, $type, $lat, $lng, $source)
{
    date_default_timezone_set("America/Monterrey");
    $createdAt = date('Y-m-d H:i:s');

    $stmt = $pdo->prepare("
        INSERT INTO attendance_records 
            (employee_id, attendance_date, attendance_hour, attendance_type, attendance_latitude, attendance_longitude, source, created_at)
        VALUES
            (:emp, :date, :hour, :type, :lat, :lng, :src, :created_at)
    ");

    $stmt->execute([
        ':emp'        => $employeeId,
        ':date'       => $date,
        ':hour'       => $hour,
        ':type'       => $type,
        ':lat'        => $lat,
        ':lng'        => $lng,
        ':src'        => $source,
        ':created_at' => $createdAt,
    ]);

    return [
        "success" => true,
        "message" => "Asistencia registrada",
        "type"    => $type
    ];
}