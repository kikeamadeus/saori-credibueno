<?php
/**
 * Reglas de despliegue por rol
 */

// Roles que pueden registrar asistencia
function roleCanRegisterAttendance($roleId) {
    // Ajusta los IDs según tu tabla "roles"
    // 1 = administrador
    // 2 = sistemas
    // 3 = auxiliar administrativo
    // 4 = empleado

    $allowed = [2, 3, 4];

    return in_array($roleId, $allowed);
}