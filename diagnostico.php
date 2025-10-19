<?php include 'config.php'; 

if(!isset($_GET['orden'])) {
    header("Location: ordenes.php");
    exit;
}

$orden_id = $_GET['orden'];
$stmt = $pdo->prepare("
    SELECT o.*, e.*, c.nombre as cliente, c.telefono, c.email 
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
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnóstico - Taller Lykos</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
        .header { background: #2c3e50; color: white; padding: 1rem; text-align: center; }
        .nav { background: #34495e; padding: 1rem; display: flex; justify-content: center; gap: 10px; flex-wrap: wrap; }
        .nav a { padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 5px; }
        .nav a:hover { background: #2980b9; }
        .container { max-width: 1200px; margin: 2rem auto; padding: 0 1rem; }
        .card { background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 1rem; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: bold; color: #2c3e50; }
        .form-control { width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem; }
        .btn { padding: 10px 20px; background: #27ae60; color: white; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background: #219a52; }
        .btn-info { background: #3498db; }
        .btn-warning { background: #f39c12; }
        .info-box { background: #e8f4fd; padding: 1rem; border-radius: 5px; margin-bottom: 1rem; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .status-badge { padding: 5px 10px; border-radius: 5px; font-weight: bold; }
        .status-recibido { background: #ffc107; color: black; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Diagnóstico de Orden - Taller Lykos</h1>
    </div>

    <?php include 'nav.php'; ?>

    <div class="container">
        <!-- INFORMACIÓN DE LA ORDEN -->
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2>Orden: <?php echo $orden['folio']; ?></h2>
                <span class="status-badge status-<?php echo strtolower($orden['estado_orden']); ?>">
                    <?php echo $orden['estado_orden']; ?>
                </span>
            </div>
            
            <div class="grid-2">
                <div class="info-box">
                    <h4>Información del Cliente</h4>
                    <p><strong>Nombre:</strong> <?php echo $orden['cliente']; ?></p>
                    <p><strong>Teléfono:</strong> <?php echo $orden['telefono']; ?></p>
                    <p><strong>Email:</strong> <?php echo $orden['email']; ?></p>
                </div>
                
                <div class="info-box">
                    <h4>Información del Equipo</h4>
                    <p><strong>Equipo:</strong> <?php echo $orden['tipo_equipo']; ?> - <?php echo $orden['marca']; ?> <?php echo $orden['modelo']; ?></p>
                    <p><strong>Serie:</strong> <?php echo $orden['numero_serie']; ?></p>
                    <p><strong>Problema Reportado:</strong> <?php echo $orden['problemas_reportados']; ?></p>
                </div>
            </div>
        </div>

        <!-- FORMULARIO DE DIAGNÓSTICO -->
        <div class="card">
            <h2>Diagnóstico Técnico</h2>
            <form method="POST" action="guardar_diagnostico.php">
                <input type="hidden" name="id_orden" value="<?php echo $orden_id; ?>">
                
                <div class="form-group">
                    <label>Diagnóstico Detallado *</label>
                    <textarea name="diagnostico" class="form-control" rows="6" required placeholder="Describa detalladamente el diagnóstico técnico..."><?php echo $orden['diagnostico']; ?></textarea>
                </div>

                <div class="form-group">
                    <label>Piezas Necesarias</label>
                    <textarea name="piezas_necesarias" class="form-control" rows="3" placeholder="Lista de piezas necesarias para la reparación..."><?php echo $orden['piezas_necesarias']; ?></textarea>
                </div>

                <div class="form-group">
                    <label>Trabajo a Realizar</label>
                    <textarea name="trabajo_realizado" class="form-control" rows="3" placeholder="Descripción del trabajo de reparación..."><?php echo $orden['trabajo_realizado']; ?></textarea>
                </div>

                <div class="form-group">
                    <label>Dificultades Encontradas</label>
                    <textarea name="dificultades_encontradas" class="form-control" rows="3" placeholder="Problemas o dificultades durante el diagnóstico..."><?php echo $orden['dificultades_encontradas']; ?></textarea>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Costo Estimado</label>
                        <input type="number" name="costo_estimado" class="form-control" step="0.01" min="0" value="<?php echo $orden['costo_estimado']; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Nuevo Estado</label>
                        <select name="estado_orden" class="form-control">
                            <option value="Recibido" <?php echo $orden['estado_orden'] == 'Recibido' ? 'selected' : ''; ?>>Recibido</option>
                            <option value="En Diagnóstico" <?php echo $orden['estado_orden'] == 'En Diagnóstico' ? 'selected' : ''; ?>>En Diagnóstico</option>
                            <option value="Diagnóstico Completado" <?php echo $orden['estado_orden'] == 'Diagnóstico Completado' ? 'selected' : ''; ?>>Diagnóstico Completado</option>
                            <option value="Esperando Aprobación Cliente" <?php echo $orden['estado_orden'] == 'Esperando Aprobación Cliente' ? 'selected' : ''; ?>>Esperando Aprobación Cliente</option>
                            <option value="En Reparación" <?php echo $orden['estado_orden'] == 'En Reparación' ? 'selected' : ''; ?>>En Reparación</option>
                            <option value="Esperando Repuestos" <?php echo $orden['estado_orden'] == 'Esperando Repuestos' ? 'selected' : ''; ?>>Esperando Repuestos</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Observaciones Internas</label>
                    <textarea name="observaciones_internas" class="form-control" rows="3" placeholder="Observaciones internas del técnico..."><?php echo $orden['observaciones_internas']; ?></textarea>
                </div>

                <button type="submit" class="btn">Guardar Diagnóstico</button>
                <a href="ordenes.php" class="btn btn-info">Volver a Órdenes</a>
                <a href="seguimiento.php?orden=<?php echo $orden_id; ?>" class="btn btn-warning">Ver Seguimiento</a>
            </form>
        </div>
    </div>
</body>
</html>