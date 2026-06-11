<?php

use App\Models\Company;
use App\Models\Project;

test('company has many projects', function () {
    $company = Company::factory()->create();
    $projects = Project::factory(3)->create(['company_id' => $company->id]);

    expect($company->projects)->toHaveCount(3)
        ->each->toBeInstanceOf(Project::class);
});

test('company can be created with fillable attributes', function () {
    $company = Company::create([
        'name' => 'Acme Corp',
        'cnpj' => '12.345.678/0001-99',
    ]);

    expect($company->name)->toBe('Acme Corp')
        ->and($company->cnpj)->toBe('12.345.678/0001-99');
});

test('company cnpj is nullable', function () {
    $company = Company::factory()->create(['cnpj' => null]);

    expect($company->cnpj)->toBeNull();
});
