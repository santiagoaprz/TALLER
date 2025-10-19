<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recepción Rápida - Taller Lykos</title>
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
        .btn { padding: 12px 25px; background: #27ae60; color: white; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; font-size: 1rem; }
        .btn:hover { background: #219a52; }
        .btn-large { padding: 15px 30px; font-size: 1.1rem; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; }
        .grid-4 { display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 1rem; }
        .section-title { background: #ecf0f1; padding: 10px; border-radius: 5px; margin: 1.5rem 0 1rem 0; font-weight: bold; color: #2c3e50; }
        .checkbox-group { display: flex; flex-wrap: wrap; gap: 15px; margin: 10px 0; }
        .checkbox-group label { display: flex; align-items: center; gap: 5px; font-weight: normal; }
        .search-client { background: #e8f4fd; padding: 1rem; border-radius: 5px; margin-bottom: 1rem; }
        .client-results { max-height: 200px; overflow-y: auto; border: 1px solid #ddd; border-radius: 5px; margin-top: 10px; display: none; }
        .client-item { padding: 10px; border-bottom: 1px solid #eee; cursor: pointer; }
        .client-item:hover { background: #f8f9fa; }
        .required::after { content: " *"; color: #e74c3c; }
        .tabs { display: flex; margin-bottom: 1rem; border-bottom: 2px solid #3498db; flex-wrap: wrap; }
        .tab { padding: 10px 20px; background: #ecf0f1; border: none; cursor: pointer; margin-right: 5px; border-radius: 5px 5px 0 0; }
        .tab.active { background: #3498db; color: white; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .equipo-specific { background: #fff3cd; padding: 1rem; border-radius: 5px; margin: 1rem 0; border-left: 4px solid #ffc107; }
        .disabled-field { color: #ccc; text-decoration: line-through; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Recepción Rápida - Taller Lykos</h1>
    </div>

    <?php include 'nav.php'; ?>

    <div class="container">
        <div class="card">
            <h2>Recepción Rápida de Equipo</h2>
            <p style="color: #666; margin-bottom: 1.5rem;">Complete toda la información en un solo formulario</p>

            <form method="POST" action="guardar_recepcion_rapida.php" id="formRecepcion">
                
                <!-- PESTAÑAS -->
                <div class="tabs">
                    <button type="button" class="tab active" onclick="openTab('tab-cliente')">1. Cliente</button>
                    <button type="button" class="tab" onclick="openTab('tab-equipo')">2. Equipo</button>
                    <button type="button" class="tab" onclick="openTab('tab-estado')">3. Estado Físico</button>
                    <button type="button" class="tab" onclick="openTab('tab-funcionalidad')">4. Funcionalidad</button>
                    <button type="button" class="tab" onclick="openTab('tab-accesorios')">5. Accesorios</button>
                    <button type="button" class="tab" onclick="openTab('tab-orden')">6. Orden</button>
                </div>

                <!-- TAB CLIENTE -->
                <div id="tab-cliente" class="tab-content active">
                    <div class="search-client">
                        <h3>Buscar Cliente Existente</h3>
                        <input type="text" id="searchCliente" class="form-control" placeholder="Buscar por nombre o teléfono..." onkeyup="buscarClientes()">
                        <div id="clientResults" class="client-results"></div>
                        <p style="text-align: center; margin: 10px 0; color: #666;">- O -</p>
                    </div>

                    <h3>Registrar Nuevo Cliente</h3>
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="required">Nombre Completo</label>
                            <input type="text" name="cliente_nombre" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="required">Teléfono</label>
                            <input type="tel" name="cliente_telefono" class="form-control" required>
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="cliente_email" class="form-control" title="Formato de email válido: usuario@dominio.com">
                        </div>
                        
                        <div class="form-group">
                            <label>Dirección</label>
                            <input type="text" name="cliente_direccion" class="form-control" placeholder="Calle, número, colonia...">
                        </div>
                    </div>
                    
                    <div style="text-align: right; margin-top: 1rem;">
                        <button type="button" class="btn" onclick="openTab('tab-equipo')">Siguiente →</button>
                    </div>
                </div>

                <!-- TAB EQUIPO -->
                <div id="tab-equipo" class="tab-content">
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="required">Tipo de Equipo</label>
                            <select name="equipo_tipo" class="form-control" required onchange="mostrarCamposEspecificos(this.value)">
                                <option value="">Seleccionar tipo</option>
                                <option value="Laptop">Laptop</option>
                                <option value="Celular">Celular</option>
                                <option value="Tablet">Tablet</option>
                                <option value="PC Escritorio">PC Escritorio</option>
                                <option value="Videoconsola">Videoconsola</option>
                                <option value="Impresora">Impresora</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="required">Marca</label>
                            <input type="text" name="equipo_marca" class="form-control" required>
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="required">Modelo</label>
                            <input type="text" name="equipo_modelo" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Número de Serie</label>
                            <input type="text" name="equipo_serie" class="form-control">
                        </div>
                    </div>

                    <div class="grid-3">
                        <div class="form-group">
                            <label>Color</label>
                            <input type="text" name="equipo_color" class="form-control">
                        </div>
                        
                        <div class="form-group">
                            <label>Almacenamiento</label>
                            <input type="text" name="equipo_almacenamiento" class="form-control" placeholder="Ej: 256GB, 1TB, N/A">
                        </div>
                        
                        <div class="form-group">
                            <label>RAM</label>
                            <input type="text" name="equipo_ram" class="form-control" placeholder="Ej: 8GB, 16GB, N/A">
                        </div>
                    </div>

                    <!-- CAMPOS ESPECÍFICOS PARA CELULARES/TABLETS -->
                    <div id="campos-celular" class="equipo-specific" style="display: none;">
                        <div class="section-title">Información Específica para Celular/Tablet</div>
                        <div class="grid-2">
                            <div class="form-group">
                                <label>IMEI</label>
                                <input type="text" name="equipo_imei" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>¿Incluye Chip?</label>
                                <select name="celular_tiene_chip" class="form-control">
                                    <option value="0">No</option>
                                    <option value="1">Sí</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- CAMPOS ESPECÍFICOS PARA LAPTOPS/PCs -->
                    <div id="campos-laptop" class="equipo-specific" style="display: none;">
                        <div class="section-title">Información Específica para Laptop/PC</div>
                        <div class="grid-2">
                            <div class="form-group">
                                <label>Procesador</label>
                                <input type="text" name="equipo_procesador" class="form-control" placeholder="Ej: Intel i5-8400, AMD Ryzen 5">
                            </div>
                            
                            <div class="form-group">
                                <label>Sistema Operativo</label>
                                <input type="text" name="equipo_so" class="form-control" placeholder="Ej: Windows 10, Linux Ubuntu">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Versión del Sistema Operativo</label>
                            <input type="text" name="equipo_version_so" class="form-control" placeholder="Ej: 21H2, Kernel 5.15">
                        </div>
                    </div>

                    <div style="display: flex; justify-content: space-between; margin-top: 1rem;">
                        <button type="button" class="btn" onclick="openTab('tab-cliente')">← Anterior</button>
                        <button type="button" class="btn" onclick="openTab('tab-estado')">Siguiente →</button>
                    </div>
                </div>

                <!-- TAB ESTADO FÍSICO -->
                <div id="tab-estado" class="tab-content">
                    <div class="form-group">
                        <label class="required">Problema Reportado por el Cliente</label>
                        <textarea name="problema_reportado" class="form-control" rows="4" required placeholder="Describa detalladamente el problema que reporta el cliente..."></textarea>
                    </div>

                    <div class="section-title">Estado Físico del Equipo</div>
                    
                    <div class="form-group">
                        <label>Estado General del Equipo</label>
                        <textarea name="estado_fisico" class="form-control" rows="3" placeholder="Descripción general del estado físico del equipo..."></textarea>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label>Golpes/Maltratos Visibles</label>
                            <textarea name="golpes" class="form-control" rows="3" placeholder="Describa golpes, abolladuras, fracturas..."></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label>Rayones/Desgaste</label>
                            <textarea name="rayones" class="form-control" rows="3" placeholder="Describa rayones, desgaste de pintura..."></textarea>
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label>Tornillos Faltantes</label>
                            <textarea name="faltan_tornillos" class="form-control" rows="2" placeholder="Especifique tornillos faltantes y ubicación..."></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label>Desgaste por Uso Normal</label>
                            <textarea name="desgaste_uso" class="form-control" rows="2" placeholder="Desgaste normal por uso..."></textarea>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: space-between; margin-top: 1rem;">
                        <button type="button" class="btn" onclick="openTab('tab-equipo')">← Anterior</button>
                        <button type="button" class="btn" onclick="openTab('tab-funcionalidad')">Siguiente →</button>
                    </div>
                </div>

                <!-- TAB FUNCIONALIDAD -->
                <div id="tab-funcionalidad" class="tab-content">
                    <div class="section-title">Funcionalidad Básica</div>
                    <div class="checkbox-group">
                        <label><input type="checkbox" name="func_enciende" value="1" onchange="toggleFuncionalidad()"> ¿El equipo prende?</label>
                        <label><input type="checkbox" name="func_carga" value="1" id="checkCarga"> ¿Carga la batería?</label>
                        <label><input type="checkbox" name="func_display" value="1" id="checkDisplay"> ¿Display/Pantalla funciona?</label>
                    </div>

                    <!-- PRUEBAS ESPECÍFICAS PARA CELULARES -->
                    <div id="pruebas-celular" style="display: none;">
                        <div class="section-title">Pruebas Específicas para Celular/Tablet</div>
                        <div class="checkbox-group" id="celular-checkboxes">
                            <label><input type="checkbox" name="cel_bocinas" value="1"> Bocinas funcionan</label>
                            <label><input type="checkbox" name="cel_microfono" value="1"> Micrófono funciona</label>
                            <label><input type="checkbox" name="cel_camara" value="1"> Cámaras funcionan</label>
                            <label><input type="checkbox" name="cel_botones" value="1"> Botones físicos funcionan</label>
                            <label><input type="checkbox" name="cel_huella" value="1"> Lector de huella funciona</label>
                            <label><input type="checkbox" name="cel_wifi" value="1"> WiFi funciona</label>
                            <label><input type="checkbox" name="cel_señal" value="1"> Señal celular funciona</label>
                            <label><input type="checkbox" name="cel_sensores" value="1"> Sensores (proximidad, luz)</label>
                            <label><input type="checkbox" name="cel_pantalla_tactil" value="1"> Pantalla táctil responde</label>
                            <label><input type="checkbox" name="cel_reporta_robo" value="1"> Reporte de robo/bloqueo</label>
                        </div>
                    </div>

                    <!-- PRUEBAS ESPECÍFICAS PARA LAPTOPS -->
                    <div id="pruebas-laptop" style="display: none;">
                        <div class="section-title">Pruebas Específicas para Laptop/PC</div>
                        <div class="checkbox-group" id="laptop-checkboxes">
                            <label><input type="checkbox" name="lap_usb" value="1"> Puertos USB funcionan</label>
                            <label><input type="checkbox" name="lap_hdmi" value="1"> Puerto HDMI funciona</label>
                            <label><input type="checkbox" name="lap_vga" value="1"> Puerto VGA funciona</label>
                            <label><input type="checkbox" name="lap_ethernet" value="1"> Puerto Ethernet funciona</label>
                            <label><input type="checkbox" name="lap_wifi" value="1"> WiFi funciona</label>
                            <label><input type="checkbox" name="lap_bluetooth" value="1"> Bluetooth funciona</label>
                            <label><input type="checkbox" name="lap_teclado" value="1"> Teclado funciona</label>
                            <label><input type="checkbox" name="lap_touchpad" value="1"> Touchpad funciona</label>
                            <label><input type="checkbox" name="lap_webcam" value="1"> Cámara web funciona</label>
                            <label><input type="checkbox" name="lap_audio" value="1"> Audio (altavoces/audífonos)</label>
                            <label><input type="checkbox" name="lap_lector" value="1"> Lector de CD/DVD</label>
                            <label><input type="checkbox" name="lap_bateria" value="1"> Batería carga y descarga</label>
                        </div>

                        <div class="section-title">Información de Puertos</div>
                        <div class="grid-3">
                            <div class="form-group">
                                <label>Puertos USB Tipo-A</label>
                                <input type="number" name="lap_puertos_usb" class="form-control" min="0" max="10" value="2" id="inputPuertosUSB">
                            </div>
                            <div class="form-group">
                                <label>Puertos USB Tipo-C</label>
                                <input type="number" name="lap_puertos_usbc" class="form-control" min="0" max="10" value="0" id="inputPuertosUSBC">
                            </div>
                            <div class="form-group">
                                <label>Puerto de Carga</label>
                                <input type="text" name="lap_puerto_carga" class="form-control" placeholder="Ej: USB-C, Barrel, MagSafe" id="inputPuertoCarga">
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: space-between; margin-top: 1rem;">
                        <button type="button" class="btn" onclick="openTab('tab-estado')">← Anterior</button>
                        <button type="button" class="btn" onclick="openTab('tab-accesorios')">Siguiente →</button>
                    </div>
                </div>

                <!-- TAB ACCESORIOS -->
                <div id="tab-accesorios" class="tab-content">
                    <div class="section-title">Accesorios que Ingresan con el Equipo</div>
                    
                    <div class="grid-2">
                        <div class="form-group">
                            <label>¿Incluye Cargador?</label>
                            <select name="accesorio_cargador" class="form-control">
                                <option value="0">No</option>
                                <option value="1">Sí</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Número de Serie del Cargador</label>
                            <input type="text" name="accesorio_serie_cargador" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Estado del Cargador</label>
                        <textarea name="accesorio_estado_cargador" class="form-control" rows="2" placeholder="Describa estado del cargador, cable, etc..."></textarea>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label>¿Incluye Funda/Protección?</label>
                            <select name="accesorio_funda" class="form-control">
                                <option value="0">No</option>
                                <option value="1">Sí</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Cables Extra</label>
                            <input type="text" name="accesorio_cables" class="form-control" placeholder="USB-C, HDMI, Ethernet, etc.">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Otros Accesorios</label>
                        <textarea name="accesorio_otros" class="form-control" rows="3" placeholder="Audífonos, stylus, adaptadores, fundas, estuches, manuales..."></textarea>
                    </div>

                    <div class="form-group">
                        <label>Accesorios que se DEVOLVERÁN al cliente</label>
                        <textarea name="accesorio_devolver" class="form-control" rows="2" placeholder="Especifique accesorios que deben devolverse..."></textarea>
                    </div>

                    <div style="display: flex; justify-content: space-between; margin-top: 1rem;">
                        <button type="button" class="btn" onclick="openTab('tab-funcionalidad')">← Anterior</button>
                        <button type="button" class="btn" onclick="openTab('tab-orden')">Siguiente →</button>
                    </div>
                </div>

                <!-- TAB ORDEN -->
                <div id="tab-orden" class="tab-content">
                    <div class="section-title">Información de la Orden de Servicio</div>
                    
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="required">Fecha de Ingreso</label>
                            <input type="date" name="orden_fecha" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Hora de Ingreso</label>
                            <input type="time" name="orden_hora" class="form-control" value="<?php echo date('H:i'); ?>">
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label>Fecha de Entrega Estimada</label>
                            <input type="date" name="orden_fecha_entrega" class="form-control">
                        </div>
                        
                        <div class="form-group">
                            <label>Técnico Asignado</label>
                            <input type="text" name="orden_tecnico" class="form-control" value="Santiago">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Observaciones Iniciales del Técnico</label>
                        <textarea name="orden_observaciones" class="form-control" rows="4" placeholder="Observaciones internas del técnico, prioridad, urgencia..."></textarea>
                    </div>

                    <div class="form-group">
                        <label>Costo Estimado Inicial</label>
                        <input type="number" name="orden_costo_estimado" class="form-control" step="0.01" min="0" placeholder="0.00">
                    </div>

                    <div style="text-align: center; margin-top: 2rem; padding-top: 1rem; border-top: 2px solid #ecf0f1;">
                        <button type="submit" class="btn btn-large">✅ Guardar Recepción Completa</button>
                        <p style="color: #666; margin-top: 10px; font-size: 0.9em;">
                            Se creará: Cliente + Equipo + Orden de Servicio + PDF de Recepción
                        </p>
                    </div>

                    <div style="display: flex; justify-content: center; margin-top: 1rem;">
                        <button type="button" class="btn" onclick="openTab('tab-accesorios')">← Anterior</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
    // SISTEMA DE PESTAÑAS
    function openTab(tabName) {
        // Ocultar todas las pestañas
        var tabs = document.getElementsByClassName('tab-content');
        for (var i = 0; i < tabs.length; i++) {
            tabs[i].classList.remove('active');
        }
        
        // Desactivar todos los botones de pestaña
        var tabButtons = document.getElementsByClassName('tab');
        for (var i = 0; i < tabButtons.length; i++) {
            tabButtons[i].classList.remove('active');
        }
        
        // Mostrar la pestaña seleccionada
        document.getElementById(tabName).classList.add('active');
        
        // Activar el botón correspondiente
        event.currentTarget.classList.add('active');
    }

    // FUNCIÓN PARA ACTIVAR/DESACTIVAR FUNCIONALIDAD
    function toggleFuncionalidad() {
        var prende = document.querySelector('input[name="func_enciende"]').checked;
        
        // Desactivar checkboxes de funcionalidad básica
        document.getElementById('checkCarga').disabled = !prende;
        document.getElementById('checkDisplay').disabled = !prende;
        
        // Desactivar checkboxes de celular
        var celularCheckboxes = document.querySelectorAll('#celular-checkboxes input[type="checkbox"]');
        celularCheckboxes.forEach(function(checkbox) {
            checkbox.disabled = !prende;
            if (!prende) checkbox.checked = false;
        });
        
        // Desactivar checkboxes de laptop
        var laptopCheckboxes = document.querySelectorAll('#laptop-checkboxes input[type="checkbox"]');
        laptopCheckboxes.forEach(function(checkbox) {
            checkbox.disabled = !prende;
            if (!prende) checkbox.checked = false;
        });
        
        // Desactivar campos de puertos
        document.getElementById('inputPuertosUSB').disabled = !prende;
        document.getElementById('inputPuertosUSBC').disabled = !prende;
        document.getElementById('inputPuertoCarga').disabled = !prende;
        
        // Cambiar estilo visual
        var labels = document.querySelectorAll('#celular-checkboxes label, #laptop-checkboxes label');
        labels.forEach(function(label) {
            if (!prende) {
                label.classList.add('disabled-field');
            } else {
                label.classList.remove('disabled-field');
            }
        });
    }

    // BUSCAR CLIENTES EXISTENTES
    function buscarClientes() {
        var query = document.getElementById('searchCliente').value;
        if (query.length < 2) {
            document.getElementById('clientResults').style.display = 'none';
            return;
        }
        
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'ajax_buscar_clientes.php?q=' + encodeURIComponent(query), true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                document.getElementById('clientResults').innerHTML = xhr.responseText;
                document.getElementById('clientResults').style.display = 'block';
            }
        };
        xhr.send();
    }

    // SELECCIONAR CLIENTE EXISTENTE
    function seleccionarCliente(id, nombre, telefono, email, direccion) {
        document.querySelector('input[name="cliente_nombre"]').value = nombre;
        document.querySelector('input[name="cliente_telefono"]').value = telefono;
        document.querySelector('input[name="cliente_email"]').value = email || '';
        document.querySelector('input[name="cliente_direccion"]').value = direccion || '';
        
        document.getElementById('clientResults').style.display = 'none';
        document.getElementById('searchCliente').value = '';
        
        // Marcar que estamos usando un cliente existente
        document.querySelector('input[name="cliente_existente_id"]')?.remove();
        var hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'cliente_existente_id';
        hiddenInput.value = id;
        document.getElementById('formRecepcion').appendChild(hiddenInput);
    }

    // MOSTRAR CAMPOS ESPECÍFICOS SEGÚN TIPO DE EQUIPO
    function mostrarCamposEspecificos(tipo) {
        // Ocultar todos los campos específicos primero
        document.getElementById('campos-celular').style.display = 'none';
        document.getElementById('campos-laptop').style.display = 'none';
        document.getElementById('pruebas-celular').style.display = 'none';
        document.getElementById('pruebas-laptop').style.display = 'none';
        
        // Mostrar según el tipo
        if (tipo === 'Celular' || tipo === 'Tablet') {
            document.getElementById('campos-celular').style.display = 'block';
            document.getElementById('pruebas-celular').style.display = 'block';
        } else if (tipo === 'Laptop' || tipo === 'PC Escritorio') {
            document.getElementById('campos-laptop').style.display = 'block';
            document.getElementById('pruebas-laptop').style.display = 'block';
        }
        
        // Actualizar estado de funcionalidad
        toggleFuncionalidad();
    }

    // VALIDACIÓN ANTES DE ENVIAR
    document.getElementById('formRecepcion').onsubmit = function() {
        var tipoEquipo = document.querySelector('select[name="equipo_tipo"]').value;
        if (!tipoEquipo) {
            alert('Por favor selecciona el tipo de equipo');
            openTab('tab-equipo');
            return false;
        }
        
        var problema = document.querySelector('textarea[name="problema_reportado"]').value;
        if (!problema.trim()) {
            alert('Por favor describe el problema reportado por el cliente');
            openTab('tab-estado');
            return false;
        }
        
        return confirm('¿Estás seguro de guardar la recepción completa?\n\nSe creará:\n• Cliente\n• Equipo con todas las especificaciones\n• Orden de servicio\n• PDF de recepción');
    };

    // INICIALIZAR AL CARGAR LA PÁGINA
    document.addEventListener('DOMContentLoaded', function() {
        // Verificar si hay un tipo de equipo seleccionado (en caso de volver)
        var tipoSelect = document.querySelector('select[name="equipo_tipo"]');
        if (tipoSelect.value) {
            mostrarCamposEspecificos(tipoSelect.value);
        }
        
        // Inicializar estado de funcionalidad
        toggleFuncionalidad();
    });
    </script>
</body>
</html>