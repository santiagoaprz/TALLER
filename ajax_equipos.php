<?php
include 'config.php';

if(isset($_GET['cliente'])) {
    $cliente_id = $_GET['cliente'];
    $stmt = $pdo->prepare("SELECT * FROM equipos WHERE id_cliente = ? ORDER BY fecha_registro DESC");
    $stmt->execute([$cliente_id]);
    $equipos = $stmt->fetchAll();
    
    echo '<option value="">Seleccionar equipo</option>';
    foreach($equipos as $equipo) {
        echo "<option value='{$equipo['id_equipo']}'>{$equipo['marca']} {$equipo['modelo']} - {$equipo['tipo_equipo']} - {$equipo['problemas_reportados']}</option>";
    }
}
?>