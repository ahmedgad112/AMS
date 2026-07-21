@props(['status'])

@php
    $config = match($status) {
        'present' => ['label' => 'حاضر', 'class' => 'bg-emerald-100 text-emerald-800'],
        'absent' => ['label' => 'غائب', 'class' => 'bg-red-100 text-red-800'],
        'late' => ['label' => 'متأخر', 'class' => 'bg-amber-100 text-amber-800'],
        'excused' => ['label' => 'غياب بعذر', 'class' => 'bg-blue-100 text-blue-800'],
        'active' => ['label' => 'نشط', 'class' => 'bg-emerald-100 text-emerald-800'],
        'completed' => ['label' => 'مكتمل', 'class' => 'bg-slate-100 text-slate-800'],
        'suspended' => ['label' => 'موقوف', 'class' => 'bg-red-100 text-red-800'],
        'open' => ['label' => 'مفتوحة', 'class' => 'bg-emerald-100 text-emerald-800'],
        'closed' => ['label' => 'مغلقة', 'class' => 'bg-slate-100 text-slate-800'],
        default => ['label' => $status, 'class' => 'bg-slate-100 text-slate-800'],
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {$config['class']}"]) }}>
    {{ $config['label'] }}
</span>
