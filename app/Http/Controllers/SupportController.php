<?php

namespace App\Http\Controllers;

use App\Services\SupabaseService;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function index(Request $request, SupabaseService $supabase)
    {
        $query = ['order' => 'created_at.desc'];
        if ($status = $request->query('status')) {
            $query['status'] = "eq.{$status}";
        }

        $tickets = $supabase->select('wc_support_tickets', $query);

        return view('support.index', compact('tickets', 'status'));
    }

    public function updateStatus(Request $request, SupabaseService $supabase, string $id)
    {
        $request->validate(['status' => 'required|in:open,in_progress,resolved']);

        $supabase->update('wc_support_tickets', ['id' => "eq.{$id}"], ['status' => $request->input('status')]);

        return back()->with('status', 'Ticket status updated.');
    }

    public function reply(Request $request, SupabaseService $supabase, string $id)
    {
        $request->validate(['admin_reply' => 'required|string|max:2000']);

        $supabase->update('wc_support_tickets', ['id' => "eq.{$id}"], [
            'admin_reply' => $request->input('admin_reply'),
            'replied_at' => now()->toIso8601String(),
            'status' => $request->input('status', 'in_progress'),
        ]);

        return back()->with('status', 'Reply sent to the user.');
    }
}
