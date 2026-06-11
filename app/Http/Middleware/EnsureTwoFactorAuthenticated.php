<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        // Primeiro acesso: bloqueia tudo até o 2FA ser configurado
        if (
            $request->session()->get('first_access_2fa_required')
            && ! $request->routeIs('two-factor.setup', 'two-factor.enable', 'logout')
        ) {
            return redirect()->route('two-factor.setup');
        }

        // Login normal: bloqueia se 2FA está ativo mas ainda não foi desafiado nesta sessão
        if (
            $user->hasTwoFactorEnabled()
            && ! $request->session()->get('two_factor_authenticated')
            && ! $request->routeIs('two-factor.*', 'logout')
        ) {
            return redirect()->route('two-factor.challenge');
        }

        return $next($request);
    }
}
