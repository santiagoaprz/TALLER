<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Orden - Taller Lykos</title>
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
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: bold; color: #2c3e50; }
        .form-control { width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem; }
        .info-box { background: #e8f4fd; padding: 1rem; border-radius: 5px; margin-bottom: 1rem; }
        .section-title { background: #ecf0f1; padding: 10px; border-radius: 5px; margin: 1.5rem 0 1rem 0; font-weight: bold; color: #2c3e50; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Detalle de Orden de Servicio - Taller Lykos</h1>
    </div>

    <?php include 'nav.php'; ?>

    <div class="container">
        <?php
        $id_orden = $_GET['id'] ?? null;
        if (!$id_orden) {
            die("No se especificó la orden");
        }

        // Obtener información de la orden
        $stmt = $pdo->prepare("
            SELECT os.*, e.*, c.nombre as cliente_nombre, c.telefono, c.email, c.direccion
            FROM ordenes_servicio os
            JOIN equipos e ON os.id_equipo = e.id_equipo
            JOIN clientes c ON e.id_cliente = c.id_cliente
            WHERE os.id_orden = ?
        ");
        $stmt->execute([$id_orden]);
        $orden = $stmt->fetch();

        if (!$orden) {
            die("Orden no encontrada");
        }
        ?>

        <!-- INFORMACIÓN GENERAL -->
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h2>Orden: <?php echo $orden['folio']; ?></h2>
                <div>
                    <a href="pdf_recepcion.php?id=<?php echo $orden['id_orden']; ?>" class="btn btn-info" target="_blank">PDF Recepción</a>
                    <a href="pdf_entrega.php?id=<?php echo $orden['id_orden']; ?>" class="btn" target="_blank">PDF Entrega</a>
                </div>
            </div>

            <div class="grid-3">
                <div class="info-box">
                    <h4>Información del Cliente</h4>
                    <p><strong>Nombre:</strong> <?php echo $orden['cliente_nombre']; ?></p>
                    <p><strong>Teléfono:</strong> <?php echo $orden['telefono']; ?></p>
                    <p><strong>Email:</strong> <?php echo $orden['email']; ?></p>
                </div>
                
                <div class="info-box">
                    <h4>Información del Equipo</h4>
                    <p><strong>Tipo:</strong> <?php echo $orden['tipo_equipo']; ?></p>
                    <p><strong>Marca/Modelo:</strong> <?php echo $orden['marca'] . ' ' . $orden['modelo']; ?></p>
                    <p><strong>Serie:</strong> <?php echo $orden['numero_serie']; ?></p>
                </div>
                
                <div class="info-box">
                    <h4>Estado de la Orden</h4>
                    <p><strong>Estado:</strong> <?php echo $orden['estado_orden']; ?></p>
                    <p><strong>Técnico:</strong> <?php echo $orden['tecnico_asignado']; ?></p>
                    <p><strong>Ingreso:</strong> <?php echo $orden['fecha_ingreso']; ?></p>
                </div>
            </div>
        </div>

        <!-- DIAGNÓSTICO Y TRABAJO REALIZADO -->
        <div class="card">
            <h3>Diagnóstico y Trabajo</h3>
            
            <div class="form-group">
                <label>Problema Reportado por el Cliente</label>
                <textarea class="form-control" rows="3" readonly><?php echo $orden['problemas_reportados']; ?></textarea>
            </div>

            <div class="form-group">
                <label>Diagnóstico del Técnico</label>
                <textarea name="diagnostico" class="form-control" rows="3" placeholder="Diagnóstico técnico..."><?php echo $orden['diagnostico']; ?></textarea>
            </div>

            <div class="form-group">
                <label>Trabajo Realizado</label>
                <textarea name="trabajo_realizado" class="form-control" rows="3" placeholder="Descripción del trabajo realizado..."><?php echo $orden['trabajo_realizado']; ?></textarea>
            </div>
        </div>

        <!-- SECCIÓN DE PIEZAS SOLICITADAS -->
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3>Piezas para Cotización</h3>
                <button type="button" class="btn" onclick="abrirModalPiezas(<?php echo $orden['id_orden']; ?>)">
                    + Agregar Pieza
                </button>
            </div>
            
            <?php
            $stmt_piezas = $pdo->prepare("
                SELECT ps.*, i.nombre_pieza as nombre_inventario, i.cantidad_stock 
                FROM piezas_solicitadas ps 
                LEFT JOIN inventario i ON ps.id_pieza_inventario = i.id_pieza 
                WHERE ps.id_orden = ? 
                ORDER BY ps.urgencia DESC, ps.fecha_solicitud DESC
            ");
            $stmt_piezas->execute([$orden['id_orden']]);
            $piezas = $stmt_piezas->fetchAll();
            
            if ($piezas): 
                $total_piezas = 0;
            ?>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #34495e; color: white;">
                        <th style="padding: 10px; text-align: left;">Pieza</th>
                        <th style="padding: 10px; text-align: center;">Cant</th>
                        <th style="padding: 10px; text-align: right;">Precio Unit.</th>
                        <th style="padding: 10px; text-align: right;">Subtotal</th>
                        <th style="padding: 10px; text-align: center;">Urgencia</th>
                        <th style="padding: 10px; text-align: center;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($piezas as $pieza): 
                        $subtotal = $pieza['cantidad'] * $pieza['precio_unitario'];
                        $total_piezas += $subtotal;
                        
                        // Color por urgencia
                        $color_urgencia = [
                            'Baja' => '#27ae60',
                            'Media' => '#f39c12', 
                            'Alta' => '#e74c3c',
                            'Crítica' => '#8b0000'
                        ];
                    ?>
                    <tr>
                        <td style="padding: 10px; border-bottom: 1px solid #eee;">
                            <strong><?php echo $pieza['nombre_pieza']; ?></strong>
                            <?php if($pieza['descripcion']): ?>
                            <br><small style="color: #666;"><?php echo $pieza['descripcion']; ?></small>
                            <?php endif; ?>
                            <?php if($pieza['nombre_inventario']): ?>
                            <br><small style="color: #27ae60;">✓ En inventario (Stock: <?php echo $pieza['cantidad_stock']; ?>)</small>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 10px; text-align: center; border-bottom: 1px solid #eee;"><?php echo $pieza['cantidad']; ?></td>
                        <td style="padding: 10px; text-align: right; border-bottom: 1px solid #eee;">$<?php echo number_format($pieza['precio_unitario'], 2); ?></td>
                        <td style="padding: 10px; text-align: right; border-bottom: 1px solid #eee;">$<?php echo number_format($subtotal, 2); ?></td>
                        <td style="padding: 10px; text-align: center; border-bottom: 1px solid #eee;">
                            <span style="background: <?php echo $color_urgencia[$pieza['urgencia']]; ?>; color: white; padding: 2px 8px; border-radius: 10px; font-size: 0.8em;">
                                <?php echo $pieza['urgencia']; ?>
                            </span>
                        </td>
                        <td style="padding: 10px; text-align: center; border-bottom: 1px solid #eee;">
                            <button class="btn btn-small" onclick="editarPieza(<?php echo $pieza['id_pieza_solicitada']; ?>)">Editar</button>
                            <button class="btn btn-small btn-danger" onclick="eliminarPieza(<?php echo $pieza['id_pieza_solicitada']; ?>)">Eliminar</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr style="background: #ecf0f1;">
                        <td colspan="3" style="padding: 10px; text-align: right; font-weight: bold;">Total Piezas:</td>
                        <td style="padding: 10px; text-align: right; font-weight: bold;">$<?php echo number_format($total_piezas, 2); ?></td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
            <?php else: ?>
            <p style="text-align: center; color: #666; padding: 2rem;">No hay piezas agregadas a la cotización</p>
            <?php endif; ?>
        </div>

        <!-- COSTOS Y PAGOS -->
        <div class="card">
            <h3>Costos y Pagos</h3>
            <div class="grid-3">
                <div class="form-group">
                    <label>Costo Estimado</label>
                    <input type="number" name="costo_estimado" class="form-control" value="<?php echo $orden['costo_estimado']; ?>" step="0.01">
                </div>
                
                <div class="form-group">
                    <label>Costo Final</label>
                    <input type="number" name="costo_final" class="form-control" value="<?php echo $orden['costo_final']; ?>" step="0.01">
                </div>
                
                <div class="form-group">
                    <label>Anticipo</label>
                    <input type="number" name="anticipo" class="form-control" value="<?php echo $orden['anticipo']; ?>" step="0.01">
                </div>
            </div>
        </div>

        <!-- SEGUIMIENTO -->
        <div class="card">
            <h3>Seguimiento de la Orden</h3>
            <?php
            $stmt_seguimiento = $pdo->prepare("
                SELECT * FROM seguimiento_orden 
                WHERE id_orden = ? 
                ORDER BY fecha_hora DESC
            ");
            $stmt_seguimiento->execute([$orden['id_orden']]);
            $seguimientos = $stmt_seguimiento->fetchAll();
            
            foreach($seguimientos as $seg):
            ?>
            <div style="border-left: 4px solid #3498db; padding-left: 1rem; margin-bottom: 1rem;">
                <strong><?php echo $seg['fecha_hora']; ?> - <?php echo $seg['tecnico']; ?></strong>
                <br>
                <em><?php echo $seg['tipo_seguimiento']; ?></em>
                <br>
                <?php echo $seg['observaciones']; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- MODAL PARA AGREGAR PIEZAS -->
    <?php include 'modal_piezas.php'; ?>

    <script>
    function abrirModalPiezas(ordenId) {
        document.getElementById('modalPiezas').style.display = 'block';
        document.querySelector('input[name="id_orden"]').value = ordenId;
    }

    function cerrarModalPiezas() {
        document.getElementById('modalPiezas').style.display = 'none';
    }

    function editarPieza(idPieza) {
        alert('Editar pieza: ' + idPieza);
        // Implementar edición de pieza
    }

    function eliminarPieza(idPieza) {
        if (confirm('¿Estás seguro de eliminar esta pieza?')) {
            window.location.href = 'eliminar_pieza.php?id=' + idPieza;
        }
    }

    // Cerrar modal al hacer click fuera
    document.getElementById('modalPiezas').addEventListener('click', function(e) {
        if (e.target.id === 'modalPiezas') {
            cerrarModalPiezas();
        }
    });
    </script>
</body>
</html>