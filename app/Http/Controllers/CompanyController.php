<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CompanyController extends Controller
{
    public function index(): Response
    {
        $companies = Company::withCount('projects')->latest()->paginate(10);

        return Inertia::render('Companies/Index', [
            'companies' => $companies,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Companies/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'cnpj' => ['nullable', 'string', 'max:18', 'unique:companies'],
        ]);

        Company::create($validated);

        return redirect()->route('companies.index')
            ->with('success', 'Empresa criada com sucesso.');
    }

    public function show(Company $company): Response
    {
        $company->load(['projects.tickets']);

        return Inertia::render('Companies/Show', [
            'company' => $company,
        ]);
    }

    public function edit(Company $company): Response
    {
        return Inertia::render('Companies/Edit', [
            'company' => $company,
        ]);
    }

    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'cnpj' => ['nullable', 'string', 'max:18', 'unique:companies,cnpj,'.$company->id],
        ]);

        $company->update($validated);

        return redirect()->route('companies.index')
            ->with('success', 'Empresa atualizada com sucesso.');
    }

    public function destroy(Company $company)
    {
        $company->delete();

        return redirect()->route('companies.index')
            ->with('success', 'Empresa removida com sucesso.');
    }
}
