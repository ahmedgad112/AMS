@extends('layouts.app')

@section('title', 'تعديل فصل')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-900">تعديل: {{ $schoolClass->name }}</h1>
</div>

<div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 max-w-2xl">
    <form method="POST" action="{{ route('school-classes.update', $schoolClass) }}" class="space-y-5">
        @csrf @method('PUT')

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">اسم الفصل</label>
            <input type="text" name="name" value="{{ old('name', $schoolClass->name) }}" required
                   class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
            @error('name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">كود الفصل</label>
            <input type="text" name="code" value="{{ old('code', $schoolClass->code) }}" required
                   class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
            @error('code')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">الموقع</label>
            <input type="text" name="location" value="{{ old('location', $schoolClass->location) }}"
                   class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">إسناد المفتشين</label>
            <div class="space-y-2 max-h-48 overflow-y-auto border border-slate-200 rounded-lg p-3">
                @php $assigned = old('inspector_ids', $schoolClass->inspectors->pluck('id')->all()); @endphp
                @foreach($inspectors as $inspector)
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="inspector_ids[]" value="{{ $inspector->id }}"
                           {{ in_array($inspector->id, $assigned) ? 'checked' : '' }}
                           class="rounded border-slate-300 text-indigo-600">
                    {{ $inspector->name }}
                </label>
                @endforeach
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-5 py-2.5 rounded-lg">تحديث</button>
            <a href="{{ route('school-classes.index') }}" class="text-sm text-slate-600 hover:text-slate-800 px-5 py-2.5">إلغاء</a>
        </div>
    </form>
</div>
@endsection
