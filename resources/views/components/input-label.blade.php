@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-zinc-950']) }}>
    {{ $value ?? $slot }}
</label>
