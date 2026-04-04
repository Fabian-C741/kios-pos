<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\MovimientoCaja;
use Illuminate\Http\Request;

class CajaController extends Controller
{
    public function index()
    {
        $cajas = Caja::with('usuario')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('cajas.index', compact('cajas'));
    }

    public function create()
    {
        return view('cajas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'saldo_inicial' => 'nullable|numeric|min:0',
        ]);

        Caja::create([
            'nombre' => $request->nombre,
            'saldo_inicial' => $request->saldo_inicial ?? 0,
        ]);

        return redirect()->route('cajas.index')
            ->with('success', 'Caja creada exitosamente');
    }

    public function show(Caja $caja)
    {
        $caja->load(['usuario', 'movimientos.user']);
        
        $movimientos = $caja->movimientos()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return view('cajas.show', compact('caja', 'movimientos'));
    }

    public function abrir(Caja $caja)
    {
        if ($caja->abierta) {
            return redirect()->back()->with('error', 'Esta caja ya está abierta');
        }

        $caja->abrir(auth()->user());

        return redirect()->back()
            ->with('success', 'Caja abierta exitosamente');
    }

    public function cerrar(Request $request, Caja $caja)
    {
        if (!$caja->abierta) {
            return redirect()->back()->with('error', 'Esta caja ya está cerrada');
        }

        $caja->cerrar();

        return redirect()->back()
            ->with('success', 'Caja cerrada exitosamente');
    }

    public function movimiento(Request $request, Caja $caja)
    {
        $request->validate([
            'tipo' => 'required|in:entrada,salida',
            'monto' => 'required|numeric|min:0.01',
            'concepto' => 'required|string|max:255',
        ]);

        if ($request->tipo === 'salida' && $caja->saldo_actual < $request->monto) {
            return redirect()->back()->with('error', 'Saldo insuficiente');
        }

        if ($request->tipo === 'entrada') {
            $caja->agregarMonto($request->monto, $request->concepto);
        } else {
            $caja->quitarMonto($request->monto, $request->concepto);
        }

        return redirect()->back()
            ->with('success', 'Movimiento registrado exitosamente');
    }
}
