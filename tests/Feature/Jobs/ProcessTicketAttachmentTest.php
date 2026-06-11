<?php

use App\Jobs\ProcessTicketAttachment;
use App\Models\Ticket;
use App\Models\TicketDetail;
use App\Models\User;
use App\Notifications\TicketProcessed;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

test('job is dispatched when ticket has attachment', function () {
    Queue::fake();

    $user = User::factory()->create();
    $project = \App\Models\Project::factory()->create();

    Storage::fake('local');
    Storage::disk('local')->put('attachments/test.txt', 'conteúdo de teste');

    $ticket = Ticket::factory()->create([
        'user_id' => $user->id,
        'project_id' => $project->id,
        'attachment_path' => 'attachments/test.txt',
    ]);
    TicketDetail::factory()->create(['ticket_id' => $ticket->id]);

    ProcessTicketAttachment::dispatch($ticket);

    Queue::assertPushed(ProcessTicketAttachment::class);
});

test('job processes txt attachment and updates ticket detail', function () {
    Notification::fake();
    Storage::fake('local');

    $user = User::factory()->create();
    $ticket = Ticket::factory()->create([
        'user_id' => $user->id,
        'attachment_path' => 'attachments/test.txt',
    ]);
    TicketDetail::factory()->create(['ticket_id' => $ticket->id]);

    Storage::disk('local')->put('attachments/test.txt', 'Conteúdo técnico do arquivo.');

    (new ProcessTicketAttachment($ticket))->handle();

    $detail = $ticket->fresh()->detail;

    expect($detail->technical_notes)->toBe('Conteúdo técnico do arquivo.')
        ->and($detail->processed_at)->not->toBeNull();
});

test('job processes json attachment and stores additional_data', function () {
    Notification::fake();
    Storage::fake('local');

    $user = User::factory()->create();
    $ticket = Ticket::factory()->create([
        'user_id' => $user->id,
        'attachment_path' => 'attachments/data.json',
    ]);
    TicketDetail::factory()->create(['ticket_id' => $ticket->id]);

    Storage::disk('local')->put('attachments/data.json', json_encode(['error' => 'NullPointerException', 'line' => 42]));

    (new ProcessTicketAttachment($ticket))->handle();

    $detail = $ticket->fresh()->detail;

    expect($detail->additional_data)->toBeArray()
        ->and($detail->additional_data['error'])->toBe('NullPointerException')
        ->and($detail->processed_at)->not->toBeNull();
});

test('job sends notification to ticket owner after processing', function () {
    Notification::fake();
    Storage::fake('local');

    $user = User::factory()->create();
    $ticket = Ticket::factory()->create([
        'user_id' => $user->id,
        'attachment_path' => 'attachments/test.txt',
    ]);
    TicketDetail::factory()->create(['ticket_id' => $ticket->id]);

    Storage::disk('local')->put('attachments/test.txt', 'Nota técnica.');

    (new ProcessTicketAttachment($ticket))->handle();

    Notification::assertSentTo($user, TicketProcessed::class);
});

test('job does nothing when attachment file does not exist', function () {
    Notification::fake();
    Storage::fake('local');

    $user = User::factory()->create();
    $ticket = Ticket::factory()->create([
        'user_id' => $user->id,
        'attachment_path' => 'attachments/missing.txt',
    ]);
    $detail = TicketDetail::factory()->create(['ticket_id' => $ticket->id]);

    (new ProcessTicketAttachment($ticket))->handle();

    expect($detail->fresh()->processed_at)->toBeNull();
    Notification::assertNothingSent();
});
