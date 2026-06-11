<?php

namespace App\Jobs;

use App\Models\Ticket;
use App\Notifications\TicketProcessed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessTicketAttachment implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $timeout = 60;

    public function __construct(public Ticket $ticket)
    {
    }

    public function handle(): void
    {
        $ticket = $this->ticket->fresh(['detail', 'user']);

        if (! $ticket || ! $ticket->attachment_path) {
            return;
        }

        if (! Storage::disk('local')->exists($ticket->attachment_path)) {
            Log::warning("Attachment not found for ticket #{$ticket->id}");
            return;
        }

        $content = Storage::disk('local')->get($ticket->attachment_path);
        $extension = pathinfo($ticket->attachment_path, PATHINFO_EXTENSION);

        $additionalData = null;
        $technicalNotes = null;

        if ($extension === 'json') {
            $decoded = json_decode($content, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $additionalData = $decoded;
                $technicalNotes = 'Dados JSON processados automaticamente via fila.';
            } else {
                $technicalNotes = "Arquivo JSON inválido: ".json_last_error_msg();
            }
        } else {
            $technicalNotes = substr($content, 0, 5000);
        }

        $ticket->detail()->updateOrCreate(
            ['ticket_id' => $ticket->id],
            [
                'technical_notes' => $technicalNotes,
                'additional_data' => $additionalData,
                'processed_at' => now(),
            ]
        );

        $ticket->user->notify(new TicketProcessed($ticket));

        Log::info("Ticket #{$ticket->id} attachment processed successfully.");
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Failed to process attachment for ticket #{$this->ticket->id}: ".$exception->getMessage());
    }
}
