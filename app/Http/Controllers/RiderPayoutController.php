<?php

namespace App\Http\Controllers;

use App\Services\SupabaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class RiderPayoutController extends Controller
{
    public function index(SupabaseService $supabase)
    {
        $riders = $supabase->select('wc_riders', ['select' => 'id,full_name,vehicle_type']);

        $delivered = $supabase->select('wc_rider_assignments', [
            'select' => 'rider_id,delivered_at,wc_bookings(fee,currency)',
            'status' => 'eq.delivered',
        ]);

        $payouts = $supabase->select('wc_rider_payouts', ['select' => 'rider_id,amount,paid_at']);

        $rows = [];
        foreach ($riders as $rider) {
            $riderId = $rider['id'];

            $deliveries = array_filter($delivered, fn ($d) => $d['rider_id'] === $riderId);
            $totalEarned = array_sum(array_map(fn ($d) => (float) ($d['wc_bookings']['fee'] ?? 0), $deliveries));

            $riderPayouts = array_filter($payouts, fn ($p) => $p['rider_id'] === $riderId);
            $totalPaid = array_sum(array_map(fn ($p) => (float) $p['amount'], $riderPayouts));

            $lastPaidAt = collect($riderPayouts)->max('paid_at');

            $rows[] = [
                'id' => $riderId,
                'full_name' => $rider['full_name'],
                'vehicle_type' => $rider['vehicle_type'],
                'delivery_count' => count($deliveries),
                'total_earned' => $totalEarned,
                'total_paid' => $totalPaid,
                'owed' => round($totalEarned - $totalPaid, 2),
                'last_paid_at' => $lastPaidAt,
            ];
        }

        usort($rows, fn ($a, $b) => $b['owed'] <=> $a['owed']);

        return view('riders.payouts', ['rows' => $rows]);
    }

    public function markPaid(Request $request, SupabaseService $supabase, string $riderId)
    {
        $request->validate(['amount' => 'required|numeric|min:0.01']);

        $lastPayout = $supabase->select('wc_rider_payouts', [
            'select' => 'paid_at',
            'rider_id' => "eq.{$riderId}",
            'order' => 'paid_at.desc',
            'limit' => '1',
        ]);

        $periodStart = $lastPayout[0]['paid_at'] ?? Carbon::now()->subMonths(6)->toIso8601String();

        $supabase->insert('wc_rider_payouts', [
            'rider_id' => $riderId,
            'amount' => $request->input('amount'),
            'period_start' => $periodStart,
            'period_end' => Carbon::now()->toIso8601String(),
        ]);

        return back()->with('status', 'Payout recorded.');
    }
}
