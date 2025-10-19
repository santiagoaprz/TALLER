<?php
include 'config.php';

if($_POST) {
    $id_orden = $_POST['id_orden'];
    $diagnostico = sanitizar($_POST['diagnostico']);
    $piezas_necesarias = sanitizar($_POST['piezas_necesarias']);
    $trabajo_realizado = sanitizar($_POST['trabajo_realizado']);
    $dificultades_encontradas = sanitizar($_POST['dificultades_encontradas']);
    $costo_estimado = $_POST['costo_estimado'] ?: 0;
    $estado_orden = $_POST['estado_orden'];
    $observaciones_internas = sanitizar($_POST['observaciones_internas']);
    
    // OBTENER ESTADO ANTERIOR
    $stmt = $pdo->prepare("SELECT estado_orden FROM ordenes_servicio WHERE id_orden = ?");
    $stmt->execute([$id_orden]);
    $estado_anterior = $stmt->fetchColumn();
    
    // ACTUALIZAR ORDEN
    $stmt = $pdo->prepare("UPDATE ordenes_servicio SET diagnostico = ?, piezas_necesarias = ?, trabajo_realizado = ?, dificultades_encontradas = ?, costo_estimado = ?, estado_orden = ?, observaciones_internas = ? WHERE id_orden = ?");
    $stmt->execute([$diagnostico, $piezas_necesarias, $trabajo_realizado, $dificultades_encontradas, $costo_estimado, $estado_orden, $observaciones_internas, $id_orden]);
    
    // REGISTRAR EN SEGUIMIENTO SI CAMBIÓ EL ESTADO
    if($estado_anterior != $estado_orden) {
        $observacion_seguimiento = "Diagnóstico actualizado";
        if($diagnostico) {
            $observacion_seguimiento .= ": " . (strlen($diagnostico) > 100 ? substr($diagnostico, 0, 100) . '...' : $diagnostico);
        }
        
        $stmt = $pdo->prepare("INSERT INTO seguimiento_orden (id_orden, estado_anterior, estado_nuevo, observaciones, tecnico, tipo_seguimiento) VALUES (?, ?, ?, ?, ?, 'Actualización Diagnóstico')");
        $stmt->execute([$id_orden, $estado_anterior, $estado_orden, $observacion_seguimiento, 'Santiago']);
    }
    
    header("Location: diagnostico.php?orden=$id_orden&mensaje=Diagnóstico guardado correctamente");
    exit;
}
?>