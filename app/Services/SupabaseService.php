<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

/**
 * Thin wrapper over Supabase's PostgREST API using the service_role key,
 * which bypasses RLS. This admin portal is a separate Laravel app with its
 * own login, so it never authenticates as a Supabase auth.users row — it
 * always talks to the same wc_* tables the Flutter app uses, just with
 * full read/write access.
 */
class SupabaseService
{
    protected string $url;
    protected string $key;

    public function __construct()
    {
        $this->url = rtrim(Config::get('supabase.url'), '/');
        $this->key = Config::get('supabase.service_key');
    }

    protected function client()
    {
        return Http::baseUrl("{$this->url}/rest/v1")
            ->withHeaders([
                'apikey' => $this->key,
                'Authorization' => "Bearer {$this->key}",
                'Content-Type' => 'application/json',
                'Prefer' => 'return=representation',
            ]);
    }

    public function select(string $table, array $query = []): array
    {
        $response = $this->client()->get("/{$table}", $query);
        $response->throw();

        return $response->json();
    }

    public function insert(string $table, array $data): array
    {
        $response = $this->client()->post("/{$table}", $data);
        $response->throw();

        return $response->json();
    }

    /**
     * PATCH with query-string filters (e.g. ['id' => 'eq.<uuid>']) — Http's
     * patch() signature doesn't take query params directly like get() does,
     * so build the URL manually.
     */
    public function update(string $table, array $filters, array $data): array
    {
        $query = http_build_query($filters);
        $response = $this->client()->patch("/{$table}?{$query}", $data);
        $response->throw();

        return $response->json();
    }

    public function deleteWhere(string $table, array $filters): void
    {
        $query = http_build_query($filters);
        $response = $this->client()->delete("/{$table}?{$query}");
        $response->throw();
    }
}
