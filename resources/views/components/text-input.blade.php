@props(['disabled' => false])

<input @disabled($disabled)
    {{ $attributes->merge(['class' => 'placeholder-gray-300 border-gray-300 dark:border-gray-700 dark:bg-white a dark:autofilled dark:text-gray-300 focus:border-teal-500 dark:focus:border-teal-600 focus:ring-teal-500 dark:focus:ring-teal-600 rounded-md shadow-sm']) }}>
