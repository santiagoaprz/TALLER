<?php
include 'config.php';

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // VERIFICAR SI TIENE EQUIPOS ACTIVOS
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM equipos WHERE id_cliente = ?");
    $stmt->execute([$id]);
    $tiene_equipos = $stmt->fetchColumn();
    
    if($tiene_equipos > 0) {
        // MARCAR COMO INACTIVO EN LUGAR DE ELIMINAR
        $stmt = $pdo->prepare("UPDATE clientes SET activo = 0 WHERE id_cliente = ?");
        $stmt->execute([$id]);
        $mensaje = "Cliente marcado como inactivo (tiene equipos registrados)";
    } else {
        // ELIMINAR DIRECTAMENTE
        $stmt = $pdo->prepare("DELETE FROM clientes WHERE id_cliente = ?");
        $stmt->execute([$id]);
        $mensaje = "Cliente eliminado correctamente";
    }
    
    header("Location: clientes.php?mensaje=" . urlencode($mensaje));
    exit;
}
?>