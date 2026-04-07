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
        $buscar = trim($request->get('buscar'));
        $categoria = $request->get('categoria');
        
        $productos = Producto::query()
            ->when($buscar, function ($query) use ($buscar) {
                return $query->where(function ($q) use ($buscar) {
                    $q->where('nombre', 'like', "%{$buscar}%")
                        ->orWhere('codigo_barras', 'like', "%{$buscar}%")
                        ->orWhere('id', $buscar);
                });
            })
            ->when($categoria, function ($query) use ($categoria) {
                return $query->where('categoria', $categoria);
            })
            ->orderBy('stock', 'asc') // Ver primero los que se están acabando
            ->paginate(20)
            ->withQueryString();

        $categorias = Producto::whereNotNull('categoria')
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
            'nombre' => 'required|string|max:255|unique:productos,nombre',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|numeric|min:0',
            'stock_minimo' => 'required|numeric|min:0',
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
            'nombre' => ['required', 'string', 'max:255', Rule::unique('productos')->ignore($producto->id)],
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|numeric|min:0',
            'stock_minimo' => 'required|numeric|min:0',
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
        // Usamos SoftDeletes: desaparece de la vista pero se mantiene para integridad de ventas
        $producto->delete();

        return redirect()->route('productos.index')
            ->with('success', 'Producto eliminado exitosamente.');
    }

    /**
     * MÉTODO DE EMERGENCIA: Borrado directo por ID
     */
    public function eliminarUrgente($id)
    {
        $producto = Producto::findOrFail($id);
        
        $producto->delete();

        return redirect()->route('productos.index')
            ->with('success', 'Producto eliminado de la lista exitosamente.');
    }

    public function buscarPorCodigo(Request $request)
    {
        try {
            // Aceptamos 'q' o 'codigo' para evadir firewalls de hosting
            $codigo = trim($request->get('q') ?? $request->get('codigo'));
            
            if (!$codigo) {
                return response()->json(['success' => false, 'error' => 'Código no proporcionado'], 422);
            }

            $producto = Producto::where('codigo_barras', $codigo)
                ->orWhere('id', $codigo)
                ->first();

            if (!$producto) {
                return response()->json(['success' => false, 'error' => 'Producto no encontrado en inventario'], 200);
            }

            if (!$producto->activo) {
                return response()->json(['success' => false, 'error' => 'Este producto está marcado como INACTIVO'], 200);
            }

            return response()->json([
                'success' => true,
                'id' => $producto->id,
                'nombre' => $producto->nombre,
                'precio' => (float)($producto->precio ?? 0),
                'stock' => (int)($producto->stock ?? 0)
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'error' => 'Error en el Servidor: ' . $e->getMessage()
            ], 500);
        }
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
