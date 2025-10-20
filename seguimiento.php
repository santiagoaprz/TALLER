<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seguimiento y Cotización - Taller Lykos</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
        .header { background: #2c3e50; color: white; padding: 1rem; text-align: center; }
        .nav { background: #34495e; padding: 1rem; display: flex; justify-content: center; gap: 10px; flex-wrap: wrap; }
        .nav a { padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 5px; }
        .nav a:hover { background: #2980b9; }
        .container { max-width: 1200px; margin: 2rem auto; padding: 0 1rem; }
        .card { background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 1rem; }
        .btn { padding: 10px 20px; background: #27ae60; color: white; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background: #219a52; }
        .btn-small { padding: 5px 10px; font-size: 0.8rem; }
        .btn-danger { background: #e74c3c; }
        .btn-danger:hover { background: #c0392b; }
        .btn-info { background: #3498db; }
        .btn-info:hover { background: #2980b9; }
        .btn-warning { background: #f39c12; }
        .btn-warning:hover { background: #e67e22; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: bold; color: #2c3e50; }
        .form-control { width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem; }
        .info-box { background: #e8f4fd; padding: 1rem; border-radius: 5px; margin-bottom: 1rem; }
        .section-title { background: #ecf0f1; padding: 10px; border-radius: 5px; margin: 1.5rem 0 1rem 0; font-weight: bold; color: #2c3e50; }
        .pieza-item { border: 1px solid #ddd; padding: 1rem; margin-bottom: 0.5rem; border-radius: 5px; }
        .total-cotizacion { background: #2c3e50; color: white; padding: 1rem; border-radius: 5px; font-size: 1.2rem; font-weight: bold; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; }
        .modal-content { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 2rem; border-radius: 10px; width: 90%; max-width: 500px; }
        .selector-orden { background: #fff3cd; padding: 1rem; border-radius: 5px; margin-bottom: 1rem; }
        .seguimiento-item { border-left: 4px solid #3498db; padding: 1rem; margin-bottom: 1rem; background: #f8f9fa; }
        .seguimiento-fecha { font-weight: bold; color: #2c3e50; }
        .seguimiento-tecnico { color: #7f8c8d; font-size: 0.9rem; }
        .seguimiento-observaciones { margin-top: 0.5rem; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Seguimiento y Cotización - Taller Lykos</h1>
    </div>

    <?php include 'nav.php'; ?>

    <div class="container">
        <?php
        // OBTENER EL ID DE LA ORDEN DE DIFERENTES MANERAS
        $id_orden = $_GET['id'] ?? $_GET['orden'] ?? $_POST['id_orden'] ?? null;
        
        if (!$id_orden) {
            // Si no hay ID, mostrar selector de órdenes
            echo '<div class="card">';
            echo '<h3>Seleccionar Orden para Seguimiento</h3>';
            echo '<div class="selector-orden">';
            echo '<form method="GET">';
            echo '<div class="form-group">';
            echo '<label>Seleccionar Orden:</label>';
            echo '<select name="id" class="form-control" onchange="this.form.submit()">';
            echo '<option value="">-- Seleccionar orden --</option>';
            
            $stmt = $pdo->query("
                SELECT os.id_orden, os.folio, os.estado_orden, e.marca, e.modelo, c.nombre as cliente 
                FROM ordenes_servicio os 
                JOIN equipos e ON os.id_equipo = e.id_equipo 
                JOIN clientes c ON e.id_cliente = c.id_cliente 
                WHERE os.activa = 1 
                ORDER BY os.fecha_creacion DESC
            ");
            while($orden = $stmt->fetch()) {
                echo "<option value='{$orden['id_orden']}'>
                    {$orden['folio']} - {$orden['cliente']} - {$orden['marca']} {$orden['modelo']} ({$orden['estado_orden']})
                </option>";
            }
            echo '</select>';
            echo '</div>';
            echo '</form>';
            echo '</div>';
            echo '</div>';
            exit;
        }

        // Obtener información de la orden
        $stmt = $pdo->prepare("
            SELECT os.*, e.*, c.nombre as cliente_nombre, c.telefono, c.email
            FROM ordenes_servicio os
            JOIN equipos e ON os.id_equipo = e.id_equipo
            JOIN clientes c ON e.id_cliente = c.id_cliente
            WHERE os.id_orden = ?
        ");
        $stmt->execute([$id_orden]);
        $orden = $stmt->fetch();

        if (!$orden) {
            echo '<div class="card"><p style="color: red; text-align: center;">Orden no encontrada</p></div>';
            exit;
        }

        // Procesar nuevo seguimiento
        if ($_POST && isset($_POST['observaciones_seguimiento'])) {
            try {
                $observaciones = trim($_POST['observaciones_seguimiento']);
                $tipo_seguimiento = $_POST['tipo_seguimiento'];
                $nuevo_estado = $_POST['nuevo_estado'] ?? $orden['estado_orden'];
                
                if (empty($observaciones)) {
                    throw new Exception("Las observaciones son obligatorias");
                }
                
                // Insertar en seguimiento
                $stmt_seg = $pdo->prepare("INSERT INTO seguimiento_orden (id_orden, estado_anterior, estado_nuevo, observaciones, tecnico, tipo_seguimiento) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt_seg->execute([$id_orden, $orden['estado_orden'], $nuevo_estado, $observaciones, 'Sistema', $tipo_seguimiento]);
                
                // Actualizar estado si es diferente
                if ($nuevo_estado != $orden['estado_orden']) {
                    $stmt_update = $pdo->prepare("UPDATE ordenes_servicio SET estado_orden = ? WHERE id_orden = ?");
                    $stmt_update->execute([$nuevo_estado, $id_orden]);
                }
                
                $_SESSION['success'] = "Seguimiento agregado correctamente";
                header("Location: seguimiento.php?id=" . $id_orden);
                exit;
                
            } catch (Exception $e) {
                $_SESSION['error'] = "Error: " . $e->getMessage();
            }
        }

        // Obtener piezas de la cotización
        $stmt_piezas = $pdo->prepare("
            SELECT * FROM piezas_solicitadas WHERE id_orden = ? ORDER BY fecha_solicitud DESC
        ");
        $stmt_piezas->execute([$id_orden]);
        $piezas = $stmt_piezas->fetchAll();
        $total_cotizacion = 0;
        foreach ($piezas as $pieza) {
            $total_cotizacion += $pieza['cantidad'] * $pieza['precio_unitario'];
        }

        // Obtener historial de seguimiento
        $stmt_seguimiento = $pdo->prepare("
            SELECT * FROM seguimiento_orden 
            WHERE id_orden = ? 
            ORDER BY fecha_hora DESC
        ");
        $stmt_seguimiento->execute([$id_orden]);
        $seguimientos = $stmt_seguimiento->fetchAll();
        ?>

        <!-- Mostrar mensajes -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="card" style="background: #d4edda; color: #155724;">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="card" style="background: #f8d7da; color: #721c24;">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <!-- INFORMACIÓN DE LA ORDEN -->
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2>Orden: <?php echo $orden['folio']; ?></h2>
                <a href="seguimiento.php" class="btn btn-info">Cambiar Orden</a>
            </div>
            <div class="grid-3">
                <div class="info-box">
                    <h4>Cliente</h4>
                    <p><strong><?php echo $orden['cliente_nombre']; ?></strong></p>
                    <p><?php echo $orden['telefono']; ?></p>
                </div>
                <div class="info-box">
                    <h4>Equipo</h4>
                    <p><strong><?php echo $orden['tipo_equipo']; ?> <?php echo $orden['marca']; ?> <?php echo $orden['modelo']; ?></strong></p>
                    <p>Serie: <?php echo $orden['numero_serie']; ?></p>
                </div>
                <div class="info-box">
                    <h4>Estado Actual</h4>
                    <p><strong style="font-size: 1.1rem; color: #e74c3c;"><?php echo $orden['estado_orden']; ?></strong></p>
                    <p>Técnico: <?php echo $orden['tecnico_asignado']; ?></p>
                </div>
            </div>
        </div>

        <!-- RESUMEN DE COTIZACIÓN -->
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3>Cotización de Piezas y Accesorios</h3>
                <button type="button" class="btn" onclick="abrirModalPieza()">+ Agregar Pieza/Accesorio</button>
            </div>

            <div class="grid-3">
                <div class="info-box">
                    <h4>Piezas Reparación</h4>
                    <p style="font-size: 1.5rem; font-weight: bold; color: #2c3e50;">
                        $<?php echo number_format($total_cotizacion, 2); ?>
                    </p>
                    <p><?php echo count($piezas); ?> items</p>
                </div>
                <div class="info-box">
                    <h4>Mano de Obra</h4>
                    <p style="font-size: 1.5rem; font-weight: bold; color: #2c3e50;">
                        $<?php echo number_format($orden['costo_estimado'] ?: 0, 2); ?>
                    </p>
                </div>
                <div class="info-box">
                    <h4>Total</h4>
                    <p style="font-size: 1.5rem; font-weight: bold; color: #27ae60;">
                        $<?php echo number_format($total_cotizacion + ($orden['costo_estimado'] ?: 0), 2); ?>
                    </p>
                </div>
            </div>

            <?php if ($piezas): ?>
                <div style="margin-top: 1rem;">
                    <h4>Detalle de Piezas:</h4>
                    <?php foreach ($piezas as $pieza): 
                        $subtotal = $pieza['cantidad'] * $pieza['precio_unitario'];
                    ?>
                    <div class="pieza-item">
                        <div style="display: flex; justify-content: space-between; align-items: start;">
                            <div style="flex: 1;">
                                <strong><?php echo $pieza['nombre_pieza']; ?></strong>
                                <?php if ($pieza['descripcion']): ?>
                                <br><small style="color: #666;"><?php echo $pieza['descripcion']; ?></small>
                                <?php endif; ?>
                                <br><small>Cantidad: <?php echo $pieza['cantidad']; ?> x $<?php echo number_format($pieza['precio_unitario'], 2); ?></small>
                            </div>
                            <div style="text-align: right; margin-left: 1rem;">
                                <strong>$<?php echo number_format($subtotal, 2); ?></strong>
                                <br>
                                <button type="button" class="btn-small btn-danger" onclick="eliminarPieza(<?php echo $pieza['id_pieza_solicitada']; ?>)">🗑️ Eliminar</button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="text-align: center; color: #666; padding: 1rem;">No hay piezas en la cotización</p>
            <?php endif; ?>
        </div>

        <!-- AGREGAR SEGUIMIENTO -->
        <div class="card">
            <h3>Agregar Seguimiento</h3>
            <form method="POST">
                <input type="hidden" name="id_orden" value="<?php echo $id_orden; ?>">
                
                <div class="grid-2">
                    <div class="form-group">
                        <label>Tipo de Seguimiento</label>
                        <select name="tipo_seguimiento" class="form-control" required>
                            <option value="Observación">Observación</option>
                            <option value="Actualización Diagnóstico">Actualización Diagnóstico</option>
                            <option value="Solicitud Piezas">Solicitud Piezas</option>
                            <option value="Cambio Estado">Cambio Estado</option>
                            <option value="Comunicación Cliente">Comunicación Cliente</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Cambiar Estado (opcional)</label>
                        <select name="nuevo_estado" class="form-control">
                            <option value="<?php echo $orden['estado_orden']; ?>">Mantener estado actual</option>
                            <option value="Recibido">Recibido</option>
                            <option value="En Diagnóstico">En Diagnóstico</option>
                            <option value="Diagnóstico Completado">Diagnóstico Completado</option>
                            <option value="Esperando Aprobación Cliente">Esperando Aprobación Cliente</option>
                            <option value="En Reparación">En Reparación</option>
                            <option value="Esperando Repuestos">Esperando Repuestos</option>
                            <option value="Reparado">Reparado</option>
                            <option value="Pruebas Finales">Pruebas Finales</option>
                            <option value="Listo para Entregar">Listo para Entregar</option>
                            <option value="Entregado">Entregado</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Observaciones *</label>
                    <textarea name="observaciones_seguimiento" class="form-control" rows="4" required placeholder="Describa el avance, problemas encontrados, comunicación con el cliente..."></textarea>
                </div>
                
                <button type="submit" class="btn">💾 Guardar Seguimiento</button>
            </form>
        </div>

        <!-- HISTORIAL DE SEGUIMIENTO -->
        <div class="card">
            <h3>Historial de Seguimiento</h3>
            
            <?php if ($seguimientos): ?>
                <?php foreach ($seguimientos as $seg): ?>
                <div class="seguimiento-item">
                    <div class="seguimiento-fecha">
                        <?php echo date('d/m/Y H:i', strtotime($seg['fecha_hora'])); ?>
                        - <?php echo $seg['tecnico']; ?>
                        <span style="background: #3498db; color: white; padding: 2px 8px; border-radius: 10px; font-size: 0.8em; margin-left: 10px;">
                            <?php echo $seg['tipo_seguimiento']; ?>
                        </span>
                    </div>
                    
                    <?php if ($seg['estado_anterior'] && $seg['estado_nuevo']): ?>
                    <div style="color: #e74c3c; font-weight: bold;">
                        Estado: <?php echo $seg['estado_anterior']; ?> → <?php echo $seg['estado_nuevo']; ?>
                    </div>
                    <?php endif; ?>
                    
                    <div class="seguimiento-observaciones">
                        <?php echo nl2br(htmlspecialchars($seg['observaciones'])); ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align: center; color: #666; padding: 2rem;">No hay seguimientos registrados</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- MODAL PARA AGREGAR PIEZAS -->
    <div class="modal" id="modalPieza">
        <div class="modal-content">
            <h3>Agregar Pieza/Accesorio a Cotización</h3>
            <form id="formPieza" method="POST" action="agregar_pieza_diagnostico.php">
                <input type="hidden" name="id_orden" value="<?php echo $id_orden; ?>">
                
                <div class="form-group">
                    <label>Tipo *</label>
                    <select name="tipo_item" class="form-control">
                        <option value="pieza_reparacion">Pieza para Reparación</option>
                        <option value="accesorio_venta">Accesorio para Venta</option>
                        <option value="consumible">Consumible/Herramienta</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Nombre del Item *</label>
                    <input type="text" name="nombre_pieza" class="form-control" required 
                           placeholder="Ej: Pantalla LCD, Funda, Mica, Batería...">
                </div>
                
                <div class="form-group">
                    <label>Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="2" 
                              placeholder="Especificaciones, modelo compatible..."></textarea>
                </div>
                
                <div class="grid-3">
                    <div class="form-group">
                        <label>Cantidad *</label>
                        <input type="number" name="cantidad" class="form-control" value="1" min="1" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Precio Unitario *</label>
                        <input type="number" name="precio_unitario" class="form-control" step="0.01" min="0" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Urgencia</label>
                        <select name="urgencia" class="form-control">
                            <option value="Baja">Baja</option>
                            <option value="Media" selected>Media</option>
                            <option value="Alta">Alta</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Proveedor</label>
                    <input type="text" name="proveedor_sugerido" class="form-control" 
                           placeholder="Proveedor recomendado">
                </div>
                
                <div class="form-group">
                    <label>Observaciones</label>
                    <textarea name="observaciones" class="form-control" rows="2" 
                              placeholder="Notas internas..."></textarea>
                </div>
                
                <div style="display: flex; justify-content: space-between; margin-top: 1rem;">
                    <button type="button" class="btn btn-danger" onclick="cerrarModalPieza()">Cancelar</button>
                    <button type="submit" class="btn">✅ Agregar a Cotización</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function abrirModalPieza() {
        document.getElementById('modalPieza').style.display = 'block';
    }

    function cerrarModalPieza() {
        document.getElementById('modalPieza').style.display = 'none';
    }

    function eliminarPieza(idPieza) {
        if (confirm('¿Estás seguro de eliminar esta pieza de la cotización?')) {
            window.location.href = 'eliminar_pieza.php?id=' + idPieza + '&orden=<?php echo $id_orden; ?>';
        }
    }

    // Cerrar modal al hacer click fuera
    document.getElementById('modalPieza').addEventListener('click', function(e) {
        if (e.target.id === 'modalPieza') {
            cerrarModalPieza();
        }
    });
    </script>
</body>
</html>