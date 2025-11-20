<?php

require_once __DIR__ . '/../../config/bootstrap.php';

/**
 * Obtener todos los roles
 */
function getAllRoles(): array {
    $pdo = getConnectionMySql();

    $stmt = $pdo->query("
        SELECT 
            id,
            name,
            description,
            created_at
        FROM roles
        ORDER BY id ASC
    ");

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Obtener un rol por su ID
 */
function getRoleById(int $id): ?array {
    $pdo = getConnectionMySql();

    $stmt = $pdo->prepare("
        SELECT 
            id,
            name,
            description,
            created_at
        FROM roles
        WHERE id = :id
        LIMIT 1
    ");
    $stmt->execute(['id' => $id]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ?: null;
}
