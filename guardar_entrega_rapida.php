<?php
include 'config.php';

if($_POST) {
    $orden_id = $_POST['orden_id'];
    $trabajo_realizado = sanitizar($_POST['trabajo_realizado']);
    $piezas_utilizadas = sanitizar($_POST['piezas_utilizadas']);
    $costo_final = $_POST['costo_final'];
    $anticipo = $_POST['anticipo'] ?: 0;
    $observaciones_finales = sanitizar($_POST['observaciones_finales']);
    
    $saldo_pendiente = $costo_final - $anticipo;
    
    try {
        $pdo->beginTransaction();
        
        // ACTUALIZAR ORDEN
        $stmt = $pdo->prepare("UPDATE ordenes_servicio SET 
            estado_orden = 'Entregado',
            trabajo_realizado = ?,
            piezas_necesarias = ?,
            costo_final = ?,
            anticipo = ?,
            saldo_pendiente = ?,
            observaciones_internas = CONCAT(COALESCE(observaciones_internas, ''), '\n--- ENTREGA ---\n', ?),
            fecha_entrega_real = CURDATE()
            WHERE id_orden = ?
        ");
        
        $stmt->execute([
            $trabajo_realizado,
            $piezas_utilizadas,
            $costo_final,
            $anticipo,
            $saldo_pendiente,
            $observaciones_finales,
            $orden_id
        ]);
        
        // REGISTRAR SEGUIMIENTO
        $stmt = $pdo->prepare("INSERT INTO seguimiento_orden (id_orden, estado_anterior, estado_nuevo, observaciones, tecnico, tipo_seguimiento) VALUES (?, 'Reparado', 'Entregado', 'Equipo entregado al cliente. Trabajo realizado: ' || ?, 'Sistema', 'Cambio Estado')");
        $stmt->execute([$orden_id, substr($trabajo_realizado, 0, 100)]);
        
        $pdo->commit();
        
        // REDIRIGIR AL PDF DE ENTREGA
        header("Location: pdf_entrega.php?orden=$orden_id");
        exit;
        
    } catch(Exception $e) {
        $pdo->rollBack();
        header("Location: entrega_rapida.php?error=1&mensaje=" . urlencode($e->getMessage()));
        exit;
    }
}
?>