<?php

namespace App\Http\Controllers;

use App\Services\SupabaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApprovalController extends Controller
{
    protected array $tables = [
        'doctor' => 'wc_doctors',
        'lab' => 'wc_labs',
        'pharmacy' => 'wc_pharmacies',
        'rider' => 'wc_riders',
    ];

    public function index(SupabaseService $supabase)
    {
        $pending = [];
        foreach ($this->tables as $role => $table) {
            $pending[$role] = $supabase->select($table, [
                'verification_status' => 'eq.pending',
                'order' => 'created_at.desc',
            ]);
        }

        return view('approvals.index', ['pending' => $pending]);
    }

    public function updateStatus(Request $request, SupabaseService $supabase, string $role, string $id)
    {
        abort_unless(isset($this->tables[$role]), 404);

        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $status = $request->input('status');
        $rows = $supabase->update(
            $this->tables[$role],
            ['id' => "eq.{$id}"],
            ['verification_status' => $status]
        );

        // Best-effort push to the staff member whose account this is —
        // never lets a notification failure hide that the actual approval
        // decision above already succeeded.
        $authUserId = $rows[0]['auth_user_id'] ?? null;
        if ($authUserId) {
            try {
                Http::withHeaders(['apikey' => config('supabase.service_key')])
                    ->post(config('supabase.url').'/functions/v1/send-push', [
                        'userId' => $authUserId,
                        'title' => $status === 'approved' ? 'Application approved!' : 'Application update',
                        'body' => $status === 'approved'
                            ? "Your {$role} account has been approved — you're live now."
                            : "Your {$role} application wasn't approved this time.",
                    ]);
            } catch (\Throwable $e) {
                // swallow — see comment above
            }
        }

        return back()->with('status', ucfirst($role).' application '.$status.'.');
    }
}
