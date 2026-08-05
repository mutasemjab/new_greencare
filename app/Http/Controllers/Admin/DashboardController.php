<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;

class DashboardController extends Controller
{

    public function index()
    {
        $stats = $this->stats->adminStats();

        $recentContacts = ContactMessage::where('status', 'new')
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('recentContacts'));
    }
}
