<?php
// conexion.php
$host = '127.0.0.1';
$db   = 'taller_lykos';
$user = 'root';      // Usuario por defecto de XAMPP
$pass = '';          // Contraseña por defecto de XAMPP (vacía)
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // Manejo de errores más detallado
    error_log("Error de conexión: " . $e->getMessage());
    die("Error de conexión a la base de datos. Por favor contacta al administrador.");
}
?>