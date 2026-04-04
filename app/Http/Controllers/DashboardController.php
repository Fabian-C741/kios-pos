<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProductos = Producto::count();
        $productosActivos = Producto::where('activo', true)->count();
        $stockTotal = Producto::sum('stock');
        $valorInventario = Producto::selectRaw('SUM(precio * stock) as total')->value('total') ?? 0;

        $productosStockBajo = Producto::stockBajo()
            ->where('activo', true)
            ->orderBy('stock', 'asc')
            ->limit(10)
            ->get();

        $ventasHoy = Venta::completadas()->delDia()->count();
        $ventasHoyMonto = Venta::completadas()->delDia()->sum('total');

        $ventasSemana = Venta::completadas()
            ->whereBetween('fecha_venta', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->count();
        $ventasSemanaMonto = Venta::completadas()
            ->whereBetween('fecha_venta', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->sum('total');

        $productosMasVendidos = Producto::withCount(['detalleVentas as total_vendido' => function ($query) {
            $query->selectRaw('SUM(cantidad)');
        }])
            ->where('activo', true)
            ->orderByDesc('total_vendido')
            ->limit(10)
            ->get();

        $ventasRecientes = Venta::with('user')
            ->completadas()
            ->orderBy('fecha_venta', 'desc')
            ->limit(10)
            ->get();

        $usuariosActivos = User::where('activo', true)->count();
        $cajerosActivos = User::whereHas('roles', function ($q) {
            $q->where('name', 'cajero');
        })->where('activo', true)->count();

        return view('dashboard.index', compact(
            'totalProductos',
            'productosActivos',
            'stockTotal',
            'valorInventario',
            'productosStockBajo',
            'ventasHoy',
            'ventasHoyMonto',
            'ventasSemana',
            'ventasSemanaMonto',
            'productosMasVendidos',
            'ventasRecientes',
            'usuariosActivos',
            'cajerosActivos'
        ));
    }

    public function estadisticas(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', Carbon::now()->startOfMonth());
        $fechaFin = $request->get('fecha_fin', Carbon::now()->endOfMonth());

        $ventasPorDia = Venta::completadas()
            ->whereBetween('fecha_venta', [$fechaInicio, $fechaFin])
            ->selectRaw('DATE(fecha_venta) as fecha, COUNT(*) as cantidad, SUM(total) as total')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        $productosPorCategoria = Producto::where('activo', true)
            ->whereNotNull('categoria')
            ->selectRaw('categoria, COUNT(*) as cantidad, SUM(stock) as stock')
            ->groupBy('categoria')
            ->get();

        $metodosPago = Venta::completadas()
            ->whereBetween('fecha_venta', [$fechaInicio, $fechaFin])
            ->selectRaw('metodo_pago, COUNT(*) as cantidad, SUM(total) as total')
            ->groupBy('metodo_pago')
            ->get();

        $topVendedores = User::withCount(['ventas' => function ($query) use ($fechaInicio, $fechaFin) {
            $query->completadas()
                ->whereBetween('fecha_venta', [$fechaInicio, $fechaFin]);
        }])
            ->withSum(['ventas' => function ($query) use ($fechaInicio, $fechaFin) {
                $query->completadas()
                    ->whereBetween('fecha_venta', [$fechaInicio, $fechaFin]);
            }], 'total')
            ->orderBy('ventas_count', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'ventas_por_dia' => $ventasPorDia,
            'productos_por_categoria' => $productosPorCategoria,
            'metodos_pago' => $metodosPago,
            'top_vendedores' => $topVendedores,
        ]);
    }
}
