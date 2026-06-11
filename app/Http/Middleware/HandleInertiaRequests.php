<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ? (function () use ($request) {
                    $user = $request->user()->loadMissing('roles.permissions');
                    return [
                        'id'          => $user->id,
                        'name'        => $user->name,
                        'email'       => $user->email,
                        'roles'       => $user->roles->pluck('name'),
                        'permissions' => $user->roles->flatMap(fn ($r) => $r->permissions->pluck('name'))->unique()->values(),
                    ];
                })() : null,
            ],
            'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ];
    }
}
