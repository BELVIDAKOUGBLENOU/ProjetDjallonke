@props([
    'disabled' => false,
    'label' => __('Choisir un PDF'),
    'preview' => null, // URL d'un PDF existant (pour édition)
    'accept' => 'application/pdf',
    'maxSizeMb' => 5, // taille maximale en Mo
])

@php
    $id = $attributes->get('id') ?? 'pdf_' . uniqid();
@endphp

<div x-data="pdfInputComponent('{{ $id }}', @js($preview))" class="w-full">
    <div x-bind:class="{ 'ring-2 ring-teal-500': dragging }" @dragover.prevent="dragging=true"
        @dragleave.prevent="dragging=false" @drop.prevent="onDrop($event)"
        class="relative flex flex-col items-center justify-center gap-3 border-2 border-dashed rounded-2xl p-6 transition bg-white dark:bg-gray-900 border-gray-300/70 hover:border-teal-500/70">
        <template x-if="!fileData.name">
            <div class="flex flex-col items-center text-center gap-2">
                <div class="w-14 h-14 rounded-xl bg-teal-50 flex items-center justify-center text-teal-600">
                    <span class="ti ti-file-type-pdf text-2xl"></span>
                </div>
                <div class="space-y-0.5">
                    <p class="text-sm font-medium text-gray-700">{{ $label }}</p>
                    <p class="text-[11px] text-gray-500">PDF • &lt; {{ $maxSizeMb }} Mo</p>
                </div>
                <div class="flex items-center gap-2 mt-1">
                    <label for="{{ $id }}"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-slate-900 text-white text-xs font-medium cursor-pointer hover:bg-slate-800">
                        <span class="ti ti-upload text-sm"></span>
                        {{ __('Parcourir') }}
                    </label>
                    <span class="text-xs text-gray-400">{{ __('ou glisser-déposer') }}</span>
                </div>
                <template x-if="fileData.url">
                    <!-- cas où un preview initial existe mais pas encore chargé (nom null) -->
                    <div class="mt-2">
                        <a :href="fileData.url" target="_blank"
                            class="text-xs text-teal-600 hover:underline flex items-center gap-1">
                            <span class="ti ti-external-link"></span> {{ __('Voir le PDF existant') }}
                        </a>
                    </div>
                </template>
            </div>
        </template>
        <template x-if="fileData.name">
            <div class="relative w-full max-w-md flex flex-col items-center gap-3">
                <div class="flex items-start gap-3 w-full">
                    <div
                        class="w-12 h-12 flex-shrink-0 rounded-lg bg-red-50 text-red-600 flex items-center justify-center">
                        <span class="ti ti-file-type-pdf text-xl"></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate" x-text="fileData.name"></p>
                        <p class="text-[11px] text-gray-500" x-text="fileData.size"></p>
                        <div class="mt-1 flex items-center gap-2 text-xs">
                            <a :href="fileData.url" target="_blank"
                                class="text-teal-600 hover:underline inline-flex items-center gap-1">
                                <span class="ti ti-eye"></span> {{ __('Ouvrir') }}
                            </a>
                            <button type="button" @click="clear()"
                                class="text-gray-400 hover:text-red-600 inline-flex items-center gap-1">
                                <span class="ti ti-x"></span> {{ __('Retirer') }}
                            </button>
                        </div>
                    </div>
                </div>
                <div class="w-full h-64 border rounded-lg overflow-hidden bg-gray-50 dark:bg-gray-800"
                    x-show="supportsInline">
                    <iframe :src="fileData.url" class="w-full h-full" x-show="fileData.url" loading="lazy"></iframe>
                </div>
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
        window.pdfInputComponent = (id, existingUrl) => ({
            dragging: false,
            fileData: {
                url: existingUrl,
                name: null,
                size: null,
            },
            maxBytes: {{ (int) $maxSizeMb }} * 1024 * 1024,
            supportsInline: !!window.navigator && /Chrome|Firefox|Safari|Edg/.test(navigator.userAgent),
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
                if (file.type !== 'application/pdf') {
                    // certains navigateurs peuvent donner application/octet-stream; on tolère l'extension
                    if (!file.name.toLowerCase().endsWith('.pdf')) {
                        this.error('Format non supporté (PDF requis)');
                        return this.clear();
                    }
                }
                if (file.size > this.maxBytes) {
                    const limitMb = (this.maxBytes / 1024 / 1024).toFixed(0);
                    this.error('Fichier trop lourd (> ' + limitMb + ' Mo)');
                    return this.clear();
                }
                this.fileData.url = URL.createObjectURL(file);
                this.fileData.name = file.name;
                this.fileData.size = this.humanSize(file.size);
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
                this.fileData.url = existingUrl || null;
                this.fileData.name = null;
                this.fileData.size = null;
            },
            humanSize(bytes) {
                const units = ['octets', 'Ko', 'Mo', 'Go'];
                let u = 0;
                let v = bytes;
                while (v >= 1024 && u < units.length - 1) {
                    v /= 1024;
                    u++;
                }
                return v.toFixed(v >= 10 || u === 0 ? 0 : 1) + ' ' + units[u];
            }
        })
    });
</script>
