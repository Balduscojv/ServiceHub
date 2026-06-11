<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\TotpService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TwoFactorController extends Controller
{
    public function __construct(private TotpService $totp)
    {
    }

    public function showChallenge(Request $request): Response
    {
        if (! $request->user()?->hasTwoFactorEnabled()) {
            return redirect()->route('dashboard');
        }

        return Inertia::render('Auth/TwoFactorChallenge');
    }

    public function verifyChallenge(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6', 'regex:/^[0-9]+$/'],
        ]);

        $user = $request->user();

        if (! $this->totp->verify($user->two_factor_secret, $request->code)) {
            return back()->withErrors(['code' => 'Código inválido ou expirado.']);
        }

        $request->session()->put('two_factor_authenticated', true);

        return redirect()->intended(route('dashboard'));
    }

    public function showSetup(Request $request): Response
    {
        $user = $request->user();

        $secret = $request->session()->get('2fa_setup_secret');
        if (! $secret) {
            $secret = $this->totp->generateSecret();
            $request->session()->put('2fa_setup_secret', $secret);
        }

        return Inertia::render('Auth/TwoFactorSetup', [
            'qrUri'       => $this->totp->getQrUri($user->email, $secret),
            'secret'      => $secret,
            'enabled'     => $user->hasTwoFactorEnabled(),
            'firstAccess' => $request->session()->has('first_access_2fa_required'),
        ]);
    }

    public function enable(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6', 'regex:/^[0-9]+$/'],
        ]);

        $secret = $request->session()->get('2fa_setup_secret');

        if (! $secret || ! $this->totp->verify($secret, $request->code)) {
            return back()->withErrors(['code' => 'Código inválido. Tente novamente.']);
        }

        $request->user()->update([
            'two_factor_secret'      => $secret,
            'two_factor_enabled_at'  => now(),
        ]);

        $request->session()->forget('2fa_setup_secret');

        $isFirstAccess = $request->session()->pull('first_access_2fa_required', false);

        if ($isFirstAccess) {
            // O código foi verificado agora mesmo durante o setup; sessão já está autenticada
            $request->session()->put('two_factor_authenticated', true);
            return redirect()->route('dashboard')
                ->with('success', '2FA configurado. Bem-vindo ao ServiceHub!');
        }

        return redirect()->route('profile.edit')
            ->with('success', 'Autenticação de dois fatores ativada com sucesso.');
    }

    public function disable(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $request->user()->update([
            'two_factor_secret' => null,
            'two_factor_enabled_at' => null,
        ]);

        return redirect()->route('profile.edit')
            ->with('success', 'Autenticação de dois fatores desativada.');
    }
}
