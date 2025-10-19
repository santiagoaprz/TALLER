<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Cliente - Taller Lykos</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
        .header { background: #2c3e50; color: white; padding: 1rem; text-align: center; }
        .nav { background: #34495e; padding: 1rem; display: flex; justify-content: center; gap: 10px; flex-wrap: wrap; }
        .nav a { padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 5px; }
        .nav a:hover { background: #2980b9; }
        .container { max-width: 800px; margin: 2rem auto; padding: 0 1rem; }
        .card { background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: bold; color: #2c3e50; }
        .form-control { width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem; }
        .btn { padding: 12px 25px; background: #27ae60; color: white; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background: #219a52; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Nuevo Cliente - Taller Lykos</h1>
    </div>

    <?php include 'nav.php'; ?>

    <div class="container">
        <div class="card">
            <h2>Registrar Nuevo Cliente</h2>
            <form method="POST" action="guardar_cliente.php">
                <div class="grid-2">
                    <div class="form-group">
                        <label>Nombre Completo *</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Teléfono *</label>
                        <input type="tel" name="telefono" class="form-control" required>
                    </div>
                </div>
                
                <div class="grid-2">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label>RFC</label>
                        <input type="text" name="rfc" class="form-control">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Dirección</label>
                    <textarea name="direccion" class="form-control" rows="3"></textarea>
                </div>
                
                <button type="submit" class="btn">Guardar Cliente</button>
                <a href="clientes.php" class="btn" style="background: #95a5a6;">Cancelar</a>
            </form>
        </div>
    </div>
</body>
</html>