<div class="space-y-6" x-data="{ selectAll: false, toggleAll() { this.selectAll = !this.selectAll; document.querySelectorAll('.perm-checkbox').forEach(cb => { if (!cb.disabled) cb.checked = this.selectAll; }); } }">
    <div class="flex items-center justify-between">
        <h3 class="font-bold text-slate-900">الصلاحيات</h3>
        <button type="button" @click="toggleAll()" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
            تحديد / إلغاء الكل
        </button>
    </div>

    @foreach($permissionGroups as $groupKey => $group)
    <div class="border border-slate-200 rounded-lg overflow-hidden">
        <div class="bg-slate-50 px-4 py-2.5 border-b border-slate-200">
            <h4 class="font-semibold text-slate-800 text-sm">{{ $group['label'] }}</h4>
        </div>
        <div class="p-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
            @foreach($group['permissions'] as $permName => $permLabel)
            <label class="flex items-start gap-2 text-sm cursor-pointer hover:bg-slate-50 p-2 rounded-lg transition">
                <input type="checkbox" name="permissions[]" value="{{ $permName }}"
                       class="perm-checkbox mt-0.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                       {{ in_array($permName, $assignedPermissions) ? 'checked' : '' }}>
                <span>
                    <span class="font-medium text-slate-800">{{ $permLabel }}</span>
                    <span class="block text-xs text-slate-400 font-mono">{{ $permName }}</span>
                </span>
            </label>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
