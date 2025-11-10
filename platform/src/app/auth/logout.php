<?php

session_start();

require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../services/sessions/sessionService.php';
require_once __DIR__ . '/../../helpers/jwt.php';

$pdo = getConnectionMySql();

// ===============================
// 1) Obtener refresh token
// ===============================
$refreshToken = $_COOKIE['refresh_token'] ?? null;

if ($refreshToken) {
    // ===============================
    // 2) Validar estructura del refresh token
    // ===============================
    $validation = validateRefreshToken($refreshToken);

    if ($validation['valid']) {
        $userId = (int)$validation['user_id'];

        // ===============================
        // 3) Buscar sesión en BD
        // ===============================
        $session = findSessionByRefreshToken($pdo, $userId, $refreshToken);

        if ($session) {
            // ===============================
            // 4) Revocar sesión en BD
            // ===============================
            revokeSession($pdo, (int)$session['id']);
        }
    }
}

// ===============================
// 5) Borrar cookies
// ===============================
setcookie('access_token', '', time() - 3600, '/');
setcookie('refresh_token', '', time() - 3600, '/');

// ===============================
// 6) Limpiar sesión PHP
// ===============================
$_SESSION = [];
session_unset();
session_destroy();

// ===============================
// 7) Redirigir a login
// ===============================
header("Location: /auth/");
exit;