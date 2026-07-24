<?php

namespace App\Http\Controllers;

use App\Services\SupabaseService;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index(SupabaseService $supabase)
    {
        $counts = [];
        foreach ([
            'doctors' => 'wc_doctors',
            'labs' => 'wc_labs',
            'pharmacies' => 'wc_pharmacies',
            'riders' => 'wc_riders',
            'patients' => 'wc_profiles',
            'bookings' => 'wc_bookings',
            'medicines' => 'wc_medicines',
            'support_tickets' => 'wc_support_tickets',
        ] as $label => $table) {
            $counts[$label] = count($supabase->select($table, ['select' => 'id']));
        }

        $pendingCount = 0;
        foreach (['wc_doctors', 'wc_labs', 'wc_pharmacies', 'wc_riders'] as $table) {
            $pendingCount += count($supabase->select($table, ['select' => 'id', 'verification_status' => 'eq.pending']));
        }

        $recentBookings = $supabase->select('wc_bookings', [
            'select' => 'id,status,scheduled_at,created_at',
            'order' => 'created_at.desc',
            'limit' => '8',
        ]);

        $openTickets = $supabase->select('wc_support_tickets', [
            'select' => 'id,subject,status,created_at',
            'status' => 'eq.open',
            'order' => 'created_at.desc',
            'limit' => '5',
        ]);

        $growth = $this->signupTrend($supabase);

        return view('dashboard', compact('counts', 'pendingCount', 'recentBookings', 'openTickets', 'growth'));
    }

    /**
     * Monthly signup counts per role for the last 6 months, so the
     * dashboard can plot a 5-line growth chart. wc_profiles has no role
     * breakdown worth graphing separately from the role tables, so
     * "patients" here is wc_profiles minus staff sign-ups isn't needed —
     * we just chart each role table's own created_at directly.
     */
    protected function signupTrend(SupabaseService $supabase): array
    {
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $months[] = Carbon::now()->subMonths($i)->format('Y-m');
        }

        $series = [];
        foreach ([
            'Doctors' => 'wc_doctors',
            'Labs' => 'wc_labs',
            'Pharmacies' => 'wc_pharmacies',
            'Riders' => 'wc_riders',
            'Patients' => 'wc_profiles',
        ] as $label => $table) {
            $rows = $supabase->select($table, [
                'select' => 'created_at',
                'created_at' => 'gte.'.Carbon::now()->subMonths(5)->startOfMonth()->toIso8601String(),
            ]);

            $buckets = array_fill_keys($months, 0);
            foreach ($rows as $row) {
                $key = Carbon::parse($row['created_at'])->format('Y-m');
                if (isset($buckets[$key])) {
                    $buckets[$key]++;
                }
            }

            $series[$label] = array_values($buckets);
        }

        return [
            'labels' => array_map(fn ($m) => Carbon::createFromFormat('Y-m', $m)->format('M Y'), $months),
            'series' => $series,
        ];
    }
}
