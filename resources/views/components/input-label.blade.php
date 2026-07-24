@props(['value'])

<label {{ $attributes->merge(['class' => 'form-label', 'style' => 'display:block; font-size:0.85rem; font-weight:700; color:var(--text-secondary); margin-bottom:6px;']) }}>
    {{ $value ?? $slot }}
</label>
