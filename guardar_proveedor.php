<?php
include 'config.php';

if($_POST) {
    $id_proveedor = $_POST['id_proveedor'] ?: null;
    $nombre = sanitizar($_POST['nombre']);
    $contacto = sanitizar($_POST['contacto']);
    $telefono = sanitizar($_POST['telefono']);
    $email = sanitizar($_POST['email']);
    $direccion = sanitizar($_POST['direccion']);
    $productos_especialidad = sanitizar($_POST['productos_especialidad']);
    $calificacion = $_POST['calificacion'];
    $confiable = $_POST['confiable'];
    $observaciones = sanitizar($_POST['observaciones']);
    
    if($id_proveedor) {
        // ACTUALIZAR
        $stmt = $pdo->prepare("UPDATE proveedores SET nombre=?, contacto=?, telefono=?, email=?, direccion=?, productos_especialidad=?, calificacion=?, confiable=?, observaciones=? WHERE id_proveedor=?");
        $stmt->execute([$nombre, $contacto, $telefono, $email, $direccion, $productos_especialidad, $calificacion, $confiable, $observaciones, $id_proveedor]);
        $mensaje = "Proveedor actualizado correctamente";
    } else {
        // NUEVO
        $stmt = $pdo->prepare("INSERT INTO proveedores (nombre, contacto, telefono, email, direccion, productos_especialidad, calificacion, confiable, observaciones) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nombre, $contacto, $telefono, $email, $direccion, $productos_especialidad, $calificacion, $confiable, $observaciones]);
        $mensaje = "Proveedor registrado correctamente";
    }
    
    header("Location: proveedores.php?mensaje=" . urlencode($mensaje));
    exit;
}
?>