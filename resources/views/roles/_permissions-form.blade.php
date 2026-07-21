<div class="space-y-6" x-data="{
    selectAll: false,
    toggleAll() {
        this.selectAll = !this.selectAll;
        document.querySelectorAll('.perm-checkbox').forEach(cb => {
            if (!cb.disabled) cb.checked = this.selectAll;
        });
    },
    toggleRow(entityId) {
        const boxes = document.querySelectorAll(`[data-entity='${entityId}'] .perm-checkbox`);
        const allChecked = Array.from(boxes).every(cb => cb.checked);
        boxes.forEach(cb => { if (!cb.disabled) cb.checked = !allChecked; });
    }
}">
    <div class="flex items-center justify-between">
        <h3 class="font-bold text-slate-900">الصلاحيات</h3>
        <button type="button" @click="toggleAll()" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
            تحديد / إلغاء الكل
        </button>
    </div>

    @php
        $actionColumns = [
            'view' => 'عرض',
            'create' => 'إضافة',
            'edit' => 'تعديل',
            'delete' => 'حذف',
        ];
    @endphp

    @foreach($permissionGroups as $groupKey => $group)
    @php
        $entities = [];
        foreach ($group['permissions'] as $permName => $permLabel) {
            $segments = explode('-', $permName, 2);
            $action = $segments[0];
            $entity = $segments[1] ?? $permName;
            $column = array_key_exists($action, $actionColumns) ? $action : 'other';
            $entities[$entity]['items'][$column][$permName] = $permLabel;
            if ($column === 'view') {
                $entities[$entity]['label'] = preg_replace('/^عرض\s+/u', '', $permLabel);
            }
        }
        foreach ($entities as &$entity) {
            $entity['label'] ??= '—';
        }
        unset($entity);
    @endphp
    <div class="border border-slate-200 rounded-lg overflow-hidden">
        <div class="bg-slate-50 px-4 py-2.5 border-b border-slate-200">
            <h4 class="font-semibold text-slate-800 text-sm">{{ $group['label'] }}</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50/80 text-slate-600">
                    <tr>
                        <th class="text-right px-4 py-2.5 font-semibold w-40">القسم</th>
                        @foreach($actionColumns as $label)
                        <th class="text-center px-3 py-2.5 font-semibold">{{ $label }}</th>
                        @endforeach
                        <th class="text-right px-4 py-2.5 font-semibold">إجراءات أخرى</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($entities as $entityKey => $entity)
                    <tr class="hover:bg-slate-50/50" data-entity="{{ $groupKey }}-{{ $entityKey }}">
                        <td class="px-4 py-3 font-medium text-slate-800">
                            <button type="button" @click="toggleRow('{{ $groupKey }}-{{ $entityKey }}')"
                                    class="text-indigo-600 hover:text-indigo-800 text-xs ml-2">الكل</button>
                            {{ $entity['label'] }}
                        </td>
                        @foreach(array_keys($actionColumns) as $column)
                        <td class="px-3 py-3 text-center align-top">
                            @foreach($entity['items'][$column] ?? [] as $permName => $permLabel)
                            <label class="inline-flex flex-col items-center gap-1 cursor-pointer p-1 rounded hover:bg-slate-100" title="{{ $permName }}">
                                <input type="checkbox" name="permissions[]" value="{{ $permName }}"
                                       class="perm-checkbox rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                       {{ in_array($permName, $assignedPermissions) ? 'checked' : '' }}>
                                <span class="text-[11px] text-slate-500 leading-tight max-w-[5.5rem]">{{ $permLabel }}</span>
                            </label>
                            @endforeach
                        </td>
                        @endforeach
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-2">
                                @foreach($entity['items']['other'] ?? [] as $permName => $permLabel)
                                <label class="inline-flex items-center gap-1.5 cursor-pointer bg-slate-50 border border-slate-200 rounded-lg px-2 py-1 hover:bg-slate-100">
                                    <input type="checkbox" name="permissions[]" value="{{ $permName }}"
                                           class="perm-checkbox rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                           {{ in_array($permName, $assignedPermissions) ? 'checked' : '' }}>
                                    <span class="text-xs text-slate-700">{{ $permLabel }}</span>
                                </label>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endforeach
</div>
