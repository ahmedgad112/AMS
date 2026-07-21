@extends('layouts.app')

@section('title', 'تعديل مستخدم')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-900">تعديل: {{ $user->name }}</h1>
</div>

<div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 max-w-2xl"
     x-data="{
        role: '{{ old('role', $user->roles->first()?->name ?? '') }}',
        rolesAccessAll: {{ $rolesAccessAll->toJson() }},
        needsClasses() { return this.role && !this.rolesAccessAll[this.role]; }
     }">
    <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-5">
        @csrf @method('PUT')

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">الاسم</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                   class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">البريد الإلكتروني</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                   class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">الهاتف</label>
            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                   class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">كلمة مرور جديدة (اختياري)</label>
            <input type="password" name="password"
                   class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">تأكيد كلمة المرور</label>
            <input type="password" name="password_confirmation"
                   class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">الدور</label>
            <select name="role" x-model="role" required
                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                @foreach($roles as $roleOption)
                <option value="{{ $roleOption->name }}" {{ old('role', $user->roles->first()?->name) === $roleOption->name ? 'selected' : '' }}>
                    {{ $roleOption->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div x-show="needsClasses()" x-cloak>
            <label class="block text-sm font-medium text-slate-700 mb-2">إسناد الفصول</label>
            <div class="space-y-2 max-h-48 overflow-y-auto border border-slate-200 rounded-lg p-3">
                @php $assigned = old('class_ids', $user->schoolClasses->pluck('id')->all()); @endphp
                @foreach($classes as $class)
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="class_ids[]" value="{{ $class->id }}"
                           {{ in_array($class->id, $assigned) ? 'checked' : '' }}
                           class="rounded border-slate-300 text-indigo-600">
                    {{ $class->name }} ({{ $class->code }})
                </label>
                @endforeach
            </div>
            @error('class_ids')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-5 py-2.5 rounded-lg">تحديث</button>
            <a href="{{ route('users.index') }}" class="text-sm text-slate-600 hover:text-slate-800 px-5 py-2.5">إلغاء</a>
        </div>
    </form>
</div>
@endsection
