<?php
require_once __DIR__ . '/../../middleware/checkAuth.php';
require_once __DIR__ . '/../../helpers/permissions.php'; // ⬅ NUEVO
require_once APP_PATH . '/services/attendance/attendanceServices.php';

$pageTitle = ": Dashboard";

// Obtener asistencias del día
$pdo = getConnectionMySql();
$attendance = getTodayAttendance($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<?php include APP_PATH . '/layouts/head.php' ?>

<body>
    <?php include APP_PATH . '/layouts/navbar.php' ?>

    <main>
        <div class="container">
            <h1>Registro de Asistencia</h1>
            <table>
                <thead>
                    <tr>
                        <th class="th radius-top-left">Empleado</th>
                        <th class="th">Fecha</th>
                        <th class="th">Hora</th>
                        <th class="th">Tipo</th>
                        <th class="th">Origen</th>
                        <th class="th radius-top-right">Minutos restantes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($attendance)): ?>
                        <?php foreach ($attendance as $row): ?>
                            <tr>
                                <td class="td"><?= htmlspecialchars($row['employee_name']) ?></td>
                                <td class="td"><?= $row['attendance_date'] ?></td>
                                <td class="td"><?= $row['attendance_hour'] ?></td>
                                <td class="td"><?= $row['attendance_type'] ?></td>
                                <td class="td"><?= strtoupper($row['source']) ?></td>
                                <td class="td"><?= $row['remaining_minutes'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No hay asistencias registradas hoy.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <?php if (hasPermission('register_attendance')): ?>
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