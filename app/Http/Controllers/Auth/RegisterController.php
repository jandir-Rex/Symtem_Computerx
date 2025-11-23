<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /**
     * Mostrar formulario de registro
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Registrar nuevo usuario (SOLO CLIENTES, SIN ROLES)
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'dni' => 'nullable|string|max:20',
        ], [
            'name.required' => 'El nombre es obligatorio',
            'email.required' => 'El correo es obligatorio',
            'email.email' => 'Debe ser un correo válido',
            'email.unique' => 'Este correo ya está registrado',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'password.confirmed' => 'Las contraseñas no coinciden',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // ✅ CREAR USUARIO SIN ROL (Cliente del e-commerce)
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'dni' => $request->dni,
            'stand_id' => null, // Los clientes no pertenecen a ningún stand
            'failed_attempts' => 0,
        ]);

        // ✅ NO ASIGNAR NINGÚN ROL (es un cliente normal)
        // Los usuarios con roles se crean manualmente por el admin

        // Login automático después del registro
        Auth::login($user);

        return redirect()->route('home')->with('success', '✅ ¡Cuenta creada exitosamente! Bienvenido ' . $user->name);
    }
}