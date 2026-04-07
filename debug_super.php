<?php
/**
 * Script de Super Diagnóstico y Limpieza para Sistema Kiosco
 * Sube este archivo a la carpeta public/ de tu servidor.
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Super Diagnóstico de Sistema Kiosco</h1>";

$basePath = dirname(__DIR__) . '/';

// 1. Verificar Cache de Configuración (El principal culpable)
echo "<h3>1. Verificando Cache de Laravel:</h3>";
$configCache = $basePath . 'bootstrap/cache/config.php';
if (file_exists($configCache)) {
    echo "<span style='color:orange'>[AVISO] Se detectó un archivo de configuración en caché. Esto impide que Laravel lea el .env.</span><br>";
    if (unlink($configCache)) {
        echo "<span style='color:green'>[OK] Archivo de caché ELIMINADO con éxito.</span><br>";
    } else {
        echo "<span style='color:red'>[ERROR] No se pudo eliminar el archivo de caché. Borra manualmente: bootstrap/cache/config.php</span><br>";
    }
} else {
    echo "[OK] No hay caché de configuración detectada.<br>";
}

// 2. Leer .env directamente por PHP
echo "<h3>2. Verificando el archivo .env real:</h3>";
$envFile = $basePath . '.env';
if (file_exists($envFile)) {
    $content = file_get_contents($envFile);
    if (preg_match('/APP_KEY=(.+)/', $content, $matches)) {
        echo "<span style='color:green'>[OK] La APP_KEY está escrita en el archivo .env: </span> <code>" . htmlspecialchars($matches[1]) . "</code><br>";
    } else {
        echo "<span style='color:red'>[ERROR] La APP_KEY NO está escrita en el archivo .env.</span><br>";
    }
} else {
    echo "<span style='color:red'>[ERROR] No existe el archivo .env en la raíz ($basePath).</span><br>";
}

// 3. Probar Laravel
try {
    echo "<h3>3. Probando Laravel Boot:</h3>";
    $app = require_once $basePath . 'bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    
    // Forzar recarga de config
    $app->make('config')->set('app.key', 'base64:9S8V1Z+6hB+xR4bGhXyJz/4v8Z/w7wY+OqKk7o9p1lU=');
    
    if (env('APP_KEY')) {
        echo "<span style='color:green'>[OK] Laravel ahora reconoce la APP_KEY.</span><br>";
    } else {
        echo "<span style='color:red'>[ERROR] Laravel sigue sin reconocer la APP_KEY a pesar del archivo. Esto puede ser por OPcache del servidor.</span><br>";
    }

    // 4. Probar Base de Datos
    echo "<h3>4. Base de Datos:</h3>";
    try {
        \Illuminate\Support\Facades\DB::connection()->getPdo();
        echo "<span style='color:green'>[OK] Conexión establecida.</span><br>";
    } catch (\Exception $e) {
        echo "<span style='color:red'>[FALLO] " . $e->getMessage() . "</span><br>";
    }

} catch (\Exception $e) {
    echo "<div style='background:red; color:white; padding:10px;'>ERROR CRÍTICO:<br>" . $e->getMessage() . "</div>";
}
