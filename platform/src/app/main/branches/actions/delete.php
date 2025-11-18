<?php

$id = $_GET['id'];
if(!isset($id)) {
    header('Location: ../branches/index.php');
    exit();
}

echo "<script>alert('Este módulo se encuentra en fase de desarrollo. Consulta la documentación de deuda técnica para más información.'); window.location.href='../index.php';</script>";
exit;
?>