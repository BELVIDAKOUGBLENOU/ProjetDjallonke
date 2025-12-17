<div class="flex flex-col gap-y-3">

    <div>
        <x-input-label for="district_id" :value="__('District')" isRequired="true" />
        {{ html()->select('district_id', $districts->pluck('name', 'id')->toArray(), old('district_id', $subDistrict?->district_id))->class('mt-1 block w-full select2')->required() }}
        <x-input-error class="mt-2" :messages="$errors->get('district_id')" />
    </div>
    <div>
        <x-input-label for="name" :value="__('Name')" isRequired="true" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $subDistrict?->name)"
            autocomplete="name" placeholder="Name" />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>


</div>
