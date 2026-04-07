<?php
/**
 * MIGRADO PROFESIONAL DE BASE DE DATOS - KIOSCO POS
 * Activa email opcional y añade campo username.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

$basePath = dirname(__DIR__) . '/';
require $basePath . 'vendor/autoload.php';
$app = require_once $basePath . 'bootstrap/app.php';

// BOOTSTRAP DEL KERNEL (Esto evita el error de Facade root)
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

echo "<h1>Actualizando Base de Datos...</h1>";

try {
    // 1. Añadir username y hacer email nullable
    Schema::table('users', function (Blueprint $table) {
        if (!Schema::hasColumn('users', 'username')) {
            $table->string('username')->unique()->after('name')->nullable();
            echo "[OK] Columna 'username' añadida.<br>";
        }
        
        // Hacer email nullable (Sintaxis compatible con MariaDB/MySQL compartida)
        DB::statement('ALTER TABLE users MODIFY email VARCHAR(255) NULL');
        echo "[OK] Columna 'email' ahora es opcional.<br>";
    });

    // 2. Rellenar usernames vacíos basándose en el email
    $users = DB::table('users')->whereNull('username')->get();
    foreach ($users as $user) {
        $username = explode('@', $user->email)[0];
        DB::table('users')->where('id', $user->id)->update(['username' => $username]);
    }
    echo "[OK] Usernames generados para usuarios existentes.<br>";

    // 3. Crear tabla de configuraciones si no existe
    if (!Schema::hasTable('configuraciones')) {
        Schema::create('configuraciones', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
        
        // Valores por defecto
        DB::table('configuraciones')->insert([
            ['key' => 'nombre_kiosco', 'value' => 'Software para Kiosco'],
            ['key' => 'moneda', 'value' => '$'],
            ['key' => 'logo', 'value' => ''],
            ['key' => 'direccion', 'value' => ''],
            ['key' => 'telefono', 'value' => ''],
        ]);
        echo "[OK] Tabla 'configuraciones' creada con valores iniciales.<br>";
    }

} catch (\Exception $e) {
    echo "<b style='color:red'>[ERROR] Fallo en la migración: " . $e->getMessage() . "</b><br>";
}

echo "<h2>¡ACTUALIZACIÓN COMPLETADA!</h2>";
echo "<p>Ya puedes borrar este archivo y seguir con los siguientes pasos.</p>";
