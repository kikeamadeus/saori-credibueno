<?php
/**
 * Servicios de validación de horarios para asistencia
 * ---------------------------------------------------
 * Este archivo SOLO contiene consultas y funciones
 * auxiliares para validar horarios y registros previos.
 */

require_once __DIR__ . '/../../config/bootstrap.php';

/**
 * Verificar si el empleado ya registró ENTRADA hoy
 * entre 07:30:00 y 09:30:00.
 */
function hasEntradaToday(int $employeeId): bool
{
    $pdo = getConnectionMySql();

    $stmt = $pdo->prepare("
        SELECT COUNT(*) AS total
        FROM attendance_records
        WHERE employee_id = :id
          AND event_type = 'entrada'
          AND DATE(event_datetime) = CURDATE()
          AND TIME(event_datetime) BETWEEN '07:30:00' AND '09:30:00'
        LIMIT 1
    ");

    $stmt->execute(['id' => $employeeId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return ($row['total'] ?? 0) > 0;
}

/**
 * Verifica si la hora actual es MAYOR a 09:30:00.
 */
function isLateForEntrada(DateTime $now): bool
{
    $limit = new DateTime($now->format("Y-m-d") . " 09:30:00");
    return $now > $limit;
}

/**
 * Verifica si el empleado ya tiene una FALTA hoy
 */
function hasFaltaToday(int $employeeId): bool
{
    $pdo = getConnectionMySql();

    $today = date("Y-m-d");

    $stmt = $pdo->prepare("
        SELECT id 
        FROM attendance_records
        WHERE employee_id = :id
          AND DATE(event_datetime) = :today
          AND event_type = 'falta'
        LIMIT 1
    ");
    
    $stmt->execute([
        ':id' => $employeeId,
        ':today' => $today
    ]);

    return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
}
?>