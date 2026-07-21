@extends('layouts.app')

@section('title', 'الفصول والورش')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">الفصول والورش</h1>
        <p class="text-slate-500 text-sm mt-1">إدارة الفصول وإسناد المفتشين</p>
    </div>
    <div class="flex gap-2">
        @can('import-classes')
        <x-excel-import
            modal-id="import-classes"
            title="استيراد الفصول من Excel"
            description="العمود المطلوب: اسم الفصل — يتم توليد الكود تلقائياً."
            :template-route="route('school-classes.import.template')"
            :import-route="route('school-classes.import')"
        />
        @endcan
        @can('create-classes')
        <a href="{{ route('school-classes.create') }}"
           class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition">
            + إضافة فصل
        </a>
        @endcan
    </div>
</div>

@if(session('import_errors'))
<div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6 text-sm">
    <p class="font-semibold text-amber-800 mb-2">تفاصيل الصفوف المتخطاة:</p>
    <ul class="space-y-1 text-amber-700 max-h-40 overflow-y-auto">
        @foreach(session('import_errors') as $row => $error)
        <li>صف {{ $row }}: {{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="text-right px-6 py-3 font-semibold">الاسم</th>
                    <th class="text-right px-6 py-3 font-semibold">الكود</th>
                    <th class="text-right px-6 py-3 font-semibold">الموقع</th>
                    <th class="text-right px-6 py-3 font-semibold">المشرفين</th>
                    <th class="text-right px-6 py-3 font-semibold">المفتشين</th>
                    <th class="text-right px-6 py-3 font-semibold">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($classes as $class)
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-3 font-medium">{{ $class->name }}</td>
                    <td class="px-6 py-3 font-mono text-xs">{{ $class->code }}</td>
                    <td class="px-6 py-3">{{ $class->location ?? '—' }}</td>
                    <td class="px-6 py-3">{{ $class->supervisors_count }}</td>
                    <td class="px-6 py-3">{{ $class->inspectors_count }}</td>
                    <td class="px-6 py-3 space-x-reverse space-x-2">
                        @can('edit-classes')
                        <a href="{{ route('school-classes.edit', $class) }}" class="text-indigo-600 hover:text-indigo-800">تعديل</a>
                        @endcan
                        @can('delete-classes')
                        <form action="{{ route('school-classes.destroy', $class) }}" method="POST" class="inline"
                              onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800">حذف</button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-8 text-center text-slate-400">لا توجد فصول</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($classes->hasPages())
    <div class="px-6 py-4 border-t border-slate-200">{{ $classes->links() }}</div>
    @endif
</div>
@endsection
