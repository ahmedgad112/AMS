@extends('layouts.app')

@section('title', 'إنشاء دور')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-900">إنشاء دور جديد</h1>
    <p class="text-slate-500 text-sm mt-1">اختر الصلاحيات التي يمتلكها هذا الدور</p>
</div>

<div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 max-w-3xl">
    <form method="POST" action="{{ route('roles.store') }}">
        @csrf

        <div class="mb-6">
            <label class="block text-sm font-medium text-slate-700 mb-1.5">اسم الدور (بالإنجليزية)</label>
            <input type="text" name="name" value="{{ old('name') }}" required placeholder="مثال: coordinator"
                   class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
            <p class="text-xs text-slate-400 mt-1">حروف إنجليزية صغيرة وأرقام وشرطة (-) فقط</p>
            @error('name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        @include('roles._permissions-form', ['assignedPermissions' => old('permissions', [])])

        <div class="flex gap-3 mt-6 pt-4 border-t border-slate-200">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-5 py-2.5 rounded-lg">حفظ الدور</button>
            <a href="{{ route('roles.index') }}" class="text-sm text-slate-600 hover:text-slate-800 px-5 py-2.5">إلغاء</a>
        </div>
    </form>
</div>
@endsection
