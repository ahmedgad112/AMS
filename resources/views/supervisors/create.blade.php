@extends('layouts.app')

@section('title', 'إضافة مشرف')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-900">إضافة مشرف</h1>
</div>

<div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 max-w-2xl">
    <form method="POST" action="{{ route('supervisors.store') }}" class="space-y-5">
        @csrf

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">اسم المشرف</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
            @error('name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">الهاتف</label>
            <input type="text" name="phone" value="{{ old('phone') }}"
                   class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">الفصل</label>
            <select name="school_class_id" required
                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                <option value="">— اختر الفصل —</option>
                @foreach($classes as $class)
                <option value="{{ $class->id }}" {{ old('school_class_id') == $class->id ? 'selected' : '' }}>
                    {{ $class->name }} ({{ $class->code }})
                </option>
                @endforeach
            </select>
            @error('school_class_id')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">إجمالي أيام التدريب</label>
            <input type="number" name="total_training_days" value="{{ old('total_training_days', 30) }}" min="1" max="365" required
                   class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">الحالة</label>
            <select name="status" required
                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>نشط</option>
                <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>مكتمل</option>
                <option value="suspended" {{ old('status') === 'suspended' ? 'selected' : '' }}>موقوف</option>
            </select>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-5 py-2.5 rounded-lg">حفظ</button>
            <a href="{{ route('supervisors.index') }}" class="text-sm text-slate-600 hover:text-slate-800 px-5 py-2.5">إلغاء</a>
        </div>
    </form>
</div>
@endsection
