<x-admin-layout :title="'Rider Deliveries'">
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-6 py-4 border-b">
            <p class="text-sm text-gray-500">Read-only handover status — riders and pharmacies settle payment between themselves; the admin just confirms a delivery went through.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide">
                    <tr>
                        <th class="py-3 px-6">Rider</th>
                        <th class="py-3 px-6">Vehicle</th>
                        <th class="py-3 px-6">Deliveries Completed</th>
                        <th class="py-3 px-6">Last Status</th>
                        <th class="py-3 px-6">Last Delivered</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($rows as $r)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-6 font-medium text-gray-900">{{ $r['full_name'] }}</td>
                            <td class="py-3 px-6 text-gray-600">{{ $r['vehicle_type'] ?? '—' }}</td>
                            <td class="py-3 px-6 text-gray-600">{{ $r['delivery_count'] }}</td>
                            <td class="py-3 px-6">
                                @if ($r['last_status'] === 'delivered')
                                    <span class="text-green-600 font-medium">Delivered</span>
                                @elseif ($r['last_status'])
                                    <span class="text-gray-500">{{ ucfirst($r['last_status']) }}</span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="py-3 px-6 text-gray-500 text-xs">
                                {{ $r['last_delivered_at'] ? \Illuminate\Support\Carbon::parse($r['last_delivered_at'])->format('M j, Y') : 'Never' }}
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-6 px-6 text-gray-400 text-center">No riders found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>
