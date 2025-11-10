<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../middleware/guest.php';

$pageTitle = ': Nómina & Asistencia';
?>
<!DOCTYPE html>
<html lang="es-MX">
    <?php include APP_PATH . '/layouts/head.php' ?>
<body>
    <main>
        <div class="container center-align">
            <div class="card card-login">
                <div class="card-content">
                    <img class="img-login" src="<?php echo BASE_URL . '/public/image/logo.png'; ?>" alt="Credibueno: Nómina & Asistencia">
                    <form action="login.php" method="POST">
                        <div class="input-field">
                            <input class="input-login" placeholder="Usuario" id="username" name="username" type="text">
                        </div>
                        <div class="input-field">
                            <input class="input-login" placeholder="Contraseña" id="password" name="password" type="password">
                        </div>
                        <button type="submit">Iniciar Sesión</button>
                        <?php if (isset($_SESSION['error_message'])): ?>
                            <p style="color: red;"><?php echo htmlspecialchars($_SESSION['error_message']); ?></p>
                            <?php unset($_SESSION['error_message']); ?>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </main>
</body>
</html>