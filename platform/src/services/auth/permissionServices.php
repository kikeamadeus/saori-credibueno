<?php
/**
 * ==========================================================
 * SERVICE: Permisos por Rol
 * ----------------------------------------------------------
 * Obtiene todos los permisos asignados a un rol
 * desde las tablas:
 *
 *    roles
 *    permissions
 *    role_permissions
 *
 * El resultado se devuelve como:
 *
 *   [
 *      "register_attendance" => true,
 *      "other_permission"    => true
 *   ]
 *
 * Este servicio es utilizado tanto por:
 *   - loginServices.php
 *   - refresh.php
 *   - futuras APIs que necesiten permisos
 * ==========================================================
 */

/**
 * Obtiene los permisos asignados a un rol.
 *
 * @param PDO $pdo
 * @param int $roleId
 * @return array Permisos en formato ["permission_key" => true]
 */
function getPermissionsByRole(PDO $pdo, int $roleId): array 
{
    // Obtener los permissions.permission_key asignados al rol
    $stmt = $pdo->prepare("
        SELECT p.permission_key
        FROM role_permissions rp
        INNER JOIN permissions p ON p.id = rp.permission_id
        WHERE rp.role_id = :role_id
    ");

    $stmt->execute([':role_id' => $roleId]);

    $permissions = [];

    // Convertir a un arreglo asociativo simple
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $permissions[$row['permission_key']] = true;
    }

    return $permissions;
}
