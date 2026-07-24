<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js" defer></script>
    <script>
        window.OneSignalDeferred = window.OneSignalDeferred || [];
        OneSignalDeferred.push(async function(OneSignal) {
            await OneSignal.init({
                appId: "{{ config('services.onesignal.app_id') }}",
                allowLocalhostAsSecureOrigin: true,
            });
            await OneSignal.User.addTag('role', 'admin');
            await OneSignal.Notifications.requestPermission();
        });
    </script>
</head>
<body class="font-sans antialiased bg-gray-50">
<div class="min-h-screen flex">

    {{-- Sidebar --}}
    <aside class="w-64 shrink-0 bg-[#101B3D] text-white flex flex-col">
        <div class="h-16 flex items-center gap-2 px-6 border-b border-white/10">
            <div class="h-8 w-8 rounded-lg bg-[#4C6FFF] flex items-center justify-center font-bold text-sm">S</div>
            <span class="font-semibold tracking-wide">SehtyGo Admin</span>
        </div>

        <nav class="flex-1 px-3 py-6 space-y-1">
            @php
                $navItems = [
                    ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'grid'],
                    ['route' => 'approvals.index', 'label' => 'Pending Approvals', 'icon' => 'check'],
                    ['route' => 'accounts.index', 'label' => 'Accounts', 'icon' => 'users'],
                    ['route' => 'bookings.index', 'label' => 'Bookings', 'icon' => 'calendar'],
                    ['route' => 'catalog.index', 'label' => 'Medicine Catalog', 'icon' => 'box'],
                    ['route' => 'riders.payouts', 'label' => 'Rider Deliveries', 'icon' => 'cash'],
                    ['route' => 'support.index', 'label' => 'Support Tickets', 'icon' => 'chat'],
                ];
                $icons = [
                    'grid' => 'M4 4h6v6H4V4zm10 0h6v6h-6V4zM4 14h6v6H4v-6zm10 0h6v6h-6v-6z',
                    'check' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                    'users' => 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m5-3a4 4 0 100-8 4 4 0 000 8zm6-1a4 4 0 100-8',
                    'calendar' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                    'box' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                    'cash' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V6m0 10v2M3 12a9 9 0 1018 0 9 9 0 00-18 0z',
                    'chat' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.86 9.86 0 01-4-.8L3 20l1.2-3.6A7.9 7.9 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
                ];
            @endphp

            @foreach ($navItems as $item)
                @php $active = request()->routeIs($item['route']) || request()->routeIs(str_replace('.index', '.*', $item['route'])); @endphp
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                   {{ $active ? 'bg-[#4C6FFF] text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icons[$item['icon']] }}" />
                    </svg>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="p-3 border-t border-white/10">
            <div class="px-3 py-2 text-xs text-gray-400 truncate">{{ Auth::user()->email }}</div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-300 hover:bg-white/5 hover:text-white">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Log out
                </button>
            </form>
        </div>
    </aside>

    {{-- Main --}}
    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-16 bg-white border-b flex items-center justify-between px-8">
            <h1 class="text-lg font-semibold text-gray-800">{{ $title ?? 'Dashboard' }}</h1>
        </header>

        <main class="flex-1 p-8">
            @if (session('status'))
                <div class="mb-6 bg-green-50 text-green-700 border border-green-200 px-4 py-3 rounded-lg text-sm">
                    {{ session('status') }}
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>
</div>
@stack('scripts')
</body>
</html>
