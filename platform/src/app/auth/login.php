<?php
session_start();

require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../helpers/jwt.php';
require_once __DIR__ . '/../../services/auth/loginServices.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Ejecutar lógica central de login
    $result = loginUser($username, $password);

    if ($result['success']) {

        // =====================================================
        // 1) Guardar datos básicos del usuario en $_SESSION
        // =====================================================
        $_SESSION['employee_id'] = $result['employee']['id'];
        $_SESSION['employee_name'] = $result['employee']['full_name'];
        $_SESSION['employee_role'] = $result['employee']['id_role'];

        // Regenerar la sesión por seguridad
        session_regenerate_id(true);

        // =====================================================
        // 2) Guardar Access Token y Refresh Token en cookies
        //    - HttpOnly previene acceso por JavaScript
        //    - Path "/" permite acceso global en la app
        // =====================================================
        setcookie(
            'access_token',
            $result['access_token'],
            time() + 3600, // 1 hora
            '/',
            '',
            false,
            true // HttpOnly
        );

        setcookie(
            'refresh_token',
            $result['refresh_token'],
            time() + (86400 * 90), // 90 días
            '/',
            '',
            false,
            true // HttpOnly
        );

        // =====================================================
        // 3) Evitar caché del navegador (importante para botón atrás)
        // =====================================================
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Pragma: no-cache");

        // =====================================================
        // 4) Redirigir al dashboard principal
        // =====================================================
        header("Location: /main/");
        exit;

    } else {

        // =====================================================
        // Login fallido → mensaje de error en sesión
        // =====================================================
        $_SESSION['error_message'] = $result['message'];

        header("Location: /auth/");
        exit;
    }
}
?>