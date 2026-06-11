<?php

use App\Http\Controllers\Admin\RoleController as AdminRoleController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login/identify', [AuthController::class, 'identify'])->name('login.identify');
    Route::get('/login/reset', [AuthController::class, 'resetLogin'])->name('login.reset');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
    Route::get('/first-access', [AuthController::class, 'showFirstAccess'])->name('first-access');
    Route::post('/first-access', [AuthController::class, 'setPassword'])->name('first-access.set');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // 2FA (acessível sem completar o challenge)
    Route::get('/two-factor-challenge', [TwoFactorController::class, 'showChallenge'])->name('two-factor.challenge');
    Route::post('/two-factor-challenge', [TwoFactorController::class, 'verifyChallenge'])->name('two-factor.verify');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('companies', CompanyController::class);
    Route::resource('projects', ProjectController::class);
    Route::resource('tickets', TicketController::class);

    Route::get('/profile', [UserProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [UserProfileController::class, 'update'])->name('profile.update');

    Route::get('/two-factor-setup', [TwoFactorController::class, 'showSetup'])->name('two-factor.setup');
    Route::post('/two-factor-setup', [TwoFactorController::class, 'enable'])->name('two-factor.enable');
    Route::delete('/two-factor-setup', [TwoFactorController::class, 'disable'])->name('two-factor.disable');

    // Admin — gerenciamento de usuários
    Route::prefix('admin')->name('admin.')->middleware('permission:users.view')->group(function () {
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit')
            ->middleware('permission:users.promote');
        Route::patch('/users/{user}', [AdminUserController::class, 'update'])->name('users.update')
            ->middleware('permission:users.promote');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy')
            ->middleware('permission:users.delete');
    });

    // Gerenciamento de roles — exclusivo para manager
    Route::prefix('admin')->name('admin.')->middleware('role:manager')->group(function () {
        Route::get('/roles', [AdminRoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/{role}/edit', [AdminRoleController::class, 'edit'])->name('roles.edit');
        Route::patch('/roles/{role}', [AdminRoleController::class, 'update'])->name('roles.update');
    });
});
