<textarea
    id="{{ $id ?? $name }}"
    name="{{ $name }}"
    class="{{ $class ?? 'border-gray-500 rounded-md' }}"
    autocomplete="{{ $autocomplete ?? '' }}"
    placeholder="{{ $placeholder ?? '' }}"
>{{ $slot ?? '' }}</textarea>
