<?php
session_start();

require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../helpers/jwt.php';
require_once __DIR__ . '/../../services/auth/loginServices.php';
require_once __DIR__ . '/../../helpers/permissions.php'; // ⬅ NUEVO

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Ejecutar lógica central de login
    $result = loginUser($username, $password);

    if ($result['success']) {

        // =====================================================
        // 1) Guardar datos básicos del usuario en $_SESSION
        // =====================================================
        $_SESSION['employee_id']   = $result['employee']['id'];
        $_SESSION['employee_name'] = $result['employee']['full_name'];
        $_SESSION['employee_role'] = $result['employee']['id_role'];

        // =====================================================
        // 2) Guardar permisos en sesión (CRUCIAL)
        // =====================================================
        $_SESSION['permissions'] = $result['employee']['permissions'] ?? [];

        // Regenerar la sesión por seguridad
        session_regenerate_id(true);

        // =====================================================
        // 3) Guardar Access Token y Refresh Token en cookies
        // =====================================================
        setcookie(
            'access_token',
            $result['access_token'],
            time() + 3600,
            '/',
            '',
            false,
            true
        );

        setcookie(
            'refresh_token',
            $result['refresh_token'],
            time() + (86400 * 90),
            '/',
            '',
            false,
            true
        );

        // =====================================================
        // 4) Evitar caché
        // =====================================================
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Pragma: no-cache");

        // =====================================================
        // 5) Redirigir a dashboard
        // =====================================================
        header("Location: /main/");
        exit;

    } else {
        // =====================================================
        // Login fallido → mensaje de error
        // =====================================================
        $_SESSION['error_message'] = $result['message'];

        header("Location: /auth/");
        exit;
    }
}