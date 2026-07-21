@extends('layouts.app')

@section('title', 'الأدوار والصلاحيات')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">الأدوار والصلاحيات</h1>
        <p class="text-slate-500 text-sm mt-1">تحكم كامل في صلاحيات كل دور بالنظام</p>
    </div>
    <a href="{{ route('roles.create') }}"
       class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition">
        + دور جديد
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
    @foreach($roles as $role)
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-start justify-between mb-4">
            <div>
                <h3 class="font-bold text-slate-900 text-lg">{{ $role->name }}</h3>
                <p class="text-sm text-slate-500 mt-1">
                    {{ $role->permissions_count }} صلاحية · {{ $role->users_count }} مستخدم
                </p>
            </div>
            @if(\App\Support\PermissionCatalog::isProtectedRole($role->name))
            <span class="bg-purple-100 text-purple-800 text-xs font-semibold px-2 py-0.5 rounded-full">محمي</span>
            @endif
        </div>

        <div class="flex flex-wrap gap-1 mb-4 max-h-24 overflow-y-auto">
            @forelse($role->permissions as $permission)
            <span class="bg-slate-100 text-slate-600 text-xs px-2 py-0.5 rounded">{{ \App\Support\PermissionCatalog::label($permission->name) }}</span>
            @empty
            <span class="text-slate-400 text-xs">لا توجد صلاحيات</span>
            @endforelse
        </div>

        <div class="flex gap-3 pt-2 border-t border-slate-100">
            <a href="{{ route('roles.edit', $role) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">تعديل الصلاحيات</a>
            @if(! \App\Support\PermissionCatalog::isProtectedRole($role->name) && $role->users_count === 0)
            <form action="{{ route('roles.destroy', $role) }}" method="POST" class="inline"
                  onsubmit="return confirm('هل أنت متأكد من حذف هذا الدور؟')">
                @csrf @method('DELETE')
                <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">حذف</button>
            </form>
            @endif
        </div>
    </div>
    @endforeach
</div>
@endsection
