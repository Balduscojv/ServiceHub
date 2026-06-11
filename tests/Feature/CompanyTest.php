<?php

use App\Models\Company;
use App\Models\User;

test('authenticated user can list companies', function () {
    $user = User::factory()->create();
    Company::factory(3)->create();

    $this->actingAs($user)
        ->get(route('companies.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Companies/Index'));
});

test('authenticated user can create a company', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('companies.store'), [
            'name' => 'Nova Empresa',
            'cnpj' => '12.345.678/0001-99',
        ])->assertRedirect(route('companies.index'));

    expect(Company::where('name', 'Nova Empresa')->exists())->toBeTrue();
});

test('company name is required', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('companies.store'), ['name' => ''])
        ->assertSessionHasErrors('name');
});

test('company cnpj must be unique', function () {
    $user = User::factory()->create();
    Company::factory()->create(['cnpj' => '12.345.678/0001-99']);

    $this->actingAs($user)
        ->post(route('companies.store'), [
            'name' => 'Outra Empresa',
            'cnpj' => '12.345.678/0001-99',
        ])->assertSessionHasErrors('cnpj');
});

test('authenticated user can update a company', function () {
    $user = User::factory()->create();
    $company = Company::factory()->create(['name' => 'Velha Empresa']);

    $this->actingAs($user)
        ->patch(route('companies.update', $company), [
            'name' => 'Nova Empresa',
            'cnpj' => null,
        ])->assertRedirect(route('companies.index'));

    expect($company->fresh()->name)->toBe('Nova Empresa');
});

test('authenticated user can delete a company', function () {
    $user = User::factory()->create();
    $company = Company::factory()->create();

    $this->actingAs($user)
        ->delete(route('companies.destroy', $company))
        ->assertRedirect(route('companies.index'));

    expect(Company::find($company->id))->toBeNull();
});
