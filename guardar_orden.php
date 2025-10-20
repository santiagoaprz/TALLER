<?php
include 'config.php';

if($_POST) {
    try {
        $id_equipo = $_POST['id_equipo'] ?? null;
        
        if (!$id_equipo) {
            throw new Exception("No se especificó el equipo");
        }

        // VERIFICAR SI YA EXISTE ORDEN ACTIVA PARA ESTE EQUIPO
        $stmt_check = $pdo->prepare("SELECT COUNT(*) as tiene_orden FROM ordenes_servicio WHERE id_equipo = ? AND activa = 1");
        $stmt_check->execute([$id_equipo]);
        $result = $stmt_check->fetch();
        
        if ($result['tiene_orden'] > 0) {
            throw new Exception("Este equipo ya tiene una orden de servicio activa");
        }

        // GENERAR FOLIO Y OBTENER DATOS
        $folio = generarFolio();
        $fecha_ingreso = $_POST['fecha_ingreso'];
        $hora_ingreso = $_POST['hora_ingreso'];
        $fecha_entrega_estimada = $_POST['fecha_entrega_estimada'] ?? null;
        $tecnico_asignado = $_POST['tecnico_asignado'];
        $observaciones_internas = sanitizar($_POST['observaciones_internas'] ?? '');
        $costo_estimado = $_POST['costo_estimado'] ?: 0;
        
        // INSERTAR ORDEN
        $stmt = $pdo->prepare("INSERT INTO ordenes_servicio (folio, id_equipo, fecha_ingreso, hora_ingreso, fecha_entrega_estimada, tecnico_asignado, observaciones_internas, costo_estimado, estado_orden) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Recibido')");
        $stmt->execute([$folio, $id_equipo, $fecha_ingreso, $hora_ingreso, $fecha_entrega_estimada, $tecnico_asignado, $observaciones_internas, $costo_estimado]);
        
        $id_orden = $pdo->lastInsertId();
        
        // REGISTRAR EN SEGUIMIENTO
        $stmt = $pdo->prepare("INSERT INTO seguimiento_orden (id_orden, estado_anterior, estado_nuevo, observaciones, tecnico, tipo_seguimiento) VALUES (?, NULL, 'Recibido', 'Orden creada en el sistema', ?, 'Cambio Estado')");
        $stmt->execute([$id_orden, $tecnico_asignado]);
        
        $_SESSION['success'] = "✅ Orden creada correctamente con folio: $folio";
        header("Location: ordenes_servicio.php");
        exit;
        
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: nueva_orden.php" . ($id_equipo ? "?equipo=" . $id_equipo : ""));
        exit;
    }
} else {
    header("Location: nueva_orden.php");
    exit;
}
?>