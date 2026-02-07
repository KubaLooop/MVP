<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FakeAuth
{
    public function handle(Request $request, Closure $next)
    {
        // Přečteme ID uživatele z hlavičky 'X-User-ID'
        // Pokud tam není, vezmeme natvrdo ID 1 (Pepa)
        $userId = $request->header('X-User-ID', 1);

        $user = User::find($userId);

        if ($user) {
            // "Přihlásíme" ho pro tento request
            Auth::setUser($user); 
        }

        return $next($request);
    }
}
