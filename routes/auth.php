<?php

use App\Http\Controllers\AuthController; 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;

// ------------------------------------------------------------------
// RUTAS PARA USUARIOS NO AUTENTICADOS (GUEST)
// ------------------------------------------------------------------
Route::middleware('guest')->group(function () {
    
    // REGISTRO DE USUARIO
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    // LOGIN PERSONALIZADO
    Route::get('login', [AuthController::class, 'showLogin'])->name('login'); 
    Route::post('login', [AuthController::class, 'login']); 

    // ⚠️ Desactivado temporalmente: recuperación de contraseña
    // (Descomenta cuando crees los controladores necesarios)
    /*
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
    */
});

// ------------------------------------------------------------------
// RUTAS PARA USUARIOS AUTENTICADOS (AUTH)
// ------------------------------------------------------------------
Route::middleware('auth')->group(function () {
    
    // ⚠️ Desactivado temporalmente: verificación de email y cambio de contraseña
    // (Descomenta cuando implementes los controladores)
    /*
    Route::get('verify-email', [EmailVerificationPromptController::class, '__invoke'])
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');
    */

    // LOGOUT PERSONALIZADO
    Route::post('logout', [AuthController::class, 'logout'])->name('logout'); 
});
