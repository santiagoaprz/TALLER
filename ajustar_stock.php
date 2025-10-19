<?php
include 'config.php';

if(!isset($_GET['pieza'])) {
    header("Location: inventario.php");
    exit;
}

$id_pieza = $_GET['pieza'];
$stmt = $pdo->prepare("SELECT * FROM inventario WHERE id_pieza = ?");
$stmt->execute([$id_pieza]);
$pieza = $stmt->fetch();

if(!$pieza) {
    header("Location: inventario.php");
    exit;
}

if($_POST) {
    $nueva_cantidad = $_POST['nueva_cantidad'];
    $tipo_ajuste = $_POST['tipo_ajuste'];
    $motivo = sanitizar($_POST['motivo']);
    
    // ACTUALIZAR STOCK
    $stmt = $pdo->prepare("UPDATE inventario SET cantidad_stock = ? WHERE id_pieza = ?");
    $stmt->execute([$nueva_cantidad, $id_pieza]);
    
    // REGISTRAR EN BITÁCORA (podrías crear una tabla para esto)
    
    header("Location: inventario.php?mensaje=" . urlencode("Stock ajustado correctamente"));
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajustar Stock - Taller Lykos</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
        .header { background: #2c3e50; color: white; padding: 1rem; text-align: center; }
        .nav { background: #34495e; padding: 1rem; display: flex; justify-content: center; gap: 10px; flex-wrap: wrap; }
        .nav a { padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 5px; }
        .nav a:hover { background: #2980b9; }
        .container { max-width: 600px; margin: 2rem auto; padding: 0 1rem; }
        .card { background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: bold; color: #2c3e50; }
        .form-control { width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem; }
        .btn { padding: 10px 20px; background: #27ae60; color: white; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background: #219a52; }
        .info-box { background: #e8f4fd; padding: 1rem; border-radius: 5px; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Ajustar Stock - Taller Lykos</h1>
    </div>

    <?php include 'nav.php'; ?>

    <div class="container">
        <div class="card">
            <h2>Ajustar Stock de Pieza</h2>
            
            <div class="info-box">
                <p><strong>Pieza:</strong> <?php echo $pieza['nombre_pieza']; ?></p>
                <p><strong>Categoría:</strong> <?php echo $pieza['categoria']; ?></p>
                <p><strong>Stock Actual:</strong> <?php echo $pieza['cantidad_stock']; ?> unidades</p>
                <p><strong>Stock Mínimo:</strong> <?php echo $pieza['stock_minimo']; ?> unidades</p>
            </div>

            <form method="POST" action="ajustar_stock.php?pieza=<?php echo $id_pieza; ?>">
                <div class="form-group">
                    <label>Nueva Cantidad en Stock *</label>
                    <input type="number" name="nueva_cantidad" class="form-control" value="<?php echo $pieza['cantidad_stock']; ?>" min="0" required>
                </div>

                <div class="form-group">
                    <label>Tipo de Ajuste</label>
                    <select name="tipo_ajuste" class="form-control">
                        <option value="entrada">Entrada de Mercancía</option>
                        <option value="salida">Salida por Uso/Venta</option>
                        <option value="ajuste">Ajuste de Inventario</option>
                        <option value="devolucion">Devolución</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Motivo del Ajuste</label>
                    <textarea name="motivo" class="form-control" rows="3" placeholder="Explique el motivo del ajuste de stock..." required></textarea>
                </div>

                <button type="submit" class="btn">Actualizar Stock</button>
                <a href="inventario.php" class="btn" style="background: #95a5a6;">Cancelar</a>
            </form>
        </div>
    </div>
</body>
</html>