@props([
    'name',
    'options' => [], // [value => label]
    'value' => null,
    'label' => null,
    'inline' => false,
    'cols' => 3, // max columns on large screens
])

@php
    $fieldId = $attributes->get('id') ?? $name . '_' . uniqid();
    $selected = old($name, $value);
    $baseWrapper = $inline
        ? 'flex flex-wrap gap-4'
        : 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-' . intval($cols) . ' gap-3';
@endphp

<div {{ $attributes->class('space-y-2') }}>
    @if ($label)
        <x-input-label :for="$fieldId" :value="$label" :isRequired="$attributes->get('isRequired')" />
    @endif
    <div class="{{ $baseWrapper }}">
        @foreach ($options as $optValue => $optLabel)
            @php $id = $name . '_' . $optValue; @endphp
            <label for="{{ $id }}" class="relative group cursor-pointer">
                <input type="radio" id="{{ $id }}" name="{{ $name }}" value="{{ $optValue }}"
                    class="peer sr-only" @checked((string) $selected === (string) $optValue) />
                <div
                    class="rounded-lg border border-gray-300 peer-checked:border-teal-600 peer-checked:ring-2 peer-checked:ring-teal-500/40 bg-white px-3 py-2 flex items-center gap-2 text-sm transition shadow-sm peer-focus-visible:outline-none peer-focus-visible:ring-2 peer-focus-visible:ring-teal-500">
                    <span
                        class="w-4 h-4 flex items-center justify-center rounded-full border border-gray-400 peer-checked:border-teal-600 peer-checked:bg-teal-600 text-white text-[10px]">
                        <span class="ti ti-check" x-show="false"></span>
                    </span>
                    <span class="font-medium text-gray-700">{{ $optLabel }}</span>
                </div>
            </label>
        @endforeach
    </div>
    @error($name)
        <p class="text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>
