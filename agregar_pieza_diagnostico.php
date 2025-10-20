<?php
session_start();
require_once 'conexion.php';

if ($_POST) {
    try {
        $id_orden = $_POST['id_orden'];
        $tipo_item = $_POST['tipo_item'];
        $nombre_pieza = trim($_POST['nombre_pieza']);
        $descripcion = trim($_POST['descripcion'] ?? '');
        $cantidad = intval($_POST['cantidad']);
        $precio_unitario = floatval($_POST['precio_unitario']);
        $proveedor_sugerido = trim($_POST['proveedor_sugerido'] ?? '');
        $urgencia = $_POST['urgencia'] ?? 'Media';
        $observaciones = trim($_POST['observaciones'] ?? '');
        
        if (empty($nombre_pieza) || $cantidad <= 0) {
            throw new Exception("Nombre y cantidad son obligatorios");
        }
        
        // Buscar en inventario automáticamente
        $id_pieza_inventario = null;
        $stmt = $pdo->prepare("SELECT id_pieza FROM inventario WHERE nombre_pieza LIKE ? AND activo = 1 LIMIT 1");
        $stmt->execute(["%$nombre_pieza%"]);
        $pieza_inv = $stmt->fetch();
        if ($pieza_inv) {
            $id_pieza_inventario = $pieza_inv['id_pieza'];
        }
        
        $total_item = $cantidad * $precio_unitario;
        
        // REGISTRAR EN GASTOS SI ES CONSUMIBLE O PIEZA PARA REPARACIÓN
        if ($tipo_item === 'consumible' || $tipo_item === 'pieza_reparacion') {
            $categoria = ($tipo_item === 'consumible') ? 'Materiales' : 'Herramientas';
            $stmt_gasto = $pdo->prepare("INSERT INTO gastos (descripcion, monto, fecha_gasto, categoria, observaciones) VALUES (?, ?, CURDATE(), ?, ?)");
            $stmt_gasto->execute([
                "Compra: $nombre_pieza - Orden: $id_orden", 
                $total_item, 
                $categoria, 
                "Cantidad: $cantidad, Proveedor: $proveedor_sugerido, $observaciones"
            ]);
        }
        
        // Si es un accesorio para venta, registrar automáticamente en inventario
        if ($tipo_item === 'accesorio_venta' && !$id_pieza_inventario) {
            $stmt_inv = $pdo->prepare("INSERT INTO inventario (nombre_pieza, descripcion, categoria, cantidad_stock, precio_compra, precio_venta, proveedor) VALUES (?, ?, 'Accesorios', 0, ?, ?, ?)");
            $precio_compra = $precio_unitario * 0.6; // 40% margen
            $stmt_inv->execute([$nombre_pieza, $descripcion, $precio_compra, $precio_unitario, $proveedor_sugerido]);
            $id_pieza_inventario = $pdo->lastInsertId();
        }
        
        // Insertar en piezas solicitadas
        $sql = "INSERT INTO piezas_solicitadas (id_orden, id_pieza_inventario, nombre_pieza, descripcion, cantidad, precio_unitario, proveedor_sugerido, urgencia, observaciones) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_orden, $id_pieza_inventario, $nombre_pieza, $descripcion, $cantidad, $precio_unitario, $proveedor_sugerido, $urgencia, $observaciones]);
        
        // ACTUALIZAR COSTO ESTIMADO EN LA ORDEN
        $stmt_total = $pdo->prepare("
            SELECT COALESCE(SUM(cantidad * precio_unitario), 0) as total_piezas 
            FROM piezas_solicitadas 
            WHERE id_orden = ?
        ");
        $stmt_total->execute([$id_orden]);
        $total_piezas = $stmt_total->fetch()['total_piezas'];
        
        // Obtener costo de mano de obra actual
        $stmt_orden = $pdo->prepare("SELECT costo_estimado FROM ordenes_servicio WHERE id_orden = ?");
        $stmt_orden->execute([$id_orden]);
        $costo_mano_obra = $stmt_orden->fetch()['costo_estimado'] ?: 0;
        
        $costo_total = $total_piezas + $costo_mano_obra;
        
        // Actualizar costo estimado en la orden
        $stmt_update = $pdo->prepare("UPDATE ordenes_servicio SET costo_estimado = ? WHERE id_orden = ?");
        $stmt_update->execute([$costo_total, $id_orden]);
        
        // Registrar en seguimiento
        $obs_seg = "Agregada a cotización: $nombre_pieza (Cant: $cantidad, Precio: $$precio_unitario, Total: $$total_item)";
        $stmt_seg = $pdo->prepare("INSERT INTO seguimiento_orden (id_orden, estado_anterior, estado_nuevo, observaciones, tecnico, tipo_seguimiento) VALUES (?, NULL, NULL, ?, 'Sistema', 'Solicitud Piezas')");
        $stmt_seg->execute([$id_orden, $obs_seg]);
        
        $_SESSION['success'] = "✅ Item agregado a la cotización. Total actualizado: $" . number_format($costo_total, 2);
        
    } catch (Exception $e) {
        $_SESSION['error'] = "❌ Error: " . $e->getMessage();
    }
    
    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? "diagnostico.php?id=" . $id_orden));
    exit;
}
?>