<?php
session_start();

// CONFIGURACIÓN DE LA BASE DE DATOS
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'taller_lykos');

// CONFIGURACIÓN DEL TALLER
define('TALLER_NOMBRE', 'Reparación computadoras laptops celulares tablets CDMX Lykos');
define('TALLER_DIRECCION', 'Av. Niños Héroes 43, Doctores, Ciudad de México');
define('TALLER_TELEFONOS', '55-9191-1406 // 56-4117-0209');
define('TALLER_EMAIL', 'reparacioncomputadorasmexico@gmail.com');
define('TALLER_TECNICO', 'Santiago');
define('FPDF_FONTPATH', 'fpdf/font/');
// CONEXIÓN A LA BASE DE DATOS
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("ERROR: No se pudo conectar. " . $e->getMessage());
}

// FUNCIONES ÚTILES
function generarFolio() {
    return 'LYK-' . date('Ymd') . '-' . rand(1000, 9999);
}

function sanitizar($dato) {
    return htmlspecialchars(trim($dato), ENT_QUOTES, 'UTF-8');
}
?>