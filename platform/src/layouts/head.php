<?php
function getCurrentView() {
    $currentDir = basename(dirname($_SERVER['SCRIPT_FILENAME']));
    $parentDir  = basename(dirname(dirname($_SERVER['SCRIPT_FILENAME'])));
    $rootDir    = basename(realpath(APP_PATH));

    // Si estamos en /src → init
    if ($currentDir === $rootDir) {
        return 'init';
    }

    // Si estamos en /main directamente → main
    if ($currentDir === 'main') {
        return 'main';
    }

    // Si estamos en /main/subcarpeta → nombre de la subcarpeta
    if ($parentDir === 'main') {
        return $currentDir;
    }

    return 'init';
}

$currentView = getCurrentView();

$globalCss = PROJECT . ".css";
$viewCss   = PROJECT . "." . $currentView . ".css";
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="title" content="Titulo del proyecto" />
    <meta name="description" content="Descripción del proyecto">
    <meta name="keywords" content="Palabras, clave, separadas, por comas" />
    <meta name="robots" content="index,follow" />

    <!-- Open Graph Social Network-->
    <meta property="og:locale" content="es_MX" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="Titulo del proyecto" />
    <meta property="og:description" content="Descripción del proyecto" />
    <meta property="og:url" content="<?php echo BASE_URL ?>" />
    <meta property="og:site_name" content="Titulo del proyecto" />
    <meta property="og:image" content="<?php echo BASE_URL ?>/public/image/icon.png" />

    <!-- Twitter(x) Meta Tags -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta property="twitter:domain" content="<?php echo BASE_URL ?>" />
    <meta property="twitter:url" content="<?php echo BASE_URL ?>" />
    <meta name="twitter:title" content="Titulo del proyecto" />
    <meta name="twitter:description" content="Descripción del proyecto" />
    <meta name="twitter:image" content="<?php echo BASE_URL ?>/public/image/icon.png" />
    
    <title>Credibueno <?php echo $pageTitle ?></title>

     <!-- Settings -->
    <link rel="canonical" href="<?php echo BASE_URL ?>" />
    <link rel="icon" type="image/png" href="<?php echo BASE_URL ?>/public/image/icon.png">

    <!-- Styles -->
    <link rel="stylesheet" href="<?php echo BASE_URL ?>/public/css/<?php echo $globalCss ?>">
    <?php if (file_exists(APP_PATH . "/public/css/$viewCss")): ?>
        <link rel="stylesheet" href="<?php echo BASE_URL ?>/public/css/<?php echo $viewCss ?>">
    <?php endif; ?>

    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">
</head>