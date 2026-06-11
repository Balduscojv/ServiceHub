<?php

use App\Models\User;
use App\Models\UserProfile;

test('user can view login page', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Auth/Login'));
});

test('user can register', function () {
    $this->post(route('register'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertRedirect(route('dashboard'));

    $this->assertAuthenticated();

    expect(User::where('email', 'test@example.com')->exists())->toBeTrue();
    expect(UserProfile::whereHas('user', fn ($q) => $q->where('email', 'test@example.com'))->exists())->toBeTrue();
});

test('user can login', function () {
    $user = User::factory()->create();

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($user);
});

test('user cannot login with wrong credentials', function () {
    $user = User::factory()->create();

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('authenticated user can logout', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('logout'))
        ->assertRedirect(route('login'));

    $this->assertGuest();
});

test('guest is redirected to login when accessing dashboard', function () {
    $this->get(route('dashboard'))
        ->assertRedirect(route('login'));
});
