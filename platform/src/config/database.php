<?php
function getConnectionMySql() {
    require_once 'config.php'; // Cargar configuración de conexión

    try {
        // Data Source Name (DSN) corregido
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
    
        // Configurar atributos para mayor seguridad y rendimiento
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  // Manejo de errores con excepciones
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Fetch mode por defecto (arrays asociativos)
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // Deshabilitar emulación de consultas preparadas
    
        return $pdo;
    }
    catch(PDOException $e) {
        die("Error al conectar la base de datos: " . $e->getMessage());
    }
}
?>