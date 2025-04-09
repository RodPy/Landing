
<?php
require 'db_config.php';

$stmt = $pdo->query("SELECT * FROM contactos ORDER BY fecha_envio DESC");
$registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registros de Contacto</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 2rem;
            background-color: #f5f5f5;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #2196F3;
            color: white;
        }
        h2 {
            color: #333;
        }
    </style>
</head>
<body>
    <h2>Registros de Contacto</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Mensaje</th>
            <th>Fecha de Envío</th>
        </tr>
        <?php foreach ($registros as $fila): ?>
        <tr>
            <td><?php echo htmlspecialchars($fila['id']); ?></td>
            <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
            <td><?php echo htmlspecialchars($fila['email']); ?></td>
            <td><?php echo htmlspecialchars($fila['mensaje']); ?></td>
            <td><?php echo htmlspecialchars($fila['fecha_envio']); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
