<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'technical_notes',
        'additional_data',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'additional_data' => 'array',
            'processed_at' => 'datetime',
        ];
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
