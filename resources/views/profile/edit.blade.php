@extends('layouts.app')

@section('title', 'الملف الشخصي')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-900">الملف الشخصي</h1>
    <p class="text-slate-500 mt-1">إدارة بيانات حسابك</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xl font-bold shrink-0">
                    {{ mb_substr($user->name, 0, 1) }}
                </div>
                <div class="min-w-0">
                    <p class="font-bold text-slate-900 truncate">{{ $user->name }}</p>
                    <p class="text-sm text-slate-500 truncate">{{ $user->email }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <h2 class="font-semibold text-slate-900 mb-4">معلومات الحساب</h2>
            <dl class="space-y-4 text-sm">
                <div>
                    <dt class="text-slate-500 mb-1">الدور</dt>
                    <dd class="font-medium text-slate-900">{{ $user->roleLabel() }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500 mb-1">الفصول المسندة</dt>
                    <dd class="font-medium text-slate-900">
                        @if($user->canAccessAllClasses())
                            جميع الفصول
                        @elseif($user->schoolClasses->isNotEmpty())
                            {{ $user->schoolClasses->pluck('name')->join('، ') }}
                        @else
                            —
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-slate-500 mb-1">تاريخ الانضمام</dt>
                    <dd class="font-medium text-slate-900">{{ $user->created_at->format('Y-m-d') }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <h2 class="font-semibold text-slate-900 mb-5">تعديل البيانات</h2>

            <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">الاسم</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    @error('name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">البريد الإلكتروني</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    @error('email')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">الهاتف</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                           class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    @error('phone')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="border-t border-slate-200 pt-5">
                    <h3 class="text-sm font-semibold text-slate-900 mb-4">تغيير كلمة المرور</h3>
                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">كلمة مرور جديدة (اختياري)</label>
                            <input type="password" name="password"
                                   class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                            @error('password')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">تأكيد كلمة المرور</label>
                            <input type="password" name="password_confirmation"
                                   class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition">
                        حفظ التغييرات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
