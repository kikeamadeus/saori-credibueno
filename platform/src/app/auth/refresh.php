<?php
// src/app/auth/refresh.php

require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../helpers/jwt.php';
require_once __DIR__ . '/../../services/sessions/sessionService.php';

$pdo = getConnectionMySql();

/** 1) Tomar refresh token de cookie */
$refreshToken = $_COOKIE['refresh_token'] ?? null;
if (!$refreshToken) {
    header("Location: /auth/");
    exit;
}

/** 2) Validar refresh (estructura/firma/exp) */
$validation = validateRefreshToken($refreshToken); // ver sección 3
if (!$validation['valid']) {
    // limpiar cookies por higiene
    setcookie('access_token', '', time() - 3600, '/');
    setcookie('refresh_token', '', time() - 3600, '/');
    header("Location: /auth/");
    exit;
}

$userId = (int)$validation['user_id'];

/** 3) Confirmar sesión en BD vía servicio */
$session = findSessionByRefreshToken($pdo, $userId, $refreshToken);
if (!$session || (int)$session['is_revoked'] === 1) {
    setcookie('access_token', '', time() - 3600, '/');
    setcookie('refresh_token', '', time() - 3600, '/');
    header("Location: /auth/");
    exit;
}

/** 4) Ver caducidad en BD */
if (strtotime($session['expires_at']) < time()) {
    setcookie('access_token', '', time() - 3600, '/');
    setcookie('refresh_token', '', time() - 3600, '/');
    header("Location: /auth/");
    exit;
}

/** 5) Emitir nuevo access token */
$payload = [
    'id'   => $userId,
    // Si quieres, aquí podrías obtener el nombre desde BD (servicio de usuarios) y ponerlo
];
$newAccessToken = generateToken($payload);

/** 6) Setear cookie del access token (sin imprimir nada antes) */
setcookie(
    'access_token',
    $newAccessToken,
    time() + (int)($_ENV['JWT_EXPIRE'] ?? 3600),
    '/',
    '',
    false,
    true
);

/** 7) Tocar/actualizar la sesión en BD */
touchSession($pdo, (int)$session['id']);

/** 8) Volver al dashboard (URL amigable) */
header("Location: /main/");
exit;