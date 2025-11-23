<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Muestra el formulario de inicio de sesión.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Procesa el intento de inicio de sesión.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Redirección según el rol asignado
            if ($user->hasRole('stand1')) {
                return redirect()->route('dashboard.stand1');
            } elseif ($user->hasRole('stand2')) {
                return redirect()->route('dashboard.stand2');
            } elseif ($user->hasRole('almacen')) {
                return redirect()->route('dashboard.almacen');
            } elseif ($user->hasRole('contador')) {
                return redirect()->route('dashboard.contador');
            } elseif ($user->hasRole('admin')) {
                return redirect()->route('dashboard.admin');
            }

            // Por si acaso no tiene rol asignado
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Tu usuario no tiene un rol asignado. Contacta con el administrador.',
            ]);
        }

        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    /**
     * Cierra la sesión del usuario.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
