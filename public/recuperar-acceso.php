<?php
/**
 * SCRIPT DE RECUPERACIÓN DE UN SOLO USO
 * Este script resetea la clave del primer administrador a: admin123
 */

// 1. Cargar configuración desde .env de Laravel
$envPath = __DIR__ . '/../.env';
if (!file_exists($envPath)) {
    die("Error: No se encontró el archivo .env en $envPath");
}

$config = [];
$lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $line) {
    if (strpos(trim($line), '#') === 0) continue;
    list($name, $value) = explode('=', $line, 2);
    $config[trim($name)] = trim($value, '"\' ');
}

// 2. Conectar a la base de datos
$host = $config['DB_HOST'] ?? '127.0.0.1';
$db   = $config['DB_DATABASE'] ?? '';
$user = $config['DB_USERNAME'] ?? '';
$pass = $config['DB_PASSWORD'] ?? '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 3. Generar Hash de Laravel (Bcrypt)
    $nuevaClave = "admin123";
    $hash = password_hash($nuevaClave, PASSWORD_BCRYPT);

    // 4. Actualizar el primer usuario (ID 1 suele ser el admin)
    $stmt = $pdo->prepare("UPDATE users SET password = ? ORDER BY id ASC LIMIT 1");
    $stmt->execute([$hash]);

    if ($stmt->rowCount() > 0) {
        echo "<div style='font-family:sans-serif; text-align:center; margin-top:50px;'>";
        echo "<h1 style='color:green;'>✅ ACCESO RESTAURADO EXITOSAMENTE</h1>";
        echo "<p>Tu nueva clave es: <strong>$nuevaClave</strong></p>";
        echo "<p style='color:red;'><strong>⚠️ IMPORTANTE:</strong> Borrá este archivo (public/recuperar-acceso.php) de tu servidor AHORA MISMO por seguridad.</p>";
        echo "<a href='/login' style='padding:10px 20px; background:#4e73df; color:white; text-decoration:none; border-radius:5px;'>Ir al Login</a>";
        echo "</div>";
    } else {
        echo "No se encontró ningún usuario para actualizar.";
    }

} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}
