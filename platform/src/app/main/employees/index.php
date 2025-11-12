<?php
require_once __DIR__ . '/../../../middleware/checkAuth.php';
$pageTitle = ": Dashboard";
?>
<!DOCTYPE html>
<html lang="en">
<?php include APP_PATH . '/layouts/head.php' ?>
<body>
    <?php include APP_PATH . '/layouts/navbar.php' ?>
    <main>
        <div class="container">
            <p>Módulo de empleados</p>
        </div>
    </main>
    <?php include APP_PATH . '/layouts/scripts.php' ?>
</body>
</html>