<?php include 'config.php'; 

if(!isset($_GET['orden'])) {
    header("Location: ordenes.php");
    exit;
}

$orden_id = $_GET['orden'];

// OBTENER DATOS DE LA ORDEN
$stmt = $pdo->prepare("
    SELECT o.*, e.*, c.nombre as cliente 
    FROM ordenes_servicio o 
    JOIN equipos e ON o.id_equipo = e.id_equipo 
    JOIN clientes c ON e.id_cliente = c.id_cliente 
    WHERE o.id_orden = ?
");
$stmt->execute([$orden_id]);
$orden = $stmt->fetch();

if(!$orden) {
    header("Location: ordenes.php");
    exit;
}

// OBTENER SEGUIMIENTO
$stmt = $pdo->prepare("SELECT * FROM seguimiento_orden WHERE id_orden = ? ORDER BY fecha_hora DESC");
$stmt->execute([$orden_id]);
$seguimientos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seguimiento - Taller Lykos</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
        .header { background: #2c3e50; color: white; padding: 1rem; text-align: center; }
        .nav { background: #34495e; padding: 1rem; display: flex; justify-content: center; gap: 10px; flex-wrap: wrap; }
        .nav a { padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 5px; }
        .nav a:hover { background: #2980b9; }
        .container { max-width: 1000px; margin: 2rem auto; padding: 0 1rem; }
        .card { background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 1rem; }
        .btn { padding: 10px 20px; background: #27ae60; color: white; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background: #219a52; }
        .btn-info { background: #3498db; }
        .timeline { position: relative; margin: 20px 0; }
        .timeline::before { content: ''; position: absolute; left: 20px; top: 0; bottom: 0; width: 2px; background: #3498db; }
        .timeline-item { position: relative; margin-bottom: 20px; padding-left: 50px; }
        .timeline-item::before { content: ''; position: absolute; left: 15px; top: 5px; width: 12px; height: 12px; border-radius: 50%; background: #3498db; }
        .timeline-date { font-weight: bold; color: #7f8c8d; }
        .timeline-content { background: #ecf0f1; padding: 10px; border-radius: 5px; }
        .badge { padding: 4px 8px; border-radius: 12px; font-size: 0.8em; font-weight: bold; }
        .badge-cambio { background: #3498db; color: white; }
        .badge-observacion { background: #f39c12; color: white; }
        .badge-solicitud { background: #9b59b6; color: white; }
        .badge-diagnostico { background: #2ecc71; color: white; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Seguimiento de Orden - Taller Lykos</h1>
    </div>

    <?php include 'nav.php'; ?>

    <div class="container">
        <!-- INFORMACIÓN DE LA ORDEN -->
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2>Orden: <?php echo $orden['folio']; ?></h2>
                <div>
                    <a href="diagnostico.php?orden=<?php echo $orden_id; ?>" class="btn btn-info">Volver al Diagnóstico</a>
                    <a href="ordenes.php" class="btn">Volver a Órdenes</a>
                </div>
            </div>
            
            <p><strong>Cliente:</strong> <?php echo $orden['cliente']; ?></p>
            <p><strong>Equipo:</strong> <?php echo $orden['marca']; ?> <?php echo $orden['modelo']; ?> - <?php echo $orden['tipo_equipo']; ?></p>
            <p><strong>Estado Actual:</strong> <?php echo $orden['estado_orden']; ?></p>
        </div>

        <!-- AGREGAR NUEVO SEGUIMIENTO -->
        <div class="card">
            <h2>Agregar Nuevo Seguimiento</h2>
            <form method="POST" action="guardar_seguimiento.php">
                <input type="hidden" name="id_orden" value="<?php echo $orden_id; ?>">
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label><strong>Tipo de Seguimiento</strong></label>
                        <select name="tipo_seguimiento" class="form-control" required>
                            <option value="Observación">Observación</option>
                            <option value="Solicitud Piezas">Solicitud de Piezas</option>
                            <option value="Actualización Diagnóstico">Actualización de Diagnóstico</option>
                            <option value="Cambio Estado">Cambio de Estado</option>
                        </select>
                    </div>
                    
                    <div>
                        <label><strong>Nuevo Estado (opcional)</strong></label>
                        <select name="estado_nuevo" class="form-control">
                            <option value="">Mantener estado actual</option>
                            <option value="Recibido">Recibido</option>
                            <option value="En Diagnóstico">En Diagnóstico</option>
                            <option value="Diagnóstico Completado">Diagnóstico Completado</option>
                            <option value="Esperando Aprobación Cliente">Esperando Aprobación Cliente</option>
                            <option value="En Reparación">En Reparación</option>
                            <option value="Esperando Repuestos">Esperando Repuestos</option>
                            <option value="Reparado">Reparado</option>
                            <option value="Listo para Entregar">Listo para Entregar</option>
                            <option value="Entregado">Entregado</option>
                        </select>
                    </div>
                </div>
                
                <div style="margin-bottom: 1rem;">
                    <label><strong>Observaciones</strong></label>
                    <textarea name="observaciones" class="form-control" rows="4" required placeholder="Describa el avance, problema encontrado, piezas necesarias..."></textarea>
                </div>
                
                <div style="margin-bottom: 1rem;">
                    <label><strong>Técnico</strong></label>
                    <input type="text" name="tecnico" class="form-control" value="<?php echo $orden['tecnico_asignado']; ?>">
                </div>
                
                <button type="submit" class="btn">Guardar Seguimiento</button>
            </form>
        </div>

        <!-- HISTORIAL DE SEGUIMIENTO -->
        <div class="card">
            <h2>Historial de Seguimiento</h2>
            
            <?php if(count($seguimientos) > 0): ?>
            <div class="timeline">
                <?php foreach($seguimientos as $seg): ?>
                <div class="timeline-item">
                    <div class="timeline-date">
                        <?php echo date('d/m/Y H:i', strtotime($seg['fecha_hora'])); ?>
                        <span class="badge badge-<?php echo strtolower(str_replace(' ', '', $seg['tipo_seguimiento'])); ?>">
                            <?php echo $seg['tipo_seguimiento']; ?>
                        </span>
                    </div>
                    <div class="timeline-content">
                        <?php if($seg['estado_anterior'] && $seg['estado_nuevo']): ?>
                            <p><strong>Cambio de estado:</strong> <?php echo $seg['estado_anterior']; ?> → <?php echo $seg['estado_nuevo']; ?></p>
                        <?php endif; ?>
                        <p><?php echo nl2br($seg['observaciones']); ?></p>
                        <small><strong>Técnico:</strong> <?php echo $seg['tecnico']; ?></small>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
                <p>No hay registros de seguimiento para esta orden.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>