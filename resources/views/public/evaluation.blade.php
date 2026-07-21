<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>استعلام التقييم — جامعة برج العرب التكنولوجية</title>
    @include('partials.favicon')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 min-h-screen font-sans antialiased">
    <div class="min-h-screen flex flex-col items-center justify-center p-4 py-10">
        <div class="text-center mb-8">
            <x-brand-logo size="lg" class="mx-auto mb-4" />
            <p class="text-indigo-300 text-sm">استعلام عن التقييم والإنذارات</p>
        </div>

        <div class="w-full max-w-lg">
            <div class="bg-white rounded-2xl shadow-2xl p-8 mb-4">
                <h2 class="text-xl font-bold text-slate-800 mb-2">ادخل رقم تليفونك</h2>
                <p class="text-sm text-slate-500 mb-6">أدخل رقم التليفون المسجل في النظام لعرض تقييمك وإنذاراتك</p>

                @if(!empty($notFound))
                    <div class="mb-5 px-4 py-3 rounded-lg border text-sm font-medium bg-red-50 border-red-200 text-red-800">
                        لم يتم العثور على بيانات مرتبطة بهذا الرقم. تأكد من صحة الرقم أو تواصل مع الإدارة.
                    </div>
                @endif

                <form method="POST" action="{{ route('public.evaluation.lookup') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label for="phone" class="block text-sm font-medium text-slate-700 mb-1.5">رقم التليفون</label>
                        <input type="tel" name="phone" id="phone" value="{{ $phone ?? '' }}" required autofocus
                               inputmode="numeric" maxlength="11" pattern="01[0-9]{9}"
                               placeholder="01xxxxxxxxx"
                               class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                        @error('phone')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit"
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 rounded-lg transition text-sm">
                        عرض النتيجة
                    </button>
                </form>
            </div>

            @if(isset($supervisor) && $supervisor)
            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
                <div class="bg-indigo-600 px-8 py-5 text-white">
                    <p class="text-indigo-200 text-sm">بيانات المشرف</p>
                    <h3 class="text-xl font-bold mt-1">{{ $supervisor->name }}</h3>
                    <p class="text-indigo-200 text-sm mt-1">{{ $supervisor->schoolClass->name }}</p>
                </div>

                <div class="p-8">
                    @if($latestEvaluation)
                    <div class="text-center mb-6">
                        <p class="text-sm text-slate-500 mb-2">التقييم النهائي</p>
                        <div class="inline-flex items-baseline gap-1">
                            <span class="text-6xl font-bold text-indigo-600">{{ $latestEvaluation->score }}</span>
                            <span class="text-2xl text-slate-400 font-medium">/ 100</span>
                        </div>
                        <p class="text-xs text-slate-400 mt-3">تاريخ التقييم: {{ $latestEvaluation->created_at->format('Y-m-d') }}</p>
                    </div>

                    @php
                        $score = $latestEvaluation->score;
                        $gradeLabel = match (true) {
                            $score >= 90 => ['ممتاز', 'text-emerald-600', 'bg-emerald-100'],
                            $score >= 80 => ['جيد جداً', 'text-blue-600', 'bg-blue-100'],
                            $score >= 70 => ['جيد', 'text-indigo-600', 'bg-indigo-100'],
                            $score >= 60 => ['مقبول', 'text-amber-600', 'bg-amber-100'],
                            default => ['ضعيف', 'text-red-600', 'bg-red-100'],
                        };
                    @endphp
                    <div class="text-center mb-6">
                        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-semibold {{ $gradeLabel[2] }} {{ $gradeLabel[1] }}">
                            {{ $gradeLabel[0] }}
                        </span>
                    </div>

                    @if($latestEvaluation->notes)
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                        <p class="text-xs font-semibold text-slate-500 mb-2">ملاحظات التقييم</p>
                        <p class="text-sm text-slate-700 leading-relaxed">{{ $latestEvaluation->notes }}</p>
                    </div>
                    @endif
                    @else
                    <div class="text-center py-6">
                        <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <p class="text-slate-600 font-medium">لم يتم إدخال تقييم بعد</p>
                        <p class="text-sm text-slate-400 mt-1">سيظهر تقييمك هنا بعد اعتماد الإدارة</p>
                    </div>
                    @endif

                    <div class="grid grid-cols-2 gap-3 mt-6 pt-6 border-t border-slate-100">
                        <div class="text-center p-3 bg-emerald-50 rounded-lg">
                            <p class="text-xs text-slate-500">أيام الحضور</p>
                            <p class="text-xl font-bold text-emerald-600">{{ $supervisor->presentDaysCount() }}</p>
                        </div>
                        <div class="text-center p-3 bg-red-50 rounded-lg">
                            <p class="text-xs text-slate-500">أيام الغياب</p>
                            <p class="text-xl font-bold text-red-600">{{ $supervisor->absentDaysCount() }}</p>
                        </div>
                    </div>

                    {{-- Warnings --}}
                    <div class="mt-6 pt-6 border-t border-slate-100">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="font-bold text-slate-900">الإنذارات</h4>
                            @if($supervisor->active_warnings_count > 0)
                            <span class="bg-amber-100 text-amber-800 text-xs font-bold px-2.5 py-1 rounded-full">
                                {{ $supervisor->active_warnings_count }}/3 نشط
                            </span>
                            @endif
                        </div>

                        @if($supervisor->deducted_days > 0)
                        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-4 flex items-start gap-3">
                            <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <div>
                                <p class="text-sm font-semibold text-red-800">خصم أيام تدريب</p>
                                <p class="text-sm text-red-700 mt-0.5">تم خصم <strong>{{ $supervisor->deducted_days }}</strong> يوماً من إجمالي أيام التدريب</p>
                            </div>
                        </div>
                        @endif

                        @if($supervisor->warnings->isNotEmpty())
                        <div class="space-y-3 max-h-64 overflow-y-auto">
                            @foreach($supervisor->warnings as $warning)
                            <div class="border border-slate-200 rounded-xl p-4 {{ $warning->triggered_deduction ? 'border-red-200 bg-red-50/50' : '' }}">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="bg-amber-100 text-amber-800 text-xs font-bold px-2.5 py-0.5 rounded-full">
                                        إنذار {{ $warning->warning_level }}
                                    </span>
                                    <span class="text-xs text-slate-400">{{ $warning->created_at->format('Y-m-d') }}</span>
                                </div>
                                <p class="text-sm text-slate-700">{{ $warning->reason }}</p>
                                @if($warning->triggered_deduction)
                                <p class="text-xs text-red-600 font-semibold mt-2">↳ تم خصم 14 يوماً تلقائياً</p>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-4 bg-emerald-50 border border-emerald-200 rounded-xl">
                            <p class="text-sm text-emerald-700 font-medium">لا توجد إنذارات مسجلة ✓</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <p class="text-center mt-6">
                <a href="{{ route('login') }}" class="text-indigo-300 hover:text-white text-sm transition">دخول الموظفين</a>
            </p>
        </div>
    </div>
</body>
</html>
