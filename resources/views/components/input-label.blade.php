@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-[var(--text)]']) }}>
    {{ $value ?? $slot }}
</label>
