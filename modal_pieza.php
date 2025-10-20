<?php
$id_orden = $_GET['id_orden'] ?? '';
?>
<div id="modalPiezas" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 2rem; border-radius: 10px; width: 90%; max-width: 600px; max-height: 90vh; overflow-y: auto;">
        <h3>Agregar Pieza a Cotización</h3>
        
        <form id="formPieza" method="POST" action="agregar_pieza_orden.php">
            <input type="hidden" name="id_orden" value="<?php echo $id_orden; ?>">
            
            <div class="form-group">
                <label>Nombre de la Pieza *</label>
                <input type="text" name="nombre_pieza" class="form-control" required 
                       placeholder="Ej: Pantalla LCD, Batería, Flex de carga...">
            </div>
            
            <div class="form-group">
                <label>Descripción</label>
                <textarea name="descripcion" class="form-control" rows="2" 
                          placeholder="Especificaciones técnicas, modelo compatible..."></textarea>
            </div>
            
            <div class="grid-3">
                <div class="form-group">
                    <label>Cantidad *</label>
                    <input type="number" name="cantidad" class="form-control" value="1" min="1" required>
                </div>
                
                <div class="form-group">
                    <label>Precio Unitario *</label>
                    <input type="number" name="precio_unitario" class="form-control" step="0.01" min="0" required>
                </div>
                
                <div class="form-group">
                    <label>Urgencia</label>
                    <select name="urgencia" class="form-control">
                        <option value="Baja">Baja</option>
                        <option value="Media" selected>Media</option>
                        <option value="Alta">Alta</option>
                        <option value="Crítica">Crítica</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label>Proveedor Sugerido</label>
                <input type="text" name="proveedor_sugerido" class="form-control" 
                       placeholder="Proveedor recomendado para esta pieza">
            </div>
            
            <div class="form-group">
                <label>Observaciones</label>
                <textarea name="observaciones" class="form-control" rows="2" 
                          placeholder="Notas internas para el técnico..."></textarea>
            </div>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" name="buscar_inventario" value="1" checked>
                    Buscar en inventario y sugerir existencias
                </label>
            </div>
            
            <div style="display: flex; justify-content: space-between; margin-top: 1rem;">
                <button type="button" class="btn btn-cancel" onclick="cerrarModalPiezas()">Cancelar</button>
                <button type="submit" class="btn">Agregar Pieza</button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalPiezas(ordenId) {
    document.getElementById('modalPiezas').style.display = 'block';
    document.querySelector('input[name="id_orden"]').value = ordenId;
}

function cerrarModalPiezas() {
    document.getElementById('modalPiezas').style.display = 'none';
}

// Cerrar modal al hacer click fuera
document.getElementById('modalPiezas').addEventListener('click', function(e) {
    if (e.target.id === 'modalPiezas') {
        cerrarModalPiezas();
    }
});
</script>