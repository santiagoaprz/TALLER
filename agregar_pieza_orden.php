<?php
session_start();
require_once 'conexion.php';

if ($_POST) {
    try {
        $id_orden = $_POST['id_orden'];
        $nombre_pieza = trim($_POST['nombre_pieza']);
        $descripcion = trim($_POST['descripcion'] ?? '');
        $cantidad = intval($_POST['cantidad']);
        $precio_unitario = floatval($_POST['precio_unitario']);
        $proveedor_sugerido = trim($_POST['proveedor_sugerido'] ?? '');
        $urgencia = $_POST['urgencia'] ?? 'Media';
        $observaciones = trim($_POST['observaciones'] ?? '');
        
        if (empty($nombre_pieza) || $cantidad <= 0) {
            throw new Exception("Nombre de pieza y cantidad son obligatorios");
        }
        
        // Verificar si la pieza existe en inventario
        $id_pieza_inventario = null;
        if (!empty($_POST['buscar_inventario'])) {
            $stmt = $pdo->prepare("SELECT id_pieza FROM inventario WHERE nombre_pieza LIKE ? AND activo = 1 LIMIT 1");
            $stmt->execute(["%$nombre_pieza%"]);
            $pieza = $stmt->fetch();
            if ($pieza) {
                $id_pieza_inventario = $pieza['id_pieza'];
            }
        }
        
        // Insertar pieza solicitada
        $sql = "INSERT INTO piezas_solicitadas (
            id_orden, id_pieza_inventario, nombre_pieza, descripcion, cantidad, 
            precio_unitario, proveedor_sugerido, urgencia, observaciones
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $id_orden, $id_pieza_inventario, $nombre_pieza, $descripcion, $cantidad,
            $precio_unitario, $proveedor_sugerido, $urgencia, $observaciones
        ]);
        
        // Registrar en seguimiento
        $observacion_seguimiento = "Pieza solicitada: $nombre_pieza (Cantidad: $cantidad)";
        $stmt_seg = $pdo->prepare("INSERT INTO seguimiento_orden (id_orden, estado_anterior, estado_nuevo, observaciones, tecnico, tipo_seguimiento) VALUES (?, NULL, NULL, ?, 'Sistema', 'Solicitud Piezas')");
        $stmt_seg->execute([$id_orden, $observacion_seguimiento]);
        
        $_SESSION['success'] = "Pieza agregada a la cotización correctamente";
        
    } catch (Exception $e) {
        $_SESSION['error'] = "Error al agregar pieza: " . $e->getMessage();
    }
    
    header("Location: detalle_orden.php?id=" . $id_orden);
    exit;
}
?>