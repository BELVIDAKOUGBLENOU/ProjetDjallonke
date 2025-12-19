<template>
    <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-black/40 backdrop-blur-md" aria-hidden="true" @click="$emit('close')"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class=" relative z-10  inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                {{ isEdit ? 'Modifier' : 'Nouveau' }} {{ config.label }}
                            </h3>
                            <div class="mt-4">
                                <form @submit.prevent="save">
                                    <div v-for="field in config.fields" :key="field.name" class="mb-4">
                                        <label :for="field.name"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            {{ field.label }} <span v-if="field.required" class="text-red-500">*</span>
                                        </label>

                                        <input v-if="field.type === 'text'" v-model="formData[field.name]"
                                            :id="field.name" type="text"
                                            class="shadow-sm focus:ring-green-500 focus:border-green-500 block w-full sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                            :required="field.required" />

                                        <div v-if="field.type === 'checkbox'" class="flex items-center">
                                            <input v-model="formData[field.name]" :id="field.name" type="checkbox"
                                                class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600" />
                                            <label :for="field.name"
                                                class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                                                {{ field.label }}
                                            </label>
                                        </div>
                                    </div>

                                    <div v-if="errors" class="mb-4 text-red-600 text-sm">
                                        <div v-for="(msgs, field) in errors" :key="field">
                                            <span v-for="msg in msgs" :key="msg" class="block">{{ msg }}</span>
                                        </div>
                                    </div>

                                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                        <button type="submit"
                                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm"
                                            :disabled="loading">
                                            {{ loading ? 'Enregistrement...' : 'Enregistrer' }}
                                        </button>
                                        <button type="button"
                                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:mt-0 sm:w-auto sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600"
                                            @click="$emit('close')">
                                            Annuler
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { ENTITY_CONFIG } from '../../config/entityConfig';
import axios from 'axios';

const props = defineProps({
    show: Boolean,
    type: String,
    entity: Object, // If provided, it's edit mode
    parentId: Number // If provided, it's create mode with parent
});

const emit = defineEmits(['close', 'saved']);

const config = computed(() => ENTITY_CONFIG[props.type]);
const isEdit = computed(() => !!props.entity);
const formData = ref({});
const loading = ref(false);
const errors = ref(null);

onMounted(() => {
    if (isEdit.value) {
        // Clone entity data to form data
        config.value.fields.forEach(field => {
            formData.value[field.name] = props.entity[field.name];
        });
        // Boolean fields might need explicit conversion if coming as 0/1 from DB
        if (props.type === 'country') {
            formData.value.is_active = !!props.entity.is_active;
        }
    } else {
        // Initialize defaults
        config.value.fields.forEach(field => {
            if (field.type === 'checkbox') formData.value[field.name] = true;
            else formData.value[field.name] = '';
        });
    }
});

const save = async () => {
    loading.value = true;
    errors.value = null;

    try {
        const data = { ...formData.value };

        // Add parent ID if creating
        if (!isEdit.value && props.parentId) {
            if (props.type === 'district') data.country_id = props.parentId;
            if (props.type === 'sub_district') data.district_id = props.parentId;
            if (props.type === 'village') data.sub_district_id = props.parentId;
        }

        let routeName = `api.geo.${config.value.apiRoute}.`;
        let params = {};

        if (isEdit.value) {
            routeName += 'update';
            // Need correct param key
            if (props.type === 'country') params.country = props.entity.id;
            if (props.type === 'district') params.district = props.entity.id;
            if (props.type === 'sub_district') params.subDistrict = props.entity.id;
            if (props.type === 'village') params.village = props.entity.id;

            await axios.put(route(routeName, params), data);
        } else {
            routeName += 'store';
            await axios.post(route(routeName), data);
        }

        emit('saved');
    } catch (error) {
        if (error.response && error.response.status === 422) {
            errors.value = error.response.data.errors;
        } else {
            console.error(error);
            alert('Une erreur est survenue.');
        }
    } finally {
        loading.value = false;
    }
};
</script>
