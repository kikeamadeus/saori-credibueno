<?php
require_once __DIR__ . '/../../services/auth/loginServices.php';
$errors = require __DIR__ . '/../../config/errors.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "code"    => "METHOD_NOT_ALLOWED",
        "message" => "Método no permitido. Usa POST"
    ]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);
$username = $input['username'] ?? '';
$password = $input['password'] ?? '';

$result = loginUser($username, $password);

if ($result['success']) {
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "message" => $result['message'],
        "employee" => $result['employee'] ?? null,
        "access_token" => $result['access_token'],
        "refresh_token" => $result['refresh_token'],
    ]);
} else {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "code"    => $result['code'] ?? "AUTH_INVALID_CREDENTIALS",
        "message" => $result['message'] ?? $errors['AUTH_INVALID_CREDENTIALS']
    ]);
}