<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../helpers/jwt.php';
$errors = require __DIR__ . '/../../config/errors.php';

/**
 * Devuelve la información del empleado asociado al token JWT.
 *
 * @param string $token
 * @return array
 */
function getEmployeeProfile($token) {
    global $errors;

    $pdo = getConnectionMySql();

    try {
        // =====================================================
        // VALIDAR TOKEN JWT
        // =====================================================
        $data = validateToken($token);

        // 🔹 Caso 1: token completamente inválido (firma corrupta o manipulado)
        if ($data === false) {
            return [
                "success" => false,
                "code" => "TOKEN_INVALID",
                "message" => "Token inválido o corrupto."
            ];
        }

        // 🔹 Caso 2: token expirado pero decodificable (según validateToken)
        if (isset($data['expired']) && $data['expired'] === true) {
            // Intentar extraer información mínima del payload
            $payload = $data['payload'] ?? [];

            if (!empty($payload['id'])) {
                // Obtener el empleado para devolver al menos el nombre
                $stmt = $pdo->prepare("
                    SELECT id, names, surname1, surname2
                    FROM employees
                    WHERE id = :id
                    LIMIT 1
                ");
                $stmt->execute([':id' => $payload['id']]);
                $employee = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($employee) {
                    return [
                        "success" => true,
                        "expired" => true,
                        "message" => "Token expirado, pero perfil recuperado desde el payload.",
                        "employee" => [
                            "id" => $employee['id'],
                            "full_name" => trim($employee['names'] . ' ' . $employee['surname1'] . ' ' . ($employee['surname2'] ?? ''))
                        ]
                    ];
                }
            }

            // Si no se puede recuperar, enviar error genérico
            return [
                "success" => false,
                "code" => "TOKEN_EXPIRED",
                "message" => "El token ha expirado y no se pudo recuperar información."
            ];
        }

        // 🔹 Caso 3: token válido → continuar normalmente
        $stmt = $pdo->prepare("
            SELECT id, names, surname1, surname2
            FROM employees
            WHERE id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $data['id']]);
        $employee = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$employee) {
            return [
                "success" => false,
                "code" => "EMPLOYEE_NOT_FOUND",
                "message" => "Empleado no encontrado."
            ];
        }

        // 🔹 Respuesta normal
        return [
            "success" => true,
            "message" => "Perfil obtenido correctamente.",
            "employee" => [
                "id" => $employee['id'],
                "full_name" => trim($employee['names'] . ' ' . $employee['surname1'] . ' ' . ($employee['surname2'] ?? ''))
            ]
        ];
    } catch (PDOException $e) {
        return [
            "success" => false,
            "code" => "DB_ERROR",
            "message" => $errors['DB_ERROR'] . ' ' . $e->getMessage()
        ];
    } catch (Exception $e) {
        return [
            "success" => false,
            "code" => "UNEXPECTED_ERROR",
            "message" => "Error inesperado: " . $e->getMessage()
        ];
    }
}
?>
