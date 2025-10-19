<?php
include 'config.php';

if($_POST) {
    // RECOGER TODOS LOS DATOS DEL FORMULARIO
    $id_equipo = $_POST['id_equipo'] ?: null;
    $datos = [
        'id_cliente' => $_POST['id_cliente'],
        'tipo_equipo' => $_POST['tipo_equipo'],
        'marca' => sanitizar($_POST['marca']),
        'modelo' => sanitizar($_POST['modelo']),
        'numero_serie' => sanitizar($_POST['numero_serie']),
        'imei' => sanitizar($_POST['imei']),
        'color' => sanitizar($_POST['color']),
        'capacidad_almacenamiento' => sanitizar($_POST['capacidad_almacenamiento']),
        'ram' => sanitizar($_POST['ram']),
        'procesador' => sanitizar($_POST['procesador']),
        'sistema_operativo' => sanitizar($_POST['sistema_operativo']),
        'version_so' => sanitizar($_POST['version_so']),
        'estado_fisico' => sanitizar($_POST['estado_fisico']),
        'golpes' => sanitizar($_POST['golpes']),
        'rayones' => sanitizar($_POST['rayones']),
        'faltan_tornillos' => sanitizar($_POST['faltan_tornillos']),
        'desgaste_uso' => sanitizar($_POST['desgaste_uso']),
        'enciende' => isset($_POST['enciende']) ? 1 : 0,
        'carga' => isset($_POST['carga']) ? 1 : 0,
        'display_funciona' => isset($_POST['display_funciona']) ? 1 : 0,
        'accesorios_ingreso' => sanitizar($_POST['accesorios_ingreso']),
        'tiene_cargador' => $_POST['tiene_cargador'],
        'numero_serie_cargador' => sanitizar($_POST['numero_serie_cargador']),
        'estado_cargador' => sanitizar($_POST['estado_cargador']),
        'funda_proteccion' => $_POST['funda_proteccion'],
        'cables_extra' => sanitizar($_POST['cables_extra']),
        'otros_accesorios' => sanitizar($_POST['otros_accesorios']),
        'problemas_reportados' => sanitizar($_POST['problemas_reportados']),
        // Campos para celulares
        'bocinas_funcionan' => isset($_POST['bocinas_funcionan']) ? 1 : 0,
        'microfono_funciona' => isset($_POST['microfono_funciona']) ? 1 : 0,
        'camara_funciona' => isset($_POST['camara_funciona']) ? 1 : 0,
        'botones_funcionan' => isset($_POST['botones_funcionan']) ? 1 : 0,
        'huella_funciona' => isset($_POST['huella_funciona']) ? 1 : 0,
        'wifi_funciona' => isset($_POST['wifi_funciona']) ? 1 : 0,
        'señal_funciona' => isset($_POST['señal_funciona']) ? 1 : 0,
        'sensores_funcionan' => isset($_POST['sensores_funcionan']) ? 1 : 0,
        'reporte_robo' => isset($_POST['reporte_robo']) ? 1 : 0,
        // Campos para laptops
        'usb_funciona' => isset($_POST['usb_funciona']) ? 1 : 0,
        'hdmi_funciona' => isset($_POST['hdmi_funciona']) ? 1 : 0,
        'vga_funciona' => isset($_POST['vga_funciona']) ? 1 : 0,
        'ethernet_funciona' => isset($_POST['ethernet_funciona']) ? 1 : 0,
        'wifi_pc_funciona' => isset($_POST['wifi_pc_funciona']) ? 1 : 0,
        'bluetooth_funciona' => isset($_POST['bluetooth_funciona']) ? 1 : 0,
        'teclado_funciona' => isset($_POST['teclado_funciona']) ? 1 : 0,
        'touchpad_funciona' => isset($_POST['touchpad_funciona']) ? 1 : 0
    ];
    
    if($id_equipo) {
        // ACTUALIZAR EQUIPO
        $campos = [];
        $valores = [];
        foreach($datos as $campo => $valor) {
            $campos[] = "$campo = ?";
            $valores[] = $valor;
        }
        $valores[] = $id_equipo;
        
        $sql = "UPDATE equipos SET " . implode(', ', $campos) . " WHERE id_equipo = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($valores);
        $mensaje = "Equipo actualizado correctamente";
    } else {
        // NUEVO EQUIPO
        $campos = implode(', ', array_keys($datos));
        $placeholders = implode(', ', array_fill(0, count($datos), '?'));
        
        $sql = "INSERT INTO equipos ($campos) VALUES ($placeholders)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_values($datos));
        $mensaje = "Equipo registrado correctamente";
    }
    
    header("Location: equipos.php?mensaje=" . urlencode($mensaje));
    exit;
}
?>