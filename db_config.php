
<?php
$host = 'localhost';
$db = 'u302504635_landing_db';
$user = 'u302504635_root';
$pass = 'a9G~7WbT:~';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
