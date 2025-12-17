<div class="flex flex-col gap-y-3">

    <div>
        <x-input-label for="name" :value="__('Nom')" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user?->name)"
            autocomplete="name" placeholder="Nom" />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>
    <div>
        <x-input-label for="email" :value="__('Email')" />
        <x-text-input id="email" name="email" type="text" class="mt-1 block w-full" :value="old('email', $user?->email)"
            autocomplete="email" placeholder="Email" />
        <x-input-error class="mt-2" :messages="$errors->get('email')" />
    </div>

    <div class="mt-4">
        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Rôles</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2">
            @foreach ($roles as $role)
                <div class="flex items-center">
                    <input id="role_{{ $role->id }}" name="roles[]" value="{{ $role->name }}" type="checkbox"
                        @checked(in_array($role->name, old('roles', $user->roles->pluck('name')->toArray())))
                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                    <label for="role_{{ $role->id }}" class="ml-2 block text-sm text-gray-900 dark:text-gray-100">
                        {{ $role->name }}
                    </label>
                </div>
            @endforeach
        </div>
    </div>

    <div class="mt-4">
        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Permissions directes</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2">
            @foreach ($permissions as $permission)
                <div class="flex items-center">
                    <input id="permission_{{ $permission->id }}" name="permissions[]" value="{{ $permission->name }}"
                        type="checkbox" @checked(in_array($permission->name, old('permissions', $user->getDirectPermissions()->pluck('name')->toArray())))
                        class="h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-600">
                    <label for="permission_{{ $permission->id }}"
                        class="ml-2 block text-sm text-gray-900 dark:text-gray-100">
                        {{ $permission->name }}
                    </label>
                </div>
            @endforeach
        </div>
    </div>

</div>
