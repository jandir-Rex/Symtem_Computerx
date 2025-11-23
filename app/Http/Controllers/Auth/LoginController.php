<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Dónde redirigir después del login (se sobrescribe en authenticated)
     */
    protected $redirectTo = '/dashboard';

    /**
     * Mostrar formulario de login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Manejar el intento de login
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // Verificar si la cuenta está bloqueada
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->filled('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            $user = Auth::user();

            // Resetear intentos fallidos
            if (isset($user->failed_attempts) && $user->failed_attempts > 0) {
                $user->update(['failed_attempts' => 0, 'locked_until' => null]);
            }

            // Redirigir según tenga rol o no
            return $this->sendLoginResponse($request);
        }

        // Credenciales incorrectas - registrar intento fallido
        $this->incrementLoginAttempts($request);
        
        $user = \App\Models\User::where('email', $request->email)->first();
        if ($user && isset($user->failed_attempts)) {
            $attempts = $user->failed_attempts + 1;
            
            if ($attempts >= 5) {
                $user->update([
                    'failed_attempts' => $attempts,
                    'locked_until' => now()->addMinutes(15)
                ]);
                
                return back()->withErrors([
                    'email' => '❌ Cuenta bloqueada por múltiples intentos fallidos. Intenta en 15 minutos.',
                ])->withInput($request->only('email'));
            }
            
            $user->update(['failed_attempts' => $attempts]);
        }

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * ✨ REDIRECCIÓN INTELIGENTE SEGÚN ROL
     */
    protected function authenticated(Request $request, $user)
    {
        // Verificar si el usuario tiene roles asignados
        if (!$user->roles || $user->roles->isEmpty()) {
            // Sin roles = Cliente del e-commerce
            return redirect()->route('home')->with('success', '¡Bienvenido de nuevo, ' . $user->name . '!');
        }

        // Con roles = Personal administrativo
        // Redirigir al dashboard que manejará la lógica interna
        return redirect()->route('dashboard.index');
    }

    /**
     * Validar credenciales de login
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'El correo es obligatorio',
            'email.email' => 'Debe ser un correo válido',
            'password.required' => 'La contraseña es obligatoria',
        ]);
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', '✅ Has cerrado sesión correctamente');
    }
}