@extends('layouts.app')

@section('title', 'سجل النشاط')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-900">سجل النشاط</h1>
    <p class="text-slate-500 text-sm mt-1">تتبع كل العمليات التي تحدث في النظام</p>
</div>

<div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 mb-6">
    <form method="GET" action="{{ route('activity-log.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4 items-end">
        <div class="lg:col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-1.5">بحث</label>
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="ابحث في الوصف..."
                   class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">القسم</label>
            <select name="log_name" class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                <option value="">الكل</option>
                @foreach($logNames as $value => $label)
                <option value="{{ $value }}" {{ ($filters['log_name'] ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">نوع العملية</label>
            <select name="event" class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                <option value="">الكل</option>
                @foreach($events as $value => $label)
                <option value="{{ $value }}" {{ ($filters['event'] ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">المستخدم</label>
            <select name="causer_id" class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                <option value="">الكل</option>
                @foreach($users as $user)
                <option value="{{ $user->id }}" {{ ($filters['causer_id'] ?? '') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">من تاريخ</label>
            <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}"
                   class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">إلى تاريخ</label>
            <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}"
                   class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
        </div>
        <div class="flex gap-2 sm:col-span-2 lg:col-span-6">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-5 py-2.5 rounded-lg">
                تطبيق الفلتر
            </button>
            @if(collect($filters)->filter()->isNotEmpty())
            <a href="{{ route('activity-log.index') }}" class="text-sm text-slate-600 hover:text-slate-800 px-5 py-2.5 border border-slate-300 rounded-lg">
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
                    <th class="text-right px-6 py-3 font-semibold">التاريخ والوقت</th>
                    <th class="text-right px-6 py-3 font-semibold">المستخدم</th>
                    <th class="text-right px-6 py-3 font-semibold">القسم</th>
                    <th class="text-right px-6 py-3 font-semibold">العملية</th>
                    <th class="text-right px-6 py-3 font-semibold">الوصف</th>
                    <th class="text-right px-6 py-3 font-semibold">التفاصيل</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($logs as $log)
                <tr class="hover:bg-slate-50 align-top">
                    <td class="px-6 py-3 whitespace-nowrap text-slate-600">
                        {{ $log->created_at->format('Y-m-d') }}
                        <span class="block text-xs text-slate-400">{{ $log->created_at->format('H:i:s') }}</span>
                    </td>
                    <td class="px-6 py-3">
                        {{ $log->causer?->name ?? '—' }}
                    </td>
                    <td class="px-6 py-3">
                        <span class="bg-slate-100 text-slate-700 text-xs font-medium px-2 py-0.5 rounded-full">
                            {{ \App\Support\ActivityLogPresenter::logNameLabel($log->log_name) }}
                        </span>
                    </td>
                    <td class="px-6 py-3">
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ \App\Support\ActivityLogPresenter::eventBadgeClass($log->event) }}">
                            {{ \App\Support\ActivityLogPresenter::eventLabel($log->event) }}
                        </span>
                    </td>
                    <td class="px-6 py-3 font-medium text-slate-900 max-w-md">
                        {{ $log->description }}
                    </td>
                    <td class="px-6 py-3 text-xs text-slate-500 max-w-xs">
                        @if($log->properties)
                            @if(isset($log->properties['changes']))
                            <details class="cursor-pointer">
                                <summary class="text-indigo-600 font-medium">التغييرات</summary>
                                <ul class="mt-2 space-y-1">
                                    @foreach($log->properties['changes'] as $field => $value)
                                    <li>
                                        <span class="font-mono text-slate-600">{{ $field }}</span>:
                                        @if(isset($log->properties['old'][$field]))
                                        <span class="text-red-600 line-through">{{ is_array($log->properties['old'][$field]) ? json_encode($log->properties['old'][$field], JSON_UNESCAPED_UNICODE) : $log->properties['old'][$field] }}</span>
                                        →
                                        @endif
                                        <span class="text-emerald-700">{{ is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value }}</span>
                                    </li>
                                    @endforeach
                                </ul>
                            </details>
                            @elseif(isset($log->properties['filters']) || isset($log->properties['imported']))
                            <ul class="space-y-0.5">
                                @foreach($log->properties as $key => $value)
                                @if(! is_array($value))
                                <li><span class="text-slate-400">{{ $key }}:</span> {{ $value }}</li>
                                @endif
                                @endforeach
                            </ul>
                            @else
                            <details class="cursor-pointer">
                                <summary class="text-indigo-600 font-medium">عرض</summary>
                                <pre class="mt-2 text-[11px] bg-slate-50 p-2 rounded overflow-x-auto">{{ json_encode($log->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </details>
                            @endif
                        @else
                            —
                        @endif
                        @if($log->ip_address)
                        <p class="mt-1 text-slate-400">IP: {{ $log->ip_address }}</p>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-slate-400">لا توجد عمليات مسجّلة</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="px-6 py-4 border-t border-slate-200">{{ $logs->links() }}</div>
    @endif
</div>
@endsection
