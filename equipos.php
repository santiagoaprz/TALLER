<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Equipos - Taller Lykos</title>
    <style>
        /* Mismos estilos que clientes.php */
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
        .btn-danger { background: #e74c3c; }
        .btn-danger:hover { background: #c0392b; }
        .btn-info { background: #3498db; }
        .btn-info:hover { background: #2980b9; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 5px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ecf0f1; }
        th { background: #34495e; color: white; }
        tr:hover { background: #f8f9fa; }
        .actions { display: flex; gap: 5px; flex-wrap: wrap; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; }
        .grid-4 { display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 1rem; }
        .section-title { background: #ecf0f1; padding: 10px; border-radius: 5px; margin: 1rem 0; font-weight: bold; }
        .checkbox-group { display: flex; flex-wrap: wrap; gap: 10px; margin: 10px 0; }
        .checkbox-group label { display: flex; align-items: center; gap: 5px; font-weight: normal; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Gestión de Equipos - Taller Lykos</h1>
    </div>

    <?php include 'nav.php'; ?>

    <div class="container">
        <!-- FORMULARIO PARA AGREGAR/EDITAR EQUIPO -->
        <div class="card">
            <h2><?php echo isset($_GET['editar']) ? 'Editar Equipo' : 'Nuevo Equipo'; ?></h2>
            <form method="POST" action="guardar_equipo.php">
                <?php
                $equipo = null;
                if(isset($_GET['editar'])) {
                    $stmt = $pdo->prepare("SELECT * FROM equipos WHERE id_equipo = ?");
                    $stmt->execute([$_GET['editar']]);
                    $equipo = $stmt->fetch();
                }
                ?>
                <input type="hidden" name="id_equipo" value="<?php echo $equipo ? $equipo['id_equipo'] : ''; ?>">
                
                <div class="grid-2">
                    <div class="form-group">
                        <label>Cliente *</label>
                        <select name="id_cliente" class="form-control" required>
                            <option value="">Seleccionar cliente</option>
                            <?php
                            $stmt = $pdo->query("SELECT * FROM clientes WHERE activo = 1 ORDER BY nombre");
                            while($cliente = $stmt->fetch()) {
                                $selected = $equipo && $equipo['id_cliente'] == $cliente['id_cliente'] ? 'selected' : '';
                                echo "<option value='{$cliente['id_cliente']}' $selected>{$cliente['nombre']} - {$cliente['telefono']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Tipo de Equipo *</label>
                        <select name="tipo_equipo" class="form-control" required onchange="mostrarCamposEspecificos(this.value)">
                            <option value="">Seleccionar tipo</option>
                            <option value="Laptop" <?php echo $equipo && $equipo['tipo_equipo'] == 'Laptop' ? 'selected' : ''; ?>>Laptop</option>
                            <option value="Celular" <?php echo $equipo && $equipo['tipo_equipo'] == 'Celular' ? 'selected' : ''; ?>>Celular</option>
                            <option value="Tablet" <?php echo $equipo && $equipo['tipo_equipo'] == 'Tablet' ? 'selected' : ''; ?>>Tablet</option>
                            <option value="Videoconsola" <?php echo $equipo && $equipo['tipo_equipo'] == 'Videoconsola' ? 'selected' : ''; ?>>Videoconsola</option>
                            <option value="PC Escritorio" <?php echo $equipo && $equipo['tipo_equipo'] == 'PC Escritorio' ? 'selected' : ''; ?>>PC Escritorio</option>
                            <option value="Impresora" <?php echo $equipo && $equipo['tipo_equipo'] == 'Impresora' ? 'selected' : ''; ?>>Impresora</option>
                            <option value="Otro" <?php echo $equipo && $equipo['tipo_equipo'] == 'Otro' ? 'selected' : ''; ?>>Otro</option>
                        </select>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Marca *</label>
                        <input type="text" name="marca" class="form-control" 
                               value="<?php echo $equipo ? $equipo['marca'] : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Modelo *</label>
                        <input type="text" name="modelo" class="form-control" 
                               value="<?php echo $equipo ? $equipo['modelo'] : ''; ?>" required>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Número de Serie</label>
                        <input type="text" name="numero_serie" class="form-control" 
                               value="<?php echo $equipo ? $equipo['numero_serie'] : ''; ?>">
                    </div>
                    
                    <div class="form-group" id="imei_field" style="display: none;">
                        <label>IMEI (Solo celulares/tablets)</label>
                        <input type="text" name="imei" class="form-control" 
                               value="<?php echo $equipo ? $equipo['imei'] : ''; ?>">
                    </div>
                </div>

                <!-- ESPECIFICACIONES TÉCNICAS -->
                <div class="section-title">Especificaciones Técnicas</div>
                <div class="grid-4">
                    <div class="form-group">
                        <label>Color</label>
                        <input type="text" name="color" class="form-control" 
                               value="<?php echo $equipo ? $equipo['color'] : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Almacenamiento</label>
                        <input type="text" name="capacidad_almacenamiento" class="form-control" 
                               value="<?php echo $equipo ? $equipo['capacidad_almacenamiento'] : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>RAM</label>
                        <input type="text" name="ram" class="form-control" 
                               value="<?php echo $equipo ? $equipo['ram'] : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Procesador</label>
                        <input type="text" name="procesador" class="form-control" 
                               value="<?php echo $equipo ? $equipo['procesador'] : ''; ?>">
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Sistema Operativo</label>
                        <input type="text" name="sistema_operativo" class="form-control" 
                               value="<?php echo $equipo ? $equipo['sistema_operativo'] : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Versión SO</label>
                        <input type="text" name="version_so" class="form-control" 
                               value="<?php echo $equipo ? $equipo['version_so'] : ''; ?>">
                    </div>
                </div>

                <!-- ESTADO FÍSICO -->
                <div class="section-title">Estado Físico del Equipo</div>
                <div class="form-group">
                    <label>Estado General</label>
                    <textarea name="estado_fisico" class="form-control" rows="2" placeholder="Descripción general del estado físico..."><?php echo $equipo ? $equipo['estado_fisico'] : ''; ?></textarea>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Golpes/Maltratos</label>
                        <textarea name="golpes" class="form-control" rows="2" placeholder="Describa golpes, abolladuras..."><?php echo $equipo ? $equipo['golpes'] : ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Rayones/Desgaste</label>
                        <textarea name="rayones" class="form-control" rows="2" placeholder="Describa rayones, desgaste..."><?php echo $equipo ? $equipo['rayones'] : ''; ?></textarea>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Tornillos Faltantes</label>
                        <textarea name="faltan_tornillos" class="form-control" rows="2" placeholder="Especifique tornillos faltantes..."><?php echo $equipo ? $equipo['faltan_tornillos'] : ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Desgaste por Uso</label>
                        <textarea name="desgaste_uso" class="form-control" rows="2" placeholder="Desgaste normal por uso..."><?php echo $equipo ? $equipo['desgaste_uso'] : ''; ?></textarea>
                    </div>
                </div>

                <!-- FUNCIONALIDAD BÁSICA -->
                <div class="section-title">Funcionalidad Básica al Ingresar</div>
                <div class="checkbox-group">
                    <label><input type="checkbox" name="enciende" value="1" <?php echo $equipo && $equipo['enciende'] ? 'checked' : ''; ?>> Enciende</label>
                    <label><input type="checkbox" name="carga" value="1" <?php echo $equipo && $equipo['carga'] ? 'checked' : ''; ?>> Carga</label>
                    <label><input type="checkbox" name="display_funciona" value="1" <?php echo $equipo && $equipo['display_funciona'] ? 'checked' : ''; ?>> Display funciona</label>
                </div>

                <!-- CAMPOS ESPECÍFICOS PARA CELULARES -->
                <div id="campos_celular" style="display: none;">
                    <div class="section-title">Pruebas Específicas para Celulares/Tablets</div>
                    <div class="checkbox-group">
                        <label><input type="checkbox" name="bocinas_funcionan" value="1" <?php echo $equipo && $equipo['bocinas_funcionan'] ? 'checked' : ''; ?>> Bocinas</label>
                        <label><input type="checkbox" name="microfono_funciona" value="1" <?php echo $equipo && $equipo['microfono_funciona'] ? 'checked' : ''; ?>> Micrófono</label>
                        <label><input type="checkbox" name="camara_funciona" value="1" <?php echo $equipo && $equipo['camara_funciona'] ? 'checked' : ''; ?>> Cámaras</label>
                        <label><input type="checkbox" name="botones_funcionan" value="1" <?php echo $equipo && $equipo['botones_funcionan'] ? 'checked' : ''; ?>> Botones</label>
                        <label><input type="checkbox" name="huella_funciona" value="1" <?php echo $equipo && $equipo['huella_funciona'] ? 'checked' : ''; ?>> Huella</label>
                        <label><input type="checkbox" name="wifi_funciona" value="1" <?php echo $equipo && $equipo['wifi_funciona'] ? 'checked' : ''; ?>> WiFi</label>
                        <label><input type="checkbox" name="señal_funciona" value="1" <?php echo $equipo && $equipo['señal_funciona'] ? 'checked' : ''; ?>> Señal</label>
                        <label><input type="checkbox" name="sensores_funcionan" value="1" <?php echo $equipo && $equipo['sensores_funcionan'] ? 'checked' : ''; ?>> Sensores</label>
                        <label><input type="checkbox" name="reporte_robo" value="1" <?php echo $equipo && $equipo['reporte_robo'] ? 'checked' : ''; ?>> Reporte de robo</label>
                    </div>
                </div>

                <!-- CAMPOS ESPECÍFICOS PARA LAPTOPS -->
                <div id="campos_laptop" style="display: none;">
                    <div class="section-title">Pruebas Específicas para Laptops/PCs</div>
                    <div class="checkbox-group">
                        <label><input type="checkbox" name="usb_funciona" value="1" <?php echo $equipo && $equipo['usb_funciona'] ? 'checked' : ''; ?>> Puertos USB</label>
                        <label><input type="checkbox" name="hdmi_funciona" value="1" <?php echo $equipo && $equipo['hdmi_funciona'] ? 'checked' : ''; ?>> HDMI</label>
                        <label><input type="checkbox" name="vga_funciona" value="1" <?php echo $equipo && $equipo['vga_funciona'] ? 'checked' : ''; ?>> VGA</label>
                        <label><input type="checkbox" name="ethernet_funciona" value="1" <?php echo $equipo && $equipo['ethernet_funciona'] ? 'checked' : ''; ?>> Ethernet</label>
                        <label><input type="checkbox" name="wifi_pc_funciona" value="1" <?php echo $equipo && $equipo['wifi_pc_funciona'] ? 'checked' : ''; ?>> WiFi</label>
                        <label><input type="checkbox" name="bluetooth_funciona" value="1" <?php echo $equipo && $equipo['bluetooth_funciona'] ? 'checked' : ''; ?>> Bluetooth</label>
                        <label><input type="checkbox" name="teclado_funciona" value="1" <?php echo $equipo && $equipo['teclado_funciona'] ? 'checked' : ''; ?>> Teclado</label>
                        <label><input type="checkbox" name="touchpad_funciona" value="1" <?php echo $equipo && $equipo['touchpad_funciona'] ? 'checked' : ''; ?>> Touchpad</label>
                    </div>
                </div>

                <!-- ACCESORIOS -->
                <div class="section-title">Accesorios que Ingresan con el Equipo</div>
                <div class="form-group">
                    <label>Accesorios que Acompañan</label>
                    <textarea name="accesorios_ingreso" class="form-control" rows="2" placeholder="Fundas, audífonos, stylus, etc..."><?php echo $equipo ? $equipo['accesorios_ingreso'] : ''; ?></textarea>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>¿Incluye Cargador?</label>
                        <select name="tiene_cargador" class="form-control">
                            <option value="0">No</option>
                            <option value="1" <?php echo $equipo && $equipo['tiene_cargador'] ? 'selected' : ''; ?>>Sí</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Número de Serie del Cargador</label>
                        <input type="text" name="numero_serie_cargador" class="form-control" 
                               value="<?php echo $equipo ? $equipo['numero_serie_cargador'] : ''; ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Estado del Cargador</label>
                    <textarea name="estado_cargador" class="form-control" rows="2" placeholder="Describa estado del cargador..."><?php echo $equipo ? $equipo['estado_cargador'] : ''; ?></textarea>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>¿Incluye Funda/Protección?</label>
                        <select name="funda_proteccion" class="form-control">
                            <option value="0">No</option>
                            <option value="1" <?php echo $equipo && $equipo['funda_proteccion'] ? 'selected' : ''; ?>>Sí</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Cables Extra</label>
                        <input type="text" name="cables_extra" class="form-control" 
                               value="<?php echo $equipo ? $equipo['cables_extra'] : ''; ?>" placeholder="USB-C, HDMI, etc.">
                    </div>
                </div>

                <div class="form-group">
                    <label>Otros Accesorios</label>
                    <textarea name="otros_accesorios" class="form-control" rows="2" placeholder="Audífonos, stylus, adaptadores..."><?php echo $equipo ? $equipo['otros_accesorios'] : ''; ?></textarea>
                </div>

                <!-- PROBLEMAS REPORTADOS -->
                <div class="form-group">
                    <label>Problemas Reportados por el Cliente *</label>
                    <textarea name="problemas_reportados" class="form-control" rows="3" required placeholder="Describa detalladamente lo que el cliente reporta que no funciona..."><?php echo $equipo ? $equipo['problemas_reportados'] : ''; ?></textarea>
                </div>

                <button type="submit" class="btn"><?php echo $equipo ? 'Actualizar' : 'Guardar'; ?> Equipo</button>
                <?php if($equipo): ?>
                    <a href="equipos.php" class="btn btn-danger">Cancelar</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- LISTA DE EQUIPOS -->
               <!-- LISTA DE EQUIPOS -->
        <div class="card">
            <h2>Lista de Equipos Registrados</h2>
            
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Tipo</th>
                        <th>Marca/Modelo</th>
                        <th>Serie</th>
                        <th>Problema Reportado</th>
                        <th>Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->query("
                        SELECT e.*, c.nombre as cliente,
                               (SELECT COUNT(*) FROM ordenes_servicio os WHERE os.id_equipo = e.id_equipo AND os.activa = 1) as tiene_orden_activa
                        FROM equipos e 
                        JOIN clientes c ON e.id_cliente = c.id_cliente 
                        ORDER BY e.fecha_registro DESC
                    ");
                    while($equipo = $stmt->fetch()) {
                        $problema = strlen($equipo['problemas_reportados']) > 50 ? 
                                   substr($equipo['problemas_reportados'], 0, 50) . '...' : 
                                   $equipo['problemas_reportados'];
                        
                        // Verificar si ya tiene orden activa
                        $tiene_orden = $equipo['tiene_orden_activa'] > 0;
                        
                        $boton_orden = $tiene_orden 
                            ? '<span class="btn" style="background: #95a5a6; cursor: not-allowed;" title="Este equipo ya tiene una orden activa">Ya tiene orden</span>'
                            : '<a href="nueva_orden.php?equipo=' . $equipo['id_equipo'] . '" class="btn btn-info">Crear Orden</a>';
                        
                        echo "
                        <tr>
                            <td>{$equipo['id_equipo']}</td>
                            <td><strong>{$equipo['cliente']}</strong></td>
                            <td>{$equipo['tipo_equipo']}</td>
                            <td>{$equipo['marca']} {$equipo['modelo']}</td>
                            <td>{$equipo['numero_serie']}</td>
                            <td title='{$equipo['problemas_reportados']}'>$problema</td>
                            <td>" . date('d/m/Y', strtotime($equipo['fecha_registro'])) . "</td>
                            <td class='actions'>
                                <a href='equipos.php?editar={$equipo['id_equipo']}' class='btn'>Editar</a>
                                {$boton_orden}
                            </td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
    function mostrarCamposEspecificos(tipo) {
        // Ocultar todos primero
        document.getElementById('campos_celular').style.display = 'none';
        document.getElementById('campos_laptop').style.display = 'none';
        document.getElementById('imei_field').style.display = 'none';
        
        // Mostrar según el tipo
        if(tipo === 'Celular' || tipo === 'Tablet') {
            document.getElementById('campos_celular').style.display = 'block';
            document.getElementById('imei_field').style.display = 'block';
        } else if(tipo === 'Laptop' || tipo === 'PC Escritorio') {
            document.getElementById('campos_laptop').style.display = 'block';
        }
    }
    
    // Ejecutar al cargar la página si hay un equipo editándose
    <?php if($equipo): ?>
    mostrarCamposEspecificos('<?php echo $equipo['tipo_equipo']; ?>');
    <?php endif; ?>
    </script>
</body>
</html>