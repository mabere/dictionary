<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if ($user && in_array($user->role, $roles, true)) {
            return $next($request);
        }

        if ($user) {
            return response()
                ->redirectToRoute('dictionary.index')
                ->withErrors(['access' => 'Akun Anda dapat memakai kamus publik, tetapi tidak memiliki akses ke halaman tersebut.']);
        }

        abort(403);
    }
}
