<?php
include 'config.php';

if($_POST) {
    try {
        $pdo->beginTransaction();
        
        // 1. MANEJAR CLIENTE (NUEVO O EXISTENTE)
        if(isset($_POST['cliente_existente_id']) && $_POST['cliente_existente_id']) {
            $id_cliente = $_POST['cliente_existente_id'];
        } else {
            // CREAR NUEVO CLIENTE
            $stmt = $pdo->prepare("INSERT INTO clientes (nombre, telefono, email, direccion) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                sanitizar($_POST['cliente_nombre']),
                sanitizar($_POST['cliente_telefono']),
                sanitizar($_POST['cliente_email']),
                sanitizar($_POST['cliente_direccion'])
            ]);
            $id_cliente = $pdo->lastInsertId();
        }
        
        // 2. DETERMINAR SI PRENDE O NO
        $prende = isset($_POST['func_enciende']) ? 1 : 0;
        
        // 3. CREAR EQUIPO - VERSIÓN SIMPLIFICADA
        $stmt = $pdo->prepare("INSERT INTO equipos (
            id_cliente, tipo_equipo, marca, modelo, numero_serie, imei, color,
            capacidad_almacenamiento, ram, 
            estado_fisico, golpes, rayones, faltan_tornillos, desgaste_uso,
            problemas_reportados, enciende, carga, display_funciona,
            tiene_cargador, numero_serie_cargador, estado_cargador, funda_proteccion,
            cables_extra, otros_accesorios,
            -- Campos para celulares
            bocinas_funcionan, microfono_funciona, camara_funciona, botones_funcionan,
            huella_funciona, wifi_funciona, señal_funciona, sensores_funcionan, reporte_robo
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        // VALORES PARA CAMPOS DE CELULAR (solo si prende)
        $bocinas_funcionan = $prende && isset($_POST['cel_bocinas']) ? 1 : 0;
        $microfono_funciona = $prende && isset($_POST['cel_microfono']) ? 1 : 0;
        $camara_funciona = $prende && isset($_POST['cel_camara']) ? 1 : 0;
        $botones_funcionan = $prende && isset($_POST['cel_botones']) ? 1 : 0;
        $huella_funciona = $prende && isset($_POST['cel_huella']) ? 1 : 0;
        $wifi_funciona = $prende && isset($_POST['cel_wifi']) ? 1 : 0;
        $señal_funciona = $prende && isset($_POST['cel_señal']) ? 1 : 0;
        $sensores_funcionan = $prende && isset($_POST['cel_sensores']) ? 1 : 0;
        
        $stmt->execute([
            // Datos básicos
            $id_cliente,
            $_POST['equipo_tipo'],
            sanitizar($_POST['equipo_marca']),
            sanitizar($_POST['equipo_modelo']),
            sanitizar($_POST['equipo_serie']),
            sanitizar($_POST['equipo_imei'] ?? ''),
            sanitizar($_POST['equipo_color']),
            sanitizar($_POST['equipo_almacenamiento']),
            sanitizar($_POST['equipo_ram']),
            // Estado físico
            sanitizar($_POST['estado_fisico']),
            sanitizar($_POST['golpes']),
            sanitizar($_POST['rayones']),
            sanitizar($_POST['faltan_tornillos']),
            sanitizar($_POST['desgaste_uso']),
            // Problema y funcionalidad básica
            sanitizar($_POST['problema_reportado']),
            $prende,
            isset($_POST['func_carga']) ? 1 : 0,
            isset($_POST['func_display']) ? 1 : 0,
            // Accesorios
            $_POST['accesorio_cargador'],
            sanitizar($_POST['accesorio_serie_cargador']),
            sanitizar($_POST['accesorio_estado_cargador']),
            $_POST['accesorio_funda'],
            sanitizar($_POST['accesorio_cables']),
            sanitizar($_POST['accesorio_otros']),
            // Campos celulares (solo si prende)
            $bocinas_funcionan,
            $microfono_funciona,
            $camara_funciona,
            $botones_funcionan,
            $huella_funciona,
            $wifi_funciona,
            $señal_funciona,
            $sensores_funcionan,
            isset($_POST['cel_reporta_robo']) ? 1 : 0
        ]);
        
        $id_equipo = $pdo->lastInsertId();
        
        // 4. CREAR ORDEN DE SERVICIO
        $folio = generarFolio();
        $stmt = $pdo->prepare("INSERT INTO ordenes_servicio (
            folio, id_equipo, fecha_ingreso, hora_ingreso, fecha_entrega_estimada,
            tecnico_asignado, observaciones_internas, costo_estimado
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        $fecha_entrega = !empty($_POST['orden_fecha_entrega']) ? $_POST['orden_fecha_entrega'] : null;
        
        $stmt->execute([
            $folio,
            $id_equipo,
            $_POST['orden_fecha'],
            $_POST['orden_hora'],
            $fecha_entrega,
            sanitizar($_POST['orden_tecnico']),
            sanitizar($_POST['orden_observaciones']),
            $_POST['orden_costo_estimado'] ?: 0
        ]);
        
        $id_orden = $pdo->lastInsertId();
        
        // 5. REGISTRAR SEGUIMIENTO INICIAL
        $stmt = $pdo->prepare("INSERT INTO seguimiento_orden (id_orden, estado_anterior, estado_nuevo, observaciones, tecnico, tipo_seguimiento) VALUES (?, 'Nuevo', 'Recibido', ?, ?, 'Cambio Estado')");
        $observacion_seguimiento = "Equipo recibido: " . substr(sanitizar($_POST['problema_reportado']), 0, 100);
        $stmt->execute([$id_orden, $observacion_seguimiento, sanitizar($_POST['orden_tecnico'])]);
        
        $pdo->commit();
        
        // REDIRIGIR AL PDF DE RECEPCIÓN
        header("Location: pdf_recepcion.php?orden=$id_orden");
        exit;
        
    } catch(Exception $e) {
        $pdo->rollBack();
        echo "<h2>Error al guardar la recepción:</h2>";
        echo "<pre style='background: #ffe6e6; padding: 20px; border: 1px solid red;'>";
        echo "ERROR: " . $e->getMessage() . "\n\n";
        echo "ARCHIVO: " . $e->getFile() . " (Línea: " . $e->getLine() . ")\n\n";
        echo "TRACE:\n" . $e->getTraceAsString() . "\n\n";
        echo "</pre>";
        exit;
    }
} else {
    echo "<h2>No se recibieron datos del formulario</h2>";
    echo "<a href='recepcion_rapida_mejorada.php'>Volver al formulario</a>";
}
?>