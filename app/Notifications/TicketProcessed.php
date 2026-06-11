<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketProcessed extends Notification
{
    use Queueable;

    public function __construct(public Ticket $ticket)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Ticket #{$this->ticket->id} processado - {$this->ticket->title}")
            ->greeting("Olá, {$notifiable->name}!")
            ->line("O ticket **{$this->ticket->title}** foi processado com sucesso.")
            ->line("Os dados do anexo foram extraídos e adicionados aos detalhes técnicos.")
            ->action('Ver Ticket', url('/tickets/'.$this->ticket->id))
            ->line('Obrigado por usar o ServiceHub.');
    }
}
