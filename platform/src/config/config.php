<?php
define('PROJECT', 'saori');
define('APP_PATH', dirname(__DIR__));

/* ::: Detectar script y directorio actual ::: */
define('CURRENT_SCRIPT', basename($_SERVER['SCRIPT_FILENAME']));
define('CURRENT_DIR', basename(dirname($_SERVER['SCRIPT_FILENAME'])));

// Detectar raíz absoluta del proyecto
$getRoot = realpath(__DIR__ . '/..');

/* ::: Definir BASE_DIR ::: */
if (CURRENT_SCRIPT === 'index.php' && realpath($_SERVER['SCRIPT_FILENAME']) === $getRoot . '/index.php') {
    define('BASE_DIR', 'init'); // caso especial para index raíz
} else {
    define('BASE_DIR', CURRENT_DIR); // caso normal
}

/* ::: Detectar si existen páginas públicas ::: */
$pagesDir = APP_PATH . '/pages';
$hasPages = (is_dir($pagesDir) && !empty(glob($pagesDir . '/*', GLOB_ONLYDIR)));

/* ::: Definir APP_CONTEXT ::: */
if (BASE_DIR === 'init') {
    define('APP_CONTEXT', $hasPages ? 'webpage' : 'onepage');
} elseif (BASE_DIR === 'main') {
    define('APP_CONTEXT', 'platform');
} else {
    define('APP_CONTEXT', 'unknown');
}

/* ::: Detectamos el entorno de desarrollo o producción ::: */
$envFile = (getenv('DOCKER') || isset($_ENV['DOCKER'])) ? APP_PATH . '/.env.development' : APP_PATH . '/.env';

/* ::: Cargar variables de entorno ::: */
if (file_exists($envFile)) {
    $dotenv = parse_ini_file($envFile);

    define('DB_HOST', $dotenv['DB_HOST']);
    define('DB_NAME', $dotenv['DB_NAME']);
    define('DB_USER', $dotenv['DB_USER']);
    define('DB_PASS', $dotenv['DB_PASS']);
    define('BASE_URL', $dotenv['BASE_URL']);
} else {
    die("Error: No se encuentra un archivo .env en la configuración");
}
?>