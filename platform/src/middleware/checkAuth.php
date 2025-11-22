<?php
/**
 * Middleware de autenticación para SAORI-Credibueno
 * -------------------------------------------------
 * - Valida sesión PHP existente.
 * - Si no hay sesión, valida JWT (access_token y refresh_token).
 * - Reconstruye sesión desde el token si es válido.
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
// 1) Si la sesión ya existe, continuar
// =====================================
if (
    !empty($_SESSION['employee_id']) &&
    !empty($_SESSION['employee_name']) &&
    isset($_SESSION['employee_role'])
) {
    return; // Sesión válida → continuar
}

// =====================================
// 2) Validar access_token (JWT)
// =====================================
$accessToken = $_COOKIE['access_token'] ?? null;

if ($accessToken) {

    $validation = validateToken($accessToken);

    if ($validation['valid'] && !$validation['expired']) {

        // ===============================
        // 🔁 Reconstruir sesión desde token
        // ===============================
        if (!empty($validation['data'])) {

            $data = $validation['data'];

            $fullName = trim(
                ($data['name'] ?? '') . ' ' .
                ($data['surname1'] ?? '') . ' ' .
                ($data['surname2'] ?? '')
            );

            $_SESSION['employee_id']   = $data['id'] ?? 0;
            $_SESSION['employee_name'] = $fullName;
            $_SESSION['employee_role'] = $data['id_role'] ?? null;
        }

        return; // Token válido → permitir acceso
    }

    // token expirado → intentar refrescar
    if ($validation['expired']) {
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
// 4) Ninguna autenticación válida → login
// =====================================
header("Location: /auth/");
exit;
?>