<x-admin-layout :title="'Accounts'">
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-6 py-4 border-b flex flex-wrap items-center justify-between gap-3">
            <div class="flex gap-1">
                @foreach ($roles as $r)
                    <a href="{{ route('accounts.index', ['role' => $r]) }}"
                       class="px-3 py-1.5 rounded-lg text-sm font-medium capitalize
                       {{ $role === $r ? 'bg-[#16243E] text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                        {{ $r }}s
                    </a>
                @endforeach
            </div>

            <form method="GET" action="{{ route('accounts.index') }}" class="flex gap-2">
                <input type="hidden" name="role" value="{{ $role }}">
                <input type="text" name="q" value="{{ $search }}" placeholder="Search by name..."
                       class="border-gray-300 rounded-lg text-sm focus:ring-[#16243E] focus:border-[#16243E]">
                <button class="bg-gray-100 px-3 py-1.5 rounded-lg text-sm font-medium hover:bg-gray-200">Search</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide">
                    <tr>
                        <th class="py-3 px-6">Name</th>
                        <th class="py-3 px-6">Contact</th>
                        @if ($role !== 'patient')
                            <th class="py-3 px-6">Status</th>
                        @endif
                        <th class="py-3 px-6">Joined</th>
                        @if ($role !== 'patient')
                            <th class="py-3 px-6">Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($accounts as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-6 font-medium text-gray-900">
                                {{ $item['name'] ?? $item['full_name'] ?? '—' }}
                            </td>
                            <td class="py-3 px-6 text-gray-600">
                                {{ $item['contact'] ?? $item['phone'] ?? $item['email'] ?? '—' }}
                            </td>
                            @if ($role !== 'patient')
                                <td class="py-3 px-6">
                                    @php $vs = $item['verification_status'] ?? 'approved'; @endphp
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                        {{ $vs === 'approved' ? 'bg-green-100 text-green-700' : ($vs === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                        {{ ucfirst($vs) }}
                                    </span>
                                </td>
                            @endif
                            <td class="py-3 px-6 text-gray-600">
                                {{ \Illuminate\Support\Carbon::parse($item['created_at'])->format('M j, Y') }}
                            </td>
                            @if ($role !== 'patient')
                                <td class="py-3 px-6 whitespace-nowrap">
                                    @if (($item['verification_status'] ?? 'approved') === 'approved')
                                        <form method="POST" action="{{ route('accounts.toggle', [$role, $item['id']]) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="rejected">
                                            <button class="bg-red-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-red-700">Suspend</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('accounts.toggle', [$role, $item['id']]) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="approved">
                                            <button class="bg-green-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-green-700">Reinstate</button>
                                        </form>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-6 px-6 text-gray-400 text-center">No accounts found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>
