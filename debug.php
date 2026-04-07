<?php
/**
 * Script de Diagnóstico para Sistema Kiosco
 * Sube este archivo a public_html/ para ver qué está fallando en el servidor.
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Diagnóstico de Sistema Kiosco</h1>";

// 1. Verificar Autoload
echo "<h3>1. Verificando dependencias (Composer):</h3>";
if (file_exists('vendor/autoload.php')) {
    echo "<span style='color:green'>[OK] Autoload encontrado.</span><br>";
    require 'vendor/autoload.php';
} else {
    echo "<span style='color:red'>[ERROR] No se encuentra la carpeta vendor/. Debes subirla o ejecutar 'composer install'.</span><br>";
}

// 2. Cargar Laravel para probar base de datos
try {
    echo "<h3>2. Cargando entorno (.env):</h3>";
    $app = require_once 'bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    );
    
    if (env('APP_KEY')) {
        echo "<span style='color:green'>[OK] APP_KEY configurada.</span><br>";
    } else {
        echo "<span style='color:red'>[ERROR] APP_KEY vacía en el .env.</span><br>";
    }

    // 3. Probar Base de Datos
    echo "<h3>3. Probando Base de Datos:</h3>";
    try {
        \Illuminate\Support\Facades\DB::connection()->getPdo();
        echo "<span style='color:green'>[OK] Conexión a Base de Datos exitosa.</span><br>";
        
        $tables = ['users', 'roles', 'ventas', 'productos'];
        foreach ($tables as $table) {
            if (\Illuminate\Support\Facades\Schema::hasTable($table)) {
                echo "[OK] Tabla '$table' existe.<br>";
            } else {
                echo "<span style='color:red'>[ERROR] Tabla '$table' NO existe. Debes ejecutar php artisan migrate.</span><br>";
            }
        }
    } catch (\Exception $e) {
        echo "<span style='color:red'>[ERROR] Fallo de conexión: " . $e->getMessage() . "</span><br>";
    }

    // 4. Leer logs de error
    echo "<h3>4. Últimos errores en Laravel Log:</h3>";
    $logFile = 'storage/logs/laravel.log';
    if (file_exists($logFile)) {
        $logs = shell_exec('tail -n 20 ' . escapeshellarg($logFile));
        echo "<pre style='background:#f4f4f4; padding:10px;'>" . htmlspecialchars($logs) . "</pre>";
    } else {
        echo "No se encontró el archivo de logs en storage/logs/laravel.log";
    }

} catch (\Exception $e) {
    echo "<div style='background:red; color:white; padding:10px;'>";
    echo "ERROR CRÍTICO AL CARGAR LARAVEL:<br>";
    echo $e->getMessage() . "<br>";
    echo "Archivo: " . $e->getFile() . " en línea " . $e->getLine();
    echo "</div>";
}
