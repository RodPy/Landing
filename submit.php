<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require 'db_config.php';

    // Sanitizar entradas
    $nombre     = htmlspecialchars(trim($_POST['nombre'] ?? ''));
    $apellido   = htmlspecialchars(trim($_POST['apellido'] ?? ''));
    $email      = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $telefono   = htmlspecialchars(trim($_POST['telefono'] ?? ''));
    $area       = htmlspecialchars(trim($_POST['area'] ?? ''));
    $titulo     = htmlspecialchars(trim($_POST['titulo'] ?? ''));
    $descripcion = htmlspecialchars(trim($_POST['descripcion'] ?? ''));

    // Validar campos obligatorios
    if (!$nombre || !$apellido || !$email || !$telefono || !$area || !$titulo || !$descripcion) {
        http_response_code(400);
        echo "Todos los campos son obligatorios.";
        exit;
    }

    // Insertar en la base de datos
    $stmt = $pdo->prepare("INSERT INTO consultas (nombre, apellido, email, telefono, area, titulo, descripcion) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$nombre, $apellido, $email, $telefono, $area, $titulo, $descripcion]);

    // Redirigir con mensaje de éxito
    header("Location: index.html?success=true");
    exit;
}
?>
