<?php
session_start();
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // ===== PRIMERO: MANEJAR EL CLIENTE =====
        $id_cliente = null;
        
        // Si se está usando un cliente existente
        if (isset($_POST['cliente_existente_id']) && !empty($_POST['cliente_existente_id'])) {
            $id_cliente = $_POST['cliente_existente_id'];
        } 
        // Si se está registrando un nuevo cliente
        else if (!empty($_POST['cliente_nombre']) && !empty($_POST['cliente_telefono'])) {
            $sql_cliente = "INSERT INTO clientes (nombre, telefono, email, direccion, fecha_registro) 
                           VALUES (:nombre, :telefono, :email, :direccion, NOW())";
            $stmt_cliente = $pdo->prepare($sql_cliente);
            $stmt_cliente->execute([
                ':nombre' => $_POST['cliente_nombre'],
                ':telefono' => $_POST['cliente_telefono'],
                ':email' => $_POST['cliente_email'] ?? '',
                ':direccion' => $_POST['cliente_direccion'] ?? ''
            ]);
            $id_cliente = $pdo->lastInsertId();
        } else {
            throw new Exception("Se requiere información del cliente (nombre y teléfono)");
        }

        // ===== SEGUNDO: GUARDAR EL EQUIPO =====
        $tipo_equipo = $_POST['equipo_tipo'] ?? '';
        $marca = $_POST['equipo_marca'] ?? '';
        $modelo = $_POST['equipo_modelo'] ?? '';
        $numero_serie = $_POST['equipo_serie'] ?? '';
        $imei = $_POST['equipo_imei'] ?? '';
        $color = $_POST['equipo_color'] ?? '';
        $capacidad_almacenamiento = $_POST['equipo_almacenamiento'] ?? '';
        $ram = $_POST['equipo_ram'] ?? '';
        $problemas_reportados = $_POST['problema_reportado'] ?? '';
        
        // Campos booleanos
        $enciende = isset($_POST['func_enciende']) ? 1 : 0;
        $carga = isset($_POST['func_carga']) ? 1 : 0;
        $display_funciona = isset($_POST['func_display']) ? 1 : 0;
        $tiene_cargador = isset($_POST['accesorio_cargador']) ? 1 : 0;

        // Validaciones
        if (empty($tipo_equipo)) {
            throw new Exception("El tipo de equipo es obligatorio");
        }

        // Consulta INSERT para equipo
        $sql_equipo = "INSERT INTO equipos (
            id_cliente, tipo_equipo, marca, modelo, numero_serie, imei, color, 
            capacidad_almacenamiento, ram, problemas_reportados, enciende, carga, 
            display_funciona, tiene_cargador, fecha_registro
        ) VALUES (
            :id_cliente, :tipo_equipo, :marca, :modelo, :numero_serie, :imei, :color, 
            :capacidad_almacenamiento, :ram, :problemas_reportados, :enciende, :carga, 
            :display_funciona, :tiene_cargador, NOW()
        )";

        $stmt_equipo = $pdo->prepare($sql_equipo);
        $result_equipo = $stmt_equipo->execute([
            ':id_cliente' => $id_cliente,
            ':tipo_equipo' => $tipo_equipo,
            ':marca' => $marca,
            ':modelo' => $modelo,
            ':numero_serie' => $numero_serie,
            ':imei' => $imei,
            ':color' => $color,
            ':capacidad_almacenamiento' => $capacidad_almacenamiento,
            ':ram' => $ram,
            ':problemas_reportados' => $problemas_reportados,
            ':enciende' => $enciende,
            ':carga' => $carga,
            ':display_funciona' => $display_funciona,
            ':tiene_cargador' => $tiene_cargador
        ]);

        if (!$result_equipo) {
            throw new Exception("Error al guardar el equipo");
        }

        $id_equipo = $pdo->lastInsertId();
        
        // ===== TERCERO: CALCULAR COSTOS TOTALES =====
        $costo_total = 0;
        $anticipo = 0;
        
        // DEBUG: Ver qué datos están llegando
        error_log("Datos POST recibidos: " . print_r($_POST, true));
        
        // Calcular costo total de piezas
        if (isset($_POST['piezas']) && is_array($_POST['piezas'])) {
            foreach ($_POST['piezas'] as $piezaData) {
                $precio_unitario = floatval($piezaData['precio'] ?? 0);
                $cantidad = intval($piezaData['cantidad'] ?? 1);
                $costo_total += ($precio_unitario * $cantidad);
                error_log("Pieza: {$piezaData['nombre']} - Precio: $precio_unitario x $cantidad = " . ($precio_unitario * $cantidad));
            }
        }
        
        // Agregar mano de obra si existe
        if (isset($_POST['costo_mano_obra']) && !empty($_POST['costo_mano_obra'])) {
            $mano_obra = floatval($_POST['costo_mano_obra']);
            $costo_total += $mano_obra;
            error_log("Mano de obra agregada: $mano_obra");
        }
        
        // Usar costo total directo si está definido (como respaldo)
        if (isset($_POST['costo_total_recepcion']) && !empty($_POST['costo_total_recepcion'])) {
            $costo_directo = floatval($_POST['costo_total_recepcion']);
            if ($costo_directo > $costo_total) {
                $costo_total = $costo_directo;
                error_log("Usando costo directo: $costo_directo");
            }
        }
        
        // Obtener anticipo
        if (isset($_POST['anticipo_recepcion_final']) && !empty($_POST['anticipo_recepcion_final'])) {
            $anticipo = floatval($_POST['anticipo_recepcion_final']);
        }
        
        $saldo_pendiente = $costo_total - $anticipo;
        
        error_log("COSTO TOTAL FINAL: $costo_total, ANTICIPO: $anticipo, SALDO: $saldo_pendiente");
        
        // ===== CUARTO: CREAR ORDEN DE SERVICIO CON COSTOS REALES =====
        $folio = "LYK-" . date('Ymd') . "-" . str_pad($id_equipo, 4, '0', STR_PAD_LEFT);
        $fecha_ingreso = $_POST['orden_fecha'] ?? date('Y-m-d');
        $hora_ingreso = $_POST['orden_hora'] ?? date('H:i:s');
        $tecnico = $_POST['orden_tecnico'] ?? 'Santiago';
        
        $sql_orden = "INSERT INTO ordenes_servicio (
            id_equipo, folio, fecha_ingreso, hora_ingreso, 
            tecnico_asignado, estado_orden, costo_estimado, anticipo, saldo_pendiente, fecha_creacion
        ) VALUES (
            :id_equipo, :folio, :fecha_ingreso, :hora_ingreso, 
            :tecnico, 'Recibido', :costo_estimado, :anticipo, :saldo_pendiente, NOW()
        )";
        
        $stmt_orden = $pdo->prepare($sql_orden);
        $stmt_orden->execute([
            ':id_equipo' => $id_equipo,
            ':folio' => $folio,
            ':fecha_ingreso' => $fecha_ingreso,
            ':hora_ingreso' => $hora_ingreso,
            ':tecnico' => $tecnico,
            ':costo_estimado' => $costo_total,
            ':anticipo' => $anticipo,
            ':saldo_pendiente' => $saldo_pendiente
        ]);
        
        $id_orden = $pdo->lastInsertId();
        
        // ===== QUINTO: GUARDAR PIEZAS DE LA COTIZACIÓN =====
        if (isset($_POST['piezas']) && is_array($_POST['piezas'])) {
            foreach ($_POST['piezas'] as $piezaData) {
                $tipo_item = $piezaData['tipo'] ?? 'pieza_reparacion';
                $nombre_pieza = trim($piezaData['nombre'] ?? '');
                $precio_unitario = floatval($piezaData['precio'] ?? 0);
                $cantidad = intval($piezaData['cantidad'] ?? 1);
                $descripcion = trim($piezaData['descripcion'] ?? '');
                
                if (empty($nombre_pieza) || $precio_unitario <= 0) {
                    continue; // Saltar piezas inválidas
                }
                
                // Buscar en inventario
                $id_pieza_inventario = null;
                $stmt_inv = $pdo->prepare("SELECT id_pieza FROM inventario WHERE nombre_pieza LIKE ? AND activo = 1 LIMIT 1");
                $stmt_inv->execute(["%$nombre_pieza%"]);
                $pieza_inv = $stmt_inv->fetch();
                if ($pieza_inv) {
                    $id_pieza_inventario = $pieza_inv['id_pieza'];
                }
                
                // Registrar en gastos si es pieza de reparación
                if ($tipo_item === 'pieza_reparacion') {
                    $total_item = $cantidad * $precio_unitario;
                    $stmt_gasto = $pdo->prepare("INSERT INTO gastos (descripcion, monto, fecha_gasto, categoria, observaciones) VALUES (?, ?, CURDATE(), 'Materiales', ?)");
                    $stmt_gasto->execute([
                        "Compra: $nombre_pieza - Orden: $folio", 
                        $total_item, 
                        "Recepción rápida - Cantidad: $cantidad"
                    ]);
                }
                
                // Si es accesorio para venta, registrar en inventario
                if ($tipo_item === 'accesorio_venta' && !$id_pieza_inventario) {
                    $stmt_inv_acc = $pdo->prepare("INSERT INTO inventario (nombre_pieza, descripcion, categoria, cantidad_stock, precio_compra, precio_venta, proveedor) VALUES (?, ?, 'Accesorios', 0, ?, ?, ?)");
                    $precio_compra = $precio_unitario * 0.6; // 40% margen
                    $stmt_inv_acc->execute([$nombre_pieza, $descripcion, $precio_compra, $precio_unitario, 'Recepción']);
                    $id_pieza_inventario = $pdo->lastInsertId();
                }
                
                // Insertar en piezas solicitadas
                $sql_pieza = "INSERT INTO piezas_solicitadas (id_orden, id_pieza_inventario, nombre_pieza, descripcion, cantidad, precio_unitario, proveedor_sugerido, urgencia) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt_pieza = $pdo->prepare($sql_pieza);
                $stmt_pieza->execute([$id_orden, $id_pieza_inventario, $nombre_pieza, $descripcion, $cantidad, $precio_unitario, 'Recepción', 'Media']);
            }
        }
        
        // ===== SEXTO: CREAR REGISTRO EN SEGUIMIENTO =====
        $observaciones_seguimiento = "Equipo recibido mediante recepción rápida";
        if ($costo_total > 0) {
            $observaciones_seguimiento .= ". Cotización inicial: $" . number_format($costo_total, 2);
        }
        if ($anticipo > 0) {
            $observaciones_seguimiento .= ". Anticipo: $" . number_format($anticipo, 2);
        }
        
        $sql_seguimiento = "INSERT INTO seguimiento_orden (
            id_orden, fecha_hora, estado_anterior, estado_nuevo, 
            observaciones, tecnico, tipo_seguimiento
        ) VALUES (
            :id_orden, NOW(), NULL, 'Recibido', 
            :observaciones, :tecnico, 'Cambio Estado'
        )";
        
        $stmt_seguimiento = $pdo->prepare($sql_seguimiento);
        $stmt_seguimiento->execute([
            ':id_orden' => $id_orden,
            ':observaciones' => $observaciones_seguimiento,
            ':tecnico' => $tecnico
        ]);
        
        // Mensaje de éxito con detalles
        $mensaje_exito = "✅ Recepción completada exitosamente!<br>";
        $mensaje_exito .= "<strong>Folio:</strong> " . $folio . "<br>";
        $mensaje_exito .= "<strong>Cliente:</strong> " . ($_POST['cliente_nombre'] ?? 'N/A') . "<br>";
        $mensaje_exito .= "<strong>Equipo:</strong> " . $marca . " " . $modelo . "<br>";
        if ($costo_total > 0) {
            $mensaje_exito .= "<strong>Cotización Total:</strong> $" . number_format($costo_total, 2) . "<br>";
        }
        if ($anticipo > 0) {
            $mensaje_exito .= "<strong>Anticipo:</strong> $" . number_format($anticipo, 2) . "<br>";
            $mensaje_exito .= "<strong>Saldo pendiente:</strong> $" . number_format($saldo_pendiente, 2);
        }
        
        $_SESSION['success'] = $mensaje_exito;
        header("Location: recepcion_rapida.php");
        exit;

    } catch (PDOException $e) {
        $_SESSION['error'] = "❌ Error de base de datos: " . $e->getMessage();
        header("Location: recepcion_rapida.php");
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = "❌ Error: " . $e->getMessage();
        header("Location: recepcion_rapida.php");
        exit;
    }
} else {
    header("Location: recepcion_rapida.php");
    exit;
}
?>