@extends('layouts.app')

@section('title', 'المستخدمين')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">المستخدمين</h1>
        <p class="text-slate-500 text-sm mt-1">إدارة حسابات النظام والأدوار</p>
    </div>
    @can('create-users')
    <a href="{{ route('users.create') }}"
       class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition">
        + إضافة مستخدم
    </a>
    @endcan
</div>

<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="text-right px-6 py-3 font-semibold">الاسم</th>
                    <th class="text-right px-6 py-3 font-semibold">البريد</th>
                    <th class="text-right px-6 py-3 font-semibold">الهاتف</th>
                    <th class="text-right px-6 py-3 font-semibold">الدور</th>
                    <th class="text-right px-6 py-3 font-semibold">الفصول</th>
                    <th class="text-right px-6 py-3 font-semibold">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($users as $user)
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-3 font-medium">{{ $user->name }}</td>
                    <td class="px-6 py-3">{{ $user->email }}</td>
                    <td class="px-6 py-3">{{ $user->phone ?? '—' }}</td>
                    <td class="px-6 py-3">
                        <span class="bg-indigo-100 text-indigo-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">{{ $user->roleLabel() }}</span>
                    </td>
                    <td class="px-6 py-3">{{ $user->schoolClasses->pluck('name')->join('، ') ?: '—' }}</td>
                    <td class="px-6 py-3">
                        @can('impersonate-users')
                        @if($user->id !== auth()->id() && !($isImpersonating ?? false))
                        <form action="{{ route('users.impersonate', $user) }}" method="POST" class="inline ml-3"
                              onsubmit="return confirm('الدخول بحساب {{ $user->name }}؟')">
                            @csrf
                            <button type="submit" class="text-emerald-600 hover:text-emerald-800">دخول كـ</button>
                        </form>
                        @endif
                        @endcan
                        @can('edit-users')
                        <a href="{{ route('users.edit', $user) }}" class="text-indigo-600 hover:text-indigo-800">تعديل</a>
                        @endcan
                        @can('delete-users')
                        @if($user->id !== auth()->id())
                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline mr-3"
                              onsubmit="return confirm('هل أنت متأكد؟')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800">حذف</button>
                        </form>
                        @endif
                        @endcan
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-8 text-center text-slate-400">لا يوجد مستخدمين</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="px-6 py-4 border-t border-slate-200">{{ $users->links() }}</div>
    @endif
</div>
@endsection
