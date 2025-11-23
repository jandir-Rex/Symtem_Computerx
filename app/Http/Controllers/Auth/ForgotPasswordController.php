<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        // Generar token
        $token = Str::random(64);
        
        // Guardar en tabla password_resets
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($token),
                'created_at' => Carbon::now()
            ]
        );

        // Enviar email (por ahora solo mostramos el token en pantalla)
        // En producción, usarías Mail::to($user->email)->send(new ResetPasswordMail($token));
        
        return redirect()->route('password.reset.form', ['token' => $token, 'email' => $user->email])
                        ->with('status', 'Hemos generado un enlace de recuperación. Por ahora, el token se muestra abajo para pruebas.');
    }
}