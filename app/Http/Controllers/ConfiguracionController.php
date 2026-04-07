<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConfiguracionController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        $config = DB::table('configuraciones')->pluck('value', 'key')->toArray();
        return view('configuracion.index', compact('config'));
    }

    /**
     * Update the settings.
     */
    public function update(Request $request)
    {
        $request->validate([
            'nombre_kiosco' => 'required|string|max:255',
            'moneda' => 'required|string|max:10',
            'direccion' => 'nullable|string|max:500',
            'telefono' => 'nullable|string|max:20',
            'whatsapp_notificacion' => 'nullable|string|max:25',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,ico|max:1024',
            'imagen_login' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
        ]);

        // Actualizar valores de texto
        $settings = $request->only(['nombre_kiosco', 'moneda', 'direccion', 'telefono', 'whatsapp_notificacion']);
        
        foreach ($settings as $key => $value) {
            DB::table('configuraciones')->updateOrInsert(
                ['key' => $key],
                ['value' => $value, 'updated_at' => now()]
            );
        }

        // Manejar subida de Logo, Favicon e Imagen de Login (Forma compatible con Hostinger)
        foreach (['logo', 'favicon', 'imagen_login'] as $type) {
            if ($request->hasFile($type)) {
                $file = $request->file($type);
                $filename = $type . '_' . time() . '.' . $file->getClientOriginalExtension();
                
                // Mover directamente a public/uploads
                $file->move(public_path('uploads'), $filename);
                
                $url = asset('uploads/' . $filename);
                
                DB::table('configuraciones')->updateOrInsert(
                    ['key' => $type],
                    ['value' => $url, 'updated_at' => now()]
                );
            }
        }

        return redirect()->back()->with('success', 'Configuración guardada exitosamente. Los cambios se verán en todo el sistema.');
    }
}
