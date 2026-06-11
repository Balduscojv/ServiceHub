<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Project;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $stats = [
            'companies' => Company::count(),
            'projects' => Project::count(),
            'tickets' => Ticket::count(),
            'my_tickets' => Ticket::where('user_id', $request->user()->id)->count(),
        ];

        $recentTickets = Ticket::with(['project.company', 'user'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->take(5)
            ->get();

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'recentTickets' => $recentTickets,
        ]);
    }
}
