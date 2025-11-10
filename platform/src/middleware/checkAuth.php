<?php

// Iniciar sesión solo si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../helpers/jwt.php';

// =====================================
// 1) Verificar si existe access_token
// =====================================
$accessToken = $_COOKIE['access_token'] ?? null;

if ($accessToken) {
    $validation = validateToken($accessToken);

    // Token válido → permitir acceso
    if ($validation['valid'] && !$validation['expired']) {
        return; // Puede continuar el script
    }

    // Token expirado → enviar a refresh
    if ($validation['expired']) {
        header("Location: /auth/refresh.php");
        exit;
    }
}

// =====================================
// 2) Intentar con refresh_token si existe
// =====================================
$refreshToken = $_COOKIE['refresh_token'] ?? null;

if ($refreshToken) {
    header("Location: /auth/refresh.php");
    exit;
}

// =====================================
// 3) No hay tokens → usuario no autenticado
// =====================================
header("Location: /auth/");
exit;