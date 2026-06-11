<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function index(Request $request): Response
    {
        $projects = Project::with('company')
            ->withCount('tickets')
            ->latest()
            ->paginate(10);

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
        ]);
    }

    public function create(): Response
    {
        $companies = Company::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Projects/Create', [
            'companies' => $companies,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => ['required', 'exists:companies,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive,completed'],
        ]);

        Project::create($validated);

        return redirect()->route('projects.index')
            ->with('success', 'Projeto criado com sucesso.');
    }

    public function show(Project $project): Response
    {
        $project->load(['company', 'tickets.user', 'tickets.detail']);

        return Inertia::render('Projects/Show', [
            'project' => $project,
        ]);
    }

    public function edit(Project $project): Response
    {
        $companies = Company::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Projects/Edit', [
            'project' => $project->load('company'),
            'companies' => $companies,
        ]);
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'company_id' => ['required', 'exists:companies,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive,completed'],
        ]);

        $project->update($validated);

        return redirect()->route('projects.index')
            ->with('success', 'Projeto atualizado com sucesso.');
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Projeto removido com sucesso.');
    }
}
