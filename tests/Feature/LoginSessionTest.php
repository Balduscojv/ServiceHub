<?php

use App\Models\LoginSession;
use App\Models\User;

test('login creates a login session record', function () {
    $user = User::factory()->create();

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    expect(LoginSession::where('user_id', $user->id)->whereNull('revoked_at')->exists())->toBeTrue();
});

test('logout revokes the login session', function () {
    $user = User::factory()->create();

    $this->post(route('login'), ['email' => $user->email, 'password' => 'password']);

    expect(LoginSession::where('user_id', $user->id)->whereNull('revoked_at')->exists())->toBeTrue();

    $this->actingAs($user)->post(route('logout'));

    expect(LoginSession::where('user_id', $user->id)->whereNotNull('revoked_at')->exists())->toBeTrue();
    expect(LoginSession::where('user_id', $user->id)->whereNull('revoked_at')->exists())->toBeFalse();
});

test('login session stores ip and user agent', function () {
    $user = User::factory()->create();

    $this->withHeaders(['User-Agent' => 'TestBrowser/1.0'])
        ->post(route('login'), ['email' => $user->email, 'password' => 'password']);

    $session = LoginSession::where('user_id', $user->id)->first();

    expect($session->user_agent)->toBe('TestBrowser/1.0');
});
