<?php
// Respuesta en JSON siempre
header('Content-Type: application/json; charset=utf-8');

// Validar que sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Método no permitido, usa POST"
    ]);
    exit;
}

// En APIs móviles, el logout es más simbólico
// porque Flutter no maneja sesiones PHP.
// Aquí simplemente confirmamos que se "cerró sesión".

echo json_encode([
    "success" => true,
    "message" => "Sesión cerrada correctamente"
]);
?>