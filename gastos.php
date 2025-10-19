<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Gastos - Taller Lykos</title>
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
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 5px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ecf0f1; }
        th { background: #34495e; color: white; }
        tr:hover { background: #f8f9fa; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .total { font-size: 1.2em; font-weight: bold; color: #e74c3c; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Control de Gastos - Taller Lykos</h1>
    </div>

    <?php include 'nav.php'; ?>

    <div class="container">
        <!-- FORMULARIO GASTO -->
        <div class="card">
            <h2>Registrar Nuevo Gasto</h2>
            <form method="POST" action="guardar_gasto.php">
                <div class="grid-2">
                    <div class="form-group">
                        <label>Descripción *</label>
                        <input type="text" name="descripcion" class="form-control" required placeholder="Ej: Compra de herramientas, pago de renta...">
                    </div>
                    
                    <div class="form-group">
                        <label>Monto *</label>
                        <input type="number" name="monto" class="form-control" step="0.01" min="0" required placeholder="0.00">
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Fecha del Gasto *</label>
                        <input type="date" name="fecha_gasto" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Categoría *</label>
                        <select name="categoria" class="form-control" required>
                            <option value="">Seleccionar categoría</option>
                            <option value="Herramientas">Herramientas</option>
                            <option value="Renta">Renta</option>
                            <option value="Software">Software</option>
                            <option value="Servicios">Servicios</option>
                            <option value="Materiales">Materiales</option>
                            <option value="Nómina">Nómina</option>
                            <option value="Otros">Otros</option>
                        </select>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Comprobante (Número/Referencia)</label>
                        <input type="text" name="comprobante" class="form-control" placeholder="Número de factura, referencia...">
                    </div>
                    
                    <div class="form-group">
                        <label>Observaciones</label>
                        <textarea name="observaciones" class="form-control" rows="2" placeholder="Observaciones adicionales..."></textarea>
                    </div>
                </div>

                <button type="submit" class="btn">Registrar Gasto</button>
            </form>
        </div>

        <!-- LISTA DE GASTOS -->
        <div class="card">
            <h2>Historial de Gastos</h2>
            
            <?php
            // CALCULAR TOTAL
            $stmt = $pdo->query("SELECT SUM(monto) as total FROM gastos WHERE MONTH(fecha_gasto) = MONTH(CURRENT_DATE())");
            $total_mes = $stmt->fetchColumn();
            ?>
            
            <div style="margin-bottom: 1rem;">
                <strong>Total del Mes: </strong><span class="total">$<?php echo number_format($total_mes, 2); ?></span>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Descripción</th>
                        <th>Categoría</th>
                        <th>Monto</th>
                        <th>Comprobante</th>
                        <th>Observaciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->query("SELECT * FROM gastos ORDER BY fecha_gasto DESC, id_gasto DESC");
                    while($gasto = $stmt->fetch()) {
                        echo "
                        <tr>
                            <td>" . date('d/m/Y', strtotime($gasto['fecha_gasto'])) . "</td>
                            <td>{$gasto['descripcion']}</td>
                            <td>{$gasto['categoria']}</td>
                            <td><strong>$" . number_format($gasto['monto'], 2) . "</strong></td>
                            <td>{$gasto['comprobante']}</td>
                            <td>{$gasto['observaciones']}</td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>