<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>استمارة مشرف — {{ $supervisor->name }}</title>
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            @page { margin: 1.5cm; }
        }
    </style>
</head>
<body class="bg-white text-slate-900 font-sans antialiased p-8 max-w-4xl mx-auto">
    <div class="no-print mb-6 flex gap-3">
        <button onclick="window.print()" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-semibold">طباعة</button>
        <button onclick="window.close()" class="border border-slate-300 px-4 py-2 rounded-lg text-sm">إغلاق</button>
    </div>

    <header class="text-center border-b-2 border-slate-800 pb-6 mb-8">
        <h1 class="text-xl font-bold">جامعة برج العرب التكنولوجية</h1>
        <h2 class="text-lg font-semibold mt-2">استمارة مشرف تدريب</h2>
        <p class="text-sm text-slate-500 mt-1">تاريخ الطباعة: {{ now()->format('Y-m-d') }}</p>
    </header>

    <section class="mb-8">
        <h3 class="font-bold text-lg mb-4 border-b border-slate-300 pb-2">البيانات الشخصية</h3>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div><span class="text-slate-500">الاسم:</span> <strong>{{ $supervisor->name }}</strong></div>
            <div><span class="text-slate-500">الهاتف:</span> <strong>{{ $supervisor->phone ?? '—' }}</strong></div>
            <div><span class="text-slate-500">الفصل:</span> <strong>{{ $supervisor->schoolClass->name }}</strong></div>
            <div><span class="text-slate-500">الحالة:</span> <strong>{{ $supervisor->statusLabel() }}</strong></div>
        </div>
    </section>

    <section class="mb-8">
        <h3 class="font-bold text-lg mb-4 border-b border-slate-300 pb-2">ملخص الحضور</h3>
        <div class="grid grid-cols-4 gap-4 text-center text-sm">
            <div class="border rounded-lg p-3"><p class="text-slate-500">حاضر</p><p class="text-xl font-bold text-emerald-600">{{ $supervisor->presentDaysCount() }}</p></div>
            <div class="border rounded-lg p-3"><p class="text-slate-500">غائب</p><p class="text-xl font-bold text-red-600">{{ $supervisor->absentDaysCount() }}</p></div>
            <div class="border rounded-lg p-3"><p class="text-slate-500">متأخر</p><p class="text-xl font-bold text-amber-600">{{ $supervisor->lateDaysCount() }}</p></div>
            <div class="border rounded-lg p-3"><p class="text-slate-500">بعذر</p><p class="text-xl font-bold text-blue-600">{{ $supervisor->excusedDaysCount() }}</p></div>
        </div>
        <div class="grid grid-cols-3 gap-4 mt-4 text-sm">
            <div><span class="text-slate-500">إجمالي أيام التدريب:</span> <strong>{{ $supervisor->total_training_days }}</strong></div>
            <div><span class="text-slate-500">أيام مخصومة:</span> <strong class="text-red-600">{{ $supervisor->deducted_days }}</strong></div>
            <div><span class="text-slate-500">أيام فعلية:</span> <strong>{{ $supervisor->effectiveTrainingDays() }}</strong></div>
        </div>
    </section>

    @if($supervisor->warnings->isNotEmpty())
    <section class="mb-8">
        <h3 class="font-bold text-lg mb-4 border-b border-slate-300 pb-2">سجل الإنذارات</h3>
        <table class="w-full text-sm border-collapse">
            <thead>
                <tr class="bg-slate-100">
                    <th class="border border-slate-300 px-3 py-2 text-right">التاريخ</th>
                    <th class="border border-slate-300 px-3 py-2 text-right">المستوى</th>
                    <th class="border border-slate-300 px-3 py-2 text-right">السبب</th>
                    <th class="border border-slate-300 px-3 py-2 text-right">خصم</th>
                </tr>
            </thead>
            <tbody>
                @foreach($supervisor->warnings as $warning)
                <tr>
                    <td class="border border-slate-300 px-3 py-2">{{ $warning->created_at->format('Y-m-d') }}</td>
                    <td class="border border-slate-300 px-3 py-2">إنذار {{ $warning->warning_level }}</td>
                    <td class="border border-slate-300 px-3 py-2">{{ $warning->reason }}</td>
                    <td class="border border-slate-300 px-3 py-2">{{ $warning->triggered_deduction ? '−14 يوم' : '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </section>
    @endif

    @if($supervisor->evaluations->isNotEmpty())
    <section class="mb-8">
        <h3 class="font-bold text-lg mb-4 border-b border-slate-300 pb-2">التقييمات</h3>
        @foreach($supervisor->evaluations as $evaluation)
        <div class="border border-slate-200 rounded-lg p-4 mb-3 text-sm">
            <strong class="text-lg">{{ $evaluation->score }}/100</strong>
            <span class="text-slate-500 mr-4">{{ $evaluation->created_at->format('Y-m-d') }}</span>
            @if($evaluation->notes)<p class="mt-2">{{ $evaluation->notes }}</p>@endif
        </div>
        @endforeach
    </section>
    @endif

    <section class="mt-12 grid grid-cols-3 gap-8 text-sm text-center">
        <div>
            <div class="border-b border-slate-400 h-16 mb-2"></div>
            <p>توقيع المشرف</p>
        </div>
        <div>
            <div class="border-b border-slate-400 h-16 mb-2"></div>
            <p>توقيع المفتش</p>
        </div>
        <div>
            <div class="border-b border-slate-400 h-16 mb-2"></div>
            <p>اعتماد الإدارة</p>
        </div>
    </section>
</body>
</html>
