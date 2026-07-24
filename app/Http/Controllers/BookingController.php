<?php

namespace App\Http\Controllers;

use App\Services\SupabaseService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request, SupabaseService $supabase)
    {
        $query = ['order' => 'created_at.desc', 'limit' => '100'];
        if ($status = $request->query('status')) {
            $query['status'] = "eq.{$status}";
        }

        $bookings = $supabase->select('wc_bookings', $query);

        return view('bookings.index', compact('bookings', 'status'));
    }
}
