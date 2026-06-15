@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-base text-black font-semibold mb-2 sm:mb-4']) }}>
    {{ $value ?? $slot }}
</label>
