@props(['value', 'isRequired', 'required'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-gray-700 dark:text-gray-500 mb-2']) }}>
    {{ $value ?? $slot }}
    @if ($isRequired ?? ($required ?? false))
        <span class="text-red-500">*</span>
    @else
        <span class="text-gray-400 ms-1">
            (facultatif)
        </span>
    @endif
</label>
