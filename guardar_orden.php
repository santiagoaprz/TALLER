<?php
include 'config.php';

if($_POST) {
    $folio = generarFolio();
    $id_equipo = $_POST['id_equipo'];
    $fecha_ingreso = $_POST['fecha_ingreso'];
    $hora_ingreso = $_POST['hora_ingreso'];
    $fecha_entrega_estimada = $_POST['fecha_entrega_estimada'];
    $tecnico_asignado = $_POST['tecnico_asignado'];
    $observaciones_internas = sanitizar($_POST['observaciones_internas']);
    $costo_estimado = $_POST['costo_estimado'] ?: 0;
    
    // INSERTAR ORDEN
    $stmt = $pdo->prepare("INSERT INTO ordenes_servicio (folio, id_equipo, fecha_ingreso, hora_ingreso, fecha_entrega_estimada, tecnico_asignado, observaciones_internas, costo_estimado) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$folio, $id_equipo, $fecha_ingreso, $hora_ingreso, $fecha_entrega_estimada, $tecnico_asignado, $observaciones_internas, $costo_estimado]);
    
    $id_orden = $pdo->lastInsertId();
    
    // REGISTRAR EN SEGUIMIENTO
    $stmt = $pdo->prepare("INSERT INTO seguimiento_orden (id_orden, estado_anterior, estado_nuevo, observaciones, tecnico, tipo_seguimiento) VALUES (?, 'Nuevo', 'Recibido', 'Orden creada en el sistema', ?, 'Cambio Estado')");
    $stmt->execute([$id_orden, $tecnico_asignado]);
    
    header("Location: ordenes.php?mensaje=Orden creada correctamente con folio: $folio");
    exit;
}
?>