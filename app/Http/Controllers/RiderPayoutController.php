<?php

namespace App\Http\Controllers;

use App\Services\SupabaseService;

/**
 * Read-only: the admin platform never handles rider/pharmacy/patient
 * money directly (that's settled between those parties themselves), so
 * this screen only shows that a delivery has been handed over —
 * Completed/Delivered — with no "mark paid" action or dollar figures
 * implying the admin is a party to that payment.
 */
class RiderPayoutController extends Controller
{
    public function index(SupabaseService $supabase)
    {
        $riders = $supabase->select('wc_riders', ['select' => 'id,full_name,vehicle_type']);

        $delivered = $supabase->select('wc_rider_assignments', [
            'select' => 'rider_id,status,delivered_at',
            'order' => 'delivered_at.desc',
        ]);

        $rows = [];
        foreach ($riders as $rider) {
            $riderId = $rider['id'];
            $assignments = array_values(array_filter($delivered, fn ($d) => $d['rider_id'] === $riderId));
            $completed = array_filter($assignments, fn ($d) => $d['status'] === 'delivered');

            $rows[] = [
                'id' => $riderId,
                'full_name' => $rider['full_name'],
                'vehicle_type' => $rider['vehicle_type'],
                'delivery_count' => count($completed),
                'last_status' => $assignments[0]['status'] ?? null,
                'last_delivered_at' => $assignments[0]['delivered_at'] ?? null,
            ];
        }

        usort($rows, fn ($a, $b) => $b['delivery_count'] <=> $a['delivery_count']);

        return view('riders.payouts', ['rows' => $rows]);
    }
}
