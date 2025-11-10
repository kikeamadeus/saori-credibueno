<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../helpers/jwt.php';
require_once __DIR__ . '/../../services/sessions/sessionService.php';

$pdo = getConnectionMySql();

//Validar método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Método no permitido (usa POST)"
    ]);
    exit;
}

//Recibir JSON
$input = json_decode(file_get_contents("php://input"), true);
$refreshToken = $input['refresh_token'] ?? null;

if (!$refreshToken) {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "Refresh token faltante"
    ]);
    exit;
}

//Validar firma del refresh token
$validation = validateRefreshToken($refreshToken);
if (!$validation['valid']) {
    echo json_encode(["success" => false, "message" => "REFRESH_INVALID"]);
    exit;
}

$userId = (int)$validation['user_id'];

//Confirmar sesión en BD
$session = findSessionByRefreshToken($pdo, $userId, $refreshToken);
if (!$session || (int)$session['is_revoked'] === 1) {
    echo json_encode(["success" => false, "message" => "SESSION_REVOKED"]);
    exit;
}

//Validar expiración del refresh token guardado en BD
if (strtotime($session['expires_at']) < time()) {
    echo json_encode(["success" => false, "message" => "SESSION_EXPIRED"]);
    exit;
}

//Obtener nombre completo del usuario
$stmtEmp = $pdo->prepare("
    SELECT names, surname1, surname2
    FROM employees
    WHERE id = :id
    LIMIT 1
");
$stmtEmp->execute([':id' => $userId]);
$row = $stmtEmp->fetch(PDO::FETCH_ASSOC);

$employeeFullName = trim(
    $row['names'] . ' ' . $row['surname1'] . ' ' . ($row['surname2'] ?? '')
);

//Crear nuevo access token con el mismo payload
$payload = [
    "id" => $userId,
    "full_name" => $employeeFullName
];

$newAccess = generateToken($payload);

//Registrar uso de sesión
touchSession($pdo, (int)$session['id']);

//Respuesta FINAL para Flutter
echo json_encode([
    "success" => true,
    "access_token" => $newAccess,
    "refresh_token" => $refreshToken,
    "employee" => [
        "id" => $userId,
        "full_name" => $employeeFullName
    ]
]);
exit;
?>