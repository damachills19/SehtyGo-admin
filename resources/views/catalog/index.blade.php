<x-admin-layout :title="'Medicine Catalog'">
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-6 py-4 border-b flex justify-end">
            <form method="GET" action="{{ route('catalog.index') }}" class="flex gap-2">
                <input type="text" name="q" value="{{ $search }}" placeholder="Search medicines..."
                       class="border-gray-300 rounded-lg text-sm focus:ring-[#4C6FFF] focus:border-[#4C6FFF]">
                <button class="bg-gray-100 px-3 py-1.5 rounded-lg text-sm font-medium hover:bg-gray-200">Search</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide">
                    <tr>
                        <th class="py-3 px-6">Name</th>
                        <th class="py-3 px-6">Price</th>
                        <th class="py-3 px-6">Stock</th>
                        <th class="py-3 px-6">Manufacturer</th>
                        <th class="py-3 px-6">Rx Required</th>
                        <th class="py-3 px-6">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($medicines as $m)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-6 font-medium text-gray-900">{{ $m['name'] }}</td>
                            <td class="py-3 px-6 text-gray-600">{{ $m['currency'] }} {{ $m['price'] }}</td>
                            <td class="py-3 px-6 text-gray-600">{{ $m['stock'] ?? 0 }}</td>
                            <td class="py-3 px-6 text-gray-600">{{ $m['manufacturer'] ?? '—' }}</td>
                            <td class="py-3 px-6">
                                @if ($m['prescription_required'] ?? false)
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Yes</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">No</span>
                                @endif
                            </td>
                            <td class="py-3 px-6">
                                <form method="POST" action="{{ route('catalog.destroy', $m['id']) }}" onsubmit="return confirm('Remove this medicine from the catalog?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="bg-red-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-red-700">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-6 px-6 text-gray-400 text-center">No medicines found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>
