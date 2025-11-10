<?php 
require_once __DIR__ . '/../../middleware/guest.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Haco - Iniciar sesión</title>
</head>

<body>
    <h1>Iniciar sesión</h1>
    <form action="login.php" method="POST">
        <label for="username">Usuario:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <button type="submit">Entrar</button>
        <?php if (isset($_SESSION['error_message'])): ?>
            <p style="color: red;"><?php echo htmlspecialchars($_SESSION['error_message']); ?></p>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>
    </form>
</body>

</html>