<?php

namespace App\Http\Controllers;

use App\Services\SupabaseService;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request, SupabaseService $supabase)
    {
        $query = ['order' => 'created_at.desc', 'limit' => '200'];
        if ($search = $request->query('q')) {
            $query['name'] = "ilike.*{$search}*";
        }

        $medicines = $supabase->select('wc_medicines', $query);

        return view('catalog.index', ['medicines' => $medicines, 'search' => $search ?? '']);
    }

    public function destroy(SupabaseService $supabase, string $id)
    {
        $supabase->deleteWhere('wc_medicines', ['id' => "eq.{$id}"]);

        return back()->with('status', 'Medicine removed from catalog.');
    }
}
