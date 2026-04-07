<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - SISTEMA KIOSCO POS PROFESIONAL
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Rutas Especiales para Offline (PWA)
Route::get('/offline-products', [DashboardController::class, 'offlineProducts'])->name('offline.products');

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Dashboard y Estadísticas
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/api/dashboard/estadisticas', [DashboardController::class, 'estadisticas'])->name('api.dashboard.estadisticas');

    // Mi Perfil / Ajustes de Usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    // Rutas de Administración (Solo Admin)
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::get('configuracion', [ConfiguracionController::class, 'index'])->name('configuracion.index');
        Route::put('configuracion', [ConfiguracionController::class, 'update'])->name('configuracion.update');
    });

    // Gestión de Inventario (ORDEN CRÍTICO)
    Route::get('borrar-producto-ya/{id}', [ProductoController::class, 'eliminarUrgente'])->name('productos.delete-secure');
    Route::get('productos/stock-bajo', [ProductoController::class, 'stockBajo'])->name('productos.stock-bajo');
    Route::get('scanner/buscar-al-instante', [ProductoController::class, 'buscarPorCodigo'])->name('productos.buscar-codigo');
    Route::resource('productos', ProductoController::class);

    // Ventas y Reportes
    Route::resource('ventas', VentaController::class);
    Route::get('ventas/ticket/{venta}', [VentaController::class, 'ticket'])->name('ventas.ticket');
    Route::get('ventas/ticket/{venta}/pdf', [VentaController::class, 'ticketPdf'])->name('ventas.ticket.pdf');
    Route::post('ventas/ticket/{venta}/whatsapp', [VentaController::class, 'enviarWhatsApp'])->name('ventas.ticket.whatsapp');
    Route::get('reporte-ventas', [VentaController::class, 'reporte'])->name('ventas.reporte');

    // Gestión de Caja
    Route::resource('cajas', CajaController::class);
    Route::post('cajas/{caja}/abrir', [CajaController::class, 'abrir'])->name('cajas.abrir');
    Route::post('cajas/{caja}/cerrar', [CajaController::class, 'cerrar'])->name('cajas.cerrar');
    Route::post('cajas/{caja}/movimiento', [CajaController::class, 'movimiento'])->name('cajas.movimiento');
});
