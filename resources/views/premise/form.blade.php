<div class="flex flex-col gap-y-3">

    <div>
        <x-input-label for="village_id" :value="__('Village')" isRequired="true" />
        <select name="village_id" id="village_id" required
            class="select2 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            <option value="">Select Village</option>
            @foreach ($villages as $village)
                <option value="{{ $village->id }}"
                    {{ old('village_id', $premise?->village_id) == $village->id ? 'selected' : '' }}>
                    {{ $village->name }}
                </option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('village_id')" />
    </div>

    <div>
        <x-input-label for="code" :value="__('Code')" isRequired="true" />
        <x-text-input id="code" name="code" type="text" class="mt-1 block w-full" :value="old('code', $premise?->code)"
            autocomplete="code" placeholder="Code" required />
        <x-input-error class="mt-2" :messages="$errors->get('code')" />
    </div>
    <div>
        <x-input-label for="address" :value="__('Address')" />
        <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" :value="old('address', $premise?->address)"
            autocomplete="address" placeholder="Address" />
        <x-input-error class="mt-2" :messages="$errors->get('address')" />
    </div>
    <div>
        <x-input-label for="gps_coordinates" :value="__('Gps Coordinates')" />
        <x-text-input id="gps_coordinates" name="gps_coordinates" type="text" class="mt-1 block w-full"
            :value="old('gps_coordinates', $premise?->gps_coordinates)" autocomplete="gps_coordinates" placeholder="Gps Coordinates" />
        <x-input-error class="mt-2" :messages="$errors->get('gps_coordinates')" />
    </div>
    <div>
        <x-input-label for="type" :value="__('Type')" isRequired="true" />
        <select name="type" id="type" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            <option value="">{{ __('Select Type') }}</option>
            @foreach (['FARM', 'MARKET', 'SLAUGHTERHOUSE', 'PASTURE', 'TRANSPORT'] as $type)
                <option value="{{ $type }}" {{ old('type', $premise?->type) == $type ? 'selected' : '' }}>
                    {{ $type }}
                </option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('type')" />
    </div>
    <div>
        <x-input-label for="health_status" :value="__('Health Status')" />
        <x-text-input id="health_status" name="health_status" type="text" class="mt-1 block w-full"
            :value="old('health_status', $premise?->health_status)" autocomplete="health_status" placeholder="Health Status" />
        <x-input-error class="mt-2" :messages="$errors->get('health_status')" />
    </div>


</div>
