<?php
/**
 * Middleware de autenticación para SAORI-Credibueno
 * -------------------------------------------------
 * - Valida sesión PHP existente (rápido).
 * - Si no hay sesión, valida JWT (access_token y refresh_token).
 * - Redirige al login o al refresh según corresponda.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../helpers/jwt.php';

// =====================================
// 0) Evitar caché del navegador
// =====================================
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

// =====================================
// 1) Validar sesión PHP activa
// =====================================
if (!empty($_SESSION['employee_id']) && !empty($_SESSION['employee_name'])) {
    // Ya hay sesión iniciada → permitir continuar
    return;
}

// =====================================
// 2) Validar access_token (JWT)
// =====================================
$accessToken = $_COOKIE['access_token'] ?? null;

if ($accessToken) {
    $validation = validateToken($accessToken);

    if ($validation['valid'] && !$validation['expired']) {
        // Token válido → permitir acceso
        return;
    }

    if ($validation['expired']) {
        // Token expirado → ir a refresh
        header("Location: /auth/refresh.php");
        exit;
    }
}

// =====================================
// 3) Intentar con refresh_token
// =====================================
$refreshToken = $_COOKIE['refresh_token'] ?? null;

if ($refreshToken) {
    header("Location: /auth/refresh.php");
    exit;
}

// =====================================
// 4) Ninguna autenticación → login
// =====================================
header("Location: /auth/");
exit;