<?php
/**
 * Servicio: Registrar asistencia (versión simple / fase 1)
 * -------------------------------------------------------
 * Reglas aplicadas en esta versión:
 * - Solo registra ENTRADA
 * - Determina A, R o F según hora real vs hora oficial
 * - Descuenta tolerancia
 */

require_once __DIR__ . '/../../config/bootstrap.php';

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
 * Función principal para registrar asistencia
 */
function registerAttendance($employeeId, $source, $latitude = null, $longitude = null)
{
    $pdo = getConnectionMySql();
    date_default_timezone_set("America/Monterrey");

    // ==================================================
    // 0) Validar que hoy no tenga ya asistencia
    // ==================================================
    if (hasAttendanceToday($pdo, (int)$employeeId)) {
        return [
            "success" => false,
            "message" => "Ya registraste tu asistencia el día de hoy."
        ];
    }

    // ... aquí sigue TODO lo que ya tienes:
    // - traer empleado
    // - validar sucursal / GPS
    // - calcular tipo (A / R / F)
    // - insertar en attendance_records

    // ==================================================
    // 1) Obtener datos del empleado + sucursal
    // ==================================================
    $stmt = $pdo->prepare("
        SELECT 
            e.id,
            e.tolerance_minutes,
            e.entry_time_weekday,
            e.entry_time_saturday,
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
            return ["success" => false, "message" => "Sucursal sin coordenadas"];
        }
    } elseif ($source === 'mobile') {
        if (!$latitude || !$longitude) {
            return ["success" => false, "message" => "GPS requerido"];
        }
    } else {
        return ["success" => false, "message" => "Origen inválido"];
    }

    // ==================================================
    // 3) Hora actual
    // ==================================================
    $now = new DateTime();
    $hourNow = $now->format("H:i");
    $dateToday = $now->format("Y-m-d");

    // ==================================================
    // 4) Determinar horario oficial según día
    // ==================================================
    $dayOfWeek = (int)$now->format("N"); // 1=Lun ... 7=Dom

    if ($dayOfWeek >= 1 && $dayOfWeek <= 5) {
        // Lunes a Viernes
        $entryTime = $emp['entry_time_weekday']; // 08:30:00
        $limitFalta = "09:31:00";
    } elseif ($dayOfWeek === 6) {
        // Sábado
        $entryTime = $emp['entry_time_saturday']; // 09:00:00
        $limitFalta = "09:31:00";
    } else {
        // Domingo → siempre asistencia A
        return insertAttendance($pdo, $employeeId, $dateToday, $hourNow, "A", $latitude, $longitude, $source);
    }

    // ==================================================
    // 5) Calcular minutos tarde
    // ==================================================
    $dtEntry  = new DateTime("$dateToday $entryTime");
    $dtNow    = new DateTime("$dateToday $hourNow");

    $minutesLate = max(0, ($dtNow->getTimestamp() - $dtEntry->getTimestamp()) / 60);

    // ==================================================
    // 6) Determinar A, R o F
    // ==================================================

    // FALTA automátcia
    if ($hourNow >= $limitFalta) {
        return insertAttendance($pdo, $employeeId, $dateToday, $hourNow, "F", $latitude, $longitude, $source);
    }

    $type = "A"; // default asistencia

    if ($minutesLate > 0) {

        if ($emp['tolerance_minutes'] > 0) {

            // Usa tolerancia
            $newTol = $emp['tolerance_minutes'] - $minutesLate;

            $upd = $pdo->prepare("
                UPDATE employees SET tolerance_minutes = :tol WHERE id = :id
            ");
            $upd->execute([
                ':tol' => $newTol,
                ':id'  => $employeeId
            ]);

            $type = "A"; // Mientras tenga tolerancia → Asistencia

        } else {

            // No hay tolerancia: es retardo
            $type = "R";
        }
    }

    // ==================================================
    // 7) Insertar registro
    // ==================================================
    return insertAttendance($pdo, $employeeId, $dateToday, $hourNow, $type, $latitude, $longitude, $source);
}


/**
 * Inserta en la base de datos el registro de asistencia
 */
function insertAttendance($pdo, $employeeId, $date, $hour, $type, $lat, $lng, $source)
{
    date_default_timezone_set("America/Monterrey");
    // Usar hora local definida en registerAttendance()
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