<?php
/**
 * ==========================================================
 *  API: Registrar Asistencia (Flutter / Mobile)
 * ----------------------------------------------------------
 *  Método: POST
 *  Ruta:   /api/attendance/register.php
 *
 *  Body (JSON):
 *  {
 *      "employee_id": 2,
 *      "latitude": 25.12345,
 *      "longitude": -103.12345,
 *      "source": "mobile"
 *  }
 *
 *  Requiere:
 *      - Access Token válido (Bearer)
 * ==========================================================
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../helpers/jwt.php';
require_once __DIR__ . '/../../services/attendance/attendanceServices.php';

// ==========================================================
// 1) Solo permitir POST
// ==========================================================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Método no permitido, usa POST"
    ]);
    exit;
}

// ==========================================================
// 2) Validar Access Token (Bearer Token)
// ==========================================================
$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "Token no enviado"
    ]);
    exit;
}

$token = $matches[1];
$validation = validateToken($token);

if (!$validation['valid']) {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "Token inválido o expirado"
    ]);
    exit;
}

// ==========================================================
// 3) Leer input JSON
// ==========================================================
$input = json_decode(file_get_contents("php://input"), true);

$employeeId = $input['employee_id'] ?? null;
$latitude   = $input['latitude'] ?? null;
$longitude  = $input['longitude'] ?? null;
$source     = $input['source'] ?? 'mobile';

// Validaciones básicas
if (!$employeeId) {
    echo json_encode([
        "success" => false,
        "message" => "employee_id requerido"
    ]);
    exit;
}

if ($source !== 'web' && $source !== 'mobile') {
    echo json_encode([
        "success" => false,
        "message" => "source inválido"
    ]);
    exit;
}

// ==========================================================
// 4) Registrar asistencia usando tu servicio principal
// ==========================================================
$result = registerAttendance($employeeId, $source, $latitude, $longitude);

// ==========================================================
// 5) Respuesta final
// ==========================================================
if ($result['success'] ?? false) {
    http_response_code(200);
} else {
    http_response_code(400);
}

echo json_encode($result);
exit;
