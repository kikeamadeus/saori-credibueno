<?php
require_once __DIR__ . '/../../../../middleware/checkAuth.php';
require_once APP_PATH . '/services/employees/employeesServices.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php");
    exit;
}

// Sanitizar datos
$data = [
    'id'         => $_POST['id'],
    'names'      => trim($_POST['names']),
    'surname1'   => trim($_POST['surname1']),
    'surname2'   => trim($_POST['surname2']),
    'email'      => trim($_POST['email']),
    'phone'      => trim($_POST['phone']),
    'id_area'    => $_POST['id_area'],
    'id_branch'  => $_POST['id_branch'],
    'id_role'    => $_POST['id_role'],
    'status'     => $_POST['status'],
    'hire_date'  => $_POST['hire_date']
];

// Ejecutar actualización
if (updateEmployee($data)) {
    header("Location: ../index.php?updated=1");
    exit;
} else {
    header("Location: ../index.php?error=1");
    exit;
}