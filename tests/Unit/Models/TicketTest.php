<?php

use App\Models\Project;
use App\Models\Ticket;
use App\Models\TicketDetail;
use App\Models\User;

test('ticket belongs to a project', function () {
    $ticket = Ticket::factory()->create();

    expect($ticket->project)->toBeInstanceOf(Project::class);
});

test('ticket belongs to a user', function () {
    $ticket = Ticket::factory()->create();

    expect($ticket->user)->toBeInstanceOf(User::class);
});

test('ticket has one detail', function () {
    $ticket = Ticket::factory()->create();
    TicketDetail::factory()->create(['ticket_id' => $ticket->id]);

    expect($ticket->detail)->toBeInstanceOf(TicketDetail::class);
});

test('ticket detail is unique per ticket', function () {
    $ticket = Ticket::factory()->create();
    TicketDetail::factory()->create(['ticket_id' => $ticket->id]);

    expect(fn () => TicketDetail::factory()->create(['ticket_id' => $ticket->id]))
        ->toThrow(\Illuminate\Database\QueryException::class);
});

test('ticket detail casts additional_data as array', function () {
    $ticket = Ticket::factory()->create();
    $detail = TicketDetail::factory()->processed()->create(['ticket_id' => $ticket->id]);

    expect($detail->additional_data)->toBeArray()
        ->and($detail->processed_at)->not->toBeNull();
});
