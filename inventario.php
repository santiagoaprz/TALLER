<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario - Taller Lykos</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
        .header { background: #2c3e50; color: white; padding: 1rem; text-align: center; }
        .nav { background: #34495e; padding: 1rem; display: flex; justify-content: center; gap: 10px; flex-wrap: wrap; }
        .nav a { padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 5px; }
        .nav a:hover { background: #2980b9; }
        .container { max-width: 1400px; margin: 2rem auto; padding: 0 1rem; }
        .card { background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 1rem; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: bold; color: #2c3e50; }
        .form-control { width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem; }
        .btn { padding: 10px 20px; background: #27ae60; color: white; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background: #219a52; }
        .btn-warning { background: #f39c12; }
        .btn-danger { background: #e74c3c; }
        .btn-info { background: #3498db; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; }
        .grid-4 { display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 1rem; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 5px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ecf0f1; }
        th { background: #34495e; color: white; }
        tr:hover { background: #f8f9fa; }
        .actions { display: flex; gap: 5px; flex-wrap: wrap; }
        .stock-low { background: #ffeaa7 !important; }
        .stock-out { background: #fab1a0 !important; }
        .alert-badge { background: #e74c3c; color: white; padding: 3px 8px; border-radius: 10px; font-size: 0.8em; }
        .search-box { margin-bottom: 1rem; }
        .search-box input { padding: 0.75rem; width: 300px; max-width: 100%; border: 1px solid #ddd; border-radius: 5px; }
        .filters { display: flex; gap: 10px; margin-bottom: 1rem; flex-wrap: wrap; }
        .filter-btn { padding: 8px 15px; background: #95a5a6; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .filter-btn.active { background: #3498db; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Gestión de Inventario - Taller Lykos</h1>
    </div>

    <?php include 'nav.php'; ?>

    <div class="container">
        <!-- FORMULARIO PARA AGREGAR/EDITAR PIEZA -->
        <div class="card">
            <h2><?php echo isset($_GET['editar']) ? 'Editar Pieza' : 'Agregar Nueva Pieza'; ?></h2>
            <form method="POST" action="guardar_pieza.php">
                <?php
                $pieza = null;
                if(isset($_GET['editar'])) {
                    $stmt = $pdo->prepare("SELECT * FROM inventario WHERE id_pieza = ?");
                    $stmt->execute([$_GET['editar']]);
                    $pieza = $stmt->fetch();
                }
                ?>
                <input type="hidden" name="id_pieza" value="<?php echo $pieza ? $pieza['id_pieza'] : ''; ?>">
                
                <div class="grid-3">
                    <div class="form-group">
                        <label>Nombre de la Pieza *</label>
                        <input type="text" name="nombre_pieza" class="form-control" value="<?php echo $pieza ? $pieza['nombre_pieza'] : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Categoría *</label>
                        <select name="categoria" class="form-control" required>
                            <option value="">Seleccionar categoría</option>
                            <option value="Pantalla" <?php echo $pieza && $pieza['categoria'] == 'Pantalla' ? 'selected' : ''; ?>>Pantalla</option>
                            <option value="Batería" <?php echo $pieza && $pieza['categoria'] == 'Batería' ? 'selected' : ''; ?>>Batería</option>
                            <option value="Cargador" <?php echo $pieza && $pieza['categoria'] == 'Cargador' ? 'selected' : ''; ?>>Cargador</option>
                            <option value="Flex" <?php echo $pieza && $pieza['categoria'] == 'Flex' ? 'selected' : ''; ?>>Flex</option>
                            <option value="Cámara" <?php echo $pieza && $pieza['categoria'] == 'Cámara' ? 'selected' : ''; ?>>Cámara</option>
                            <option value="Placa" <?php echo $pieza && $pieza['categoria'] == 'Placa' ? 'selected' : ''; ?>>Placa</option>
                            <option value="Memoria" <?php echo $pieza && $pieza['categoria'] == 'Memoria' ? 'selected' : ''; ?>>Memoria</option>
                            <option value="Procesador" <?php echo $pieza && $pieza['categoria'] == 'Procesador' ? 'selected' : ''; ?>>Procesador</option>
                            <option value="Otro" <?php echo $pieza && $pieza['categoria'] == 'Otro' ? 'selected' : ''; ?>>Otro</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Número de Parte</label>
                        <input type="text" name="numero_parte" class="form-control" value="<?php echo $pieza ? $pieza['numero_parte'] : ''; ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="2"><?php echo $pieza ? $pieza['descripcion'] : ''; ?></textarea>
                </div>

                <div class="grid-4">
                    <div class="form-group">
                        <label>Cantidad en Stock *</label>
                        <input type="number" name="cantidad_stock" class="form-control" min="0" value="<?php echo $pieza ? $pieza['cantidad_stock'] : '0'; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Stock Mínimo *</label>
                        <input type="number" name="stock_minimo" class="form-control" min="0" value="<?php echo $pieza ? $pieza['stock_minimo'] : '5'; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Precio de Compra</label>
                        <input type="number" name="precio_compra" class="form-control" step="0.01" min="0" value="<?php echo $pieza ? $pieza['precio_compra'] : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Precio de Venta</label>
                        <input type="number" name="precio_venta" class="form-control" step="0.01" min="0" value="<?php echo $pieza ? $pieza['precio_venta'] : ''; ?>">
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Proveedor</label>
                        <select name="proveedor" class="form-control">
                            <option value="">Seleccionar proveedor</option>
                            <?php
                            $stmt = $pdo->query("SELECT * FROM proveedores ORDER BY nombre");
                            while($proveedor = $stmt->fetch()) {
                                $selected = $pieza && $pieza['proveedor'] == $proveedor['nombre'] ? 'selected' : '';
                                echo "<option value='{$proveedor['nombre']}' $selected>{$proveedor['nombre']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Ubicación en Almacén</label>
                        <input type="text" name="ubicacion" class="form-control" value="<?php echo $pieza ? $pieza['ubicacion'] : ''; ?>" placeholder="Ej: Estante A, Caja 3">
                    </div>
                </div>

                <div class="form-group">
                    <label>Compatible Con</label>
                    <textarea name="compatible_con" class="form-control" rows="2" placeholder="Marcas y modelos compatibles..."><?php echo $pieza ? $pieza['compatible_con'] : ''; ?></textarea>
                </div>

                <button type="submit" class="btn"><?php echo $pieza ? 'Actualizar' : 'Guardar'; ?> Pieza</button>
                <?php if($pieza): ?>
                    <a href="inventario.php" class="btn" style="background: #95a5a6;">Cancelar</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- ESTADÍSTICAS RÁPIDAS -->
        <div class="card">
            <h2>Resumen del Inventario</h2>
            <div class="grid-4">
                <?php
                $stats = [
                    'Total Piezas' => "SELECT COUNT(*) FROM inventario WHERE activo = 1",
                    'Stock Bajo' => "SELECT COUNT(*) FROM inventario WHERE cantidad_stock <= stock_minimo AND activo = 1",
                    'Sin Stock' => "SELECT COUNT(*) FROM inventario WHERE cantidad_stock = 0 AND activo = 1",
                    'Valor Total' => "SELECT SUM(cantidad_stock * precio_compra) FROM inventario WHERE activo = 1"
                ];
                
                foreach($stats as $titulo => $sql) {
                    $stmt = $pdo->query($sql);
                    $valor = $stmt->fetchColumn();
                    
                    if($titulo == 'Valor Total') {
                        $valor = $valor ? '$' . number_format($valor, 2) : '$0.00';
                    }
                    
                    $color = '';
                    if($titulo == 'Stock Bajo' && $valor > 0) $color = 'style="color: #e74c3c;"';
                    if($titulo == 'Sin Stock' && $valor > 0) $color = 'style="color: #c0392b;"';
                    
                    echo "
                    <div style='text-align: center;'>
                        <h3 $color>$valor</h3>
                        <p style='color: #666;'>$titulo</p>
                    </div>";
                }
                ?>
            </div>
        </div>

        <!-- LISTA DE PIEZAS -->
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h2>Lista de Piezas en Inventario</h2>
                <div>
                    <input type="text" id="searchInventario" placeholder="Buscar piezas..." class="search-box" onkeyup="buscarPiezas()">
                </div>
            </div>

            <!-- FILTROS -->
            <div class="filters">
                <?php
                $filtro = $_GET['filter'] ?? 'todas';
                $filtros = [
                    'todas' => 'Todas',
                    'stock_bajo' => 'Stock Bajo',
                    'sin_stock' => 'Sin Stock',
                    'pantallas' => 'Pantallas',
                    'baterias' => 'Baterías',
                    'cargadores' => 'Cargadores'
                ];
                
                foreach($filtros as $key => $label) {
                    $active = $filtro === $key ? 'active' : '';
                    echo "<a href='inventario.php?filter=$key' class='filter-btn $active'>$label</a>";
                }
                ?>
            </div>

            <table id="tablaInventario">
                <thead>
                    <tr>
                        <th>Pieza</th>
                        <th>Categoría</th>
                        <th>Stock</th>
                        <th>Precios</th>
                        <th>Proveedor</th>
                        <th>Ubicación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // CONSTRUIR CONSULTA SEGÚN FILTRO
                    $where = "activo = 1";
                    switch($filtro) {
                        case 'stock_bajo':
                            $where .= " AND cantidad_stock <= stock_minimo";
                            break;
                        case 'sin_stock':
                            $where .= " AND cantidad_stock = 0";
                            break;
                        case 'pantallas':
                            $where .= " AND categoria = 'Pantalla'";
                            break;
                        case 'baterias':
                            $where .= " AND categoria = 'Batería'";
                            break;
                        case 'cargadores':
                            $where .= " AND categoria = 'Cargador'";
                            break;
                    }
                    
                    $stmt = $pdo->query("SELECT * FROM inventario WHERE $where ORDER BY cantidad_stock ASC, nombre_pieza");
                    
                    while($pieza = $stmt->fetch()) {
                        $stock_class = '';
                        $stock_alert = '';
                        
                        if($pieza['cantidad_stock'] == 0) {
                            $stock_class = 'stock-out';
                            $stock_alert = '<span class="alert-badge">AGOTADO</span>';
                        } elseif($pieza['cantidad_stock'] <= $pieza['stock_minimo']) {
                            $stock_class = 'stock-low';
                            $stock_alert = '<span class="alert-badge" style="background: #f39c12;">BAJO</span>';
                        }
                        
                        $precio_compra = $pieza['precio_compra'] ? '$' . number_format($pieza['precio_compra'], 2) : '-';
                        $precio_venta = $pieza['precio_venta'] ? '$' . number_format($pieza['precio_venta'], 2) : '-';
                        
                        echo "
                        <tr class='$stock_class'>
                            <td>
                                <strong>{$pieza['nombre_pieza']}</strong>
                                <br><small>{$pieza['numero_parte']}</small>
                            </td>
                            <td>{$pieza['categoria']}</td>
                            <td>
                                <strong>{$pieza['cantidad_stock']}</strong> / {$pieza['stock_minimo']}
                                $stock_alert
                            </td>
                            <td>
                                <small>Compra: $precio_compra</small><br>
                                <small>Venta: $precio_venta</small>
                            </td>
                            <td>{$pieza['proveedor']}</td>
                            <td>{$pieza['ubicacion']}</td>
                            <td class='actions'>
                                <a href='inventario.php?editar={$pieza['id_pieza']}' class='btn'>Editar</a>
                                <a href='ajustar_stock.php?pieza={$pieza['id_pieza']}' class='btn btn-info'>Ajustar</a>
                                <a href='eliminar_pieza.php?id={$pieza['id_pieza']}' class='btn btn-danger' onclick='return confirm(\"¿Estás seguro?\")'>Eliminar</a>
                            </td>
                        </tr>";
                    }
                    
                    if($stmt->rowCount() == 0) {
                        echo "<tr><td colspan='7' style='text-align: center;'>No hay piezas con el filtro seleccionado</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
    function buscarPiezas() {
        var input = document.getElementById('searchInventario');
        var filter = input.value.toLowerCase();
        var table = document.getElementById('tablaInventario');
        var tr = table.getElementsByTagName('tr');
        
        for (var i = 1; i < tr.length; i++) {
            var td = tr[i].getElementsByTagName('td');
            var found = false;
            for (var j = 0; j < td.length; j++) {
                if (td[j].textContent.toLowerCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }
            tr[i].style.display = found ? '' : 'none';
        }
    }
    </script>
</body>
</html>