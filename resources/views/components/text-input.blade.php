@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-xl border-[#dfe2d7] bg-[#fffefa] px-4 py-3 text-sm text-[#263a2f] shadow-sm focus:border-[#527652] focus:ring-[#527652]']) }}>
