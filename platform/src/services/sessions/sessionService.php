<?php
// src/services/sessions/sessionService.php

/**
 * Busca una sesión por user_id + refresh_token
 */
function findSessionByRefreshToken(PDO $pdo, int $userId, string $refreshToken): ?array {
    $stmt = $pdo->prepare("
        SELECT id, user_id, refresh_token, expires_at, is_revoked, last_used_at
        FROM sessions
        WHERE user_id = :user_id
          AND refresh_token = :refresh_token
        LIMIT 1
    ");
    $stmt->execute([
        ':user_id' => $userId,
        ':refresh_token' => $refreshToken
    ]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

/**
 * Marca uso de sesión
 */
function touchSession(PDO $pdo, int $sessionId): void {
    $stmt = $pdo->prepare("UPDATE sessions SET last_used_at = NOW() WHERE id = :id");
    $stmt->execute([':id' => $sessionId]);
}

/**
 * Revoca una sesión
 */
function revokeSession(PDO $pdo, int $sessionId): void {
    $stmt = $pdo->prepare("UPDATE sessions SET is_revoked = 1 WHERE id = :id");
    $stmt->execute([':id' => $sessionId]);
}

/**
 * Crea registro de sesión (para usarlo desde login)
 */
function createSession(PDO $pdo, int $userId, string $refreshToken, \DateTimeInterface $expiresAt): int {
    $stmt = $pdo->prepare("
        INSERT INTO sessions (user_id, refresh_token, expires_at, is_revoked, created_at, last_used_at)
        VALUES (:user_id, :refresh_token, :expires_at, 0, NOW(), NOW())
    ");
    $stmt->execute([
        ':user_id' => $userId,
        ':refresh_token' => $refreshToken,
        ':expires_at' => $expiresAt->format('Y-m-d H:i:s'),
    ]);
    return (int)$pdo->lastInsertId();
}