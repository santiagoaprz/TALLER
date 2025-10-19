<?php
include 'config.php';

if($_POST) {
    $id_cliente = $_POST['id_cliente'] ?: null;
    $nombre = sanitizar($_POST['nombre']);
    $telefono = sanitizar($_POST['telefono']);
    $email = sanitizar($_POST['email']);
    $rfc = sanitizar($_POST['rfc']);
    $direccion = sanitizar($_POST['direccion']);
    
    if($id_cliente) {
        // ACTUALIZAR CLIENTE
        $stmt = $pdo->prepare("UPDATE clientes SET nombre=?, telefono=?, email=?, rfc=?, direccion=? WHERE id_cliente=?");
        $stmt->execute([$nombre, $telefono, $email, $rfc, $direccion, $id_cliente]);
        $mensaje = "Cliente actualizado correctamente";
    } else {
        // NUEVO CLIENTE
        $stmt = $pdo->prepare("INSERT INTO clientes (nombre, telefono, email, rfc, direccion) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nombre, $telefono, $email, $rfc, $direccion]);
        $mensaje = "Cliente registrado correctamente";
    }
    
    header("Location: clientes.php?mensaje=" . urlencode($mensaje));
    exit;
}
?>