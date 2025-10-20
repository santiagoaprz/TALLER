<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnóstico y Cotización - Taller Lykos</title>
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
    </style>
</head>
<body>
    <div class="header">
        <h1>Diagnóstico y Cotización - Taller Lykos</h1>
    </div>

    <?php include 'nav.php'; ?>

    <div class="container">
        <?php
        // OBTENER EL ID DE LA ORDEN DE DIFERENTES MANERAS
        $id_orden = $_GET['id'] ?? $_GET['orden'] ?? $_POST['id_orden'] ?? null;
        
        if (!$id_orden) {
            // Si no hay ID, mostrar selector de órdenes
            echo '<div class="card">';
            echo '<h3>Seleccionar Orden para Diagnóstico</h3>';
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
                $selected = ($id_orden == $orden['id_orden']) ? 'selected' : '';
                echo "<option value='{$orden['id_orden']}' $selected>
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

        // Procesar formulario de diagnóstico
        if ($_POST && isset($_POST['diagnostico'])) {
            try {
                $diagnostico = $_POST['diagnostico'];
                $costo_reparacion = floatval($_POST['costo_reparacion']);
                $tiempo_estimado = $_POST['tiempo_estimado'];
                
                // Actualizar diagnóstico
                $stmt = $pdo->prepare("UPDATE ordenes_servicio SET diagnostico = ?, costo_estimado = ?, fecha_entrega_estimada = DATE_ADD(NOW(), INTERVAL ? DAY) WHERE id_orden = ?");
                $stmt->execute([$diagnostico, $costo_reparacion, $tiempo_estimado, $id_orden]);
                
                // Registrar en seguimiento
                $stmt_seg = $pdo->prepare("INSERT INTO seguimiento_orden (id_orden, estado_anterior, estado_nuevo, observaciones, tecnico, tipo_seguimiento) VALUES (?, ?, 'En Diagnóstico', ?, 'Sistema', 'Actualización Diagnóstico')");
                $stmt_seg->execute([$id_orden, $orden['estado_orden'], "Diagnóstico completado. Costo estimado: $" . $costo_reparacion]);
                
                $_SESSION['success'] = "Diagnóstico guardado correctamente";
                header("Location: diagnostico.php?id=" . $id_orden);
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
                <a href="diagnostico.php" class="btn btn-info">Cambiar Orden</a>
            </div>
            <div class="grid-3">
                <div class="info-box">
                    <h4>Cliente</h4>
                    <p><strong><?php echo $orden['cliente_nombre']; ?></strong></p>
                    <p><?php echo $orden['telefono']; ?></p>
                    <p><?php echo $orden['email']; ?></p>
                </div>
                <div class="info-box">
                    <h4>Equipo</h4>
                    <p><strong><?php echo $orden['tipo_equipo']; ?> <?php echo $orden['marca']; ?> <?php echo $orden['modelo']; ?></strong></p>
                    <p>Serie: <?php echo $orden['numero_serie']; ?></p>
                    <p>Color: <?php echo $orden['color']; ?></p>
                </div>
                <div class="info-box">
                    <h4>Estado Actual</h4>
                    <p><strong><?php echo $orden['estado_orden']; ?></strong></p>
                    <p>Técnico: <?php echo $orden['tecnico_asignado']; ?></p>
                    <p>Ingreso: <?php echo $orden['fecha_ingreso']; ?></p>
                </div>
            </div>
        </div>

        <!-- FORMULARIO DE DIAGNÓSTICO -->
        <div class="card">
            <h3>Diagnóstico Técnico</h3>
            <form method="POST">
                <input type="hidden" name="id_orden" value="<?php echo $id_orden; ?>">
                
                <div class="form-group">
                    <label>Problema Reportado por Cliente</label>
                    <textarea class="form-control" rows="2" readonly><?php echo $orden['problemas_reportados']; ?></textarea>
                </div>

                <div class="form-group">
                    <label>Diagnóstico Detallado *</label>
                    <textarea name="diagnostico" class="form-control" rows="4" required placeholder="Describa el problema encontrado, causas, solución requerida..."><?php echo $orden['diagnostico']; ?></textarea>
                </div>
                
                <div class="grid-3">
                    <div class="form-group">
                        <label>Costo de Mano de Obra *</label>
                        <input type="number" name="costo_reparacion" class="form-control" step="0.01" value="<?php echo $orden['costo_estimado'] ?: '0'; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Tiempo Estimado (días) *</label>
                        <input type="number" name="tiempo_estimado" class="form-control" min="1" value="3" required>
                    </div>
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn" style="margin-top: 1.8rem;">💾 Guardar Diagnóstico</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- COTIZACIÓN DE PIEZAS -->
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3>Cotización de Piezas y Accesorios</h3>
                <button type="button" class="btn" onclick="abrirModalPieza()">+ Agregar Pieza/Accesorio</button>
            </div>

            <?php if ($piezas): ?>
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
                            <?php if ($pieza['proveedor_sugerido']): ?>
                            <br><small>Proveedor: <?php echo $pieza['proveedor_sugerido']; ?></small>
                            <?php endif; ?>
                            <?php if ($pieza['observaciones']): ?>
                            <br><small>Notas: <?php echo $pieza['observaciones']; ?></small>
                            <?php endif; ?>
                        </div>
                        <div style="text-align: right; margin-left: 1rem;">
                            <strong style="font-size: 1.1rem;">$<?php echo number_format($subtotal, 2); ?></strong>
                            <br>
                            <button type="button" class="btn-small btn-danger" onclick="eliminarPieza(<?php echo $pieza['id_pieza_solicitada']; ?>)">🗑️ Eliminar</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <div class="total-cotizacion" style="margin-top: 1rem;">
                    <div style="display: flex; justify-content: space-between;">
                        <span>Subtotal Piezas:</span>
                        <span>$<?php echo number_format($total_cotizacion, 2); ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span>Mano de Obra:</span>
                        <span>$<?php echo number_format($orden['costo_estimado'] ?: 0, 2); ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-top: 1px solid white; padding-top: 0.5rem;">
                        <span>TOTAL COTIZACIÓN:</span>
                        <span>$<?php echo number_format($total_cotizacion + ($orden['costo_estimado'] ?: 0), 2); ?></span>
                    </div>
                </div>
            <?php else: ?>
                <p style="text-align: center; color: #666; padding: 2rem;">No hay piezas en la cotización. Agrega las piezas necesarias para la reparación.</p>
            <?php endif; ?>
        </div>

        <!-- ACCIONES -->
        <div class="card">
            <h3>Acciones</h3>
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <button type="button" class="btn" onclick="enviarCotizacion()">📧 Enviar Cotización al Cliente</button>
                <button type="button" class="btn btn-info" onclick="actualizarEstado('Esperando Aprobación Cliente')">⏳ Esperando Aprobación</button>
                <button type="button" class="btn btn-warning" onclick="actualizarEstado('En Reparación')">🔧 Iniciar Reparación</button>
                <a href="pdf_cotizacion.php?id=<?php echo $id_orden; ?>" class="btn" target="_blank">📄 Generar PDF Cotización</a>
            </div>
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
                    <select name="tipo_item" class="form-control" onchange="cambiarTipoItem(this.value)">
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

    function cambiarTipoItem(tipo) {
        // Puedes agregar lógica específica para cada tipo de item
        console.log('Tipo seleccionado:', tipo);
    }

    function eliminarPieza(idPieza) {
        if (confirm('¿Estás seguro de eliminar esta pieza de la cotización?')) {
            window.location.href = 'eliminar_pieza.php?id=' + idPieza + '&orden=<?php echo $id_orden; ?>';
        }
    }

    function enviarCotizacion() {
        if (confirm('¿Enviar cotización al cliente por WhatsApp/Email?')) {
            // Aquí implementarías el envío de cotización
            alert('Función de envío de cotización - Próximamente');
        }
    }

    function actualizarEstado(nuevoEstado) {
        if (confirm('¿Cambiar estado a: ' + nuevoEstado + '?')) {
            window.location.href = 'actualizar_estado.php?id=<?php echo $id_orden; ?>&estado=' + nuevoEstado;
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