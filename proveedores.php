<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proveedores - Taller Lykos</title>
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
        .actions { display: flex; gap: 5px; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .rating { color: #f39c12; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Proveedores - Taller Lykos</h1>
    </div>

    <?php include 'nav.php'; ?>

    <div class="container">
        <!-- FORMULARIO PROVEEDOR -->
        <div class="card">
            <h2><?php echo isset($_GET['editar']) ? 'Editar Proveedor' : 'Nuevo Proveedor'; ?></h2>
            <form method="POST" action="guardar_proveedor.php">
                <?php
                $proveedor = null;
                if(isset($_GET['editar'])) {
                    $stmt = $pdo->prepare("SELECT * FROM proveedores WHERE id_proveedor = ?");
                    $stmt->execute([$_GET['editar']]);
                    $proveedor = $stmt->fetch();
                }
                ?>
                <input type="hidden" name="id_proveedor" value="<?php echo $proveedor ? $proveedor['id_proveedor'] : ''; ?>">
                
                <div class="grid-2">
                    <div class="form-group">
                        <label>Nombre del Proveedor *</label>
                        <input type="text" name="nombre" class="form-control" value="<?php echo $proveedor ? $proveedor['nombre'] : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Persona de Contacto</label>
                        <input type="text" name="contacto" class="form-control" value="<?php echo $proveedor ? $proveedor['contacto'] : ''; ?>">
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Teléfono</label>
                        <input type="text" name="telefono" class="form-control" value="<?php echo $proveedor ? $proveedor['telefono'] : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="<?php echo $proveedor ? $proveedor['email'] : ''; ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Dirección</label>
                    <textarea name="direccion" class="form-control" rows="2"><?php echo $proveedor ? $proveedor['direccion'] : ''; ?></textarea>
                </div>

                <div class="form-group">
                    <label>Productos/Especialidad</label>
                    <textarea name="productos_especialidad" class="form-control" rows="2" placeholder="Qué productos ofrece..."><?php echo $proveedor ? $proveedor['productos_especialidad'] : ''; ?></textarea>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Calificación (1-5)</label>
                        <select name="calificacion" class="form-control">
                            <?php for($i=1; $i<=5; $i++): ?>
                                <option value="<?php echo $i; ?>" <?php echo $proveedor && $proveedor['calificacion'] == $i ? 'selected' : ''; ?>>
                                    <?php echo $i; ?> <?php echo str_repeat('★', $i); ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>¿Es Confiable?</label>
                        <select name="confiable" class="form-control">
                            <option value="1" <?php echo $proveedor && $proveedor['confiable'] ? 'selected' : ''; ?>>Sí</option>
                            <option value="0" <?php echo $proveedor && !$proveedor['confiable'] ? 'selected' : ''; ?>>No</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Observaciones</label>
                    <textarea name="observaciones" class="form-control" rows="2"><?php echo $proveedor ? $proveedor['observaciones'] : ''; ?></textarea>
                </div>

                <button type="submit" class="btn"><?php echo $proveedor ? 'Actualizar' : 'Guardar'; ?> Proveedor</button>
                <?php if($proveedor): ?>
                    <a href="proveedores.php" class="btn" style="background: #95a5a6;">Cancelar</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- LISTA DE PROVEEDORES -->
        <div class="card">
            <h2>Lista de Proveedores</h2>
            
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Contacto</th>
                        <th>Teléfono</th>
                        <th>Especialidad</th>
                        <th>Calificación</th>
                        <th>Confiable</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->query("SELECT * FROM proveedores ORDER BY nombre");
                    while($proveedor = $stmt->fetch()) {
                        $confiable = $proveedor['confiable'] ? '✅ Sí' : '❌ No';
                        echo "
                        <tr>
                            <td><strong>{$proveedor['nombre']}</strong></td>
                            <td>{$proveedor['contacto']}</td>
                            <td>{$proveedor['telefono']}</td>
                            <td>{$proveedor['productos_especialidad']}</td>
                            <td class='rating'>" . str_repeat('★', $proveedor['calificacion']) . "</td>
                            <td>$confiable</td>
                            <td class='actions'>
                                <a href='proveedores.php?editar={$proveedor['id_proveedor']}' class='btn'>Editar</a>
                            </td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>