<?php

use App\Models\User;
use App\Services\TotpService;

test('totp service generates a 16-char base32 secret', function () {
    $totp = new TotpService;
    $secret = $totp->generateSecret();

    expect($secret)->toHaveLength(16)
        ->and($secret)->toMatch('/^[A-Z2-7]{16}$/');
});

test('totp service verifies a valid code', function () {
    $totp = new TotpService;
    $secret = $totp->generateSecret();

    // Use reflection to generate the current code and verify it
    $reflection = new ReflectionClass($totp);
    $compute = $reflection->getMethod('computeCode');
    $compute->setAccessible(true);

    $counter = (int) floor(time() / 30);
    $code = $compute->invoke($totp, $secret, $counter);

    expect($totp->verify($secret, $code))->toBeTrue();
});

test('totp service rejects wrong codes', function () {
    $totp = new TotpService;
    $secret = $totp->generateSecret();

    expect($totp->verify($secret, '000000'))->toBeFalse();
    expect($totp->verify($secret, '123456'))->toBeFalse();
});

test('user with 2fa enabled is redirected to challenge after login', function () {
    $totp = new TotpService;
    $secret = $totp->generateSecret();

    $user = User::factory()->create([
        'two_factor_secret' => $secret,
        'two_factor_enabled_at' => now(),
    ]);

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('two-factor.challenge'));
});

test('user without 2fa enabled is redirected to dashboard after login', function () {
    $user = User::factory()->create([
        'two_factor_secret' => null,
        'two_factor_enabled_at' => null,
    ]);

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('dashboard'));
});

test('valid 2fa code completes the challenge', function () {
    $totp = new TotpService;
    $secret = $totp->generateSecret();

    $user = User::factory()->create([
        'two_factor_secret' => $secret,
        'two_factor_enabled_at' => now(),
    ]);

    $reflection = new ReflectionClass($totp);
    $compute = $reflection->getMethod('computeCode');
    $compute->setAccessible(true);
    $code = $compute->invoke($totp, $secret, (int) floor(time() / 30));

    $this->actingAs($user)
        ->post(route('two-factor.verify'), ['code' => $code])
        ->assertRedirect(route('dashboard'))
        ->assertSessionHas('two_factor_authenticated', true);
});

test('invalid 2fa code returns error', function () {
    $user = User::factory()->create([
        'two_factor_secret' => 'AAAAAAAAAAAAAAAA',
        'two_factor_enabled_at' => now(),
    ]);

    $this->actingAs($user)
        ->post(route('two-factor.verify'), ['code' => '000000'])
        ->assertSessionHasErrors('code');
});
