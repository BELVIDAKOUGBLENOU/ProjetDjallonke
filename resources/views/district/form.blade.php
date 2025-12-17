<div class="flex flex-col gap-y-3">

    <div>
        <x-input-label for="country_id" :value="__('Country ')" isRequired="true" />
        {{ html()->select('country_id', $countries->pluck('name', 'id')->toArray(), old('country_id', $district?->country_id))->class('mt-1 block w-full select2')->required() }}
        {{-- <x-text-input id="country_id" name="country_id" type="text" class="mt-1 block w-full" :value="old('country_id', $district?->country_id)" autocomplete="country_id" placeholder="Country Id"/> --}}
        <x-input-error class="mt-2" :messages="$errors->get('country_id')" />
    </div>
    <div>
        <x-input-label for="name" :value="__('Name')" isRequired="true" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $district?->name)"
            autocomplete="name" placeholder="Name" isRequired="true" />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>


</div>
