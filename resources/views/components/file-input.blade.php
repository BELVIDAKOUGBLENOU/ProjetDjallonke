@props([
    'disabled' => false,
    'label' => __('Choisir un fichier'),
    'accept' => null,
    'showName' => true,
])

@php
    $id = $attributes->get('id') ?? 'file_' . uniqid();
@endphp

<div x-data="{ fileName: '' }" class="w-full">
    <div
        class="flex items-stretch overflow-hidden rounded-lg border border-gray-300 bg-white dark:bg-gray-900 focus-within:ring-2 focus-within:ring-teal-500 transition">
        <label for="{{ $id }}"
            class="cursor-pointer select-none bg-teal-900 text-white px-4 py-2 text-sm font-medium flex items-center gap-2 hover:bg-slate-800 focus:outline-none">
            <span class="ti ti-upload text-base"></span>
            <span>{{ $label }}</span>
        </label>
        <div class="flex-1 px-3 py-2 text-sm text-gray-600 dark:text-gray-300 truncate"
            x-text="fileName || '{{ __('Aucun fichier choisi') }}'"></div>
        <button type="button" x-show="fileName"
            @click="fileName=''; document.getElementById('{{ $id }}').value='';"
            class="px-3 text-xs text-gray-400 hover:text-red-500 focus:outline-none">
            <span class="ti ti-x"></span>
        </button>
    </div>
    <input id="{{ $id }}" @disabled($disabled) type="file"
        x-on:change="fileName = $el.files.length ? $el.files[0].name : ''" {{ $accept ? "accept=$accept" : '' }}
        {{ $attributes->except(['class', 'id']) }} class="sr-only" />
    @if ($attributes->get('name'))
        @error($attributes->get('name'))
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    @endif
</div>
