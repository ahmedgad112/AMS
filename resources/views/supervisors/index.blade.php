@extends('layouts.app')

@section('title', 'المشرفين')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">المشرفين</h1>
        <p class="text-slate-500 text-sm mt-1">إدارة مشرفي التدريب</p>
    </div>
    <div class="flex flex-wrap gap-2">
        @can('export-reports')
        <a href="{{ route('reports.supervisors') }}"
           class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition">
            تصدير Excel
        </a>
        @endcan
        <x-excel-import
            modal-id="import-supervisors"
            title="استيراد المشرفين من Excel"
            description="الأعمدة: الاسم | رقم التليفون | الفصل (يجب أن يكون الفصل موجوداً مسبقاً)."
            :template-route="route('supervisors.import.template')"
            :import-route="route('supervisors.import')"
        />
        <a href="{{ route('supervisors.create') }}"
           class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition">
            + إضافة مشرف
        </a>
    </div>
</div>

@if(session('import_errors'))
<div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6 text-sm">
    <p class="font-semibold text-amber-800 mb-2">تفاصيل الصفوف المتخطاة:</p>
    <ul class="space-y-1 text-amber-700 max-h-40 overflow-y-auto">
        @foreach(session('import_errors') as $row => $error)
        <li>صف {{ $row }}: {{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 mb-6">
    <form method="GET" action="{{ route('supervisors.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
        <div class="lg:col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-1.5">بحث</label>
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="الاسم أو رقم التليفون..."
                   class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">الفصل</label>
            <select name="school_class_id"
                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                <option value="">كل الفصول</option>
                @foreach($classes as $class)
                <option value="{{ $class->id }}" {{ ($filters['school_class_id'] ?? '') == $class->id ? 'selected' : '' }}>
                    {{ $class->name }}
                </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">الحالة</label>
            <select name="status"
                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                <option value="">الكل</option>
                <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>نشط</option>
                <option value="completed" {{ ($filters['status'] ?? '') === 'completed' ? 'selected' : '' }}>مكتمل</option>
                <option value="suspended" {{ ($filters['status'] ?? '') === 'suspended' ? 'selected' : '' }}>موقوف</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">إنذارات / خصم</label>
            <select name="warnings"
                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                <option value="">الكل</option>
                <option value="active" {{ ($filters['warnings'] ?? '') === 'active' ? 'selected' : '' }}>لديه إنذارات نشطة</option>
                <option value="deducted" {{ ($filters['warnings'] ?? '') === 'deducted' ? 'selected' : '' }}>لديه أيام مخصومة</option>
            </select>
        </div>
        <div class="flex gap-2 sm:col-span-2 lg:col-span-5">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-5 py-2.5 rounded-lg">
                تطبيق الفلتر
            </button>
            @if(collect($filters)->filter()->isNotEmpty())
            <a href="{{ route('supervisors.index') }}" class="text-sm text-slate-600 hover:text-slate-800 px-5 py-2.5 border border-slate-300 rounded-lg">
                مسح الفلتر
            </a>
            @endif
        </div>
    </form>
</div>

<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="text-right px-6 py-3 font-semibold">الاسم</th>
                    <th class="text-right px-6 py-3 font-semibold">الهاتف</th>
                    <th class="text-right px-6 py-3 font-semibold">الفصل</th>
                    <th class="text-right px-6 py-3 font-semibold">الأيام المخصومة</th>
                    <th class="text-right px-6 py-3 font-semibold">إنذارات نشطة</th>
                    <th class="text-right px-6 py-3 font-semibold">الحالة</th>
                    <th class="text-right px-6 py-3 font-semibold">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($supervisors as $supervisor)
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-3 font-medium">{{ $supervisor->name }}</td>
                    <td class="px-6 py-3">{{ $supervisor->phone ?? '—' }}</td>
                    <td class="px-6 py-3">{{ $supervisor->schoolClass->name }}</td>
                    <td class="px-6 py-3">
                        @if($supervisor->deducted_days > 0)
                            <span class="text-red-600 font-semibold">{{ $supervisor->deducted_days }}</span>
                        @else
                            0
                        @endif
                    </td>
                    <td class="px-6 py-3">
                        @if($supervisor->active_warnings_count > 0)
                            <span class="bg-amber-100 text-amber-800 text-xs font-bold px-2 py-0.5 rounded-full">{{ $supervisor->active_warnings_count }}/3</span>
                        @else
                            0
                        @endif
                    </td>
                    <td class="px-6 py-3"><x-status-badge :status="$supervisor->status" /></td>
                    <td class="px-6 py-3">
                        <a href="{{ route('supervisors.show', $supervisor) }}" class="text-indigo-600 hover:text-indigo-800">كارت المشرف</a>
                        @can('manage-supervisors')
                        <form action="{{ route('supervisors.destroy', $supervisor) }}" method="POST" class="inline mr-3"
                              onsubmit="return confirm('هل أنت متأكد من حذف {{ $supervisor->name }}؟')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800">حذف</button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-6 py-8 text-center text-slate-400">
                    @if(collect($filters)->filter()->isNotEmpty())
                        لا توجد نتائج مطابقة للفلتر
                    @else
                        لا يوجد مشرفين
                    @endif
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($supervisors->hasPages())
    <div class="px-6 py-4 border-t border-slate-200">{{ $supervisors->links() }}</div>
    @endif
</div>
@endsection
