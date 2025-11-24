<?php
require_once __DIR__ . '/../../../../middleware/checkAuth.php';

require_once APP_PATH . '/services/branches/branchesServices.php';
require_once APP_PATH . '/services/attendance/attendanceServices.php';
require_once APP_PATH . '/services/schedules/scheduleServices.php';

// ===========================================================
// 1) Zona horaria
// ===========================================================
date_default_timezone_set("America/Monterrey");
//$now = new DateTime(); // hora local exacta
$now = new DateTime("2025-11-23 08:20:00"); // <-- Simulación de hora manual

// ===========================================================
// 2) ID del empleado
// ===========================================================
$employeeId = $_SESSION['employee_id'] ?? null;

if (!$employeeId) {
    header("Location: /auth/");
    exit;
}

// ===========================================================
// 3) Datos del empleado
// ===========================================================
$empData = getEmployeeWithBranch((int)$employeeId);

if (!$empData) {
    header("Location: /main/?error=employee_not_found");
    exit;
}

// ===========================================================
// 3.1) Validación extra: si tiene FALTA hoy, no puede registrar nada
// ===========================================================
if (hasFaltaToday($employeeId)) {

    echo "<script>
            alert('Hoy ya se registró una FALTA. No es posible registrar asistencia hasta mañana.');
            window.location.href='../../';
          </script>";
    exit;
}

// ===========================================================
// 3.2) Obtener lat/long desde SUCURSAL (de inmediato)
// ===========================================================
$latitude  = $empData['latitude'] ?? null;
$longitude = $empData['longitude'] ?? null;

if (!$latitude || !$longitude) {
    echo "<script>
            alert('Tu sucursal no tiene coordenadas configuradas. Contacta a sistemas.');
            window.location.href='../../';
          </script>";
    exit;
}


// ===========================================================
// 4) Validación #1: ¿Ya registró entrada hoy?
// ===========================================================
if (hasEntradaToday($employeeId)) {
    echo "<script>
            alert('Ya registraste tu entrada. Espera hasta tu hora de comida para volver a registrar asistencia.');
            window.location.href='../../';
          </script>";
    exit;
}


// ===========================================================
// 5) Validación #2: ¿Ya pasaron las 9:30 am?
// ===========================================================
if (isLateForEntrada($now)) {

    // Registrar falta automáticamente (ya tenemos lat/long aquí)
    registerAttendance(
        $employeeId,
        'falta',
        'web',
        $latitude,
        $longitude
    );

    echo "<script>
            alert('Ya no es posible registrar tu entrada. Se registró una FALTA.');
            window.location.href='../../';
          </script>";
    exit;
}

// ===========================================================
// 6) Preparar el evento (solo ENTRADA en esta fase)
// ===========================================================
$eventType = "entrada";
$source = "web";

$latitude  = $empData['latitude'] ?? null;
$longitude = $empData['longitude'] ?? null;

if (!$latitude || !$longitude) {
    echo "<script>
            alert('Tu sucursal no tiene coordenadas configuradas. Contacta a sistemas.');
            window.location.href='../../';
          </script>";
    exit;
}

// ===========================================================
// 7) Registrar asistencia
// ===========================================================
$response = registerAttendance(
    $employeeId,
    $eventType,
    $source,
    $latitude,
    $longitude
);

// ===========================================================
// 8) Respuesta final
// ===========================================================
if ($response['success']) {
    echo "<script>
            alert('Asistencia registrada correctamente.');
            window.location.href='../../';
          </script>";
} else {
    echo "<script>
            alert('No fue posible registrar la asistencia.');
            window.location.href='../../';
          </script>";
}
exit;
