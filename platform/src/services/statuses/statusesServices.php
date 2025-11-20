<?php
require_once __DIR__ . '/../../config/bootstrap.php';

function getAllStatuses(): array {
    $pdo = getConnectionMySql();

    $sql = "SELECT id, name FROM statuses ORDER BY id ASC";
    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}
?>