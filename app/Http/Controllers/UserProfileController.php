<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserProfileController extends Controller
{
    public function edit(Request $request): Response
    {
        $user = $request->user()->load('profile');

        return Inertia::render('Profile/Edit', [
            'user' => array_merge($user->toArray(), [
                'two_factor_enabled_at' => $user->two_factor_enabled_at?->toISOString(),
            ]),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'position' => ['nullable', 'string', 'max:100'],
        ]);

        $request->user()->update(['name' => $validated['name']]);

        $request->user()->profile()->updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'phone' => $validated['phone'],
                'position' => $validated['position'],
            ]
        );

        return back()->with('success', 'Perfil atualizado com sucesso.');
    }
}
