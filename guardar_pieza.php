<?php
include 'config.php';

if($_POST) {
    $id_pieza = $_POST['id_pieza'] ?: null;
    $nombre_pieza = sanitizar($_POST['nombre_pieza']);
    $categoria = $_POST['categoria'];
    $descripcion = sanitizar($_POST['descripcion']);
    $cantidad_stock = $_POST['cantidad_stock'];
    $stock_minimo = $_POST['stock_minimo'];
    $precio_compra = $_POST['precio_compra'] ?: null;
    $precio_venta = $_POST['precio_venta'] ?: null;
    $proveedor = sanitizar($_POST['proveedor']);
    $numero_parte = sanitizar($_POST['numero_parte']);
    $compatible_con = sanitizar($_POST['compatible_con']);
    $ubicacion = sanitizar($_POST['ubicacion']);
    
    if($id_pieza) {
        // ACTUALIZAR PIEZA
        $stmt = $pdo->prepare("UPDATE inventario SET nombre_pieza=?, categoria=?, descripcion=?, cantidad_stock=?, stock_minimo=?, precio_compra=?, precio_venta=?, proveedor=?, numero_parte=?, compatible_con=?, ubicacion=? WHERE id_pieza=?");
        $stmt->execute([$nombre_pieza, $categoria, $descripcion, $cantidad_stock, $stock_minimo, $precio_compra, $precio_venta, $proveedor, $numero_parte, $compatible_con, $ubicacion, $id_pieza]);
        $mensaje = "Pieza actualizada correctamente";
    } else {
        // NUEVA PIEZA
        $stmt = $pdo->prepare("INSERT INTO inventario (nombre_pieza, categoria, descripcion, cantidad_stock, stock_minimo, precio_compra, precio_venta, proveedor, numero_parte, compatible_con, ubicacion) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nombre_pieza, $categoria, $descripcion, $cantidad_stock, $stock_minimo, $precio_compra, $precio_venta, $proveedor, $numero_parte, $compatible_con, $ubicacion]);
        $mensaje = "Pieza agregada al inventario correctamente";
    }
    
    header("Location: inventario.php?mensaje=" . urlencode($mensaje));
    exit;
}
?>