<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../helpers/jwt.php';
require_once __DIR__ . '/../../services/sessions/sessionService.php';
require_once __DIR__ . '/../../services/auth/permissionServices.php'; // << NUEVO

$pdo = getConnectionMySql();

/** 1) Validar POST */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Método no permitido"
    ]);
    exit;
}

/** 2) Obtener refresh_token */
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

/** 3) Validar firma/exp del refresh token */
$validation = validateRefreshToken($refreshToken);
if (!$validation['valid']) {
    echo json_encode(["success" => false, "message" => "REFRESH_INVALID"]);
    exit;
}

$userId = (int)$validation['user_id'];

/** 4) Confirmar sesión en BD */
$session = findSessionByRefreshToken($pdo, $userId, $refreshToken);
if (!$session || (int)$session['is_revoked'] === 1) {
    echo json_encode(["success" => false, "message" => "SESSION_REVOKED"]);
    exit;
}

/** 5) Validar expiración */
if (strtotime($session['expires_at']) < time()) {
    echo json_encode(["success" => false, "message" => "SESSION_EXPIRED"]);
    exit;
}

/** 6) Obtener datos del empleado */
$stmtEmp = $pdo->prepare("
    SELECT names, surname1, surname2, id_role
    FROM employees
    WHERE id = :id
    LIMIT 1
");
$stmtEmp->execute([':id' => $userId]);
$row = $stmtEmp->fetch(PDO::FETCH_ASSOC);

$fullName = trim($row['names'] . ' ' . $row['surname1'] . ' ' . ($row['surname2'] ?? ''));
$roleId   = (int)$row['id_role'];

/** 7) Obtener permisos del rol */
$permissions = getPermissionsByRole($pdo, $roleId);

/** 8) Crear nuevo Access Token */
$payload = [
    "id"          => $userId,
    "full_name"   => $fullName,
    "id_role"     => $roleId,
    "permissions" => array_keys($permissions)
];

$newAccess = generateToken($payload);

/** 9) Actualizar estado de sesión */
touchSession($pdo, (int)$session['id']);

/** 10) Respuesta para Flutter */
echo json_encode([
    "success"       => true,
    "access_token"  => $newAccess,
    "refresh_token" => $refreshToken,
    "employee" => [
        "id"         => $userId,
        "full_name"  => $fullName,
        "id_role"    => $roleId,
        "permissions"=> $permissions
    ]
]);
exit;