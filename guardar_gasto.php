<?php
include 'config.php';

if($_POST) {
    $descripcion = sanitizar($_POST['descripcion']);
    $monto = $_POST['monto'];
    $fecha_gasto = $_POST['fecha_gasto'];
    $categoria = $_POST['categoria'];
    $comprobante = sanitizar($_POST['comprobante']);
    $observaciones = sanitizar($_POST['observaciones']);
    
    $stmt = $pdo->prepare("INSERT INTO gastos (descripcion, monto, fecha_gasto, categoria, comprobante, observaciones) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$descripcion, $monto, $fecha_gasto, $categoria, $comprobante, $observaciones]);
    
    header("Location: gastos.php?mensaje=Gasto registrado correctamente");
    exit;
}
?>