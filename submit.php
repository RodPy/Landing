
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require 'db_config.php';

    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $mensaje = $_POST['mensaje'];

    $stmt = $pdo->prepare("INSERT INTO contactos (nombre, email, mensaje) VALUES (?, ?, ?)");
    $stmt->execute([$nombre, $email, $mensaje]);

    echo "¡Mensaje enviado correctamente!";
}
?>
