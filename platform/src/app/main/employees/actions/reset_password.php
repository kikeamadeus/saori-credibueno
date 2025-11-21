<?php
require_once __DIR__ . '/../../../../config/bootstrap.php';
require_once APP_PATH . '/services/employees/credentialsServices.php';

// Validar parámetro
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID inválido");
}

$id = intval($_GET['id']);

// Resetear contraseña y obtener la nueva
$newPassword = resetEmployeePassword($id);

// Recargar página con alert
echo "
<script>
    alert('La nueva contraseña es: $newPassword');
    window.location.href = '../index.php';
</script>
";