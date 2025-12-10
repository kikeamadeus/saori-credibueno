<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../helpers/jwt.php';

$pdo = getConnectionMySql();

// Validar token (igual que create.php)
$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Token no enviado"]);
    exit;
}

$token = $matches[1];
$validation = validateToken($token);

if (!$validation['valid']) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Token inválido"]);
    exit;
}

$employeeId = $validation['data']['id'];

date_default_timezone_set("America/Monterrey");
$today = date('Y-m-d');

$stmt = $pdo->prepare("
    SELECT
        attendance_date,
        attendance_hour,
        attendance_type,
        source
    FROM attendance_records
    WHERE employee_id = :emp
      AND attendance_date = :today
    ORDER BY attendance_hour ASC
");

$stmt->execute([
    ':emp' => $employeeId,
    ':today' => $today
]);

echo json_encode([
    "success" => true,
    "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)
]);