<?php
include 'config.php';

if(isset($_GET['q'])) {
    $query = $_GET['q'];
    $stmt = $pdo->prepare("
        SELECT o.*, c.nombre as cliente, e.marca, e.modelo, e.tipo_equipo 
        FROM ordenes_servicio o 
        JOIN equipos e ON o.id_equipo = e.id_equipo 
        JOIN clientes c ON e.id_cliente = c.id_cliente 
        WHERE o.activa = 1 
        AND o.estado_orden != 'Entregado'
        AND (o.folio LIKE ? OR c.nombre LIKE ? OR e.marca LIKE ? OR e.modelo LIKE ?)
        ORDER BY o.fecha_creacion DESC 
        LIMIT 10
    ");
    $stmt->execute(["%$query%", "%$query%", "%$query%", "%$query%"]);
    $ordenes = $stmt->fetchAll();
    
    if(count($ordenes) > 0) {
        foreach($ordenes as $orden) {
            $equipo = "{$orden['marca']} {$orden['modelo']} ({$orden['tipo_equipo']})";
            $costo = number_format($orden['costo_estimado'], 2);
            
            echo "<div class='order-item' onclick='seleccionarOrden({$orden['id_orden']}, \"{$orden['folio']}\", \"{$orden['cliente']}\", \"$equipo\", \"$costo\")'>";
            echo "<strong>{$orden['folio']}</strong> - {$orden['cliente']}<br>";
            echo "<small>$equipo | Est: {$orden['estado_orden']} | Costo: $$costo</small>";
            echo "</div>";
        }
    } else {
        echo "<div class='order-item'>No se encontraron órdenes</div>";
    }
}
?>