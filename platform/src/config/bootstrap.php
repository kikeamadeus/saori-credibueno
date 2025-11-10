<?php
/**
 * Bootstrap del proyecto HACO
 * Centraliza la inicialización del sistema
 */

// Configuración base
require_once __DIR__ . '/config.php';

// Conexión a la base de datos
require_once __DIR__ . '/database.php';

// Helpers globales (ejemplo)
$helpers = [__DIR__ . '/../helpers/security.php', __DIR__ . '/../helpers/sanitize.php'];
foreach ($helpers as $helper) {
    if (file_exists($helper)) {
        require_once $helper;
    }
}

// Aquí podrías inicializar librerías externas en el futuro
require_once __DIR__ . '/../vendor/autoload.php';
?>
