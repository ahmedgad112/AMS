@extends('layouts.app')

@section('title', 'تعديل مشرف')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-900">تعديل: {{ $supervisor->name }}</h1>
</div>

<div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 max-w-2xl">
    <form method="POST" action="{{ route('supervisors.update', $supervisor) }}" class="space-y-5">
        @csrf @method('PUT')

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">اسم المشرف</label>
            <input type="text" name="name" value="{{ old('name', $supervisor->name) }}" required
                   class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">الهاتف</label>
            <input type="text" name="phone" value="{{ old('phone', $supervisor->phone) }}"
                   class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">الفصل</label>
            <select name="school_class_id" required
                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                @foreach($classes as $class)
                <option value="{{ $class->id }}" {{ old('school_class_id', $supervisor->school_class_id) == $class->id ? 'selected' : '' }}>
                    {{ $class->name }} ({{ $class->code }})
                </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">إجمالي أيام التدريب</label>
            <input type="number" name="total_training_days" value="{{ old('total_training_days', $supervisor->total_training_days) }}" min="1" max="365" required
                   class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
        </div>

        @can('edit-supervisor-deductions')
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">الأيام المخصومة (اعتماد يدوي)</label>
            <input type="number" name="deducted_days" value="{{ old('deducted_days', $supervisor->deducted_days) }}" min="0"
                   class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
        </div>
        @endcan

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">الحالة</label>
            <select name="status" required
                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                <option value="active" {{ old('status', $supervisor->status) === 'active' ? 'selected' : '' }}>نشط</option>
                <option value="completed" {{ old('status', $supervisor->status) === 'completed' ? 'selected' : '' }}>مكتمل</option>
                <option value="suspended" {{ old('status', $supervisor->status) === 'suspended' ? 'selected' : '' }}>موقوف</option>
            </select>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-5 py-2.5 rounded-lg">تحديث</button>
            <a href="{{ route('supervisors.show', $supervisor) }}" class="text-sm text-slate-600 hover:text-slate-800 px-5 py-2.5">إلغاء</a>
        </div>
    </form>
</div>
@endsection
