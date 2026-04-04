<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\DetalleVenta;
use App\Models\Venta;
use App\Notifications\StockBajoNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->get('buscar');
        $categoria = $request->get('categoria');
        
        $productos = Producto::when($buscar, function ($query) use ($buscar) {
            return $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                    ->orWhere('codigo_barras', 'like', "%{$buscar}%");
            });
        })
        ->when($categoria, function ($query) use ($categoria) {
            return $query->where('categoria', $categoria);
        })
        ->orderBy('nombre')
        ->paginate(15);

        $categorias = Producto::whereNotNull('categoria')
            ->where('activo', true)
            ->distinct()
            ->pluck('categoria');

        return view('productos.index', compact('productos', 'categorias'));
    }

    public function create()
    {
        return view('productos.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'codigo_barras' => 'nullable|string|unique:productos,codigo_barras',
            'categoria' => 'nullable|string|max:100',
            'activo' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Producto::create($request->all());

        return redirect()->route('productos.index')
            ->with('success', 'Producto creado exitosamente');
    }

    public function show(Producto $producto)
    {
        return view('productos.show', compact('producto'));
    }

    public function edit(Producto $producto)
    {
        return view('productos.edit', compact('producto'));
    }

    public function update(Request $request, Producto $producto)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'codigo_barras' => ['nullable', 'string', Rule::unique('productos')->ignore($producto->id)],
            'categoria' => 'nullable|string|max:100',
            'activo' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $producto->update($request->all());

        return redirect()->route('productos.index')
            ->with('success', 'Producto actualizado exitosamente');
    }

    public function destroy(Producto $producto)
    {
        if ($producto->detalleVentas()->exists()) {
            $producto->update(['activo' => false]);
            return redirect()->route('productos.index')
                ->with('warning', 'Producto deshabilitado (tiene ventas asociadas)');
        }

        $producto->delete();

        return redirect()->route('productos.index')
            ->with('success', 'Producto eliminado exitosamente');
    }

    public function buscarPorCodigo(Request $request)
    {
        $codigo = $request->get('codigo');
        
        $producto = Producto::where('codigo_barras', $codigo)
            ->orWhere('id', $codigo)
            ->first();

        if (!$producto) {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }

        if (!$producto->activo) {
            return response()->json(['error' => 'Producto deshabilitado'], 400);
        }

        if ($producto->stock <= 0) {
            return response()->json(['error' => 'Sin stock disponible'], 400);
        }

        return response()->json($producto);
    }

    public function stockBajo()
    {
        $productos = Producto::stockBajo()
            ->where('activo', true)
            ->orderBy('stock', 'asc')
            ->get();

        return view('productos.stock-bajo', compact('productos'));
    }

    public function enviarNotificacionStockBajo()
    {
        $productosStockBajo = Producto::stockBajo()
            ->where('activo', true)
            ->get();

        foreach ($productosStockBajo as $producto) {
            $admins = \App\Models\User::whereHas('roles', function ($q) {
                $q->where('name', 'admin');
            })->get();

            foreach ($admins as $admin) {
                $admin->notify(new StockBajoNotification($producto));
            }
        }

        return redirect()->back()
            ->with('success', 'Notificaciones de stock bajo enviadas');
    }
}
