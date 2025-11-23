<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;

class ResetPasswordController extends Controller
{
    public function showResetForm($token, $email)
    {
        return view('auth.passwords.reset', compact('token', 'email'));
    }
    
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $passwordReset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$passwordReset || !Hash::check($request->token, $passwordReset->token)) {
            return back()->withErrors(['token' => 'El enlace de recuperación es inválido o ha expirado.']);
        }

        // Verificar si el token ha expirado (1 hora)
        if (Carbon::parse($passwordReset->created_at)->addHour() < Carbon::now()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['token' => 'El enlace de recuperación ha expirado.']);
        }

        // Actualizar contraseña
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', '¡Tu contraseña ha sido actualizada exitosamente!');
    }
}