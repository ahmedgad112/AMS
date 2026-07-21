<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة التحكم') — {{ config('app.name', 'نظام الحضور') }}</title>
    @include('partials.favicon')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased"
      x-data="{ mobileNavOpen: false }"
      x-bind:class="mobileNavOpen ? 'overflow-hidden lg:overflow-auto' : ''"
      @keydown.escape.window="mobileNavOpen = false">
    @if($isImpersonating ?? false)
    <div class="no-print bg-amber-500 text-amber-950 px-4 py-2.5 flex flex-wrap items-center justify-between gap-3 text-sm font-medium">
        <span>
            أنت تعرض النظام كـ <strong>{{ auth()->user()->name }}</strong>
            @if($impersonator)
                (حسابك: {{ $impersonator->name }})
            @endif
        </span>
        <form method="POST" action="{{ route('impersonate.leave') }}">
            @csrf
            <button type="submit" class="bg-amber-950/10 hover:bg-amber-950/20 px-3 py-1 rounded-md transition">
                العودة لحسابي
            </button>
        </form>
    </div>
    @endif

    <div class="min-h-screen flex">
        {{-- Desktop sidebar --}}
        <aside class="no-print w-64 bg-slate-900 text-white flex-shrink-0 hidden lg:flex flex-col">
            <div class="p-6 border-b border-slate-700">
                <x-brand-logo size="md" class="mx-auto mb-3" />
                <p class="text-slate-400 text-sm text-center">نظام إدارة الحضور والتقييم</p>
            </div>
            <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                @include('layouts.partials.nav-links', ['mobile' => false])
            </nav>
            <div class="p-4 border-t border-slate-700">
                <div class="text-sm text-slate-400 mb-2">{{ auth()->user()->name }}</div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-right text-sm text-red-400 hover:text-red-300 transition">تسجيل الخروج</button>
                </form>
            </div>
        </aside>

        {{-- Mobile drawer --}}
        <div x-show="mobileNavOpen"
             x-cloak
             class="no-print lg:hidden fixed inset-0 z-50"
             role="dialog"
             aria-modal="true"
             aria-label="القائمة">
            <div class="fixed inset-0 bg-black/50 transition-opacity"
                 x-show="mobileNavOpen"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="mobileNavOpen = false"></div>

            <aside class="fixed inset-y-0 right-0 w-[min(100vw-3rem,18rem)] bg-slate-900 text-white flex flex-col shadow-2xl"
                   x-show="mobileNavOpen"
                   x-transition:enter="ease-out duration-200"
                   x-transition:enter-start="translate-x-full"
                   x-transition:enter-end="translate-x-0"
                   x-transition:leave="ease-in duration-150"
                   x-transition:leave-start="translate-x-0"
                   x-transition:leave-end="translate-x-full"
                   @click.stop>
                <div class="p-4 border-b border-slate-700 flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <x-brand-logo size="sm" />
                        <p class="text-slate-400 text-xs mt-2 truncate">{{ auth()->user()->name }}</p>
                    </div>
                    <button type="button"
                            @click="mobileNavOpen = false"
                            class="shrink-0 p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition"
                            aria-label="إغلاق القائمة">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <nav class="flex-1 p-4 space-y-1 overflow-y-auto overscroll-contain">
                    @include('layouts.partials.nav-links', ['mobile' => true])
                </nav>
                <div class="p-4 border-t border-slate-700">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center gap-2 text-sm font-medium text-red-400 hover:text-red-300 py-2.5 rounded-lg hover:bg-slate-800 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            تسجيل الخروج
                        </button>
                    </form>
                </div>
            </aside>
        </div>

        {{-- Main content --}}
        <div class="flex-1 flex flex-col min-w-0">
            <header class="no-print sticky top-0 z-40 bg-white border-b border-slate-200 px-4 py-3 flex items-center justify-between gap-3 lg:hidden">
                <button type="button"
                        @click="mobileNavOpen = true"
                        class="p-2 -mr-1 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition"
                        aria-label="فتح القائمة">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 min-w-0">
                    <x-brand-logo size="sm" />
                </a>
                <div class="w-10"></div>
            </header>

            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                @if(session('success'))
                    <x-alert type="success" :message="session('success')" />
                @endif
                @if(session('error'))
                    <x-alert type="error" :message="session('error')" />
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
