<?php
/**
 * Servicios de validación de horarios para asistencia
 * ---------------------------------------------------
 * Este archivo SOLO contiene consultas y funciones
 * auxiliares para validar horarios y registros previos.
 */

require_once __DIR__ . '/../../config/bootstrap.php';

/**
 * Crea los horarios base para un empleado recién creado.
 */
function createDefaultSchedule(int $employeeId): bool
{
    $pdo = getConnectionMySql();

    // Horario base Credibueno
    $standardSchedule = [
        1 => ['08:30:00', '14:00:00', '16:00:00', '18:00:00'], // Lunes
        2 => ['08:30:00', '14:00:00', '16:00:00', '18:00:00'], // Martes
        3 => ['08:30:00', '14:00:00', '16:00:00', '18:00:00'], // Miércoles
        4 => ['08:30:00', '14:00:00', '16:00:00', '18:00:00'], // Jueves
        5 => ['08:30:00', '14:00:00', '16:00:00', '18:00:00'], // Viernes

        // Sábado (media jornada)
        6 => ['09:00:00', null, null, '14:00:00'],

        // Domingo (descanso)
        7 => [null, null, null, null]
    ];

    $sql = "
        INSERT INTO schedules
        (employee_id, day_of_week, entry_time, lunch_out_time, lunch_in_time, exit_time, created_at)
        VALUES
        (:employee_id, :day_of_week, :entry_time, :lunch_out_time, :lunch_in_time, :exit_time, NOW())
    ";

    $stmt = $pdo->prepare($sql);

    foreach ($standardSchedule as $day => $times) {
        [$entry, $lunchOut, $lunchIn, $exit] = $times;

        $stmt->execute([
            ':employee_id'  => $employeeId,
            ':day_of_week'  => $day,
            ':entry_time'   => $entry,
            ':lunch_out_time' => $lunchOut,
            ':lunch_in_time'  => $lunchIn,
            ':exit_time'    => $exit
        ]);
    }

    return true;
}
?>