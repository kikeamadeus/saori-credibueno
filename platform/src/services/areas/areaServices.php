<?php

require_once __DIR__ . '/../../config/bootstrap.php';

/**
 * Obtener todas las áreas
 */
function getAllAreas(): array {
    $pdo = getConnectionMySql();

    $stmt = $pdo->query("
        SELECT 
            id,
            name,
            created_at
        FROM areas
        ORDER BY name ASC
    ");

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Obtener un área por su ID
 */
function getAreaById(int $id): ?array {
    $pdo = getConnectionMySql();

    $stmt = $pdo->prepare("
        SELECT 
            id,
            name,
            created_at
        FROM areas
        WHERE id = :id
        LIMIT 1
    ");
    $stmt->execute(['id' => $id]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ?: null;
}