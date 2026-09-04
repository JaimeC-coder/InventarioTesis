<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class EnsureUserHasEmployeeAndRole
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $login): void
    {
        $user = $login->user;
        $tieneRol = $user->roles()->exists(); // Spatie
        $tieneEmpleado = $user->employee()->exists();
        if (! $tieneRol || ! $tieneEmpleado) {
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            throw ValidationException::withMessages([
                'email' => 'Tu cuenta no tiene un rol o un registro de empleado asociado. Contacta al administrador.',
            ]);
        }
    }
}
