<?php
include 'config.php';

if(!isset($_POST['confirmar'])) {
    // MOSTRAR PÁGINA DE CONFIRMACIÓN
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Confirmar Limpieza - Taller Lykos</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; padding: 2rem; }
            .container { max-width: 600px; margin: 0 auto; background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 1rem; border-radius: 5px; margin: 1rem 0; }
            .btn { padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; }
            .btn-danger { background: #e74c3c; color: white; }
            .btn-secondary { background: #95a5a6; color: white; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>⚠️ Confirmar Limpieza de Base de Datos</h1>
            
            <div class="warning">
                <h3>¡ADVERTENCIA!</h3>
                <p>Estás a punto de <strong>eliminar todos los datos de prueba</strong> de la base de datos.</p>
                <p><strong>Esta acción no se puede deshacer.</strong></p>
            </div>
            
            <h3>¿Qué se eliminará?</h3>
            <ul>
                <li>Todos los clientes (excepto "Cliente Mostrador")</li>
                <li>Todos los equipos registrados</li>
                <li>Todas las órdenes de servicio</li>
                <li>Todo el historial de seguimiento</li>
                <li>Todo el inventario de piezas</li>
                <li>Todos los registros de gastos</li>
            </ul>
            
            <p><strong>¿Estás seguro de que quieres continuar?</strong></p>
            
            <form method="POST">
                <button type="submit" name="confirmar" value="si" class="btn btn-danger">✅ Sí, limpiar base de datos</button>
                <a href="index.php" class="btn btn-secondary">❌ Cancelar y volver al sistema</a>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// SI SE CONFIRMA, PROCEDER CON LA LIMPIEZA
if($_POST['confirmar'] === 'si') {
    
    // DESACTIVAR TEMPORALMENTE LAS CLAVES FORÁNEAS
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

    try {
        $pdo->beginTransaction();
        
        echo "<!DOCTYPE html>";
        echo "<html lang='es'>";
        echo "<head>";
        echo "<meta charset='UTF-8'>";
        echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
        echo "<title>Limpieza Completada - Taller Lykos</title>";
        echo "<style>";
        echo "* { margin: 0; padding: 0; box-sizing: border-box; }";
        echo "body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; padding: 2rem; }";
        echo ".container { max-width: 600px; margin: 0 auto; background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }";
        echo ".success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 1rem; border-radius: 5px; margin: 1rem 0; }";
        echo ".btn { padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 5px; display: inline-block; }";
        echo "</style>";
        echo "</head>";
        echo "<body>";
        echo "<div class='container'>";
        echo "<h1>Limpieza de Base de Datos Completada</h1>";
        
        // 1. LIMPIAR SEGUIMIENTO
        $stmt = $pdo->query("DELETE FROM seguimiento_orden");
        echo "<p>✅ Seguimientos eliminados: " . $stmt->rowCount() . "</p>";
        
        // 2. LIMPIAR ÓRDENES DE SERVICIO
        $stmt = $pdo->query("DELETE FROM ordenes_servicio");
        echo "<p>✅ Órdenes de servicio eliminadas: " . $stmt->rowCount() . "</p>";
        
        // 3. LIMPIAR EQUIPOS
        $stmt = $pdo->query("DELETE FROM equipos");
        echo "<p>✅ Equipos eliminados: " . $stmt->rowCount() . "</p>";
        
        // 4. LIMPIAR CLIENTES (excepto Cliente Mostrador)
        $stmt = $pdo->query("DELETE FROM clientes WHERE nombre != 'Cliente Mostrador'");
        echo "<p>✅ Clientes eliminados: " . $stmt->rowCount() . "</p>";
        
        // 5. LIMPIAR INVENTARIO
        $stmt = $pdo->query("DELETE FROM inventario");
        echo "<p>✅ Inventario limpiado: " . $stmt->rowCount() . "</p>";
        
        // 6. LIMPIAR GASTOS
        $stmt = $pdo->query("DELETE FROM gastos");
        echo "<p>✅ Gastos eliminados: " . $stmt->rowCount() . "</p>";
        
        // 7. REINICIAR AUTO-INCREMENTOS
        $tablas = ['clientes', 'equipos', 'ordenes_servicio', 'seguimiento_orden', 'inventario', 'proveedores', 'gastos'];
        
        foreach($tablas as $tabla) {
            $pdo->exec("ALTER TABLE $tabla AUTO_INCREMENT = 1");
            echo "<p>✅ Auto-increment reiniciado para: $tabla</p>";
        }
        
        $pdo->commit();
        
        echo "<div class='success'>";
        echo "<h3>¡Base de datos limpiada exitosamente! 🎉</h3>";
        echo "<p>Ahora puedes comenzar con datos reales de tu taller.</p>";
        echo "</div>";
        
        echo "<br>";
        echo "<a href='index.php' class='btn'>🏠 Ir al Sistema Principal</a>";
        echo "&nbsp;&nbsp;";
        echo "<a href='recepcion_rapida_mejorada.php' class='btn' style='background: #27ae60;'>🚀 Comenzar Recepción Rápida</a>";
        
    } catch(Exception $e) {
        // SOLO HACER rollBack() SI HAY UNA TRANSACCIÓN ACTIVA
        try {
            $pdo->rollBack();
        } catch(Exception $e2) {
            // IGNORAR ERROR DE rollBack SI NO HAY TRANSACCIÓN
        }
        echo "<p style='color: red;'>❌ Error durante la limpieza: " . $e->getMessage() . "</p>";
    }

    // REACTIVAR CLAVES FORÁNEAS
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    
    echo "</div>";
    echo "</body>";
    echo "</html>";
}
?>