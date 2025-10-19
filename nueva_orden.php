<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Orden - Taller Lykos</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
        .header { background: #2c3e50; color: white; padding: 1rem; text-align: center; }
        .nav { background: #34495e; padding: 1rem; display: flex; justify-content: center; gap: 10px; flex-wrap: wrap; }
        .nav a { padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 5px; }
        .nav a:hover { background: #2980b9; }
        .container { max-width: 1000px; margin: 2rem auto; padding: 0 1rem; }
        .card { background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 1rem; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: bold; color: #2c3e50; }
        .form-control { width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem; }
        .btn { padding: 10px 20px; background: #27ae60; color: white; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background: #219a52; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .info-box { background: #e8f4fd; padding: 1rem; border-radius: 5px; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Nueva Orden de Servicio - Taller Lykos</h1>
    </div>

    <?php include 'nav.php'; ?>

    <div class="container">
        <div class="card">
            <h2>Crear Nueva Orden de Servicio</h2>
            
            <?php
            // OBTENER DATOS SI VIENEN POR GET
            $cliente_id = $_GET['cliente'] ?? '';
            $equipo_id = $_GET['equipo'] ?? '';
            
            $cliente_info = null;
            $equipo_info = null;
            
            if($cliente_id) {
                $stmt = $pdo->prepare("SELECT * FROM clientes WHERE id_cliente = ?");
                $stmt->execute([$cliente_id]);
                $cliente_info = $stmt->fetch();
            }
            
            if($equipo_id) {
                $stmt = $pdo->prepare("SELECT e.*, c.nombre as cliente FROM equipos e JOIN clientes c ON e.id_cliente = c.id_cliente WHERE e.id_equipo = ?");
                $stmt->execute([$equipo_id]);
                $equipo_info = $stmt->fetch();
                $cliente_id = $equipo_info['id_cliente'];
            }
            ?>
            
            <form method="POST" action="guardar_orden.php">
                <div class="grid-2">
                    <div class="form-group">
                        <label>Cliente *</label>
                        <select name="id_cliente" id="selectCliente" class="form-control" required onchange="cargarEquipos()">
                            <option value="">Seleccionar cliente</option>
                            <?php
                            $stmt = $pdo->query("SELECT * FROM clientes WHERE activo = 1 ORDER BY nombre");
                            while($cliente = $stmt->fetch()) {
                                $selected = $cliente_id == $cliente['id_cliente'] ? 'selected' : '';
                                echo "<option value='{$cliente['id_cliente']}' $selected>{$cliente['nombre']} - {$cliente['telefono']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Equipo *</label>
                        <select name="id_equipo" id="selectEquipo" class="form-control" required>
                            <option value="">Primero seleccione un cliente</option>
                            <?php
                            if($equipo_info) {
                                echo "<option value='{$equipo_info['id_equipo']}' selected>{$equipo_info['marca']} {$equipo_info['modelo']} - {$equipo_info['tipo_equipo']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Fecha de Ingreso *</label>
                        <input type="date" name="fecha_ingreso" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Hora de Ingreso</label>
                        <input type="time" name="hora_ingreso" class="form-control" value="<?php echo date('H:i'); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Fecha de Entrega Estimada</label>
                    <input type="date" name="fecha_entrega_estimada" class="form-control">
                </div>

                <div class="form-group">
                    <label>Técnico Asignado</label>
                    <input type="text" name="tecnico_asignado" class="form-control" value="Santiago">
                </div>

                <div class="form-group">
                    <label>Observaciones Iniciales</label>
                    <textarea name="observaciones_internas" class="form-control" rows="3" placeholder="Observaciones internas del técnico..."></textarea>
                </div>

                <div class="form-group">
                    <label>Costo Estimado Inicial</label>
                    <input type="number" name="costo_estimado" class="form-control" step="0.01" min="0" placeholder="0.00">
                </div>

                <button type="submit" class="btn">Crear Orden de Servicio</button>
                <a href="ordenes.php" class="btn" style="background: #95a5a6;">Cancelar</a>
            </form>
        </div>

        <?php if($equipo_info): ?>
        <div class="card">
            <h3>Información del Equipo Seleccionado</h3>
            <div class="info-box">
                <p><strong>Cliente:</strong> <?php echo $equipo_info['cliente']; ?></p>
                <p><strong>Equipo:</strong> <?php echo $equipo_info['tipo_equipo']; ?> - <?php echo $equipo_info['marca']; ?> <?php echo $equipo_info['modelo']; ?></p>
                <p><strong>Número de Serie:</strong> <?php echo $equipo_info['numero_serie']; ?></p>
                <p><strong>Problema Reportado:</strong> <?php echo $equipo_info['problemas_reportados']; ?></p>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script>
    function cargarEquipos() {
        var clienteId = document.getElementById('selectCliente').value;
        var selectEquipo = document.getElementById('selectEquipo');
        
        if(clienteId) {
            // Limpiar select
            selectEquipo.innerHTML = '<option value="">Cargando equipos...</option>';
            
            // Hacer petición AJAX
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'ajax_equipos.php?cliente=' + clienteId, true);
            xhr.onload = function() {
                if(xhr.status === 200) {
                    selectEquipo.innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        } else {
            selectEquipo.innerHTML = '<option value="">Primero seleccione un cliente</option>';
        }
    }
    </script>
</body>
</html>