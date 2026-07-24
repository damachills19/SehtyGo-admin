<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SupabaseWebhookController extends Controller
{
    protected array $roleLabels = [
        'wc_doctors' => 'doctor',
        'wc_labs' => 'lab',
        'wc_pharmacies' => 'pharmacy',
        'wc_riders' => 'rider',
    ];

    public function newRegistration(Request $request)
    {
        abort_unless(
            hash_equals(config('services.supabase_webhook.secret'), (string) $request->header('X-Webhook-Secret')),
            403
        );

        $table = $request->input('table');
        $record = $request->input('record', []);

        if (($record['verification_status'] ?? null) !== 'pending') {
            return response()->noContent();
        }

        $role = $this->roleLabels[$table] ?? 'account';
        $name = $record['full_name'] ?? $record['name'] ?? 'Someone';

        Http::withHeaders([
            'Authorization' => 'Basic '.config('services.onesignal.rest_api_key'),
            'Content-Type' => 'application/json; charset=utf-8',
        ])->post('https://onesignal.com/api/v1/notifications', [
            'app_id' => config('services.onesignal.app_id'),
            'filters' => [
                ['field' => 'tag', 'key' => 'role', 'relation' => '=', 'value' => 'admin'],
            ],
            'headings' => ['en' => 'New approval waiting'],
            'contents' => ['en' => "{$name} applied as a {$role} — tap to review."],
            'url' => config('app.url').'/approvals',
        ]);

        return response()->noContent();
    }
}
