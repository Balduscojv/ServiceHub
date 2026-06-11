<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessTicketAttachment;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\TicketDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class TicketController extends Controller
{
    public function index(Request $request): Response
    {
        $tickets = Ticket::with(['project.company', 'user', 'detail'])
            ->when($request->search, fn ($q) => $q->where('title', 'like', '%'.$request->search.'%'))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Tickets/Index', [
            'tickets' => $tickets,
            'filters' => $request->only('search', 'status'),
        ]);
    }

    public function create(): Response
    {
        $projects = Project::with('company')->orderBy('name')->get(['id', 'name', 'company_id']);

        return Inertia::render('Tickets/Create', [
            'projects' => $projects,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'status' => ['required', 'in:open,in_progress,closed'],
            'priority' => ['required', 'in:low,medium,high'],
            'attachment' => ['nullable', 'file', 'mimes:json,txt', 'max:2048'],
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('attachments', 'local');
        }

        $ticket = Ticket::create([
            'project_id' => $validated['project_id'],
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'status' => $validated['status'],
            'priority' => $validated['priority'],
            'attachment_path' => $attachmentPath,
        ]);

        $detail = TicketDetail::create([
            'ticket_id' => $ticket->id,
            'technical_notes' => null,
            'additional_data' => null,
            'processed_at' => null,
        ]);

        if ($attachmentPath) {
            ProcessTicketAttachment::dispatch($ticket);
        }

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Ticket criado com sucesso.');
    }

    public function show(Ticket $ticket): Response
    {
        $ticket->load(['project.company', 'user.profile', 'detail']);

        return Inertia::render('Tickets/Show', [
            'ticket' => $ticket,
        ]);
    }

    public function edit(Ticket $ticket): Response
    {
        $projects = Project::with('company')->orderBy('name')->get(['id', 'name', 'company_id']);

        return Inertia::render('Tickets/Edit', [
            'ticket' => $ticket->load(['project', 'detail']),
            'projects' => $projects,
        ]);
    }

    public function update(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'status' => ['required', 'in:open,in_progress,closed'],
            'priority' => ['required', 'in:low,medium,high'],
            'attachment' => ['nullable', 'file', 'mimes:json,txt', 'max:2048'],
        ]);

        if ($request->hasFile('attachment')) {
            if ($ticket->attachment_path) {
                Storage::disk('local')->delete($ticket->attachment_path);
            }
            $validated['attachment_path'] = $request->file('attachment')->store('attachments', 'local');

            ProcessTicketAttachment::dispatch($ticket->fresh());
        }

        $ticket->update([
            'project_id' => $validated['project_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'status' => $validated['status'],
            'priority' => $validated['priority'],
            'attachment_path' => $validated['attachment_path'] ?? $ticket->attachment_path,
        ]);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Ticket atualizado com sucesso.');
    }

    public function destroy(Ticket $ticket)
    {
        if ($ticket->attachment_path) {
            Storage::disk('local')->delete($ticket->attachment_path);
        }

        $ticket->delete();

        return redirect()->route('tickets.index')
            ->with('success', 'Ticket removido com sucesso.');
    }
}
