@extends('layouts.app')

@section('title', 'لوحة التحكم')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-slate-900">مرحباً، {{ auth()->user()->name }}</h1>
    <p class="text-slate-500 mt-1">نظرة عامة على النظام</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">
    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
        <p class="text-sm text-slate-500">الفصول</p>
        <p class="text-3xl font-bold text-slate-900 mt-1">{{ $stats['classes_count'] }}</p>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
        <p class="text-sm text-slate-500">المشرفين النشطين</p>
        <p class="text-3xl font-bold text-emerald-600 mt-1">{{ $stats['supervisors_count'] }}</p>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
        <p class="text-sm text-slate-500">جلسات مفتوحة</p>
        <p class="text-3xl font-bold text-indigo-600 mt-1">{{ $stats['open_sessions'] }}</p>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
        <p class="text-sm text-slate-500">إنذارات هذا الشهر</p>
        <p class="text-3xl font-bold text-amber-600 mt-1">{{ $stats['warnings_this_month'] }}</p>
    </div>
</div>

<div class="bg-white rounded-xl border border-slate-200 shadow-sm">
    <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
        <h2 class="font-bold text-slate-900">آخر جلسات الحضور</h2>
        @can('view-attendance')
        <a href="{{ route('attendance.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">عرض الكل</a>
        @endcan
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="text-right px-6 py-3 font-semibold">التاريخ</th>
                    <th class="text-right px-6 py-3 font-semibold">الفصل</th>
                    <th class="text-right px-6 py-3 font-semibold">بواسطة</th>
                    <th class="text-right px-6 py-3 font-semibold">الحالة</th>
                    <th class="text-right px-6 py-3 font-semibold"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($recentSessions as $session)
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-3">{{ $session->date->format('Y-m-d') }}</td>
                    <td class="px-6 py-3">{{ $session->schoolClass->name }}</td>
                    <td class="px-6 py-3">{{ $session->createdBy->name }}</td>
                    <td class="px-6 py-3"><x-status-badge :status="$session->status" /></td>
                    <td class="px-6 py-3">
                        @can('view-attendance')
                        <a href="{{ route('attendance.sessions.show', $session) }}" class="text-indigo-600 hover:text-indigo-800">عرض</a>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-slate-400">لا توجد جلسات بعد</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
