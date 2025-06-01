<?php

require 'db_config.php';
// session_start();
// if (!isset($_SESSION['logueado']) || $_SESSION['logueado'] !== true) {
//     header("Location: admin.php");
//     exit;
// }

// Actualización de estado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['estado'])) {
    $id = (int) $_POST['id'];
    $estado = strtolower(trim($_POST['estado']));
    $validos = ['pendiente', 'proceso', 'cerrado'];

    if (in_array($estado, $validos, true)) {
        $stmt = $pdo->prepare("UPDATE consultas SET estado = ? WHERE id = ?");
        $stmt->execute([$estado, $id]);
    }

    header("Location: " . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}


// $stmt = $pdo->query("SELECT * FROM consultas ORDER BY fecha DESC");
// $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Construir filtro dinámico
// Filtros GET
$condiciones = [];
$params = [];

if (!empty($_GET['nombre'])) {
    $condiciones[] = "nombre LIKE :nombre";
    $params[':nombre'] = '%' . $_GET['nombre'] . '%';
}
if (!empty($_GET['email'])) {
    $condiciones[] = "email LIKE :email";
    $params[':email'] = '%' . $_GET['email'] . '%';
}
if (!empty($_GET['telefono'])) {
    $condiciones[] = "telefono LIKE :telefono";
    $params[':telefono'] = '%' . $_GET['telefono'] . '%';
}
if (!empty($_GET['area'])) {
    $condiciones[] = "area = :area";
    $params[':area'] = $_GET['area'];
}
if (!empty($_GET['estado'])) {
    $condiciones[] = "estado = :estado";
    $params[':estado'] = $_GET['estado'];
}
if (!empty($_GET['desde'])) {
    $condiciones[] = "fecha >= :desde";
    $params[':desde'] = $_GET['desde'] . ' 00:00:00';
}
if (!empty($_GET['hasta'])) {
    $condiciones[] = "fecha <= :hasta";
    $params[':hasta'] = $_GET['hasta'] . ' 23:59:59';
}

$sql = "SELECT * FROM consultas";
if ($condiciones) {
    $sql .= " WHERE " . implode(" AND ", $condiciones);
}
$sql .= " ORDER BY fecha DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Descarga CSV
if (isset($_GET['descargar']) && $_GET['descargar'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=consultas.csv');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Nombre', 'Apellido', 'Email', 'Teléfono', 'Área', 'Título', 'Descripción', 'Fecha', 'Estado']);

    foreach ($registros as $fila) {
        fputcsv($output, [
            $fila['id'],
            $fila['nombre'],
            $fila['apellido'],
            $fila['email'],
            $fila['telefono'],
            $fila['area'],
            $fila['titulo'],
            $fila['descripcion'],
            $fila['fecha'],
            $fila['estado'],
        ]);
    }

    fclose($output);
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Consultas</title>
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
            background-color: #264d2c;
            color: white;
        }
        h2 {
            color: #333;
        }
        .form-filtros input, .form-filtros select {
            margin-right: 0.5rem;
            padding: 0.4rem;
        }
        .descargar {
            margin-top: 1rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    
    <h2>Consultas Recibidas</h2>

    <form method="GET" class="form-filtros">
        <input type="text" name="nombre" placeholder="Buscar por nombre" value="<?= htmlspecialchars($_GET['nombre'] ?? '') ?>">
        <input type="text" name="email" placeholder="Buscar por email" value="<?= htmlspecialchars($_GET['email'] ?? '') ?>">
        <input type="text" name="telefono" placeholder="Buscar por teléfono" value="<?= htmlspecialchars($_GET['telefono'] ?? '') ?>">

        <select name="area">
            <option value="">Todas las áreas</option>
            <option value="tecnico" <?= ($_GET['area'] ?? '') === 'tecnico' ? 'selected' : '' ?>>Técnico</option>
            <option value="ambiental" <?= ($_GET['area'] ?? '') === 'ambiental' ? 'selected' : '' ?>>Ambiental</option>
            <option value="social" <?= ($_GET['area'] ?? '') === 'social' ? 'selected' : '' ?>>Social</option>
            <option value="legal" <?= ($_GET['area'] ?? '') === 'legal' ? 'selected' : '' ?>>Legal</option>
            <option value="otro" <?= ($_GET['area'] ?? '') === 'otro' ? 'selected' : '' ?>>Otro</option>
        </select>

        <select name="estado">
            <option value="">Todos los estados</option>
            <option value="pendiente" <?= ($_GET['estado'] ?? '') === 'pendiente' ? 'selected' : '' ?>>🟡 pendiente</option>
            <option value="proceso" <?= ($_GET['estado'] ?? '') === 'proceso' ? 'selected' : '' ?>>🔵 en proceso</option>
            <option value="cerrado" <?= ($_GET['estado'] ?? '') === 'cerrado' ? 'selected' : '' ?>>✅ cerrado</option>
        </select>

        <input type="date" name="desde" value="<?= htmlspecialchars($_GET['desde'] ?? '') ?>">
        <input type="date" name="hasta" value="<?= htmlspecialchars($_GET['hasta'] ?? '') ?>">
        <button type="submit">Filtrar</button>
    </form>

    <form method="GET" class="descargar">
        <?php foreach ($_GET as $key => $value): ?>
            <?php if ($key !== 'descargar'): ?>
                <input type="hidden" name="<?= htmlspecialchars($key) ?>" value="<?= htmlspecialchars($value) ?>">
            <?php endif; ?>
        <?php endforeach; ?>
        <input type="hidden" name="descargar" value="csv">
        <button type="submit">Descargar CSV</button>
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Email</th>
            <th>Teléfono</th>
            <th>Área</th>
            <th>Título</th>
            <th>Descripción</th>
            <th>Fecha</th>
            <th>Estado</th>
        </tr>
        <?php foreach ($registros as $fila): ?>
        <tr>
            <td><?= htmlspecialchars($fila['id']) ?></td>
            <td><?= htmlspecialchars($fila['nombre']) ?></td>
            <td><?= htmlspecialchars($fila['apellido']) ?></td>
            <td><?= htmlspecialchars($fila['email']) ?></td>
            <td><?= htmlspecialchars($fila['telefono']) ?></td>
            <td><?= htmlspecialchars($fila['area']) ?></td>
            <td><?= htmlspecialchars($fila['titulo']) ?></td>
            <td><?= nl2br(htmlspecialchars($fila['descripcion'])) ?></td>
            <td><?= htmlspecialchars($fila['fecha']) ?></td>
            <td>
                <form method="POST" style="margin:0;">
                    <input type="hidden" name="id" value="<?= $fila['id'] ?>">
                    <select name="estado" onchange="this.form.submit()" style="color:<?= $fila['estado'] === 'cerrado' ? 'green' : ($fila['estado'] === 'proceso' ? 'blue' : 'orange') ?>;">
                        <option value="pendiente" <?= $fila['estado'] === 'pendiente' ? 'selected' : '' ?>>🟡 pendiente</option>
                        <option value="proceso" <?= $fila['estado'] === 'proceso' ? 'selected' : '' ?>>🔵 en proceso</option>
                        <option value="cerrado" <?= $fila['estado'] === 'cerrado' ? 'selected' : '' ?>>✅ cerrado</option>
                    </select>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>