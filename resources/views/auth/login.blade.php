<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تسجيل الدخول — نظام الحضور</title>
    @include('partials.favicon')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 min-h-screen flex items-center justify-center p-4 font-sans antialiased">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <x-brand-logo size="lg" class="mx-auto mb-4" />
            <p class="text-indigo-300 text-sm">نظام إدارة حضور وغياب المشرفين</p>
        </div>

        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <h2 class="text-xl font-bold text-slate-800 mb-6">تسجيل الدخول</h2>

            @if($errors->any())
                <x-alert type="error" :message="$errors->first()" />
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">البريد الإلكتروني</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                           class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">كلمة المرور</label>
                    <input type="password" name="password" id="password" required
                           class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="remember" id="remember" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="remember" class="mr-2 text-sm text-slate-600">تذكرني</label>
                </div>

                <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-4 rounded-lg transition text-sm">
                    دخول
                </button>
            </form>

            <p class="text-center mt-6 pt-4 border-t border-slate-100">
                <a href="{{ route('public.evaluation') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                    استعلام عن التقييم (للمشرفين)
                </a>
            </p>
        </div>
    </div>
</body>
</html>
