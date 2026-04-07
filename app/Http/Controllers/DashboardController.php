<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Venta;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the professional dashboard with stats.
     */
    public function index()
    {
        // Estadísticas de Productos
        $totalProductos = Producto::count();
        $productosActivos = Producto::activos()->count();
        $stockTotal = Producto::activos()->sum('stock') ?? 0;
        $valorInventario = Producto::activos()->selectRaw('SUM(precio * stock) as total')->value('total') ?? 0;

        // Alertas de Stock
        $productosStockBajo = Producto::stockBajo()->limit(10)->get();

        // Ventas del Día
        $ventasHoy = Venta::completadas()->whereDate('fecha_venta', today())->count();
        $ventasHoyMonto = Venta::completadas()->whereDate('fecha_venta', today())->sum('total') ?? 0;

        // Ventas Semanales (Lunes a Domingo)
        $ventasSemana = Venta::completadas()->whereBetween('fecha_venta', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $ventasSemanaMonto = Venta::completadas()->whereBetween('fecha_venta', [now()->startOfWeek(), now()->endOfWeek()])->sum('total') ?? 0;

        // Productos más vendidos
        $productosMasVendidos = Producto::activos()
            ->withCount('detalleVentas')
            ->orderBy('detalle_ventas_count', 'desc')
            ->limit(10)
            ->get();

        // Ventas Recientes
        $ventasRecientes = Venta::completadas()->orderBy('fecha_venta', 'desc')->limit(10)->get();

        // Usuarios y Cajeros
        $totalUsuarios = User::count();
        $cajerosActivos = User::whereHas('roles', function ($q) {
            $q->where('name', 'cajero');
        })->where('activo', true)->count();

        return view('dashboard.index', compact(
            'totalProductos', 'productosActivos', 'stockTotal', 'valorInventario',
            'productosStockBajo', 'ventasHoy', 'ventasHoyMonto', 'ventasSemana', 
            'ventasSemanaMonto', 'productosMasVendidos', 'ventasRecientes',
            'totalUsuarios', 'cajerosActivos'
        ));
    }

    /**
     * Endpoint para exportar productos para el modo Offline (PWA).
     */
    public function offlineProducts()
    {
        $productos = Producto::activos()
            ->select('id', 'nombre', 'precio', 'stock', 'codigo_barras', 'categoria')
            ->get();

        return response()->json($productos);
    }

    /**
     * API endpoint para refrescar estadísticas dinámicamente.
     */
    public function estadisticas()
    {
        return response()->json([
            'ventas_hoy' => Venta::completadas()->whereDate('fecha_venta', today())->sum('total') ?? 0,
            'stock_total' => Producto::activos()->sum('stock') ?? 0,
            'productos_bajos' => Producto::stockBajo()->count(),
        ]);
    }
}
