<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin' }} - Sultan Fabrics</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-slate-900 text-slate-200 flex flex-col flex-shrink-0">
            <div class="p-4 border-b border-slate-800">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2 rtl:space-x-reverse">
                    <span class="text-lg font-bold tracking-tight text-white">Sultan Fabrics</span>
                    <span class="text-xs px-2 py-0.5 rounded bg-slate-800 text-slate-400">Admin</span>
                </a>
            </div>

            <nav class="flex-1 p-3 space-y-1 text-sm overflow-y-auto">
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center px-3 py-2 rounded-md font-medium transition {{ request()->routeIs('admin.dashboard') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800/60 text-slate-300' }}">
                    <span>Dashboard</span>
                </a>

                <div class="pt-3 pb-1 text-xs font-semibold text-slate-400 uppercase tracking-wider px-3">Catalog</div>
                <a href="{{ route('admin.categories') }}"
                   class="flex items-center px-3 py-2 rounded-md font-medium transition {{ request()->routeIs('admin.categories*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800/60 text-slate-300' }}">
                    <span>Categories</span>
                </a>
                <a href="{{ route('admin.products') }}"
                   class="flex items-center px-3 py-2 rounded-md font-medium transition {{ request()->routeIs('admin.products*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800/60 text-slate-300' }}">
                    <span>Products</span>
                </a>
                <a href="{{ route('admin.collections') }}"
                   class="flex items-center px-3 py-2 rounded-md font-medium transition {{ request()->routeIs('admin.collections*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800/60 text-slate-300' }}">
                    <span>Collections</span>
                </a>

                <div class="pt-3 pb-1 text-xs font-semibold text-slate-400 uppercase tracking-wider px-3">Showcase & Media</div>
                <a href="{{ route('admin.sliders') }}"
                   class="flex items-center px-3 py-2 rounded-md font-medium transition {{ request()->routeIs('admin.sliders*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800/60 text-slate-300' }}">
                    <span>Hero Sliders</span>
                </a>
                <a href="{{ route('admin.gallery') }}"
                   class="flex items-center px-3 py-2 rounded-md font-medium transition {{ request()->routeIs('admin.gallery*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800/60 text-slate-300' }}">
                    <span>Gallery</span>
                </a>

                <div class="pt-3 pb-1 text-xs font-semibold text-slate-400 uppercase tracking-wider px-3">Content & Inquiries</div>
                <a href="{{ route('admin.pages') }}"
                   class="flex items-center px-3 py-2 rounded-md font-medium transition {{ request()->routeIs('admin.pages*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800/60 text-slate-300' }}">
                    <span>Pages</span>
                </a>
                <a href="{{ route('admin.inquiries') }}"
                   class="flex items-center justify-between px-3 py-2 rounded-md font-medium transition {{ request()->routeIs('admin.inquiries*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800/60 text-slate-300' }}">
                    <span>Inquiries</span>
                    @php $unreadCount = \App\Models\ContactMessage::unread()->count(); @endphp
                    @if($unreadCount > 0)
                        <span class="px-2 py-0.5 text-xs rounded-full bg-amber-500 text-slate-900 font-bold">{{ $unreadCount }}</span>
                    @endif
                </a>

                <div class="pt-3 pb-1 text-xs font-semibold text-slate-400 uppercase tracking-wider px-3">System</div>
                <a href="{{ route('admin.settings') }}"
                   class="flex items-center px-3 py-2 rounded-md font-medium transition {{ request()->routeIs('admin.settings*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800/60 text-slate-300' }}">
                    <span>Site Settings</span>
                </a>
            </nav>

            <div class="p-3 border-t border-slate-800 text-xs text-slate-400 flex items-center justify-between">
                <span>{{ auth()->user()->name ?? 'Administrator' }}</span>
                <form method="POST" action="{{ route('admin.logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-rose-400 hover:text-rose-300 font-medium">Logout</button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0">
            <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                <h1 class="text-xl font-bold text-gray-800">{{ $heading ?? 'Dashboard' }}</h1>
                <div class="flex items-center space-x-3 rtl:space-x-reverse">
                    <a href="{{ url('/') }}" target="_blank" class="text-xs text-gray-500 hover:text-gray-800">
                        View Showroom &nearr;
                    </a>
                </div>
            </header>

            <main class="flex-1 p-6 overflow-y-auto">
                @if (session()->has('success'))
                    <div class="mb-4 p-4 rounded-md bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="mb-4 p-4 rounded-md bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>
