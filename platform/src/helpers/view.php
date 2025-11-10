<?php
function getCurrentView() {
    // Directorio donde se ejecuta el archivo
    $dir = basename(dirname($_SERVER['SCRIPT_FILENAME']));

    // Caso especial: index.php en la raíz del proyecto
    if ($dir === basename(realpath(APP_PATH))) {
        return 'init';
    }

    return $dir;
}
?>