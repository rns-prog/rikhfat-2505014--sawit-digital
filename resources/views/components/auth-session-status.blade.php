@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'text-sm font-medium text-[var(--green)]']) }}>
        {{ $status }}
    </div>
@endif
