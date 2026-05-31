<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // ======================================================================
        //  VALIDACIÓN DE SEGURIDAD MILITAR
        //  Simulación para el video de entrega:
        //  - Estado Inicial: 'min:5' (permite contraseñas cortas)
        //  - Estado Corregido: 'min:16' (exige contraseña militar robusta)
        // ======================================================================
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string|min:5', 
        ], [
            'email.required' => 'La identificación (cédula o correo) es obligatoria.',
            'password.required' => 'La contraseña de acceso es obligatoria.',
            'password.min' => 'Seguridad del Sistema: La clave debe tener al menos :min caracteres.',
        ]);

        // Simulación de acceso exitoso sin requerir base de datos activa en Render
        return back()->with('success', '¡Conexión segura establecida con éxito!');
    }
}
