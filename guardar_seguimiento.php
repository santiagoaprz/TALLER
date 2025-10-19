<?php
include 'config.php';

if($_POST) {
    $id_orden = $_POST['id_orden'];
    $tipo_seguimiento = $_POST['tipo_seguimiento'];
    $estado_nuevo = $_POST['estado_nuevo'];
    $observaciones = sanitizar($_POST['observaciones']);
    $tecnico = sanitizar($_POST['tecnico']);
    
    // OBTENER ESTADO ACTUAL
    $stmt = $pdo->prepare("SELECT estado_orden FROM ordenes_servicio WHERE id_orden = ?");
    $stmt->execute([$id_orden]);
    $estado_actual = $stmt->fetchColumn();
    
    // SI SE ESPECIFICÓ UN NUEVO ESTADO, ACTUALIZAR LA ORDEN
    if($estado_nuevo && $estado_nuevo != $estado_actual) {
        $stmt = $pdo->prepare("UPDATE ordenes_servicio SET estado_orden = ? WHERE id_orden = ?");
        $stmt->execute([$estado_nuevo, $id_orden]);
        
        // REGISTRAR EN SEGUIMIENTO CON CAMBIO DE ESTADO
        $stmt = $pdo->prepare("INSERT INTO seguimiento_orden (id_orden, estado_anterior, estado_nuevo, observaciones, tecnico, tipo_seguimiento) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$id_orden, $estado_actual, $estado_nuevo, $observaciones, $tecnico, $tipo_seguimiento]);
    } else {
        // REGISTRAR SEGUIMIENTO SIN CAMBIO DE ESTADO
        $stmt = $pdo->prepare("INSERT INTO seguimiento_orden (id_orden, observaciones, tecnico, tipo_seguimiento) VALUES (?, ?, ?, ?)");
        $stmt->execute([$id_orden, $observaciones, $tecnico, $tipo_seguimiento]);
    }
    
    header("Location: seguimiento.php?orden=$id_orden&mensaje=Seguimiento guardado correctamente");
    exit;
}
?>