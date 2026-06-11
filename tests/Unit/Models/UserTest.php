<?php

use App\Models\Ticket;
use App\Models\User;
use App\Models\UserProfile;

test('user has one profile', function () {
    $user = User::factory()->create();
    UserProfile::factory()->create(['user_id' => $user->id]);

    expect($user->profile)->toBeInstanceOf(UserProfile::class);
});

test('user has many tickets', function () {
    $user = User::factory()->create();
    Ticket::factory(3)->create(['user_id' => $user->id]);

    expect($user->tickets)->toHaveCount(3)
        ->each->toBeInstanceOf(Ticket::class);
});

test('user profile is unique per user', function () {
    $user = User::factory()->create();
    UserProfile::factory()->create(['user_id' => $user->id]);

    expect(fn () => UserProfile::factory()->create(['user_id' => $user->id]))
        ->toThrow(\Illuminate\Database\QueryException::class);
});
