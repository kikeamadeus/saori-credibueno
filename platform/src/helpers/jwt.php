<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;

/**
 * Obtiene tiempo de expiración del Access Token
 * (Configurado por .env o 3600 segundos)
 */
function getAccessTokenExpiration(): int {
    return $_ENV['JWT_EXPIRE'] ?? 3600; // 1 hora
}

/**
 * Obtiene tiempo de expiración del Refresh Token
 * (Configurado por .env o 90 días)
 */
function getRefreshTokenExpiration(): int {
    return $_ENV['JWT_REFRESH_EXPIRE'] ?? (86400 * 90);
}

/**
 * Genera un Access Token
 */
function generateToken(array $payload): string {
    $secret = defined('JWT_SECRET')
        ? constant('JWT_SECRET')
        : ($_ENV['JWT_SECRET'] ?? 'H4CO_DEFAULT_SECRET_KEY@Arcobit2025@SecurePassKey!');

    $issuedAt = time();
    $expire = $issuedAt + getAccessTokenExpiration();

    $token = [
        'iat'  => $issuedAt,
        'exp'  => $expire,
        'data' => $payload
    ];

    return JWT::encode($token, $secret, 'HS256');
}

/**
 * Valida un Access Token
 *
 * @return array ['valid' => bool, 'expired' => bool, 'data' => array|null, 'error' => string|null]
 */
function validateToken(string $token): array {
    $secret = defined('JWT_SECRET')
        ? constant('JWT_SECRET')
        : ($_ENV['JWT_SECRET'] ?? 'H4CO_DEFAULT_SECRET_KEY@Arcobit2025@SecurePassKey!');

    try {
        $decoded = JWT::decode($token, new Key($secret, 'HS256'));

        return [
            'valid'   => true,
            'expired' => false,
            'data'    => (array) $decoded->data,
            'error'   => null
        ];

    } catch (ExpiredException $e) {

        // Token expirado pero decodificable
        try {
            $parts   = explode('.', $token);
            $payload = json_decode(base64_decode($parts[1]), true);

            return [
                'valid'   => false,
                'expired' => true,
                'data'    => $payload['data'] ?? null,
                'error'   => 'TOKEN_EXPIRED'
            ];
        } catch (Exception $inner) {
            return [
                'valid'   => false,
                'expired' => true,
                'data'    => null,
                'error'   => 'TOKEN_EXPIRED_CORRUPT'
            ];
        }

    } catch (Exception $e) {
        return [
            'valid'   => false,
            'expired' => false,
            'data'    => null,
            'error'   => 'TOKEN_INVALID'
        ];
    }
}

/**
 * Genera un Refresh Token
 * (Se usa para revalidar sesiones largas)
 */
function generateRefreshToken(int $userId): string {
    $secret = defined('JWT_REFRESH_SECRET')
        ? constant('JWT_REFRESH_SECRET')
        : ($_ENV['JWT_REFRESH_SECRET'] ?? 'H4CO_REFRESH_TOKEN_KEY@Arcobit2025!');

    $issuedAt = time();
    $expire   = $issuedAt + getRefreshTokenExpiration();

    $token = [
        'iat'  => $issuedAt,
        'exp'  => $expire,
        'data' => [
            'user_id' => (int) $userId
        ]
    ];

    return JWT::encode($token, $secret, 'HS256');
}

/**
 * Valida un Refresh Token
 *
 * @return array ['valid' => bool, 'expired' => bool, 'user_id' => int|null, 'error' => string|null]
 */
function validateRefreshToken(string $token): array {
    $secret = defined('JWT_REFRESH_SECRET')
        ? constant('JWT_REFRESH_SECRET')
        : ($_ENV['JWT_REFRESH_SECRET'] ?? 'H4CO_REFRESH_TOKEN_KEY@Arcobit2025!');

    try {
        $decoded = JWT::decode($token, new Key($secret, 'HS256'));

        return [
            'valid'   => true,
            'expired' => false,
            'user_id' => isset($decoded->data->user_id) ? (int) $decoded->data->user_id : null,
            'error'   => null
        ];

    } catch (ExpiredException $e) {
        return [
            'valid'   => false,
            'expired' => true,
            'user_id' => null,
            'error'   => 'REFRESH_TOKEN_EXPIRED'
        ];

    } catch (Exception $e) {
        return [
            'valid'   => false,
            'expired' => false,
            'user_id' => null,
            'error'   => 'REFRESH_TOKEN_INVALID'
        ];
    }
}

/**
 * Genera un par de tokens (access + refresh)
 */
function generateTokensPair(int $userId, array $payload): array {
    return [
        'access_token'  => generateToken($payload),
        'refresh_token' => generateRefreshToken($userId)
    ];
}