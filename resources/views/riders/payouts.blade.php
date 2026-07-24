<x-admin-layout :title="'Rider Payouts'">
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-6 py-4 border-b">
            <p class="text-sm text-gray-500">Per-delivery earnings, not a flat "N rides = fixed amount" formula — each rider's owed total is the sum of the fee on every delivery they've completed, minus whatever's already been marked paid.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide">
                    <tr>
                        <th class="py-3 px-6">Rider</th>
                        <th class="py-3 px-6">Vehicle</th>
                        <th class="py-3 px-6">Deliveries</th>
                        <th class="py-3 px-6">Total Earned</th>
                        <th class="py-3 px-6">Total Paid</th>
                        <th class="py-3 px-6">Owed</th>
                        <th class="py-3 px-6">Last Paid</th>
                        <th class="py-3 px-6">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($rows as $r)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-6 font-medium text-gray-900">{{ $r['full_name'] }}</td>
                            <td class="py-3 px-6 text-gray-600">{{ $r['vehicle_type'] ?? '—' }}</td>
                            <td class="py-3 px-6 text-gray-600">{{ $r['delivery_count'] }}</td>
                            <td class="py-3 px-6 text-gray-600">PKR {{ number_format($r['total_earned'], 2) }}</td>
                            <td class="py-3 px-6 text-gray-600">PKR {{ number_format($r['total_paid'], 2) }}</td>
                            <td class="py-3 px-6 font-semibold {{ $r['owed'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                                PKR {{ number_format($r['owed'], 2) }}
                            </td>
                            <td class="py-3 px-6 text-gray-500 text-xs">
                                {{ $r['last_paid_at'] ? \Illuminate\Support\Carbon::parse($r['last_paid_at'])->format('M j, Y') : 'Never' }}
                            </td>
                            <td class="py-3 px-6">
                                @if ($r['owed'] > 0)
                                    <form method="POST" action="{{ route('riders.payouts.pay', $r['id']) }}" onsubmit="return confirm('Mark PKR {{ number_format($r['owed'], 2) }} as paid to {{ $r['full_name'] }}?');">
                                        @csrf
                                        <input type="hidden" name="amount" value="{{ $r['owed'] }}">
                                        <button class="bg-green-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-green-700">
                                            Mark Paid
                                        </button>
                                    </form>
                                @else
                                    <span class="text-gray-400 text-xs">Settled</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="py-6 px-6 text-gray-400 text-center">No riders found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>
