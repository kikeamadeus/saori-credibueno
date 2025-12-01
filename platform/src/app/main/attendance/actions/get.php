<?php
require_once __DIR__ . '/../../middleware/checkAuth.php';
require_once APP_PATH . '/services/attendance/attendanceServices.php';

$pdo = getConnectionMySql();

$attendance = getTodayAttendance($pdo);

header('Content-Type: application/json');
echo json_encode([
    "success" => true,
    "data"    => $attendance
]);
exit;