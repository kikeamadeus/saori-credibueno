<?php

require_once __DIR__ . '/../../config/bootstrap.php';

/**
 * Obtener todas las sucursales activas
 */
function getAllBranches(bool $includeInactive = false): array {
    $pdo = getConnectionMySql();

    $sql = "
        SELECT 
            id,
            name,
            address,
            city,
            state,
            zip_code,
            latitude,
            longitude,
            checkin_radius_meters,
            is_active,
            created_at
        FROM branches
    ";

    if (!$includeInactive) {
        $sql .= " WHERE is_active = 1";
    }

    $sql .= " ORDER BY id ASC";

    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Obtener sucursal por ID
 */
function getBranchById(int $id): ?array {
    $pdo = getConnectionMySql();

    $stmt = $pdo->prepare("
        SELECT 
            id,
            name,
            address,
            city,
            state,
            zip_code,
            latitude,
            longitude,
            checkin_radius_meters,
            is_active,
            created_at
        FROM branches
        WHERE id = :id
        LIMIT 1
    ");
    $stmt->execute(['id' => $id]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ?: null;
}

/**
 * Insertar una nueva sucursal
 */
function insertBranch(array $data): bool {
    $pdo = getConnectionMySql();

    $stmt = $pdo->prepare("
        INSERT INTO branches (
            name, address, city, state, zip_code, 
            latitude, longitude, checkin_radius_meters,
            is_active, created_at
        ) VALUES (
            :name, :address, :city, :state, :zip_code,
            :latitude, :longitude, :radius,
            1, NOW()
        )
    ");

    return $stmt->execute([
        'name'      => $data['name'],
        'address'   => $data['address'] ?? null,
        'city'      => $data['city'] ?? null,
        'state'     => $data['state'] ?? null,
        'zip_code'  => $data['zip_code'] ?? null,
        'latitude'  => $data['latitude'] ?? null,
        'longitude' => $data['longitude'] ?? null,
        'radius'    => $data['checkin_radius_meters'] ?? 300
    ]);
}

/**
 * Actualizar datos de sucursal
 */
function updateBranch(int $id, array $data): bool {
    $pdo = getConnectionMySql();

    $stmt = $pdo->prepare("
        UPDATE branches SET
            name = :name,
            address = :address,
            city = :city,
            state = :state,
            zip_code = :zip_code,
            latitude = :latitude,
            longitude = :longitude,
            checkin_radius_meters = :radius
        WHERE id = :id
    ");

    return $stmt->execute([
        'name'      => $data['name'],
        'address'   => $data['address'] ?? null,
        'city'      => $data['city'] ?? null,
        'state'     => $data['state'] ?? null,
        'zip_code'  => $data['zip_code'] ?? null,
        'latitude'  => $data['latitude'] ?? null,
        'longitude' => $data['longitude'] ?? null,
        'radius'    => $data['checkin_radius_meters'] ?? 300,
        'id'        => $id
    ]);
}

/**
 * "Eliminar" sucursal (cambiar estatus)
 */
function disableBranch(int $id): bool {
    $pdo = getConnectionMySql();

    $stmt = $pdo->prepare("
        UPDATE branches 
        SET is_active = 0
        WHERE id = :id
    ");

    return $stmt->execute(['id' => $id]);
}

/**
 * Restaurar una sucursal eliminada
 */
function restoreBranch(int $id): bool {
    $pdo = getConnectionMySql();

    $stmt = $pdo->prepare("
        UPDATE branches 
        SET is_active = 1
        WHERE id = :id
    ");

    return $stmt->execute(['id' => $id]);
}

/**
 * Obtiene al empleado con su sucursal y coordenadas
 */
function getEmployeeWithBranch(int $employeeId): ?array {
    $pdo = getConnectionMySql();

    $stmt = $pdo->prepare("
        SELECT 
            e.id AS employee_id,
            e.names,
            e.surname1,
            e.surname2,
            e.id_branch,
            b.latitude,
            b.longitude,
            b.checkin_radius_meters
        FROM employees e
        LEFT JOIN branches b ON e.id_branch = b.id
        WHERE e.id = :id
        LIMIT 1
    ");

    $stmt->execute(['id' => $employeeId]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    return $data ?: null;
}