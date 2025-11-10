<?php
//echo password_hash("admin1839", PASSWORD_BCRYPT);
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/middleware/guest.php';

//Constantes
$pageTitle = ': Bienvenido(a)';
?>
<!DOCTYPE html>
<html lang="en">
    <?php include APP_PATH . '/shared/head.php' ?>
<body>
    <main>
        <div class="container center-align">
            <h1>Bienvenido a Bitora, tu framework de confianza</h1>
            <p>Comienza a desarrollar tu proyecto ahora</p>
            <a href="auth/">Iniciar sesión</a>
        </div>
    </main>
</body>
</html>