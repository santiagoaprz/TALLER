<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Auditoría de Técnicos - Taller Lykos</title>
    <style>
        .card { background: white; padding: 1.5rem; margin: 1rem 0; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; }
        .estadistica { text-align: center; padding: 1rem; background: #f8f9fa; border-radius: 5px; }
        .numero { font-size: 2rem; font-weight: bold; color: #2c3e50; }
        .label { color: #7f8c8d; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Auditoría y Control de Técnicos</h1>
    </div>

    <?php include 'nav.php'; ?>

    <div class="container">
        <!-- ESTADÍSTICAS GENERALES -->
        <div class="card">
            <h2>Estadísticas del Taller</h2>
            <div class="grid-3">
                <?php
                // Órdenes por técnico
                $stmt = $pdo->query("
                    SELECT tecnico_asignado, COUNT(*) as total,
                           SUM(CASE WHEN estado_orden = 'Entregado' THEN 1 ELSE 0 END) as entregadas,
                           AVG(costo_final) as promedio_venta
                    FROM ordenes_servicio 
                    WHERE activa = 1 
                    GROUP BY tecnico_asignado
                ");
                while($tecnico = $stmt->fetch()):
                ?>
                <div class="estadistica">
                    <div class="numero"><?php echo $tecnico['total']; ?></div>
                    <div class="label">Órdenes - <?php echo $tecnico['tecnico_asignado']; ?></div>
                    <div style="font-size: 0.8rem; color: #27ae60;">
                        <?php echo $tecnico['entregadas']; ?> entregadas
                    </div>
                    <div style="font-size: 0.8rem; color: #3498db;">
                        Promedio: $<?php echo number_format($tecnico['promedio_venta'], 2); ?>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- CAMBIOS SOSPECHOSOS -->
        <div class="card">
            <h2>Cambios Recientes en Costos</h2>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #34495e; color: white;">
                        <th style="padding: 10px;">Folio</th>
                        <th style="padding: 10px;">Técnico</th>
                        <th style="padding: 10px;">Costo Original</th>
                        <th style="padding: 10px;">Costo Final</th>
                        <th style="padding: 10px;">Diferencia</th>
                        <th style="padding: 10px;">Fecha Cambio</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->query("
                        SELECT os.folio, os.tecnico_asignado, os.costo_estimado, os.costo_final,
                               (os.costo_final - os.costo_estimado) as diferencia,
                               so.fecha_hora
                        FROM ordenes_servicio os
                        JOIN seguimiento_orden so ON os.id_orden = so.id_orden
                        WHERE so.observaciones LIKE '%costo%' 
                        AND os.costo_final > os.costo_estimado * 1.5  -- Cambios mayores al 50%
                        ORDER BY so.fecha_hora DESC
                        LIMIT 20
                    ");
                    while($cambio = $stmt->fetch()):
                        $color_diferencia = $cambio['diferencia'] > 1000 ? '#e74c3c' : '#f39c12';
                    ?>
                    <tr>
                        <td style="padding: 10px; border-bottom: 1px solid #eee;"><?php echo $cambio['folio']; ?></td>
                        <td style="padding: 10px; border-bottom: 1px solid #eee;"><?php echo $cambio['tecnico_asignado']; ?></td>
                        <td style="padding: 10px; border-bottom: 1px solid #eee;">$<?php echo number_format($cambio['costo_estimado'], 2); ?></td>
                        <td style="padding: 10px; border-bottom: 1px solid #eee;">$<?php echo number_format($cambio['costo_final'], 2); ?></td>
                        <td style="padding: 10px; border-bottom: 1px solid #eee; color: <?php echo $color_diferencia; ?>;">
                            +$<?php echo number_format($cambio['diferencia'], 2); ?>
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid #eee;"><?php echo $cambio['fecha_hora']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- REGISTRO DE ACTIVIDAD -->
        <div class="card">
            <h2>Registro Completo de Actividad</h2>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #34495e; color: white;">
                        <th style="padding: 10px;">Fecha/Hora</th>
                        <th style="padding: 10px;">Técnico</th>
                        <th style="padding: 10px;">Acción</th>
                        <th style="padding: 10px;">Detalles</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->query("
                        SELECT so.fecha_hora, so.tecnico, so.tipo_seguimiento, so.observaciones, os.folio
                        FROM seguimiento_orden so
                        JOIN ordenes_servicio os ON so.id_orden = os.id_orden
                        ORDER BY so.fecha_hora DESC
                        LIMIT 50
                    ");
                    while($actividad = $stmt->fetch()):
                    ?>
                    <tr>
                        <td style="padding: 10px; border-bottom: 1px solid #eee;"><?php echo $actividad['fecha_hora']; ?></td>
                        <td style="padding: 10px; border-bottom: 1px solid #eee;"><?php echo $actividad['tecnico']; ?></td>
                        <td style="padding: 10px; border-bottom: 1px solid #eee;"><?php echo $actividad['tipo_seguimiento']; ?></td>
                        <td style="padding: 10px; border-bottom: 1px solid #eee;">
                            <strong><?php echo $actividad['folio']; ?></strong> - 
                            <?php echo $actividad['observaciones']; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>