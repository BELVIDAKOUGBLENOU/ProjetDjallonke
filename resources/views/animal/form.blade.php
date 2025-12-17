<div class="flex flex-col gap-y-3">

    <div>
        <x-input-label for="premises_id" :value="__('Premise')" isRequired="true" />
        <select name="premises_id" id="premises_id"
            class="select2 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            required>
            <option value="">{{ __('Select Premise') }}</option>
            @foreach ($premises as $premise)
                <option value="{{ $premise->id }}"
                    {{ old('premises_id', $animal?->premises_id) == $premise->id ? 'selected' : '' }}>
                    {{ $premise->code }} - {{ $premise->type }}
                </option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('premises_id')" />
    </div>

    <div>
        <x-input-label for="species" :value="__('Species')" isRequired="true" />
        <select name="species" id="species"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            required>
            <option value="">{{ __('Select Species') }}</option>
            @foreach (['OVINE', 'CAPRINE'] as $species)
                <option value="{{ $species }}"
                    {{ old('species', $animal?->species) == $species ? 'selected' : '' }}>
                    {{ $species }}
                </option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('species')" />
    </div>

    <div>
        <x-input-label for="sex" :value="__('Sex')" isRequired="true" />
        <select name="sex" id="sex"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            required>
            <option value="">{{ __('Select Sex') }}</option>
            @foreach (['M', 'F'] as $sex)
                <option value="{{ $sex }}" {{ old('sex', $animal?->sex) == $sex ? 'selected' : '' }}>
                    {{ $sex }}
                </option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('sex')" />
    </div>

    <div>
        <x-input-label for="birth_date" :value="__('Birth Date')" />
        <x-text-input id="birth_date" name="birth_date" type="date" class="mt-1 block w-full" :value="old('birth_date', $animal?->birth_date)"
            autocomplete="birth_date" placeholder="Birth Date" />
        <x-input-error class="mt-2" :messages="$errors->get('birth_date')" />
    </div>

    <div>
        <x-input-label for="life_status" :value="__('Life Status')" isRequired="true" />
        <select name="life_status" id="life_status"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            required>
            <option value="">{{ __('Select Life Status') }}</option>
            @foreach (['ALIVE', 'DEAD', 'SOLD'] as $status)
                <option value="{{ $status }}"
                    {{ old('life_status', $animal?->life_status) == $status ? 'selected' : '' }}>
                    {{ $status }}
                </option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('life_status')" />
    </div>

</div>
