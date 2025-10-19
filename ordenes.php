<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Órdenes de Servicio - Taller Lykos</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
        .header { background: #2c3e50; color: white; padding: 1rem; text-align: center; }
        .nav { background: #34495e; padding: 1rem; display: flex; justify-content: center; gap: 10px; flex-wrap: wrap; }
        .nav a { padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 5px; }
        .nav a:hover { background: #2980b9; }
        .container { max-width: 1400px; margin: 2rem auto; padding: 0 1rem; }
        .card { background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 1rem; }
        .btn { padding: 8px 15px; background: #27ae60; color: white; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; font-size: 0.9em; }
        .btn:hover { background: #219a52; }
        .btn-info { background: #3498db; }
        .btn-info:hover { background: #2980b9; }
        .btn-warning { background: #f39c12; }
        .btn-warning:hover { background: #e67e22; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 5px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ecf0f1; }
        th { background: #34495e; color: white; }
        tr:hover { background: #f8f9fa; }
        .actions { display: flex; gap: 5px; flex-wrap: wrap; }
        .badge { padding: 4px 8px; border-radius: 12px; font-size: 0.8em; font-weight: bold; }
        .badge-recibido { background: #ffc107; color: black; }
        .badge-endiagnostico { background: #17a2b8; color: white; }
        .badge-diagnosticocompletado { background: #6610f2; color: white; }
        .badge-enreparacion { background: #fd7e14; color: white; }
        .badge-esperandorepuestos { background: #6f42c1; color: white; }
        .badge-reparado { background: #20c997; color: white; }
        .badge-listoparaentregar { background: #198754; color: white; }
        .badge-entregado { background: #6c757d; color: white; }
        .filters { display: flex; gap: 10px; margin-bottom: 1rem; flex-wrap: wrap; }
        .filter-btn { padding: 8px 15px; background: #95a5a6; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .filter-btn.active { background: #3498db; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Órdenes de Servicio - Taller Lykos</h1>
    </div>

    <?php include 'nav.php'; ?>

    <div class="container">
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h2>Gestión de Órdenes</h2>
                <a href="nueva_orden.php" class="btn">➕ Nueva Orden</a>
            </div>

            <!-- FILTROS -->
            <div class="filters">
                <?php
                $filtro = $_GET['filter'] ?? 'todas';
                $filtros = [
                    'todas' => 'Todas',
                    'activas' => 'Activas',
                    'recibido' => 'Recibido',
                    'en_diagnostico' => 'En Diagnóstico',
                    'en_reparacion' => 'En Reparación',
                    'esperando_repuestos' => 'Esperando Repuestos',
                    'listo_entregar' => 'Listo para Entregar',
                    'entregado' => 'Entregado'
                ];
                
                foreach($filtros as $key => $label) {
                    $active = $filtro === $key ? 'active' : '';
                    echo "<a href='ordenes.php?filter=$key' class='filter-btn $active'>$label</a>";
                }
                ?>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Folio</th>
                        <th>Cliente</th>
                        <th>Equipo</th>
                        <th>Fecha Ingreso</th>
                        <th>Estado</th>
                        <th>Técnico</th>
                        <th>Costo Est.</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // CONSTRUIR CONSULTA SEGÚN FILTRO
                    $where = "o.activa = 1";
                    switch($filtro) {
                        case 'activas':
                            $where .= " AND o.estado_orden NOT IN ('Entregado', 'Cancelado')";
                            break;
                        case 'recibido':
                            $where .= " AND o.estado_orden = 'Recibido'";
                            break;
                        case 'en_diagnostico':
                            $where .= " AND o.estado_orden = 'En Diagnóstico'";
                            break;
                        case 'en_reparacion':
                            $where .= " AND o.estado_orden = 'En Reparación'";
                            break;
                        case 'esperando_repuestos':
                            $where .= " AND o.estado_orden = 'Esperando Repuestos'";
                            break;
                        case 'listo_entregar':
                            $where .= " AND o.estado_orden = 'Listo para Entregar'";
                            break;
                        case 'entregado':
                            $where .= " AND o.estado_orden = 'Entregado'";
                            break;
                    }
                    
                    $stmt = $pdo->query("
                        SELECT o.*, c.nombre as cliente, e.marca, e.modelo, e.tipo_equipo 
                        FROM ordenes_servicio o 
                        JOIN equipos e ON o.id_equipo = e.id_equipo 
                        JOIN clientes c ON e.id_cliente = c.id_cliente 
                        WHERE $where 
                        ORDER BY o.fecha_creacion DESC
                    ");
                    
                    while($orden = $stmt->fetch()) {
                        $badge_class = 'badge-' . strtolower(str_replace(' ', '', $orden['estado_orden']));
                        echo "
                        <tr>
                            <td><strong>{$orden['folio']}</strong></td>
                            <td>{$orden['cliente']}</td>
                            <td>{$orden['marca']} {$orden['modelo']}<br><small>{$orden['tipo_equipo']}</small></td>
                            <td>" . date('d/m/Y', strtotime($orden['fecha_ingreso'])) . "</td>
                            <td><span class='badge $badge_class'>{$orden['estado_orden']}</span></td>
                            <td>{$orden['tecnico_asignado']}</td>
                            <td>$" . number_format($orden['costo_estimado'], 2) . "</td>
                         <td class='actions'>
    <a href='diagnostico.php?orden={$orden['id_orden']}' class='btn btn-info'>Diagnóstico</a>
    <a href='seguimiento.php?orden={$orden['id_orden']}' class='btn btn-warning'>Seguimiento</a>
    <a href='pdf_recepcion.php?orden={$orden['id_orden']}' class='btn' style='background: #9b59b6;'>PDF Recepción</a>
    <a href='pdf_entrega.php?orden={$orden['id_orden']}' class='btn' style='background: #e74c3c;'>PDF Entrega</a>
</td>
                        </tr>";
                    }
                    
                    if($stmt->rowCount() == 0) {
                        echo "<tr><td colspan='8' style='text-align: center;'>No hay órdenes con el filtro seleccionado</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>