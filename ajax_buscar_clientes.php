<?php
include 'config.php';

if(isset($_GET['q'])) {
    $query = $_GET['q'];
    $stmt = $pdo->prepare("SELECT * FROM clientes WHERE (nombre LIKE ? OR telefono LIKE ?) AND activo = 1 ORDER BY nombre LIMIT 10");
    $stmt->execute(["%$query%", "%$query%"]);
    $clientes = $stmt->fetchAll();
    
    if(count($clientes) > 0) {
        foreach($clientes as $cliente) {
            echo "<div class='client-item' onclick='seleccionarCliente({$cliente['id_cliente']}, \"{$cliente['nombre']}\", \"{$cliente['telefono']}\", \"{$cliente['email']}\", \"{$cliente['direccion']}\")'>";
            echo "<strong>{$cliente['nombre']}</strong><br>";
            echo "<small>Tel: {$cliente['telefono']}";
            if($cliente['email']) echo " | Email: {$cliente['email']}";
            echo "</small>";
            echo "</div>";
        }
    } else {
        echo "<div class='client-item'>No se encontraron clientes</div>";
    }
}
?>