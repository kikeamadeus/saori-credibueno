<?php
require_once __DIR__ . '/../../../../config/bootstrap.php';
require_once APP_PATH . '/services/employees/employeesServices.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php");
    exit;
}

$data = [
    'names'     => trim($_POST['names']),
    'surname1'  => trim($_POST['surname1']),
    'surname2'  => trim($_POST['surname2']),
    'email'     => trim($_POST['email']),
    'phone'     => trim($_POST['phone']),
    'id_area'   => intval($_POST['id_area']),
    'id_branch' => intval($_POST['id_branch']),
    'id_role'   => intval($_POST['id_role']),
    'hire_date' => $_POST['hire_date'],
    'username'  => $_POST['username'],
    'password'  => $_POST['password']
];

$employeeId = createEmployee($data);

if ($employeeId !== null) {
    echo "<script>
            alert('Empleado creado correctamente.');
            window.location.href='../index.php';
         </script>";
} else {
    echo "<script>
            alert('Error al crear el empleado.');
            window.location.href='../index.php';
         </script>";
}