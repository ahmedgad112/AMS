@extends('layouts.app')

@section('title', 'التقارير')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-900">التقارير</h1>
    <p class="text-slate-500 text-sm mt-1">تصدير البيانات إلى ملفات Excel</p>
</div>

{{-- Filters --}}
<div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 mb-6">
    <h2 class="font-bold text-slate-900 mb-4">فلاتر التصدير</h2>
    <form id="report-filters" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">الفصل</label>
            <select name="class_id" id="class_id"
                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                <option value="">كل الفصول</option>
                @foreach($classes as $class)
                <option value="{{ $class->id }}" {{ ($filters['class_id'] ?? '') == $class->id ? 'selected' : '' }}>
                    {{ $class->name }}
                </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">من تاريخ</label>
            <input type="date" name="date_from" id="date_from" value="{{ $filters['date_from'] ?? '' }}"
                   class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">إلى تاريخ</label>
            <input type="date" name="date_to" id="date_to" value="{{ $filters['date_to'] ?? '' }}"
                   class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
        </div>
    </form>
</div>

{{-- Report cards --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-start gap-4">
            <div class="bg-emerald-100 text-emerald-700 rounded-lg p-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-slate-900">ملخص المشرفين</h3>
                <p class="text-sm text-slate-500 mt-1">بيانات المشرفين مع إحصائيات الحضور والإنذارات</p>
                <a href="#" onclick="downloadReport('{{ route('reports.supervisors') }}'); return false;"
                   class="inline-flex items-center gap-2 mt-4 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    تصدير Excel
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-start gap-4">
            <div class="bg-indigo-100 text-indigo-700 rounded-lg p-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-slate-900">سجل الحضور والغياب</h3>
                <p class="text-sm text-slate-500 mt-1">تفاصيل كل سجل حضور مع الفلاتر الزمنية</p>
                <a href="#" onclick="downloadReport('{{ route('reports.attendance') }}'); return false;"
                   class="inline-flex items-center gap-2 mt-4 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    تصدير Excel
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-start gap-4">
            <div class="bg-amber-100 text-amber-700 rounded-lg p-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-slate-900">سجل الإنذارات</h3>
                <p class="text-sm text-slate-500 mt-1">جميع الإنذارات والمخالفات المسجلة</p>
                <a href="#" onclick="downloadReport('{{ route('reports.warnings') }}'); return false;"
                   class="inline-flex items-center gap-2 mt-4 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    تصدير Excel
                </a>
            </div>
        </div>
    </div>

    @can('manage-evaluations')
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-start gap-4">
            <div class="bg-purple-100 text-purple-700 rounded-lg p-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-slate-900">التقييمات</h3>
                <p class="text-sm text-slate-500 mt-1">تقييمات المشرفين النهائية</p>
                <a href="#" onclick="downloadReport('{{ route('reports.evaluations') }}'); return false;"
                   class="inline-flex items-center gap-2 mt-4 bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    تصدير Excel
                </a>
            </div>
        </div>
    </div>
    @endcan
</div>
@endsection

@push('scripts')
<script>
function downloadReport(baseUrl) {
    const params = new URLSearchParams();
    const classId = document.getElementById('class_id').value;
    const dateFrom = document.getElementById('date_from').value;
    const dateTo = document.getElementById('date_to').value;

    if (classId) params.set('class_id', classId);
    if (dateFrom) params.set('date_from', dateFrom);
    if (dateTo) params.set('date_to', dateTo);

    const query = params.toString();
    window.location.href = query ? `${baseUrl}?${query}` : baseUrl;
}
</script>
@endpush
