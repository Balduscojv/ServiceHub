<?php

use App\Models\Company;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\TicketDetail;
use App\Models\User;

test('authenticated user can list tickets', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('tickets.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Tickets/Index'));
});

test('authenticated user can create a ticket', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();

    $this->actingAs($user)
        ->post(route('tickets.store'), [
            'project_id' => $project->id,
            'title' => 'Ticket de Teste',
            'description' => 'Descrição do ticket de teste',
            'status' => 'open',
            'priority' => 'medium',
        ])->assertRedirect();

    expect(Ticket::where('title', 'Ticket de Teste')->exists())->toBeTrue();
});

test('ticket creation also creates a ticket detail', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();

    $this->actingAs($user)
        ->post(route('tickets.store'), [
            'project_id' => $project->id,
            'title' => 'Ticket com Detail',
            'description' => 'Descrição',
            'status' => 'open',
            'priority' => 'low',
        ]);

    $ticket = Ticket::where('title', 'Ticket com Detail')->first();

    expect($ticket->detail)->not->toBeNull()
        ->and($ticket->detail)->toBeInstanceOf(TicketDetail::class);
});

test('authenticated user can update ticket status', function () {
    $user = User::factory()->create();
    $ticket = Ticket::factory()->create(['user_id' => $user->id, 'status' => 'open']);

    $this->actingAs($user)
        ->patch(route('tickets.update', $ticket), [
            'project_id' => $ticket->project_id,
            'title' => $ticket->title,
            'description' => $ticket->description,
            'status' => 'closed',
            'priority' => $ticket->priority,
        ])->assertRedirect();

    expect($ticket->fresh()->status)->toBe('closed');
});

test('ticket title is required', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();

    $this->actingAs($user)
        ->post(route('tickets.store'), [
            'project_id' => $project->id,
            'title' => '',
            'description' => 'Descrição',
            'status' => 'open',
            'priority' => 'medium',
        ])->assertSessionHasErrors('title');
});

test('authenticated user can delete a ticket', function () {
    $user = User::factory()->create();
    $ticket = Ticket::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->delete(route('tickets.destroy', $ticket))
        ->assertRedirect(route('tickets.index'));

    expect(Ticket::find($ticket->id))->toBeNull();
});
