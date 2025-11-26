<?php
// src/app/auth/refresh.php

require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../helpers/jwt.php';
require_once __DIR__ . '/../../services/sessions/sessionService.php';
require_once __DIR__ . '/../../services/auth/permissionServices.php'; // << NUEVO

$pdo = getConnectionMySql();

/** 1) Tomar refresh token de cookie */
$refreshToken = $_COOKIE['refresh_token'] ?? null;
if (!$refreshToken) {
    header("Location: /auth/");
    exit;
}

/** 2) Validar refresh token */
$validation = validateRefreshToken($refreshToken);
if (!$validation['valid']) {
    // limpiar cookies
    setcookie('access_token', '', time() - 3600, '/');
    setcookie('refresh_token', '', time() - 3600, '/');
    header("Location: /auth/");
    exit;
}

$userId = (int)$validation['user_id'];

/** 3) Confirmar sesión en BD */
$session = findSessionByRefreshToken($pdo, $userId, $refreshToken);
if (!$session || (int)$session['is_revoked'] === 1) {
    setcookie('access_token', '', time() - 3600, '/');
    setcookie('refresh_token', '', time() - 3600, '/');
    header("Location: /auth/");
    exit;
}

/** 4) Verificar expiración */
if (strtotime($session['expires_at']) < time()) {
    setcookie('access_token', '', time() - 3600, '/');
    setcookie('refresh_token', '', time() - 3600, '/');
    header("Location: /auth/");
    exit;
}

/** 5) Obtener datos del empleado */
$stmt = $pdo->prepare("
    SELECT names, surname1, surname2, id_role
    FROM employees
    WHERE id = :id
    LIMIT 1
");
$stmt->execute([':id' => $userId]);
$emp = $stmt->fetch(PDO::FETCH_ASSOC);

$fullName = trim($emp['names'] . ' ' . $emp['surname1'] . ' ' . ($emp['surname2'] ?? ''));
$roleId   = (int)$emp['id_role'];

/** 6) Obtener permisos del rol */
$permissions = getPermissionsByRole($pdo, $roleId);

/** 7) Crear nuevo Access Token */
$payload = [
    "id"       => $userId,
    "full_name"=> $fullName,
    "role_id"  => $roleId,
    "permissions" => array_keys($permissions) // opcional incluir
];

$newAccessToken = generateToken($payload);

/** 8) Guardar sesión reconstruida */
session_start();
$_SESSION['employee_id']   = $userId;
$_SESSION['employee_name'] = $fullName;
$_SESSION['employee_role'] = $roleId;
$_SESSION['permissions']   = $permissions;

/** 9) Actualizar cookie del access token */
setcookie(
    'access_token',
    $newAccessToken,
    time() + (int)($_ENV['JWT_EXPIRE'] ?? 3600),
    '/',
    '',
    false,
    true
);

/** 10) Actualizar sesión en BD */
touchSession($pdo, (int)$session['id']);

/** 11) Redirigir al dashboard */
header("Location: /main/");
exit;
