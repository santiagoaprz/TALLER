<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Clientes - Taller Lykos</title>
    <style>
        /* Estilos del index.php aquí también */
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
        .btn-danger { background: #e74c3c; }
        .btn-danger:hover { background: #c0392b; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 5px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ecf0f1; }
        th { background: #34495e; color: white; }
        tr:hover { background: #f8f9fa; }
        .actions { display: flex; gap: 5px; }
        .search-box { margin-bottom: 1rem; }
        .search-box input { padding: 0.75rem; width: 300px; max-width: 100%; border: 1px solid #ddd; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Gestión de Clientes - Taller Lykos</h1>
    </div>

    <?php include 'nav.php'; ?>

    <div class="container">
        <!-- FORMULARIO PARA AGREGAR/EDITAR CLIENTE -->
        <div class="card">
            <h2><?php echo isset($_GET['editar']) ? 'Editar Cliente' : 'Nuevo Cliente'; ?></h2>
            <form method="POST" action="guardar_cliente.php">
                <?php
                $cliente = null;
                if(isset($_GET['editar'])) {
                    $stmt = $pdo->prepare("SELECT * FROM clientes WHERE id_cliente = ?");
                    $stmt->execute([$_GET['editar']]);
                    $cliente = $stmt->fetch();
                }
                ?>
                <input type="hidden" name="id_cliente" value="<?php echo $cliente ? $cliente['id_cliente'] : ''; ?>">
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label>Nombre Completo *</label>
                        <input type="text" name="nombre" class="form-control" 
                               value="<?php echo $cliente ? $cliente['nombre'] : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Teléfono *</label>
                        <input type="tel" name="telefono" class="form-control" 
                               value="<?php echo $cliente ? $cliente['telefono'] : ''; ?>" required>
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" 
                               value="<?php echo $cliente ? $cliente['email'] : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>RFC</label>
                        <input type="text" name="rfc" class="form-control" 
                               value="<?php echo $cliente ? $cliente['rfc'] : ''; ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Dirección</label>
                    <textarea name="direccion" class="form-control" rows="3"><?php echo $cliente ? $cliente['direccion'] : ''; ?></textarea>
                </div>
                
                <button type="submit" class="btn"><?php echo $cliente ? 'Actualizar' : 'Guardar'; ?> Cliente</button>
                <?php if($cliente): ?>
                    <a href="clientes.php" class="btn btn-danger">Cancelar</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- LISTA DE CLIENTES -->
        <div class="card">
            <h2>Lista de Clientes</h2>
            
            <div class="search-box">
                <input type="text" id="searchClientes" placeholder="Buscar clientes..." onkeyup="buscarClientes()">
            </div>
            
            <table id="tablaClientes">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th>Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->query("SELECT * FROM clientes WHERE activo = 1 ORDER BY nombre");
                    while($cliente = $stmt->fetch()) {
                        echo "
                        <tr>
                            <td>{$cliente['id_cliente']}</td>
                            <td><strong>{$cliente['nombre']}</strong></td>
                            <td>{$cliente['telefono']}</td>
                            <td>{$cliente['email']}</td>
                            <td>" . date('d/m/Y', strtotime($cliente['fecha_registro'])) . "</td>
                            <td class='actions'>
                                <a href='clientes.php?editar={$cliente['id_cliente']}' class='btn'>Editar</a>
                                <a href='eliminar_cliente.php?id={$cliente['id_cliente']}' class='btn btn-danger' onclick='return confirm(\"¿Estás seguro?\")'>Eliminar</a>
                                <a href='nueva_orden.php?cliente={$cliente['id_cliente']}' class='btn'>Nueva Orden</a>
                            </td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
    function buscarClientes() {
        var input = document.getElementById('searchClientes');
        var filter = input.value.toLowerCase();
        var table = document.getElementById('tablaClientes');
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