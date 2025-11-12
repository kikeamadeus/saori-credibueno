<?php
/**
 * Inyector dinámico de scripts JavaScript para SAORI
 * --------------------------------------------------
 * Detecta el contexto actual y carga:
 * - saori.js (global)
 * - saori.init.js (para /src/index.php)
 * - saori.main.js (para /src/main/index.php)
 * - saori.[subcarpeta].js (para /src/main/[subcarpeta]/index.php)
 */

if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../config/config.php';
}

$currentScript = basename($_SERVER['SCRIPT_FILENAME']);
$currentDir    = basename(dirname($_SERVER['SCRIPT_FILENAME']));
$parentDir     = basename(dirname(dirname($_SERVER['SCRIPT_FILENAME'])));
$project       = defined('PROJECT') ? strtolower(PROJECT) : 'saori';

$basePath = BASE_URL . '/public/js/';
$rootPath = $_SERVER['DOCUMENT_ROOT'] . '/public/js/';

$scripts = [];

// =====================================================
// 1) Cargar siempre el global saori.js
// =====================================================
$scripts[] = $basePath . "{$project}.js";

// =====================================================
// 2) Detectar entorno actual
// =====================================================
if ($currentDir === 'main' && $currentScript === 'index.php') {
    // /main/index.php → saori.main.js
    if (file_exists($rootPath . "{$project}.main.js")) {
        $scripts[] = $basePath . "{$project}.main.js";
    }
} elseif ($parentDir === 'main') {
    // /main/subcarpeta/index.php → saori.subcarpeta.js
    $module = $currentDir;
    if (file_exists($rootPath . "{$project}.{$module}.js")) {
        $scripts[] = $basePath . "{$project}.{$module}.js";
    }
} elseif ($currentScript === 'index.php' && $currentDir === basename(realpath(APP_PATH))) {
    // /src/index.php → saori.init.js
    if (file_exists($rootPath . "{$project}.init.js")) {
        $scripts[] = $basePath . "{$project}.init.js";
    }
}

// =====================================================
// 3) Renderizar scripts
// =====================================================
foreach ($scripts as $src) {
    echo '<script type="module" src="' . htmlspecialchars($src) . '"></script>' . PHP_EOL;
}
?>