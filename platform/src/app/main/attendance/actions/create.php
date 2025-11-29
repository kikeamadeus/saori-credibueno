<?php
require_once __DIR__ . '/../../../../middleware/checkAuth.php';

require_once APP_PATH . '/services/branches/branchesServices.php';
require_once APP_PATH . '/services/attendance/attendanceServices.php';

// ==============================================================
// 1) Zona horaria
// ==============================================================
date_default_timezone_set("America/Monterrey");

// ==============================================================
// 2) ID del empleado
// ==============================================================
$employeeId = $_SESSION['employee_id'] ?? null;

if (!$employeeId) {
    header("Location: /auth/");
    exit;
}

// ==============================================================
// 3) Obtener datos del empleado + sucursal
// ==============================================================
$empData = getEmployeeWithBranch((int)$employeeId);

if (!$empData) {
    echo "<script>
            alert('No se encontró la información del empleado.');
            window.location.href='../../';
          </script>";
    exit;
}

$latitude  = $empData['latitude'] ?? null;
$longitude = $empData['longitude'] ?? null;

// Coordenadas obligatorias (solo Web aquí)
if (!$latitude || !$longitude) {
    echo "<script>
            alert('Tu sucursal no tiene coordenadas configuradas. Contacta a sistemas.');
            window.location.href='../../';
          </script>";
    exit;
}

// ==============================================================
// 4) Registrar asistencia (toda la lógica está en el servicio)
// ==============================================================
$response = registerAttendance(
    $employeeId,
    "web",
    $latitude,
    $longitude
);

// ==============================================================
// 5) Respuesta final
// ==============================================================
$message = $response['message'] ?? "No fue posible registrar la asistencia.";

echo "<script>
        alert('$message');
        window.location.href='../../';
      </script>";
exit;

