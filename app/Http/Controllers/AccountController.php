<?php

namespace App\Http\Controllers;

use App\Services\SupabaseService;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    protected array $tables = [
        'doctor' => 'wc_doctors',
        'lab' => 'wc_labs',
        'pharmacy' => 'wc_pharmacies',
        'rider' => 'wc_riders',
        'patient' => 'wc_profiles',
    ];

    public function index(Request $request, SupabaseService $supabase)
    {
        $role = $request->query('role', 'doctor');
        abort_unless(isset($this->tables[$role]), 404);

        $query = ['order' => 'created_at.desc'];
        if ($search = $request->query('q')) {
            $column = $role === 'patient' ? 'full_name' : 'name';
            if ($role === 'rider') {
                $column = 'full_name';
            }
            $query[$column] = "ilike.*{$search}*";
        }

        $accounts = $supabase->select($this->tables[$role], $query);

        return view('accounts.index', [
            'role' => $role,
            'roles' => array_keys($this->tables),
            'accounts' => $accounts,
            'search' => $search ?? '',
        ]);
    }

    public function toggleSuspend(Request $request, SupabaseService $supabase, string $role, string $id)
    {
        abort_unless(in_array($role, ['doctor', 'lab', 'pharmacy', 'rider']), 404);

        $status = $request->input('status') === 'approved' ? 'approved' : 'rejected';
        $supabase->update($this->tables[$role], ['id' => "eq.{$id}"], ['verification_status' => $status]);

        return back()->with('status', 'Account status updated.');
    }
}
