<x-admin-layout :title="'Pending Approvals'">
    <div class="space-y-6">
        @foreach ($pending as $role => $items)
            <div class="bg-white rounded-xl border overflow-hidden">
                <div class="px-6 py-4 border-b flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800 capitalize">{{ $role }}s awaiting approval</h3>
                    <span class="text-xs font-medium px-2 py-1 rounded-full bg-gray-100 text-gray-600">{{ count($items) }}</span>
                </div>

                @if (count($items) === 0)
                    <p class="px-6 py-5 text-gray-400 text-sm">Nothing pending.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-left">
                            <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide">
                                <tr>
                                    <th class="py-3 px-6">Name</th>
                                    <th class="py-3 px-6">Contact</th>
                                    <th class="py-3 px-6">License #</th>
                                    <th class="py-3 px-6">Submitted</th>
                                    <th class="py-3 px-6">Documents</th>
                                    <th class="py-3 px-6">AI Check</th>
                                    <th class="py-3 px-6">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach ($items as $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-3 px-6 font-medium text-gray-900">
                                            {{ $item['name'] ?? $item['full_name'] ?? '—' }}
                                        </td>
                                        <td class="py-3 px-6 text-gray-600">
                                            {{ $item['contact'] ?? $item['phone'] ?? $item['email'] ?? '—' }}
                                        </td>
                                        <td class="py-3 px-6 text-gray-600">
                                            {{ $item['license_number'] ?? '—' }}
                                        </td>
                                        <td class="py-3 px-6 text-gray-600">
                                            {{ \Illuminate\Support\Carbon::parse($item['created_at'])->format('M j, Y') }}
                                        </td>
                                        <td class="py-3 px-6">
                                            @php
                                                // certificate_urls (doctors) / license_doc_urls (lab, pharmacy,
                                                // rider) are the current multi-document arrays; license_doc_url
                                                // is the older single-document column, kept as a fallback for
                                                // any already-submitted row that predates the array columns.
                                                $docs = $item['certificate_urls']
                                                    ?? $item['license_doc_urls']
                                                    ?? ($item['license_doc_url'] ?? null);
                                                $docs = is_array($docs) ? array_values(array_filter($docs)) : array_filter([$docs]);
                                                $idDoc = $item['national_id_doc_url'] ?? null;
                                            @endphp
                                            @forelse ($docs as $i => $doc)
                                                <a href="{{ $doc }}" target="_blank" class="text-blue-600 hover:underline block">
                                                    {{ count($docs) > 1 ? 'View doc '.($i + 1) : 'View' }}
                                                </a>
                                            @empty
                                                <span class="text-gray-400">None</span>
                                            @endforelse
                                            @if ($idDoc)
                                                <a href="{{ $idDoc }}" target="_blank" class="text-blue-600 hover:underline block">View National ID</a>
                                            @endif
                                        </td>
                                        <td class="py-3 px-6">
                                            @php
                                                $aiStatus = $item['ai_verification_status'] ?? 'pending';
                                                $aiNotes = $item['ai_verification_notes'] ?? null;
                                                $aiBadge = match ($aiStatus) {
                                                    'passed' => ['bg-green-100 text-green-700', 'Passed'],
                                                    'flagged' => ['bg-amber-100 text-amber-700', 'Flagged'],
                                                    default => ['bg-gray-100 text-gray-500', 'Pending'],
                                                };
                                            @endphp
                                            <span
                                                class="text-xs font-medium px-2 py-1 rounded-full {{ $aiBadge[0] }}"
                                                @if ($aiNotes) title="{{ $aiNotes }}" @endif
                                            >{{ $aiBadge[1] }}</span>
                                            @if ($aiNotes)
                                                <p class="text-xs text-gray-400 mt-1 max-w-xs">{{ \Illuminate\Support\Str::limit($aiNotes, 80) }}</p>
                                            @endif
                                        </td>
                                        <td class="py-3 px-6 whitespace-nowrap space-x-2">
                                            <form method="POST" action="{{ route('approvals.update', [$role, $item['id']]) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="approved">
                                                <button class="bg-green-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-green-700">Approve</button>
                                            </form>
                                            <form method="POST" action="{{ route('approvals.update', [$role, $item['id']]) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="rejected">
                                                <button class="bg-red-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-red-700">Reject</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</x-admin-layout>
