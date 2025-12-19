<template>
    <div class="flex flex-col h-full">
        <!-- Header -->
        <div
            class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-start bg-gray-50 dark:bg-gray-900/50">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                        {{ entityName }}
                    </h2>
                    <span
                        class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                        {{ config.label }} (ID: {{ entity.id }})
                    </span>
                </div>

            </div>
            <div class="flex gap-2">
                <button v-if="config.addLabel" @click="openCreateChildModal"
                    class="px-3 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700 shadow-sm">
                    <i class="ti ti-plus mr-1"></i>
                    {{ config.addLabel }}
                </button>
                <button @click="openEditModal"
                    class="px-3 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700 shadow-sm">
                    <i class="ti ti-edit"></i>
                </button>
                <button @click="confirmDelete"
                    class="px-3 py-2 bg-white border border-red-300 rounded-md text-sm font-medium text-red-700 hover:bg-red-50 dark:bg-gray-800 dark:border-red-900 dark:text-red-400 dark:hover:bg-red-900/20 shadow-sm">
                    <i class="ti ti-trash"></i>
                </button>
            </div>
        </div>

        <!-- Content -->
        <div class="flex-1 overflow-y-auto p-6">
            <!-- Fields -->
            <div class="grid grid-cols-2 gap-6 mb-8">
                <div v-for="field in config.fields" :key="field.name" class="theme-muted p-4 rounded-lg">
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">
                        {{ field.label }}
                    </label>
                    <div class="text-gray-900 dark:text-white font-medium">
                        <span v-if="field.type === 'checkbox'">
                            <i :class="entity[field.name] ? 'ti ti-check text-green-500' : 'ti ti-x text-red-500'"></i>
                            {{ entity[field.name] ? 'Oui' : 'Non' }}
                        </span>
                        <span v-else>{{ entity[field.name] || '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- Children List -->
            <div v-if="config.children">
                <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                    <i class="ti ti-list-tree text-gray-400"></i>
                    {{ childConfig.label }}s ({{ children.length }})
                </h3>

                <div
                    class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nom</th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="child in children" :key="child.id"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    {{ child.name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button @click="selectChild(child)"
                                        class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">
                                        Voir
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="children.length === 0">
                                <td colspan="2" class="px-6 py-8 text-center text-gray-500 text-sm">
                                    Aucun élément trouvé.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <EntityFormModal v-if="showModal" :show="showModal" :type="modalType" :entity="modalEntity"
            :parent-id="modalParentId" @close="showModal = false" @saved="onSaved" />
    </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { useGeoStore } from '../../stores/geoStore';
import { ENTITY_CONFIG } from '../../config/entityConfig';
import EntityFormModal from './EntityFormModal.vue';
import Swal from 'sweetalert2';
import axios from 'axios';

const store = useGeoStore();
const entity = computed(() => store.selectedEntity);
const config = computed(() => ENTITY_CONFIG[entity.value.type]);
const childConfig = computed(() => config.value.childType ? ENTITY_CONFIG[config.value.childType] : null);

const entityName = computed(() => {
    if (entity.value.type === 'country') return `${entity.value.emoji || ''} ${entity.value.name}`;
    return entity.value.name;
});

const children = computed(() => {
    if (!config.value.children) return [];
    const cacheKey = config.value.children === 'districts' ? 'districts' :
        config.value.children === 'sub_districts' ? 'subDistricts' :
            config.value.children === 'villages' ? 'villages' : null;

    if (!cacheKey) return [];
    return store.cache[cacheKey][entity.value.id] || [];
});

// Load children when entity changes
watch(() => entity.value, async (newVal) => {
    if (newVal && config.value.children) {
        if (newVal.type === 'country') await store.fetchDistricts(newVal.id);
        if (newVal.type === 'district') await store.fetchSubDistricts(newVal.id);
        if (newVal.type === 'sub_district') await store.fetchVillages(newVal.id);
    }
}, { immediate: true });

const showModal = ref(false);
const modalType = ref(null);
const modalEntity = ref(null);
const modalParentId = ref(null);

const openCreateChildModal = () => {
    modalType.value = config.value.childType;
    modalEntity.value = null;
    modalParentId.value = entity.value.id;
    showModal.value = true;
};

const openEditModal = () => {
    modalType.value = entity.value.type;
    modalEntity.value = entity.value;
    modalParentId.value = null; // Not needed for edit
    showModal.value = true;
};

const selectChild = (child) => {
    store.selectEntity(config.value.childType, child);
    store.toggleNode(config.value.childType, child.id); // Expand in tree
};

const onSaved = () => {
    showModal.value = false;
    // Refresh current entity children or parent's children depending on action
    // Simple strategy: refresh cache for relevant parts
    if (modalType.value === entity.value.type) {
        // Edited self
        // We might need to refresh parent's children list to see name change in tree
        // For now, just update local state if possible or refresh parent
        // Ideally store should handle this.
        // Let's just refresh the parent of the current entity.
        // But we don't easily know the parent ID here without more logic.
        // For now, let's reload the page or implement smarter store updates.
        // The store.refreshCache is available.

        // If we edited a country, refresh countries
        if (entity.value.type === 'country') store.refreshCache('country');
        // If we edited a district, we need country_id. It's in entity.country_id usually.
        if (entity.value.type === 'district') store.refreshCache('district', entity.value.country_id);
        if (entity.value.type === 'sub_district') store.refreshCache('sub_district', entity.value.district_id);
        if (entity.value.type === 'village') store.refreshCache('village', entity.value.sub_district_id);

        // Also update the selected entity in store with new data
        // store.selectedEntity = { ...store.selectedEntity, ...newData }; // Need new data from modal
    } else {
        // Created child
        // Refresh children of current entity
        if (entity.value.type === 'country') store.refreshCache('district', entity.value.id);
        if (entity.value.type === 'district') store.refreshCache('sub_district', entity.value.id);
        if (entity.value.type === 'sub_district') store.refreshCache('village', entity.value.id);
    }
};

const confirmDelete = () => {
    if (children.value.length > 0) {
        Swal.fire({
            icon: 'error',
            title: 'Impossible de supprimer',
            text: `Cet élément contient des ${childConfig.value.label.toLowerCase()}s. Veuillez d'abord les supprimer.`,
        });
        return;
    }

    Swal.fire({
        title: 'Êtes-vous sûr ?',
        text: "Cette action est irréversible !",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Oui, supprimer !',
        cancelButtonText: 'Annuler'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const routeName = `api.geo.${config.value.apiRoute}.destroy`;
                // Route params need correct key
                const params = {};
                if (entity.value.type === 'country') params.country = entity.value.id;
                if (entity.value.type === 'district') params.district = entity.value.id;
                if (entity.value.type === 'sub_district') params.subDistrict = entity.value.id;
                if (entity.value.type === 'village') params.village = entity.value.id;

                await axios.delete(route(routeName, params));

                Swal.fire('Supprimé !', 'L\'élément a été supprimé.', 'success');

                // Refresh parent and clear selection
                // Similar logic to onSaved for parent refresh
                if (entity.value.type === 'country') store.refreshCache('country');
                if (entity.value.type === 'district') store.refreshCache('district', entity.value.country_id);
                if (entity.value.type === 'sub_district') store.refreshCache('sub_district', entity.value.district_id);
                if (entity.value.type === 'village') store.refreshCache('village', entity.value.sub_district_id);

                store.selectedEntity = null;

            } catch (error) {
                console.error(error);
                Swal.fire('Erreur', 'Une erreur est survenue lors de la suppression.', 'error');
            }
        }
    });
};
</script>
