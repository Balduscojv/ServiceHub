<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(): Response
    {
        $users = User::with('roles', 'profile')
            ->latest()
            ->paginate(15);

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
        ]);
    }

    public function edit(User $user): Response
    {
        $user->load('roles', 'profile');

        return Inertia::render('Admin/Users/Edit', [
            'user' => $user,
            'roles' => Role::all(['id', 'name', 'display_name']),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['string', 'exists:roles,name'],
        ]);

        $user->syncRoles($validated['roles']);

        return redirect()->route('admin.users.index')
            ->with('success', "Roles de {$user->name} atualizadas com sucesso.");
    }

    public function destroy(User $user)
    {
        abort_if($user->hasRole('admin') && User::whereHas('roles', fn ($q) => $q->where('name', 'admin'))->count() === 1, 403, 'Não é possível remover o único administrador.');

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuário removido com sucesso.');
    }
}
