@extends('layouts.app')

@section('title', 'كارت المشرف — ' . $supervisor->name)

@section('content')
<div x-data="{ warningOpen: {{ $errors->has('reason') ? 'true' : 'false' }}, excuseOpen: {{ $errors->hasAny(['date', 'excuse_reason', 'excuse_attachment']) ? 'true' : 'false' }} }">
<div class="flex flex-wrap items-start justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">{{ $supervisor->name }}</h1>
        <p class="text-slate-500 text-sm mt-1">{{ $supervisor->schoolClass->name }} — {{ $supervisor->phone ?? 'بدون هاتف' }}</p>
    </div>
    <div class="flex flex-wrap gap-2 no-print">
        @can('export-reports')
        <a href="{{ route('reports.supervisor', $supervisor) }}"
           class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition">
            تصدير Excel
        </a>
        @endcan
        @can('print-supervisors')
        <a href="{{ route('supervisors.print', $supervisor) }}" target="_blank"
           class="bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition">
            طباعة / PDF
        </a>
        @endcan
        @can('edit-supervisors')
        <a href="{{ route('supervisors.edit', $supervisor) }}"
           class="bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-sm font-semibold px-4 py-2.5 rounded-lg transition">
            تعديل
        </a>
        @endcan
        @can('create-warnings')
        <button type="button" @click="warningOpen = true"
                class="bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition">
            + إنذار / مخالفة
        </button>
        @endcan
        @can('save-attendance-records')
        <button type="button" @click="excuseOpen = true"
                class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition">
            + تسجيل عذر غياب
        </button>
        @endcan
        @can('delete-supervisors')
        <form action="{{ route('supervisors.destroy', $supervisor) }}" method="POST" class="inline"
              onsubmit="return confirm('هل أنت متأكد من حذف هذا المشرف؟ سيتم حذف سجل حضوره وإنذاراته.')">
            @csrf @method('DELETE')
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition">
                حذف
            </button>
        </form>
        @endcan
    </div>
</div>

{{-- Stats cards --}}
<div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
        <p class="text-xs text-slate-500">أيام التدريب</p>
        <p class="text-2xl font-bold mt-1">{{ $supervisor->total_training_days }}</p>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
        <p class="text-xs text-slate-500">حاضر</p>
        <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $supervisor->presentDaysCount() }}</p>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
        <p class="text-xs text-slate-500">غائب</p>
        <p class="text-2xl font-bold text-red-600 mt-1">{{ $supervisor->absentDaysCount() }}</p>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
        <p class="text-xs text-slate-500">متأخر</p>
        <p class="text-2xl font-bold text-amber-600 mt-1">{{ $supervisor->lateDaysCount() }}</p>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
        <p class="text-xs text-slate-500">غياب بعذر</p>
        <p class="text-2xl font-bold text-blue-600 mt-1">{{ $supervisor->excusedDaysCount() }}</p>
    </div>
    <div class="bg-white rounded-xl border p-4 shadow-sm {{ $supervisor->deducted_days > 0 ? 'border-red-300 bg-red-50' : 'border-slate-200' }}">
        <p class="text-xs text-slate-500">أيام مخصومة</p>
        <p class="text-2xl font-bold text-red-600 mt-1">{{ $supervisor->deducted_days }}</p>
        @if($supervisor->active_warnings_count > 0)
        <p class="text-xs text-amber-600 mt-1">إنذارات نشطة: {{ $supervisor->active_warnings_count }}/3</p>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    {{-- Left column --}}
    <div class="xl:col-span-2 space-y-6">
        @can('view-warnings')
        {{-- Warnings history --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
            <div class="px-6 py-4 border-b border-slate-200">
                <h2 class="font-bold text-slate-900">سجل الإنذارات</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="text-right px-6 py-3 font-semibold">التاريخ</th>
                            <th class="text-right px-6 py-3 font-semibold">المستوى</th>
                            <th class="text-right px-6 py-3 font-semibold">السبب</th>
                            <th class="text-right px-6 py-3 font-semibold">بواسطة</th>
                            <th class="text-right px-6 py-3 font-semibold">خصم</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($supervisor->warnings as $warning)
                        <tr>
                            <td class="px-6 py-3">{{ $warning->created_at->format('Y-m-d') }}</td>
                            <td class="px-6 py-3">
                                <span class="bg-amber-100 text-amber-800 text-xs font-bold px-2 py-0.5 rounded-full">إنذار {{ $warning->warning_level }}</span>
                            </td>
                            <td class="px-6 py-3">{{ $warning->reason }}</td>
                            <td class="px-6 py-3">{{ $warning->createdBy->name }}</td>
                            <td class="px-6 py-3">
                                @if($warning->triggered_deduction)
                                    <span class="text-red-600 font-semibold text-xs">−14 يوم</span>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-6 py-6 text-center text-slate-400">لا توجد إنذارات</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endcan

        {{-- Excuse attachments gallery --}}
        @if($excusedRecords->isNotEmpty())
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
            <div class="px-6 py-4 border-b border-slate-200">
                <h2 class="font-bold text-slate-900">مرفقات الأعذار</h2>
            </div>
            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($excusedRecords as $record)
                <div class="border border-slate-200 rounded-lg p-4">
                    <p class="text-xs text-slate-500 mb-2">{{ $record->session->date->format('Y-m-d') }}</p>
                    <p class="text-sm mb-3">{{ $record->excuse_reason }}</p>
                    @php $ext = pathinfo($record->excuse_attachment, PATHINFO_EXTENSION); @endphp
                    @if(in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                        <a href="{{ $record->attachmentUrl() }}" target="_blank">
                            <img src="{{ $record->attachmentUrl() }}" alt="مرفق العذر" class="rounded-lg max-h-40 object-cover w-full">
                        </a>
                    @else
                        <a href="{{ $record->attachmentUrl() }}" target="_blank"
                           class="inline-flex items-center gap-2 text-indigo-600 text-sm font-medium">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            عرض الملف (PDF)
                        </a>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Attendance history --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
            <div class="px-6 py-4 border-b border-slate-200">
                <h2 class="font-bold text-slate-900">سجل الحضور</h2>
            </div>
            <div class="overflow-x-auto max-h-96">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600 sticky top-0">
                        <tr>
                            <th class="text-right px-6 py-3 font-semibold">التاريخ</th>
                            <th class="text-right px-6 py-3 font-semibold">الحالة</th>
                            <th class="text-right px-6 py-3 font-semibold">ملاحظات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($supervisor->attendanceRecords->sortByDesc(fn($r) => $r->session->date) as $record)
                        <tr>
                            <td class="px-6 py-3">{{ $record->session->date->format('Y-m-d') }}</td>
                            <td class="px-6 py-3"><x-status-badge :status="$record->status" /></td>
                            <td class="px-6 py-3 text-slate-500">{{ $record->excuse_reason ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="px-6 py-6 text-center text-slate-400">لا يوجد سجل حضور</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Right column: Evaluation --}}
    <div class="space-y-6">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <h2 class="font-bold text-slate-900 mb-4">معلومات المشرف</h2>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between"><dt class="text-slate-500">الحالة</dt><dd><x-status-badge :status="$supervisor->status" /></dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">الفصل</dt><dd class="font-medium">{{ $supervisor->schoolClass->name }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">أيام فعلية</dt><dd class="font-medium">{{ $supervisor->effectiveTrainingDays() }}</dd></div>
            </dl>
        </div>

        @can('create-evaluations')
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <h2 class="font-bold text-slate-900 mb-4">التقييم النهائي</h2>
            <form method="POST" action="{{ route('supervisors.evaluations.store', $supervisor) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">الدرجة (من 100)</label>
                    <input type="number" name="score" min="0" max="100" required
                           class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">ملاحظات</label>
                    <textarea name="notes" rows="3"
                              class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
                </div>
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold py-2.5 rounded-lg">حفظ التقييم</button>
            </form>
        </div>
        @endcan

        @can('view-evaluations')
        @if($supervisor->evaluations->isNotEmpty())
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <h2 class="font-bold text-slate-900 mb-4">التقييمات السابقة</h2>
            <div class="space-y-3">
                @foreach($supervisor->evaluations->sortByDesc('created_at') as $evaluation)
                <div class="border border-slate-100 rounded-lg p-3">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-2xl font-bold text-indigo-600">{{ $evaluation->score }}</span>
                        <span class="text-xs text-slate-400">{{ $evaluation->created_at->format('Y-m-d') }}</span>
                    </div>
                    <p class="text-xs text-slate-500">بواسطة: {{ $evaluation->evaluatedBy->name }}</p>
                    @if($evaluation->notes)
                    <p class="text-sm text-slate-600 mt-2">{{ $evaluation->notes }}</p>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif
        @endcan
    </div>
</div>

{{-- Warning Modal --}}
@can('create-warnings')
<div x-show="warningOpen"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     style="display: none;">
    <div class="fixed inset-0 bg-black/50" @click="warningOpen = false"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6" @click.stop>
        <h3 class="text-lg font-bold text-slate-900 mb-4">تسجيل إنذار / مخالفة</h3>
        <p class="text-sm text-amber-700 bg-amber-50 border border-amber-200 rounded-lg p-3 mb-4">
            الإنذار الثالث يؤدي تلقائياً لخصم 14 يوماً. الإنذارات النشطة حالياً: <strong>{{ $supervisor->active_warnings_count }}/3</strong>
        </p>
        <form method="POST" action="{{ route('supervisors.warnings.store', $supervisor) }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1.5">سبب الإنذار</label>
                <textarea name="reason" rows="4" required
                          class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">{{ old('reason') }}</textarea>
                @error('reason')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex gap-3">
                <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold px-5 py-2.5 rounded-lg">تسجيل الإنذار</button>
                <button type="button" @click="warningOpen = false" class="text-sm text-slate-600 px-5 py-2.5">إلغاء</button>
            </div>
        </form>
    </div>
</div>
@endcan

{{-- Excuse Modal --}}
@can('save-attendance-records')
<div x-show="excuseOpen"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     style="display: none;">
    <div class="fixed inset-0 bg-black/50" @click="excuseOpen = false"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6" @click.stop>
        <h3 class="text-lg font-bold text-slate-900 mb-4">تسجيل عذر غياب</h3>
        <p class="text-sm text-blue-700 bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
            سيتم تسجيل حالة <strong>غياب بعذر</strong> للمشرف في اليوم المحدد. إذا كان هناك سجل حضور سابق لنفس اليوم سيتم استبداله.
        </p>
        <form method="POST" action="{{ route('supervisors.excuses.store', $supervisor) }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1.5">تاريخ الغياب</label>
                <input type="date" name="date" required value="{{ old('date', now()->toDateString()) }}"
                       class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                @error('date')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1.5">سبب العذر</label>
                <textarea name="excuse_reason" rows="4" required
                          class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">{{ old('excuse_reason') }}</textarea>
                @error('excuse_reason')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1.5">مرفق العذر (صورة / PDF) — اختياري</label>
                <input type="file" name="excuse_attachment" accept=".jpg,.jpeg,.png,.pdf"
                       class="w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                @error('excuse_attachment')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex gap-3">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2.5 rounded-lg">تسجيل العذر</button>
                <button type="button" @click="excuseOpen = false" class="text-sm text-slate-600 px-5 py-2.5">إلغاء</button>
            </div>
        </form>
    </div>
</div>
@endcan
</div>
@endsection
