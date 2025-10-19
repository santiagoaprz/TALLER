<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Taller Lykos</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .header {
            background: rgba(0,0,0,0.8);
            color: white;
            padding: 1rem;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }
        .nav {
            background: rgba(255,255,255,0.95);
            padding: 1rem;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .nav a {
            padding: 10px 20px;
            background: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s;
            font-weight: bold;
        }
        .nav a:hover {
            background: #45a049;
            transform: translateY(-2px);
        }
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        .dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            text-align: center;
        }
        .card h3 { color: #333; margin-bottom: 0.5rem; }
        .card .number { 
            font-size: 2rem; 
            font-weight: bold; 
            color: #4CAF50; 
        }
        .quick-actions {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .footer {
            background: rgba(0,0,0,0.8);
            color: white;
            text-align: center;
            padding: 1rem;
            margin-top: 2rem;
        }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: bold; }
        .form-control { 
            width: 100%; 
            padding: 0.5rem; 
            border: 1px solid #ddd; 
            border-radius: 4px; 
        }
        .btn {
            padding: 10px 20px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .btn:hover { background: #45a049; }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f8f9fa;
            font-weight: bold;
        }
        .badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8em;
            font-weight: bold;
        }
        .badge-recibido { background: #ffc107; color: black; }
        .badge-diagnostico { background: #17a2b8; color: white; }
        .badge-reparacion { background: #007bff; color: white; }
        .badge-entregado { background: #28a745; color: white; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reparación computadoras laptops celulares tablets CDMX Lykos</h1>
        <p>Av. Niños Héroes 43, Doctores, Ciudad de México</p>
    </div>

    <div class="nav">
     <a href="index.php">🏠 Inicio</a>
    <a href="recepcion_rapida.php">🚀 Recepción Rápida</a>
    <a href="entrega_rapida.php">📤 Entrega Rápida</a>
    <a href="clientes.php">👥 Clientes</a>
    <a href="equipos.php">💻 Equipos</a>
    <a href="ordenes.php">📋 Órdenes</a>
    <a href="diagnostico.php">🔧 Diagnóstico</a>
    <a href="inventario.php">📦 Inventario</a>
    <a href="proveedores.php">🏢 Proveedores</a>
    <a href="gastos.php">💰 Gastos</a>
    </div>

    <div class="container">
        <h2>Dashboard Principal</h2>
        
        <div class="dashboard">
            <?php
            // ESTADÍSTICAS
            
            
            
            $stats = [
                'Órdenes Activas' => "SELECT COUNT(*) FROM ordenes_servicio WHERE activa = 1 AND estado_orden NOT IN ('Entregado', 'Cancelado')",
                'Clientes Registrados' => "SELECT COUNT(*) FROM clientes WHERE activo = 1",
                'Equipos en Taller' => "SELECT COUNT(*) FROM equipos e JOIN ordenes_servicio o ON e.id_equipo = o.id_equipo WHERE o.activa = 1 AND o.estado_orden NOT IN ('Entregado', 'Cancelado')",
                'Ingresos del Mes' => "SELECT COALESCE(SUM(costo_final),0) FROM ordenes_servicio WHERE MONTH(fecha_entrega_real) = MONTH(CURRENT_DATE()) AND estado_orden = 'Entregado'"
            ];
            
            foreach($stats as $titulo => $sql) {
                $stmt = $pdo->query($sql);
                $valor = $stmt->fetchColumn();
                echo "
                <div class='card'>
                    <h3>$titulo</h3>
                    <div class='number'>$valor</div>
                </div>";
            }
            ?>
        </div>

<!-- En el div .dashboard del index.php, agrega: -->
<div class="dashboard">
    <?php
    // ESTADÍSTICAS EXISTENTES...
    
    // AGREGAR ESTADÍSTICAS DE INVENTARIO
    $stats_inventario = [
        'Piezas en Inventario' => "SELECT COUNT(*) FROM inventario WHERE activo = 1",
        'Stock Bajo' => "SELECT COUNT(*) FROM inventario WHERE cantidad_stock <= stock_minimo AND activo = 1",
        'Piezas Agotadas' => "SELECT COUNT(*) FROM inventario WHERE cantidad_stock = 0 AND activo = 1"
    ];
    
    foreach($stats_inventario as $titulo => $sql) {
        $stmt = $pdo->query($sql);
        $valor = $stmt->fetchColumn();
        
        $color = '#4CAF50';
        if($titulo == 'Stock Bajo' && $valor > 0) $color = '#ff9800';
        if($titulo == 'Piezas Agotadas' && $valor > 0) $color = '#f44336';
        
        echo "
        <div class='card'>
            <h3>$titulo</h3>
            <div class='number' style='color: $color;'>$valor</div>
        </div>";
    }
    ?>
</div>

        
<!-- En el div .quick-actions del index.php -->

<div class="quick-actions">
    <h3>Acciones Rápidas</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-top: 1rem;">
        <a href="recepcion_rapida.php" class="btn" style="background: #e74c3c; font-size: 1.1rem;">🚀 Recepción Rápida</a>
        <a href="nueva_orden.php" class="btn">➕ Nueva Orden</a>
        <a href="nuevo_cliente.php" class="btn">👤 Nuevo Cliente</a>
        <a href="inventario.php" class="btn">📦 Inventario</a>
    </div>
</div>

        <div class="quick-actions" style="margin-top: 1rem;">
            <h3>Órdenes Recientes</h3>
            <?php
            $stmt = $pdo->query("
                SELECT o.*, c.nombre as cliente, e.marca, e.modelo 
                FROM ordenes_servicio o 
                JOIN equipos e ON o.id_equipo = e.id_equipo 
                JOIN clientes c ON e.id_cliente = c.id_cliente 
                WHERE o.activa = 1 
                ORDER BY o.fecha_creacion DESC 
                LIMIT 5
            ");
            $ordenes_recientes = $stmt->fetchAll();
            
            if($ordenes_recientes) {
                echo "<table>";
                echo "<tr><th>Folio</th><th>Cliente</th><th>Equipo</th><th>Estado</th><th>Fecha</th></tr>";
                foreach($ordenes_recientes as $orden) {
                    $badge_class = 'badge-' . strtolower(str_replace(' ', '', $orden['estado_orden']));
                    echo "<tr>
                        <td>{$orden['folio']}</td>
                        <td>{$orden['cliente']}</td>
                        <td>{$orden['marca']} {$orden['modelo']}</td>
                        <td><span class='badge $badge_class'>{$orden['estado_orden']}</span></td>
                        <td>{$orden['fecha_ingreso']}</td>
                    </tr>";
                }
                echo "</table>";
            } else {
                echo "<p>No hay órdenes recientes.</p>";
            }
            ?>
        </div>
    </div>

    <div class="footer">
        <p><?php echo TALLER_NOMBRE; ?> | <?php echo TALLER_DIRECCION; ?> | <?php echo TALLER_TELEFONOS; ?></p>
        <p>© <?php echo date('Y'); ?> Todos los derechos reservados</p>
    </div>
</body>
</html>