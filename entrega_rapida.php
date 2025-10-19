<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrega Rápida - Taller Lykos</title>
    <style>
        /* Mismos estilos que recepcion_rapida.php */
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
        .btn { padding: 12px 25px; background: #27ae60; color: white; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; font-size: 1rem; }
        .btn:hover { background: #219a52; }
        .btn-large { padding: 15px 30px; font-size: 1.1rem; }
        .search-order { background: #e8f4fd; padding: 1rem; border-radius: 5px; margin-bottom: 1rem; }
        .order-results { max-height: 200px; overflow-y: auto; border: 1px solid #ddd; border-radius: 5px; margin-top: 10px; display: none; }
        .order-item { padding: 10px; border-bottom: 1px solid #eee; cursor: pointer; }
        .order-item:hover { background: #f8f9fa; }
        .order-info { background: #f8f9fa; padding: 1rem; border-radius: 5px; margin-bottom: 1rem; display: none; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Entrega Rápida - Taller Lykos</h1>
    </div>

    <?php include 'nav.php'; ?>

    <div class="container">
        <div class="card">
            <h2>Entrega Rápida de Equipo</h2>
            
            <!-- BUSCAR ORDEN -->
            <div class="search-order">
                <h3>Buscar Orden para Entrega</h3>
                <input type="text" id="searchOrden" class="form-control" placeholder="Buscar por folio, cliente o equipo..." onkeyup="buscarOrdenes()">
                <div id="orderResults" class="order-results"></div>
            </div>

            <!-- INFORMACIÓN DE LA ORDEN SELECCIONADA -->
            <div id="orderInfo" class="order-info">
                <h3>Información de la Orden</h3>
                <div id="orderDetails"></div>
                
                <form method="POST" action="guardar_entrega_rapida.php" id="formEntrega">
                    <input type="hidden" name="orden_id" id="orden_id">
                    
                    <div class="form-group">
                        <label class="required">Trabajo Realizado</label>
                        <textarea name="trabajo_realizado" class="form-control" rows="4" required placeholder="Describa el trabajo de reparación realizado..."></textarea>
                    </div>

                    <div class="form-group">
                        <label>Piezas Utilizadas</label>
                        <textarea name="piezas_utilizadas" class="form-control" rows="3" placeholder="Lista de piezas y repuestos utilizados..."></textarea>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="required">Costo Final</label>
                            <input type="number" name="costo_final" class="form-control" step="0.01" min="0" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Anticipo Recibido</label>
                            <input type="number" name="anticipo" class="form-control" step="0.01" min="0" value="0">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Observaciones Finales</label>
                        <textarea name="observaciones_finales" class="form-control" rows="3" placeholder="Observaciones adicionales para la entrega..."></textarea>
                    </div>

                    <div style="text-align: center; margin-top: 1.5rem;">
                        <button type="submit" class="btn btn-large">✅ Completar Entrega</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    // BUSCAR ÓRDENES LISTAS PARA ENTREGA
    function buscarOrdenes() {
        var query = document.getElementById('searchOrden').value;
        if (query.length < 2) {
            document.getElementById('orderResults').style.display = 'none';
            return;
        }
        
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'ajax_buscar_ordenes_entrega.php?q=' + encodeURIComponent(query), true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                document.getElementById('orderResults').innerHTML = xhr.responseText;
                document.getElementById('orderResults').style.display = 'block';
            }
        };
        xhr.send();
    }

    // SELECCIONAR ORDEN PARA ENTREGA
    function seleccionarOrden(ordenId, folio, cliente, equipo, costoEstimado) {
        document.getElementById('orden_id').value = ordenId;
        document.getElementById('orderInfo').style.display = 'block';
        
        // Mostrar detalles de la orden
        document.getElementById('orderDetails').innerHTML = `
            <p><strong>Folio:</strong> ${folio}</p>
            <p><strong>Cliente:</strong> ${cliente}</p>
            <p><strong>Equipo:</strong> ${equipo}</p>
            <p><strong>Costo Estimado:</strong> $${costoEstimado}</p>
        `;
        
        // Poner el costo estimado como valor por defecto
        document.querySelector('input[name="costo_final"]').value = costoEstimado;
        
        // Ocultar resultados de búsqueda
        document.getElementById('orderResults').style.display = 'none';
        document.getElementById('searchOrden').value = '';
        
        // Enfocar el primer campo del formulario
        document.querySelector('textarea[name="trabajo_realizado"]').focus();
    }

    // VALIDACIÓN ANTES DE ENTREGAR
    document.getElementById('formEntrega').onsubmit = function() {
        var costoFinal = document.querySelector('input[name="costo_final"]').value;
        var anticipo = document.querySelector('input[name="anticipo"]').value;
        
        if(parseFloat(anticipo) > parseFloat(costoFinal)) {
            alert('El anticipo no puede ser mayor al costo final');
            return false;
        }
        
        return confirm('¿Estás seguro de completar la entrega? Se generará el PDF de entrega.');
    };
    </script>
</body>
</html>