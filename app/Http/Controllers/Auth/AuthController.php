<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginSession;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AuthController extends Controller
{
    public function showLogin(Request $request): Response
    {
        return Inertia::render('Auth/Login', [
            'step'  => $request->session()->get('login_step', 'email'),
            'email' => $request->session()->get('login_email', ''),
        ]);
    }

    // Passo 1: identifica e-mail e decide se vai para senha ou primeiro acesso
    public function identify(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user) {
            return back()->withErrors([
                'email' => 'Nenhuma conta encontrada com esse e-mail.',
            ]);
        }

        if (! $user->hasPasswordSet()) {
            $request->session()->put('first_access_email', $validated['email']);
            return redirect()->route('first-access');
        }

        $request->session()->put('login_step', 'password');
        $request->session()->put('login_email', $validated['email']);

        return redirect()->route('login');
    }

    // Passo 2: autentica; e-mail vem da sessão (UI) ou do request (API/testes)
    public function login(Request $request)
    {
        $email = $request->session()->get('login_email') ?? $request->input('email');

        if (! $email) {
            return redirect()->route('login');
        }

        $request->validate([
            'password' => ['required'],
        ]);

        if (! Auth::attempt(['email' => $email, 'password' => $request->input('password')])) {
            return back()->withErrors([
                'email' => 'Credenciais inválidas.',
            ]);
        }

        $request->session()->regenerate();
        $request->session()->forget(['login_step', 'login_email']);

        $this->trackLoginSession($request);

        if (Auth::user()->hasTwoFactorEnabled()) {
            return redirect()->route('two-factor.challenge');
        }

        return redirect()->intended(route('dashboard'));
    }

    public function resetLogin(Request $request)
    {
        $request->session()->forget(['login_step', 'login_email']);
        return redirect()->route('login');
    }

    public function showFirstAccess(Request $request): Response|RedirectResponse
    {
        $email = $request->session()->get('first_access_email');

        if (! $email) {
            return redirect()->route('login');
        }

        $user = User::where('email', $email)->firstOrFail();

        if ($user->hasPasswordSet()) {
            $request->session()->forget('first_access_email');
            return redirect()->route('login');
        }

        return Inertia::render('Auth/FirstAccess', [
            'email' => $email,
            'name'  => $user->name,
        ]);
    }

    public function setPassword(Request $request)
    {
        $email = $request->session()->get('first_access_email');

        if (! $email) {
            return redirect()->route('login');
        }

        $user = User::where('email', $email)->firstOrFail();

        abort_if($user->hasPasswordSet(), 403, 'Senha já definida.');

        $request->validate([
            'password'              => ['required', 'confirmed', Password::defaults()],
            'password_confirmation' => ['required'],
        ]);

        $user->update(['password' => Hash::make($request->password)]);

        $request->session()->forget('first_access_email');

        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->put('first_access_2fa_required', true);

        $this->trackLoginSession($request);

        return redirect()->route('two-factor.setup');
    }

    public function showRegister(): Response
    {
        return Inertia::render('Auth/Register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'phone'    => ['nullable', 'string', 'max:20'],
            'position' => ['nullable', 'string', 'max:100'],
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        UserProfile::create([
            'user_id'  => $user->id,
            'phone'    => $validated['phone'] ?? null,
            'position' => $validated['position'] ?? null,
        ]);

        $user->assignRole('agent');

        Auth::login($user);
        $request->session()->regenerate();

        $this->trackLoginSession($request);

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        $this->revokeLoginSession($request);

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function trackLoginSession(Request $request): void
    {
        LoginSession::create([
            'user_id'       => Auth::id(),
            'session_id'    => hash('sha256', $request->session()->getId()),
            'ip_address'    => $request->ip(),
            'user_agent'    => $request->userAgent(),
            'last_activity' => now(),
        ]);
    }

    private function revokeLoginSession(Request $request): void
    {
        $hashedId = hash('sha256', $request->session()->getId());

        $updated = LoginSession::where('session_id', $hashedId)
            ->whereNull('revoked_at')
            ->update(['revoked_at' => now()]);

        if (! $updated && $user = Auth::user()) {
            LoginSession::where('user_id', $user->id)
                ->whereNull('revoked_at')
                ->update(['revoked_at' => now()]);
        }
    }
}
