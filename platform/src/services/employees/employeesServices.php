<?php
require_once __DIR__ . '/../../config/bootstrap.php';

/**
 * Crear un nuevo empleado
 */
function createEmployee(array $data): ?int {
    $pdo = getConnectionMySql();
    $pdo->beginTransaction();

    try {

        // Insertar empleado
        $stmt = $pdo->prepare("
            INSERT INTO employees (
                names, surname1, surname2, email, phone,
                id_area, id_branch, can_check_all,
                id_role, status_id, hire_date,
                created_at
            ) VALUES (
                :names, :surname1, :surname2, :email, :phone,
                :id_area, :id_branch, :can_check_all,
                :id_role, :status_id, :hire_date,
                NOW()
            )
        ");

        $stmt->execute([
            'names'         => $data['names'],
            'surname1'      => $data['surname1'],
            'surname2'      => $data['surname2'] ?? null,
            'email'         => $data['email'] ?? null,
            'phone'         => $data['phone'] ?? null,
            'id_area'       => $data['id_area'],
            'id_branch'     => $data['id_branch'],
            'can_check_all' => 0, // siempre 0 en alta inicial
            'id_role'       => $data['id_role'],
            'status_id'     => 1, // siempre Activo
            'hire_date'     => $data['hire_date']
        ]);

        $employeeId = $pdo->lastInsertId();

        // Insertar usuario
        $stmt2 = $pdo->prepare("
            INSERT INTO users (
                id_employee, username, password, created_at
            ) VALUES (
                :id_employee, :username, :password, NOW()
            )
        ");

        $stmt2->execute([
            'id_employee' => $employeeId,
            'username'    => $data['username'],
            'password'    => password_hash($data['password'], PASSWORD_BCRYPT)
        ]);

        $pdo->commit();
        return (int)$employeeId;

    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Error al crear empleado: " . $e->getMessage());
        return null;
    }
}

/**
 * Obtener todos los empleados con su estatus
 */
function getAllEmployees(): array {
    $pdo = getConnectionMySql();

    $sql = "
        SELECT 
            e.id,
            e.names,
            e.surname1,
            e.surname2,
            e.email,
            e.phone,
            e.id_area,
            a.name AS area_name,

            e.id_branch,
            b.name AS branch_name,

            e.can_check_all,

            e.id_role,
            r.name AS role_name,

            e.status_id,
            s.name AS status_name,

            e.hire_date,
            e.created_at,
            e.updated_at
        FROM employees e
        INNER JOIN statuses s ON s.id = e.status_id
        INNER JOIN areas a ON a.id = e.id_area
        LEFT JOIN branches b ON b.id = e.id_branch
        INNER JOIN roles r ON r.id = e.id_role
        ORDER BY e.id ASC
    ";

    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}