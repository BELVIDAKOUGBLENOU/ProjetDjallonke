<div class="flex flex-col gap-y-3">
    <div>
        <x-input-label for="country_id" :value="__('Country')" isRequired="true" />
        {{ html()->select('country_id', $countries->pluck('name', 'id')->toArray(), old('country_id', $community?->country_id))->class('mt-1 block w-full select2')->required() }}
        <x-input-error class="mt-2" :messages="$errors->get('country_id')" />
    </div>

    <div>
        <x-input-label for="name" :value="__('Name')" isRequired="true" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $community?->name)"
            autocomplete="name" placeholder="Name" isRequired="true" />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>
</div>
