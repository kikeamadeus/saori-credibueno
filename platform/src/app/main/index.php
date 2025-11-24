<?php
require_once __DIR__ . '/../../middleware/checkAuth.php';
require_once __DIR__ . '/../../helpers/roles.php';

$pageTitle = ": Dashboard";
$roleId = $_SESSION['employee_role'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<?php include APP_PATH . '/layouts/head.php' ?>

<body>
    <?php include APP_PATH . '/layouts/navbar.php' ?>
    <main>
        <div class="container">
            <p>Panel Principal de Chequeo</p>
            <?php if (roleCanRegisterAttendance($roleId)): ?>
                <a class="btn-floating" href="attendance/actions/create.php" title="Registrar Asistencia">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="22" height="22" color="#ffffff" fill="none" style="margin-bottom: 4px;">
                        <path d="M15 2H10" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M4 13.5C4 8.80558 7.80558 5 12.5 5C14.8472 5 16.9722 5.95139 18.5104 7.48959M18.5104 7.48959C20.0486 9.02779 21 11.1528 21 13.5C21 18.1944 17.1944 22 12.5 22H3M18.5104 7.48959L20 6" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M8 19H3" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M6 16H3" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M12.5 13.5L16 10" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </a>
            <?php endif; ?>
        </div>
    </main>
    <?php include APP_PATH . '/layouts/scripts.php' ?>
</body>

</html>