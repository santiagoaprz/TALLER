<?php
include 'config.php';

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // MARCAR COMO INACTIVO EN LUGAR DE ELIMINAR
    $stmt = $pdo->prepare("UPDATE inventario SET activo = 0 WHERE id_pieza = ?");
    $stmt->execute([$id]);
    
    header("Location: inventario.php?mensaje=Pieza eliminada correctamente");
    exit;
}
?>