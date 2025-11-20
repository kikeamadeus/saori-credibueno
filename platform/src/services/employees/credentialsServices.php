<?php

require_once __DIR__ . '/../../config/bootstrap.php';

/**
 * Obtener el próximo ID para generar usuario CHA00XX
 */
function getNextEmployeeUsername(): string
{
    $pdo = getConnectionMySql();

    $stmt = $pdo->query("SELECT MAX(id) AS last_id FROM employees");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $nextId = ($row['last_id'] ?? 0) + 1;

    // Prefijo + ceros + número real
    // CHA0001, CHA0002, CHA0010, CHA0123...
    return "CHA" . str_pad($nextId, 4, "0", STR_PAD_LEFT);
}

/**
 * Generar contraseña aleatoria de 10 caracteres
 */
function generateRandomPassword(int $length = 8): string
{
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $password = '';

    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, strlen($chars) - 1)];
    }

    return $password;
}

/**
 * Servicio principal: regresa usuario + password
 */
function generateEmployeeCredentials(): array
{
    return [
        'username' => getNextEmployeeUsername(),
        'password' => generateRandomPassword(8)
    ];
}