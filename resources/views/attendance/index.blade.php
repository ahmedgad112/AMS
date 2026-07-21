@extends('layouts.app')

@section('title', 'الحضور والغياب')

@section('content')
<div class="flex flex-wrap items-start justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">الحضور والغياب</h1>
        <p class="text-slate-500 text-sm mt-1">فتح جلسة يومية وتسجيل حضور المشرفين</p>
    </div>
    @can('export-reports')
    <a href="{{ route('reports.attendance', request()->only(['class_id'])) }}"
       class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition">
        تصدير Excel
    </a>
    @endcan
</div>

{{-- Open new session --}}
<div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 mb-6">
    <h2 class="font-bold text-slate-900 mb-4">فتح جلسة جديدة</h2>
    <form method="POST" action="{{ route('attendance.sessions.create') }}" class="flex flex-wrap gap-4 items-end">
        @csrf
        <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-medium text-slate-700 mb-1.5">الفصل</label>
            <select name="school_class_id" required
                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                <option value="">— اختر الفصل —</option>
                @foreach($classes as $class)
                <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                    {{ $class->name }} ({{ $class->supervisors_count }} مشرف)
                </option>
                @endforeach
            </select>
        </div>
        <div class="min-w-[160px]">
            <label class="block text-sm font-medium text-slate-700 mb-1.5">التاريخ</label>
            <input type="date" name="date" value="{{ now()->toDateString() }}"
                   class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
        </div>
        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-6 py-2.5 rounded-lg">
            فتح الجلسة
        </button>
    </form>
</div>

{{-- Sessions list --}}
<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
        <h2 class="font-bold text-slate-900">جلسات الحضور</h2>
        @if($classes->isNotEmpty())
        <form method="GET" class="flex gap-2">
            <select name="class_id" onchange="this.form.submit()"
                    class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm">
                <option value="">كل الفصول</option>
                @foreach($classes as $class)
                <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                @endforeach
            </select>
        </form>
        @endif
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="text-right px-6 py-3 font-semibold">التاريخ</th>
                    <th class="text-right px-6 py-3 font-semibold">الفصل</th>
                    <th class="text-right px-6 py-3 font-semibold">بواسطة</th>
                    <th class="text-right px-6 py-3 font-semibold">الحالة</th>
                    <th class="text-right px-6 py-3 font-semibold">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($sessions as $session)
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-3 font-medium">{{ $session->date->format('Y-m-d') }}</td>
                    <td class="px-6 py-3">{{ $session->schoolClass->name }}</td>
                    <td class="px-6 py-3">{{ $session->createdBy->name }}</td>
                    <td class="px-6 py-3"><x-status-badge :status="$session->status" /></td>
                    <td class="px-6 py-3">
                        <a href="{{ route('attendance.sessions.show', $session) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                            {{ $session->isOpen() ? 'تسجيل الحضور' : 'عرض' }}
                        </a>
                        @can('manage-attendance')
                        <form action="{{ route('attendance.sessions.destroy', $session) }}" method="POST" class="inline mr-3"
                              onsubmit="return confirm('هل أنت متأكد من حذف جلسة {{ $session->date->format('Y-m-d') }}؟ سيتم حذف كل سجلات الحضور.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800">حذف</button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-8 text-center text-slate-400">لا توجد جلسات</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($sessions->hasPages())
    <div class="px-6 py-4 border-t border-slate-200">{{ $sessions->links() }}</div>
    @endif
</div>
@endsection
