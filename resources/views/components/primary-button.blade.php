<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center rounded-full border border-transparent bg-[#315c3d] px-5 py-3 text-sm font-bold text-white transition hover:bg-[#244a30] focus:outline-none focus:ring-2 focus:ring-[#527652] focus:ring-offset-2']) }}>
    {{ $slot }}
</button>
