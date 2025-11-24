<?php
/**
 * Servicio: Registrar asistencia
 * --------------------------------------
 * Este servicio SOLO inserta en la base de datos.
 * Toda la lógica de negocio debe ocurrir en:
 *    /main/attendance/action/create.php
 */

require_once __DIR__ . '/../../config/bootstrap.php';

function registerAttendance($employeeId, $eventType, $source, $latitude = null, $longitude = null)
{
    $pdo = getConnectionMySql();

    // ==================================================
    // 1) Traer información del empleado
    // ==================================================
    $stmt = $pdo->prepare("
        SELECT e.id, e.id_branch, b.latitude AS branch_lat, b.longitude AS branch_lng
        FROM employees e
        LEFT JOIN branches b ON e.id_branch = b.id
        WHERE e.id = :id
        LIMIT 1
    ");
    $stmt->execute([':id' => $employeeId]);
    $emp = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$emp) {
        return [
            "success" => false,
            "message" => "Empleado no encontrado."
        ];
    }

    // ==================================================
    // 2) GPS según origen
    // ==================================================
    if ($source === 'web') {

        // Desde PC → usar coordenadas de Sucursal
        $latitude  = $emp['branch_lat'];
        $longitude = $emp['branch_lng'];

    } elseif ($source === 'mobile') {

        // Desde móvil → GPS es obligatorio
        if (empty($latitude) || empty($longitude)) {
            return [
                "success" => false,
                "message" => "Se requieren coordenadas para registro móvil."
            ];
        }

    } else {
        return [
            "success" => false,
            "message" => "Origen de asistencia inválido."
        ];
    }

    // ==================================================
    // 3) Preparar datos
    // ==================================================
    $eventDatetime = date("Y-m-d H:i:s");
    $createdAt     = date("Y-m-d H:i:s");

    // ==================================================
    // 4) INSERTAR EN BD
    // ==================================================
    $query = "
        INSERT INTO attendance_records
            (employee_id, event_type, event_datetime, latitude, longitude, source, created_at)
        VALUES
            (:employee_id, :event_type, :event_datetime, :latitude, :longitude, :source, :created_at)
    ";

    $stmtInsert = $pdo->prepare($query);

    $stmtInsert->execute([
        ':employee_id'   => $employeeId,
        ':event_type'    => $eventType,
        ':event_datetime'=> $eventDatetime,
        ':latitude'      => $latitude,
        ':longitude'     => $longitude,
        ':source'        => $source,
        ':created_at'    => $createdAt
    ]);

    return [
        "success" => true,
        "message" => "Registro de asistencia guardado correctamente.",
        "event" => [
            "employee_id" => $employeeId,
            "event_type"  => $eventType,
            "datetime"    => $eventDatetime,
            "latitude"    => $latitude,
            "longitude"   => $longitude,
            "source"      => $source
        ]
    ];
}