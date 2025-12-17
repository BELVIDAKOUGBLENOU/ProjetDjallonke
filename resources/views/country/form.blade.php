<div class="flex flex-col gap-y-3">

    <div>
        <x-input-label for="name" :value="__('Name')" isRequired="true" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" isRequired="true"
            :value="old('name', $country?->name)" autocomplete="name" placeholder="Name" />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>
    <div>
        <x-input-label for="code_iso" :value="__('Code Iso')" isRequired="true" />
        <x-text-input id="code_iso" name="code_iso" type="text" class="mt-1 block w-full" isRequired="true"
            :value="old('code_iso', $country?->code_iso)" autocomplete="code_iso" placeholder="Code Iso" />
        <x-input-error class="mt-2" :messages="$errors->get('code_iso')" />
    </div>
    <div>
        <x-input-label for="emoji" :value="__('Emoji (Flag)')" />
        <x-text-input id="emoji" name="emoji" type="text" class="mt-1 block w-full" :value="old('emoji', $country?->emoji)"
            placeholder="Example: 🇧🇯" />
        <x-input-error class="mt-2" :messages="$errors->get('emoji')" />
    </div>

    <div class="flex items-center gap-2 mt-2">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" id="is_active" name="is_active" value="1"
            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
            {{ old('is_active', $country?->is_active ?? true) ? 'checked' : '' }}>
        <x-input-label for="is_active" :value="__('Is Active')" class="!mb-0" />
        <x-input-error class="mt-2" :messages="$errors->get('is_active')" />
    </div>

</div>
