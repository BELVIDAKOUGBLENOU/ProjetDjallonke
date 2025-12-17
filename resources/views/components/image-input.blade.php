@props([
    'disabled' => false,
    'label' => __('Choisir une image'),
    'preview' => null, // existing image URL
    'max' => 1, // number of files (not yet multi supported)
    'accept' => 'image/*',
    'maxSizeMb' => 1, // taille maximale en Mo (par défaut 1024 Mo = 1 Go)
])

@php
    $id = $attributes->get('id') ?? 'img_' . uniqid();
@endphp

<div x-data="imageInputComponent('{{ $id }}', @js($preview))"
    @image-preview-update.window="if ($event.detail.id === '{{ $id }}') fileData.url = $event.detail.url"
    class="w-full">
    <div x-bind:class="{ 'ring-2 ring-green-500': dragging }" @dragover.prevent="dragging=true"
        @dragleave.prevent="dragging=false" @drop.prevent="onDrop($event)"
        class="relative flex flex-col items-center justify-center gap-3 border-2 border-dashed rounded-2xl p-6 transition  border-gray-300/70 hover:border-green-500/70">
        <template x-if="!fileData.url">
            <div class="flex flex-col items-center text-center gap-2">
                <div class="w-14 h-14 rounded-xl bg-green-50 flex items-center justify-center text-green-600">
                    <span class="ti ti-photo text-2xl"></span>
                </div>
                <div class="space-y-0.5">
                    <p class="text-sm font-medium text-gray-700">{{ $label }}</p>
                    <p class="text-[11px] text-gray-500">PNG / JPG / WEBP • &lt; {{ $maxSizeMb }} Mo</p>
                </div>
                <div class="flex items-center gap-2 mt-1">
                    <label for="{{ $id }}"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-slate-900 text-white text-xs font-medium cursor-pointer hover:bg-slate-800">
                        <span class="ti ti-upload text-sm"></span>
                        {{ __('Parcourir') }}
                    </label>
                    <span class="text-xs text-gray-400">{{ __('ou glisser-déposer') }}</span>
                </div>
            </div>
        </template>
        <template x-if="fileData.url">
            <div class="relative w-full max-w-[240px]">
                <img :src="fileData.url" alt="preview"
                    class="rounded-xl w-full h-48 object-cover shadow-sm ring-1 ring-gray-200/70" />
                <button type="button" @click="clear()"
                    class="absolute top-2 right-2 w-8 h-8 rounded-full bg-white/90 backdrop-blur flex items-center justify-center text-gray-600 hover:text-red-600 hover:bg-white shadow-md">
                    <span class="ti ti-x text-base"></span>
                </button>
            </div>
        </template>
        <input id="{{ $id }}" type="file" name="{{ $attributes->get('name') }}"
            @disabled($disabled) accept="{{ $accept }}" class="sr-only" @change="onFileChange($event)" />
    </div>
    @error($attributes->get('name'))
        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>

<script>
    document.addEventListener('alpine:init', () => {
        window.imageInputComponent = (id, existingUrl) => ({
            dragging: false,
            fileData: {
                url: existingUrl
            },
            maxBytes: {{ (int) $maxSizeMb }} * 1024 * 1024,
            error(msg) {
                if (window.Swal) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: msg,
                        confirmButtonColor: '#0f766e'
                    });
                } else {
                    alert(msg);
                }
            },
            onFileChange(e) {
                const file = e.target.files[0];
                if (!file) return this.clear();
                if (!file.type.startsWith('image/')) {
                    this.error('Format non supporté (image requise)');
                    return this.clear();
                }
                if (file.size > this.maxBytes) {
                    const limitMb = (this.maxBytes / 1024 / 1024).toFixed(0);
                    this.error('Image trop lourde (> ' + limitMb + ' Mo)');
                    return this.clear();
                }
                this.fileData.url = URL.createObjectURL(file);
            },
            onDrop(e) {
                this.dragging = false;
                const file = e.dataTransfer.files[0];
                if (file) {
                    const input = document.getElementById(id);
                    input.files = e.dataTransfer.files; // assign dropped
                    input.dispatchEvent(new Event('change'));
                }
            },
            clear() {
                const input = document.getElementById(id);
                input.value = '';
                this.fileData.url = null;
            }
        })
    });
</script>
