<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Caja;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class VentaController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    public function index(Request $request)
    {
        $fecha = $request->get('fecha');
        $search = $request->get('search');
        
        $ventas = Venta::with(['user', 'detalles.producto'])
            ->when($fecha, function ($query) use ($fecha) {
                return $query->whereDate('fecha_venta', $fecha);
            })
            ->when($search, function ($query) use ($search) {
                // El ID es la base de nuestro Folio V-000X
                $cleanId = preg_replace('/[^0-9]/', '', $search);
                return $query->where('id', 'like', "%{$cleanId}%");
            })
            ->orderBy('fecha_venta', 'desc')
            ->paginate(20);

        $totalDia = Venta::completadas()
            ->when($fecha, function($q) use ($fecha) {
                return $q->whereDate('fecha_venta', $fecha);
            }, function($q) {
                return $q->whereDate('fecha_venta', now());
            })
            ->sum('total');

        return view('ventas.index', compact('ventas', 'fecha', 'totalDia'));
    }

    public function create()
    {
        $productos = Producto::activos()
            ->orderBy('nombre')
            ->get();

        $categorias = Producto::whereNotNull('categoria')
            ->where('activo', true)
            ->distinct()
            ->pluck('categoria');

        return view('ventas.create', compact('productos', 'categorias'));
    }

    public function store(Request $request)
    {
        // Decodificar el JSON de productos que viene de la vista v1.6
        $productosData = json_decode($request->productos_json, true);
        
        if (!$productosData || empty($productosData)) {
            return redirect()->back()
                ->with('error', 'El carrito está vacío o el formato es inválido.')
                ->withInput();
        }

        // Inyectar los productos al request para la validación tradicional de Laravel
        $request->merge(['productos' => $productosData]);

        $validator = Validator::make($request->all(), [
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required', // Relajado para permitir códigos temporales como MANUAL_xxx
            'productos.*.cantidad' => 'required|numeric|min:0.001',
            'metodo_pago' => 'required|string',
            'efectivo_recibido' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->with('error', 'Error en la validación: ' . implode(', ', $validator->errors()->all()))
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $productosData = json_decode($request->productos_json ?? '[]', true);
            
            if (empty($productosData)) {
                $productosData = $request->productos;
            }

            $tempProductos = [];
            foreach ($productosData as $item) {
                $id = $item['id'];
                if (isset($tempProductos[$id])) {
                    $tempProductos[$id]['cantidad'] += $item['cantidad'];
                } else {
                    $tempProductos[$id] = $item;
                }
            }
            $productosData = array_values($tempProductos);

            $total = 0;
            $detalles = [];

            foreach ($productosData as $item) {
                $producto = Producto::find($item['id']);
                
                if (!$producto) {
                    // Si es una Venta Manual (ID inventado) creamos un producto comodín invisible
                    $producto = Producto::firstOrCreate(
                        ['codigo_barras' => 'MANUAL999'],
                        [
                            'nombre' => 'ARTICULO MANUAL',
                            'precio' => 1,
                            'stock' => 999999,
                            'stock_minimo' => 0
                        ]
                    );
                }

                if ($producto->codigo_barras !== 'MANUAL999' && $producto->stock < $item['cantidad']) {
                    throw new \Exception("Stock insuficiente para: {$producto->nombre} (Disponible: {$producto->stock})");
                }

                // Para ventas manuales, usamos el precio que el cajero definió en la pantalla
                $precioReal = ($producto->codigo_barras === 'MANUAL999') ? $item['precio'] : $producto->precio;
                $subtotal = $precioReal * $item['cantidad'];
                $total += $subtotal;

                $detalles[] = [
                    'producto_id' => $producto->id,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $precioReal,
                    'subtotal' => $subtotal,
                ];

                if ($producto->codigo_barras !== 'MANUAL999') {
                    $producto->decrement('stock', $item['cantidad']);
                }
            }

            $descuento = $request->descuento ?? 0;
            $totalFinal = $total - $descuento;

            $efectivoRecibido = $request->efectivo_recibido ?? 0;
            $cambio = $efectivoRecibido > $totalFinal ? $efectivoRecibido - $totalFinal : 0;

            // Capturar nombres personalizados para el ticket (Plan B: Persistencia en notas)
            $nombresManuales = [];
            foreach ($productosData as $item) {
                $nombresManuales[] = $item['nombre'];
            }
            $ajusteNotas = ($request->notas ? $request->notas . " | " : "") . "NAMES_JSON:" . json_encode($nombresManuales);

            $venta = Venta::create([
                'user_id' => auth()->id(),
                'total' => $totalFinal,
                'descuento' => $descuento,
                'metodo_pago' => $request->metodo_pago,
                'efectivo_recibido' => $efectivoRecibido,
                'cambio' => $cambio,
                'notas' => $ajusteNotas,
                'estado' => 'completada',
                'fecha_venta' => now(),
            ]);

            foreach ($detalles as $detalle) {
                $venta->detalles()->create($detalle);
            }

            DB::commit();

            // Detectar productos que quedaron con stock bajo para avisar
            $alertasStock = [];
            foreach ($venta->detalles as $detalle) {
                $p = $detalle->producto;
                if ($p && $p->stock <= $p->stock_minimo) {
                    $alertasStock[] = $p->nombre . " (" . $p->stock . ")";
                }
            }

            return redirect()->route('ventas.ticket', $venta->id)
                ->with('success', 'Venta realizada exitosamente')
                ->with('alertas_stock', $alertasStock)
                ->with('venta_id', $venta->id);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function show(Venta $venta)
    {
        $venta->load(['user', 'detalles.producto']);
        return view('ventas.show', compact('venta'));
    }

    public function ticket(Venta $venta)
    {
        $venta->load(['user', 'detalles.producto']);
        return view('ventas.ticket', compact('venta'));
    }

    public function ticketPdf(Venta $venta)
    {
        $venta->load(['user', 'detalles.producto']);
        
        $pdf = Pdf::loadView('tickets.pdf', compact('venta'));
        $pdf->setPaper([0, 0, 226, 600], 'portrait');
        
        $nombreArchivo = 'ticket-' . $venta->numero_venta . '.pdf';
        
        return $pdf->download($nombreArchivo);
    }

    public function enviarWhatsApp(Request $request, Venta $venta)
    {
        $request->validate([
            'telefono' => 'required|string',
        ]);

        $venta->load(['user', 'detalles.producto']);

        $resultado = $this->whatsappService->enviarTicket($venta, $request->telefono);

        if ($resultado['success']) {
            return redirect()->back()->with('success', 'Ticket enviado por WhatsApp');
        }

        return redirect()->back()->with('error', 'Error al enviar: ' . $resultado['error']);
    }

    public function destroy(Venta $venta)
    {
        if ($venta->estado === 'cancelada') {
            return redirect()->back()->with('error', 'Esta venta ya está cancelada');
        }

        DB::beginTransaction();
        try {
            foreach ($venta->detalles as $detalle) {
                $detalle->producto->increment('stock', $detalle->cantidad);
            }

            $venta->update(['estado' => 'cancelada']);

            DB::commit();

            return redirect()->route('ventas.index')
                ->with('success', 'Venta cancelada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al cancelar la venta');
        }
    }

    public function reporte(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth());
        $fechaFin = $request->get('fecha_fin', now()->endOfMonth());

        $ventas = Venta::with(['user', 'detalles.producto'])
            ->completadas()
            ->whereBetween('fecha_venta', [$fechaInicio, $fechaFin])
            ->orderBy('fecha_venta', 'desc')
            ->get();

        $totalVentas = $ventas->sum('total');
        $totalProductos = $ventas->sum(function ($venta) {
            return $venta->detalles->sum('cantidad');
        });

        $ventasPorMetodo = $ventas->groupBy('metodo_pago')->map(function ($grupo) {
            return [
                'cantidad' => $grupo->count(),
                'total' => $grupo->sum('total'),
            ];
        });

        return view('ventas.reporte', compact(
            'ventas',
            'fechaInicio',
            'fechaFin',
            'totalVentas',
            'totalProductos',
            'ventasPorMetodo'
        ));
    }
}
