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
        $fecha = $request->get('fecha', now()->toDateString());
        
        $ventas = Venta::with(['user', 'detalles.producto'])
            ->when($fecha, function ($query) use ($fecha) {
                return $query->whereDate('fecha_venta', $fecha);
            })
            ->orderBy('fecha_venta', 'desc')
            ->paginate(20);

        $totalDia = Venta::completadas()
            ->whereDate('fecha_venta', $fecha)
            ->sum('total');

        return view('ventas.index', compact('ventas', 'fecha', 'totalDia'));
    }

    public function create()
    {
        $productos = Producto::activos()
            ->where('stock', '>', 0)
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
        $validator = Validator::make($request->all(), [
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'metodo_pago' => 'required|in:efectivo,tarjeta,transferencia,mixto',
            'efectivo_recibido' => 'nullable|numeric|min:0',
            'descuento' => 'nullable|numeric|min:0',
            'notas' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $productosData = json_decode($request->productos_json ?? '[]', true);
            
            if (empty($productosData)) {
                $productosData = $request->productos;
            }

            $total = 0;
            $detalles = [];

            foreach ($productosData as $item) {
                $producto = Producto::find($item['id']);
                
                if ($producto->stock < $item['cantidad']) {
                    throw new \Exception("Stock insuficiente para {$producto->nombre}");
                }

                $subtotal = $producto->precio * $item['cantidad'];
                $total += $subtotal;

                $detalles[] = [
                    'producto_id' => $producto->id,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $producto->precio,
                    'subtotal' => $subtotal,
                ];

                $producto->decrement('stock', $item['cantidad']);
            }

            $descuento = $request->descuento ?? 0;
            $totalFinal = $total - $descuento;

            $efectivoRecibido = $request->efectivo_recibido ?? 0;
            $cambio = $efectivoRecibido > $totalFinal ? $efectivoRecibido - $totalFinal : 0;

            $venta = Venta::create([
                'user_id' => auth()->id(),
                'total' => $totalFinal,
                'descuento' => $descuento,
                'metodo_pago' => $request->metodo_pago,
                'efectivo_recibido' => $efectivoRecibido,
                'cambio' => $cambio,
                'notas' => $request->notas,
                'estado' => 'completada',
                'fecha_venta' => now(),
            ]);

            foreach ($detalles as $detalle) {
                $venta->detalles()->create($detalle);
            }

            DB::commit();

            return redirect()->route('ventas.ticket', $venta->id)
                ->with('success', 'Venta realizada exitosamente')
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
