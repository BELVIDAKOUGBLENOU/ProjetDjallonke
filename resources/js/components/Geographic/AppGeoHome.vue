<template>
    <div>
        <h1 class="text-lg font-semibold tracking-tight theme-title !mt-0">Données Géographiques</h1>
        <p class="mb-2 text-sm theme-muted-text">Gérez les différentes entités géographiques de la plateforme.</p>
    </div>
    <!-- Search / Filters -->
    <div class="theme-surface backdrop-blur-sm border border-gray-200 rounded-xl p-4 shadow-sm mb-4">
        <div class="flex gap-3">
            <div class="w-[300px] relative">
                <label for="q" class="sr-only">Recherche</label>
                <input v-model="searchQuery" type="text" id="q" placeholder="Rechercher ..."
                    class="w-full rounded-lg border-gray-200 bg-white/70 focus:bg-white px-4 py-2.5 shadow-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm" />
                <button v-if="searchQuery" @click="searchQuery = ''" type="button"
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                    title="Effacer">
                    <span class="ti ti-x"></span>
                </button>
            </div>
            <div class="gap-2">
                <button @click="searchQuery = ''" v-if="searchQuery"
                    class="inline-flex items-center rounded-md px-3 py-2.5 text-sm font-medium bg-slate-200 hover:bg-slate-300 text-gray-800">
                    Réinitialiser
                </button>
            </div>
            <div class="flex-1"></div>

            <a :href="route('countries.index')"
                class="inline-flex items-center gap-2 rounded-md px-4 py-1 text-sm font-medium text-white bg-slate-600 focus:outline-none focus:ring-2">
                <span class="ti ti-edit text-base"></span>
                Activer d'autres Pays
            </a>
        </div>
    </div>
    <div class="h-[calc(100vh-10rem)] grid grid-cols-3 gap-4">
        <!-- Column 1: TreeView -->
        <div class="col-span-1 bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden flex flex-col">
            <GeoTreeView :search-query="searchQuery" />
        </div>

        <!-- Columns 2-3: Viewer -->
        <div class="col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden flex flex-col">
            <EntityViewer v-if="store.selectedEntity" @create-child="openCreateModal" @edit="openEditModal" />
            <div v-else class="flex items-center justify-center h-full text-gray-500">
                Sélectionnez un élément dans l'arborescence pour voir les détails.
            </div>
        </div>
    </div>

    <EntityFormModal v-if="showModal" :show="showModal" :type="modalType" :entity="modalEntity"
        :parent-id="modalParentId" @close="showModal = false" @saved="onSaved" />
</template>

<script setup>
import { ref } from 'vue';
import GeoTreeView from './GeoTreeView.vue';
import EntityViewer from './EntityViewer.vue';
import EntityFormModal from './EntityFormModal.vue';
import { useGeoStore } from '../../stores/geoStore';

const store = useGeoStore();
const searchQuery = ref('');

const showModal = ref(false);
const modalType = ref('country');
const modalEntity = ref(null);
const modalParentId = ref(null);

const openCreateModal = (type, parentId = null) => {
    modalType.value = type;
    modalEntity.value = null;
    modalParentId.value = parentId;
    showModal.value = true;
};

const openEditModal = (entity) => {
    modalType.value = entity.type;
    modalEntity.value = entity;
    modalParentId.value = null;
    showModal.value = true;
};

const onSaved = () => {
    showModal.value = false;
    // Refresh logic is handled in store or components, but we might need to trigger something.
    // The EntityFormModal logic I wrote previously refreshed the cache.
    // Let's ensure the store is updated.
    // If we edited the current entity, we might want to update the selectedEntity in store if it changed.
    // But for now, the cache refresh should be enough as components watch the store.

    // If we created a new country, we need to refresh the country list.
    if (modalType.value === 'country' && !modalEntity.value) {
        store.refreshCache('country');
    } else if (modalType.value === 'country' && modalEntity.value) {
        store.refreshCache('country');
    } else {
        // For children, we need to know the parent ID to refresh.
        // If we have modalParentId, we can refresh that.
        if (modalParentId.value) {
            // Determine parent type based on child type
            if (modalType.value === 'district') store.refreshCache('district', modalParentId.value);
            if (modalType.value === 'sub_district') store.refreshCache('sub_district', modalParentId.value);
            if (modalType.value === 'village') store.refreshCache('village', modalParentId.value);
        } else if (modalEntity.value) {
            // Edit mode for child
            // We need parent ID. It's usually in the entity (country_id, district_id, etc.)
            if (modalType.value === 'district') store.refreshCache('district', modalEntity.value.country_id);
            if (modalType.value === 'sub_district') store.refreshCache('sub_district', modalEntity.value.district_id);
            if (modalType.value === 'village') store.refreshCache('village', modalEntity.value.sub_district_id);
        }
    }
};
</script>
