<x-admin-layout :title="'Bookings'">
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-6 py-4 border-b flex gap-1">
            <a href="{{ route('bookings.index') }}" class="px-3 py-1.5 rounded-lg text-sm font-medium {{ !$status ? 'bg-[#16243E] text-white' : 'text-gray-600 hover:bg-gray-100' }}">All</a>
            @foreach (['Upcoming', 'Completed', 'Cancelled'] as $s)
                <a href="{{ route('bookings.index', ['status' => $s]) }}"
                   class="px-3 py-1.5 rounded-lg text-sm font-medium capitalize {{ $status === $s ? 'bg-[#16243E] text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                    {{ $s }}
                </a>
            @endforeach
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide">
                    <tr>
                        <th class="py-3 px-6">Booking ID</th>
                        <th class="py-3 px-6">Title</th>
                        <th class="py-3 px-6">Scheduled</th>
                        <th class="py-3 px-6">Status</th>
                        <th class="py-3 px-6">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($bookings as $b)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-6 font-mono text-xs text-gray-500">{{ substr($b['id'], 0, 8) }}</td>
                            <td class="py-3 px-6 text-gray-600">
                                {{ $b['title'] }} <span class="text-xs text-gray-400 capitalize">({{ $b['booking_type'] }})</span>
                            </td>
                            <td class="py-3 px-6 text-gray-600">
                                {{ $b['scheduled_at'] ? \Illuminate\Support\Carbon::parse($b['scheduled_at'])->format('M j, Y g:ia') : '—' }}
                            </td>
                            <td class="py-3 px-6">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ $b['status'] === 'Completed' ? 'bg-green-100 text-green-700' : ($b['status'] === 'Cancelled' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                    {{ $b['status'] }}
                                </span>
                            </td>
                            <td class="py-3 px-6 text-gray-600">
                                {{ \Illuminate\Support\Carbon::parse($b['created_at'])->format('M j, Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-6 px-6 text-gray-400 text-center">No bookings found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>
