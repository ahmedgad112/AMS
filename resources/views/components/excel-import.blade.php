@props([
    'modalId',
    'title',
    'templateRoute',
    'importRoute',
    'description',
])

<div x-data="{ open: false }" class="inline-block">
    <button type="button" @click="open = true"
            {{ $attributes->merge(['class' => 'bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-sm font-semibold px-4 py-2.5 rounded-lg transition inline-flex items-center gap-2']) }}>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
        استيراد Excel
    </button>

    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
        <div class="fixed inset-0 bg-black/50" @click="open = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6" @click.stop>
            <h3 class="text-lg font-bold text-slate-900 mb-2">{{ $title }}</h3>
            <p class="text-sm text-slate-500 mb-4">{{ $description }}</p>

            <div class="bg-slate-50 border border-slate-200 rounded-lg p-4 mb-4 text-sm text-slate-600">
                <a href="{{ $templateRoute }}" class="text-indigo-600 hover:text-indigo-800 font-medium inline-flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    تحميل نموذج Excel
                </a>
            </div>

            <form method="POST" action="{{ $importRoute }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">ملف Excel</label>
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                           class="block w-full text-sm text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-5 py-2.5 rounded-lg">رفع واستيراد</button>
                    <button type="button" @click="open = false" class="text-sm text-slate-600 px-5 py-2.5">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>
