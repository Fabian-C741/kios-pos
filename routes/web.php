<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)->middleware('signed')->name('verification.verify');
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
            Route::resource('users', UserController::class)->names(['index' => 'users.index', 'create' => 'users.create', 'store' => 'users.store', 'edit' => 'users.edit', 'update' => 'users.update', 'destroy' => 'users.destroy']);
            Route::resource('cajas', CajaController::class);
        });

        Route::resource('productos', ProductoController::class)->names([
            'index' => 'productos.index',
            'create' => 'productos.create',
            'store' => 'productos.store',
            'show' => 'productos.show',
            'edit' => 'productos.edit',
            'update' => 'productos.update',
            'destroy' => 'productos.destroy',
        ]);
        
        Route::get('productos/stock-bajo', [ProductoController::class, 'stockBajo'])->name('productos.stock-bajo');
        Route::post('productos/buscar-codigo', [ProductoController::class, 'buscarPorCodigo'])->name('productos.buscar-codigo');
        Route::post('productos/notificar-stock-bajo', [ProductoController::class, 'enviarNotificacionStockBajo'])->name('productos.notificar-stock-bajo');

        Route::resource('ventas', VentaController::class)->names([
            'index' => 'ventas.index',
            'create' => 'ventas.create',
            'store' => 'ventas.store',
            'show' => 'ventas.show',
            'edit' => 'ventas.edit',
            'update' => 'ventas.update',
            'destroy' => 'ventas.destroy',
        ]);
        
        Route::get('ventas/ticket/{venta}', [VentaController::class, 'ticket'])->name('ventas.ticket');
        Route::get('ventas/ticket/{venta}/pdf', [VentaController::class, 'ticketPdf'])->name('ventas.ticket.pdf');
        Route::post('ventas/ticket/{venta}/whatsapp', [VentaController::class, 'enviarWhatsApp'])->name('ventas.ticket.whatsapp');
        Route::get('ventas/reporte', [VentaController::class, 'reporte'])->name('ventas.reporte');

        Route::get('cajas', [CajaController::class, 'index'])->name('cajas.index');
        Route::get('cajas/{caja}', [CajaController::class, 'show'])->name('cajas.show');
        Route::post('cajas/{caja}/abrir', [CajaController::class, 'abrir'])->name('cajas.abrir');
        Route::post('cajas/{caja}/cerrar', [CajaController::class, 'cerrar'])->name('cajas.cerrar');
        Route::post('cajas/{caja}/movimiento', [CajaController::class, 'movimiento'])->name('cajas.movimiento');

        Route::get('api/dashboard/estadisticas', [DashboardController::class, 'estadisticas'])->name('api.dashboard.estadisticas');
    });
});
