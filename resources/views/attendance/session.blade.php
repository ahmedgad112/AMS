@extends('layouts.app')

@section('title', 'جلسة حضور — ' . $session->schoolClass->name)

@section('content')
<div class="flex flex-wrap items-start justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">جلسة حضور — {{ $session->schoolClass->name }}</h1>
        <p class="text-slate-500 text-sm mt-1">
            {{ $session->date->format('Y-m-d') }} —
            <x-status-badge :status="$session->status" />
        </p>
    </div>
    <div class="flex flex-wrap gap-2">
        @if($session->isOpen())
        @can('close-attendance-sessions')
        <form method="POST" action="{{ route('attendance.sessions.close', $session) }}"
              onsubmit="return confirm('هل أنت متأكد من إغلاق الجلسة؟ لن يمكن التعديل بعد الإغلاق.')">
            @csrf
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg">
                إغلاق الجلسة
            </button>
        </form>
        @endcan
        @elseif(auth()->user()->can('reopen-sessions'))
        <form method="POST" action="{{ route('attendance.sessions.reopen', $session) }}">
            @csrf
            <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold px-4 py-2.5 rounded-lg">
                إعادة فتح الجلسة
            </button>
        </form>
        @endif
        @can('delete-attendance-sessions')
        <form method="POST" action="{{ route('attendance.sessions.destroy', $session) }}"
              onsubmit="return confirm('هل أنت متأكد من حذف هذه الجلسة بالكامل؟')">
            @csrf @method('DELETE')
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg">
                حذف الجلسة
            </button>
        </form>
        @endcan
        <a href="{{ route('attendance.index') }}" class="border border-slate-300 hover:bg-slate-50 text-slate-700 text-sm font-semibold px-4 py-2.5 rounded-lg">
            رجوع
        </a>
    </div>
</div>

@if($session->isClosed())
<div class="bg-slate-100 border border-slate-300 text-slate-700 rounded-lg px-4 py-3 text-sm mb-6">
    هذه الجلسة مغلقة — للعرض فقط. @can('reopen-sessions') يمكن إعادة فتحها بصلاحية خاصة. @endcan
</div>
@endif

@if($supervisors->isEmpty())
<div class="bg-amber-50 border border-amber-200 text-amber-800 rounded-lg px-4 py-3 text-sm">
    لا يوجد مشرفين نشطين في هذا الفصل.
</div>
@else
<form method="POST" action="{{ route('attendance.sessions.records.store', $session) }}"
      enctype="multipart/form-data"
      class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    @csrf

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="text-right px-6 py-3 font-semibold">المشرف</th>
                    <th class="text-right px-6 py-3 font-semibold">الحالة</th>
                    <th class="text-right px-6 py-3 font-semibold">تفاصيل العذر</th>
                    @if($session->isOpen())
                    <th class="text-right px-6 py-3 font-semibold w-24"></th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @php $canSaveRecords = auth()->user()->can('save-attendance-records'); @endphp
                @foreach($supervisors as $index => $supervisor)
                @php
                    $existing = $recordsBySupervisor->get($supervisor->id);
                    $currentStatus = old("records.{$index}.status", $existing?->status ?? 'present');
                @endphp
                <tr x-data="{ status: '{{ $currentStatus }}' }" class="hover:bg-slate-50">
                    <td class="px-6 py-4 align-top">
                        <input type="hidden" name="records[{{ $index }}][supervisor_id]" value="{{ $supervisor->id }}">
                        <p class="font-medium text-slate-900">{{ $supervisor->name }}</p>
                        <p class="text-xs text-slate-400">{{ $supervisor->phone }}</p>
                    </td>
                    <td class="px-6 py-4 align-top">
                        <div class="flex flex-wrap gap-2">
                            @foreach(['present' => 'حاضر', 'absent' => 'غائب', 'late' => 'متأخر', 'excused' => 'غياب بعذر'] as $value => $label)
                            @php
                                $colors = [
                                    'present' => 'peer-checked:bg-emerald-600 peer-checked:border-emerald-600',
                                    'absent' => 'peer-checked:bg-red-600 peer-checked:border-red-600',
                                    'late' => 'peer-checked:bg-amber-500 peer-checked:border-amber-500',
                                    'excused' => 'peer-checked:bg-blue-600 peer-checked:border-blue-600',
                                ];
                            @endphp
                            <label class="cursor-pointer">
                                <input type="radio" name="records[{{ $index }}][status]" value="{{ $value }}"
                                       x-model="status"
                                       {{ ($session->isClosed() || ! $canSaveRecords) ? 'disabled' : '' }}
                                       class="peer sr-only">
                                <span class="inline-block px-3 py-1.5 rounded-lg border border-slate-300 text-xs font-semibold transition
                                    peer-checked:text-white {{ $colors[$value] }}
                                    {{ ($session->isClosed() || ! $canSaveRecords) ? 'opacity-60 cursor-not-allowed' : 'hover:border-slate-400' }}">
                                    {{ $label }}
                                </span>
                            </label>
                            @endforeach
                        </div>
                        @error("records.{$index}.status")
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </td>
                    <td class="px-6 py-4 align-top">
                        <div x-show="status === 'excused'" x-cloak class="space-y-3">
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">سبب العذر</label>
                                <textarea name="records[{{ $index }}][excuse_reason]" rows="2"
                                          {{ ($session->isClosed() || ! $canSaveRecords) ? 'disabled' : '' }}
                                          class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">{{ old("records.{$index}.excuse_reason", $existing?->excuse_reason) }}</textarea>
                                @error("records.{$index}.excuse_reason")
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">مرفق العذر (صورة / PDF)</label>
                                @if($existing?->excuse_attachment)
                                <p class="text-xs text-slate-500 mb-1">
                                    مرفق حالي:
                                    <a href="{{ $existing->attachmentUrl() }}" target="_blank" class="text-indigo-600">عرض</a>
                                </p>
                                @endif
                                <input type="file" name="records[{{ $index }}][excuse_attachment]"
                                       accept=".jpg,.jpeg,.png,.pdf"
                                       {{ ($session->isClosed() || ! $canSaveRecords) ? 'disabled' : '' }}
                                       class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                @error("records.{$index}.excuse_attachment")
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <p x-show="status !== 'excused'" class="text-slate-400 text-xs">—</p>
                    </td>
                    @if($session->isOpen())
                    <td class="px-6 py-4 align-top">
                        @if($existing)
                        @can('delete-attendance-records')
                        <form action="{{ route('attendance.sessions.records.destroy', [$session, $existing]) }}" method="POST"
                              onsubmit="return confirm('حذف سجل حضور {{ $supervisor->name }}؟')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">مسح</button>
                        </form>
                        @endcan
                        @endif
                    </td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($session->isOpen())
    @can('save-attendance-records')
    <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-6 py-2.5 rounded-lg">
            حفظ سجل الحضور
        </button>
    </div>
    @endcan
    @endif
</form>
@endif
@endsection
