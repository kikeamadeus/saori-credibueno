<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../helpers/jwt.php';
require_once __DIR__ . '/../sessions/sessionService.php';
require_once __DIR__ . '/../auth/permissionServices.php';
$errors = require __DIR__ . '/../../config/errors.php';

/**
 * Valida credenciales, genera Access y Refresh Token
 * y registra la sesión correctamente en BD
 *
 * @param string $username
 * @param string $password
 * @return array
 */
function loginUser($username, $password) {
    global $errors;
    $pdo = getConnectionMySql();

    try {
        // Buscar usuario por username
        $stmt = $pdo->prepare("
            SELECT id, id_employee, password
            FROM users 
            WHERE username = :username 
            LIMIT 1
        ");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {

            // Obtener datos del empleado
            $stmtEmp = $pdo->prepare("
                SELECT id, names, surname1, surname2, id_role
                FROM employees
                WHERE id = :id_employee
                LIMIT 1
            ");
            $stmtEmp->execute([':id_employee' => $user['id_employee']]);
            $employee = $stmtEmp->fetch(PDO::FETCH_ASSOC);

            if ($employee) {

                // ==================================================
                // 1) PERMISOS DEL ROL (NUEVO)
                // ==================================================
                $permissions = getPermissionsByRole($pdo, $employee['id_role']);

                // ==================================================
                // 2) GENERAR TOKENS
                // ==================================================
                $payload = [
                    "id"       => $employee['id'],
                    "name"     => $employee['names'],
                    "surname1" => $employee['surname1'],
                    "surname2" => $employee['surname2'],
                    "id_role"  => $employee['id_role']
                ];

                $accessToken  = generateToken($payload);
                $refreshToken = generateRefreshToken($employee['id']);

                // ==================================================
                // 3) GUARDAR SESIÓN EN BD
                // ==================================================
                $expiresAt = (new DateTime())->add(new DateInterval('P90D'));

                $sessionId = createSession(
                    $pdo,
                    $employee['id'],
                    $refreshToken,
                    $expiresAt
                );

                // ==================================================
                // 4) RESPUESTA PARA API Y WEB
                // ==================================================
                return [
                    "success" => true,
                    "message" => "Login exitoso",
                    "employee" => [
                        "id" => $employee['id'],
                        "id_role" => $employee['id_role'],
                        "full_name" => trim($employee['names'] . ' ' . $employee['surname1'] . ' ' . ($employee['surname2'] ?? '')),
                        "permissions" => $permissions  // ✅ NUEVO
                    ],
                    "access_token"  => $accessToken,
                    "refresh_token" => $refreshToken,
                    "session_id"    => $sessionId
                ];
            }
        }

        // Credenciales inválidas
        return [
            "success" => false,
            "code"    => "AUTH_INVALID_CREDENTIALS",
            "message" => $errors['AUTH_INVALID_CREDENTIALS']
        ];

    } catch (PDOException $e) {
        return [
            "success" => false,
            "code"    => "DB_ERROR",
            "message" => $errors['DB_ERROR'] . $e->getMessage()
        ];
    }
}
?>