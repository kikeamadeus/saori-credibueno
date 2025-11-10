<?php
require_once __DIR__ . '/../../services/employees/profileServices.php';
$errors = require __DIR__ . '/../../config/errors.php';

header('Content-Type: application/json; charset=utf-8');

// =====================================================
// VERIFICAR MÉTODO
// =====================================================
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "code" => "METHOD_NOT_ALLOWED",
        "message" => $errors['METHOD_NOT_ALLOWED'] ?? "Método no permitido. Usa GET."
    ]);
    exit;
}

// =====================================================
// OBTENER TOKEN
// =====================================================
$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? '';

if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "code" => "TOKEN_MISSING",
        "message" => $errors['AUTH_UNAUTHORIZED'] ?? "No se proporcionó token de autenticación."
    ]);
    exit;
}

$token = $matches[1];

// =====================================================
// LLAMAR AL SERVICIO
// =====================================================
$result = getEmployeeProfile($token);

// =====================================================
// MANEJO DE RESPUESTAS
// =====================================================

// Caso especial: Token expirado pero decodificable
if (isset($result['expired']) && $result['expired'] === true) {
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "warning" => "TOKEN_EXPIRED",
        "employee" => $result['employee'], // devuelve id y nombre
        "message" => "El token expiró, pero el perfil se recuperó desde el payload."
    ]);
    exit;
}

// Caso general
http_response_code(
    $result['success']
        ? 200
        : (($result['code'] ?? '') === 'DB_ERROR' ? 500 : 401)
);

echo json_encode($result);
?>