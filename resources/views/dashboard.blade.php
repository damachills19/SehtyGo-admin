<x-admin-layout :title="'Dashboard'">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-5 mb-8">
        @php
            $cards = [
                ['label' => 'Doctors', 'value' => $counts['doctors'], 'color' => 'bg-[#16243E]/5 text-[#16243E]'],
                ['label' => 'Labs', 'value' => $counts['labs'], 'color' => 'bg-emerald-50 text-emerald-700'],
                ['label' => 'Pharmacies', 'value' => $counts['pharmacies'], 'color' => 'bg-amber-50 text-amber-700'],
                ['label' => 'Riders', 'value' => $counts['riders'], 'color' => 'bg-rose-50 text-rose-700'],
                ['label' => 'Patients', 'value' => $counts['patients'], 'color' => 'bg-[#0EA5A4]/10 text-[#0EA5A4]'],
                ['label' => 'Bookings', 'value' => $counts['bookings'], 'color' => 'bg-purple-50 text-purple-700'],
                ['label' => 'Medicines', 'value' => $counts['medicines'], 'color' => 'bg-teal-50 text-teal-700'],
                ['label' => 'Open Tickets', 'value' => $counts['support_tickets'], 'color' => 'bg-orange-50 text-orange-700'],
            ];
        @endphp
        @foreach ($cards as $card)
            <div class="bg-white rounded-2xl shadow-sm p-5">
                <div class="text-2xl font-bold text-gray-900">{{ $card['value'] }}</div>
                <div class="text-sm text-gray-500 mt-1">{{ $card['label'] }}</div>
            </div>
        @endforeach
    </div>

    <div class="bg-white rounded-2xl shadow-sm p-6 mb-8">
        <h3 class="font-semibold text-gray-800 mb-4">User Growth (last 6 months)</h3>
        <canvas id="growthChart" height="90"></canvas>
    </div>

    @if ($pendingCount > 0)
        <a href="{{ route('approvals.index') }}" class="block mb-8 bg-[#0EA5A4]/10 border border-[#0EA5A4]/20 text-[#16243E] rounded-2xl px-5 py-4 hover:bg-[#0EA5A4]/15 transition">
            <span class="font-semibold">{{ $pendingCount }}</span> account{{ $pendingCount === 1 ? '' : 's' }} waiting for approval — click to review.
        </a>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Recent Bookings</h3>
            @forelse ($recentBookings as $b)
                <div class="flex items-center justify-between py-2 border-b last:border-0 text-sm">
                    <span class="text-gray-500">{{ \Illuminate\Support\Carbon::parse($b['created_at'])->format('M j, g:ia') }}</span>
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium
                        {{ $b['status'] === 'Completed' ? 'bg-green-100 text-green-700' : ($b['status'] === 'Cancelled' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                        {{ $b['status'] }}
                    </span>
                </div>
            @empty
                <p class="text-gray-400 text-sm">No bookings yet.</p>
            @endforelse
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Open Support Tickets</h3>
            @forelse ($openTickets as $t)
                <div class="py-2 border-b last:border-0 text-sm">
                    <div class="font-medium text-gray-800">{{ $t['subject'] }}</div>
                    <div class="text-gray-400 text-xs">{{ \Illuminate\Support\Carbon::parse($t['created_at'])->diffForHumans() }}</div>
                </div>
            @empty
                <p class="text-gray-400 text-sm">No open tickets.</p>
            @endforelse
        </div>
    </div>

    @push('scripts')
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                const ctx = document.getElementById('growthChart');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json($growth['labels']),
                        datasets: [
                            { label: 'Doctors', data: @json($growth['series']['Doctors']), borderColor: '#16243E', backgroundColor: '#16243E', tension: 0.35 },
                            { label: 'Labs', data: @json($growth['series']['Labs']), borderColor: '#10B981', backgroundColor: '#10B981', tension: 0.35 },
                            { label: 'Pharmacies', data: @json($growth['series']['Pharmacies']), borderColor: '#F59E0B', backgroundColor: '#F59E0B', tension: 0.35 },
                            { label: 'Riders', data: @json($growth['series']['Riders']), borderColor: '#F43F5E', backgroundColor: '#F43F5E', tension: 0.35 },
                            { label: 'Patients', data: @json($growth['series']['Patients']), borderColor: '#6366F1', backgroundColor: '#6366F1', tension: 0.35 },
                        ],
                    },
                    options: {
                        responsive: true,
                        interaction: { mode: 'index', intersect: false },
                        scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
                    },
                });
            });
        </script>
    @endpush
</x-admin-layout>
