<x-admin-layout :title="'Support Tickets'">
    <div class="bg-white rounded-xl border overflow-hidden mb-6">
        <div class="px-6 py-4 border-b flex gap-1">
            <a href="{{ route('support.index') }}" class="px-3 py-1.5 rounded-lg text-sm font-medium {{ !$status ? 'bg-[#16243E] text-white' : 'text-gray-600 hover:bg-gray-100' }}">All</a>
            @foreach (['open', 'in_progress', 'resolved'] as $s)
                <a href="{{ route('support.index', ['status' => $s]) }}"
                   class="px-3 py-1.5 rounded-lg text-sm font-medium capitalize {{ $status === $s ? 'bg-[#16243E] text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                    {{ str_replace('_', ' ', $s) }}
                </a>
            @endforeach
        </div>
    </div>

    <div class="space-y-4">
        @forelse ($tickets as $t)
            <div class="bg-white rounded-xl border p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="font-semibold text-gray-900">{{ $t['subject'] }}</h3>
                        <p class="text-xs text-gray-500 mt-1">
                            <span class="font-medium">{{ $t['reporter_name'] ?? 'Unknown' }}</span>
                            @if ($t['reporter_role'] ?? null)
                                <span class="capitalize text-gray-400">({{ $t['reporter_role'] }})</span>
                            @endif
                            @if ($t['reporter_email'] ?? null)
                                &middot; {{ $t['reporter_email'] }}
                            @endif
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">Filed {{ \Illuminate\Support\Carbon::parse($t['created_at'])->format('M j, Y g:ia') }}</p>
                    </div>
                    <span class="shrink-0 px-2 py-0.5 rounded-full text-xs font-medium
                        {{ $t['status'] === 'resolved' ? 'bg-green-100 text-green-700' : ($t['status'] === 'in_progress' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700') }}">
                        {{ str_replace('_', ' ', ucfirst($t['status'])) }}
                    </span>
                </div>

                <p class="text-sm text-gray-600 mt-3 whitespace-pre-line">{{ $t['body'] }}</p>

                @if ($t['admin_reply'] ?? null)
                    <div class="mt-4 bg-blue-50 border border-blue-100 rounded-lg p-4">
                        <p class="text-xs font-semibold text-blue-700 mb-1">
                            Admin replied {{ \Illuminate\Support\Carbon::parse($t['replied_at'])->diffForHumans() }}
                        </p>
                        <p class="text-sm text-blue-900 whitespace-pre-line">{{ $t['admin_reply'] }}</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('support.reply', $t['id']) }}" class="mt-4 flex flex-col gap-2">
                    @csrf
                    <textarea name="admin_reply" rows="2" placeholder="Write a reply the user will see, e.g. &quot;We're looking into this, will resolve within a day.&quot;"
                              class="w-full border-gray-300 rounded-lg text-sm focus:ring-[#16243E] focus:border-[#16243E]">{{ $t['admin_reply'] ?? '' }}</textarea>
                    <div class="flex items-center justify-between">
                        <select name="status" class="border-gray-300 rounded-lg text-xs">
                            <option value="open" @selected($t['status'] === 'open')>Open</option>
                            <option value="in_progress" @selected($t['status'] === 'in_progress')>In progress</option>
                            <option value="resolved" @selected($t['status'] === 'resolved')>Resolved</option>
                        </select>
                        <button class="bg-[#16243E] text-white px-4 py-1.5 rounded-lg text-xs font-medium hover:bg-blue-700">
                            Send Reply
                        </button>
                    </div>
                </form>
            </div>
        @empty
            <div class="bg-white rounded-xl border p-10 text-center text-gray-400">No support tickets.</div>
        @endforelse
    </div>
</x-admin-layout>
