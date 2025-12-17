<div class="flex flex-col gap-y-3">

    <div>
        <x-input-label for="sub_district_id" :value="__('Sub District')" isRequired="true" />
        {{ html()->select('sub_district_id', $subDistricts->pluck('name', 'id')->toArray(), old('sub_district_id', $village?->sub_district_id))->class('mt-1 block w-full select2')->required() }}
        <x-input-error class="mt-2" :messages="$errors->get('sub_district_id')" />
    </div>
    <div>
        <x-input-label for="name" :value="__('Name')" isRequired="true" />
        <x-text-input id="name" name="name" type="text" isRequired="true" class="mt-1 block w-full"
            :value="old('name', $village?->name)" autocomplete="name" placeholder="Name" />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>
    <div>
        <x-input-label for="local_code" isRequired="true" :value="__('Local Code')" />
        <x-text-input id="local_code" name="local_code" type="text" isRequired="true" class="mt-1 block w-full"
            :value="old('local_code', $village?->local_code)" autocomplete="local_code" placeholder="Local Code" />
        <x-input-error class="mt-2" :messages="$errors->get('local_code')" />
    </div>


</div>
