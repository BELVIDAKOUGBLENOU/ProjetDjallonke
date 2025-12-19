<template>
    <div class="flex flex-col h-full">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <div>
                <h1 class="text-sm font-semibold tracking-tight theme-title !mt-0">Arborescence Géographique</h1>
                <p class="text-xs theme-muted-text">Cliquez sur un élément pour voir les détails.</p>
            </div>
            <div v-if="store.loading" class="text-green-600" title="Chargement en cours...">
                <i class="ti ti-loader animate-spin text-lg"></i>
            </div>
        </div>

        <!-- Tree -->
        <div class="flex-1 overflow-y-auto p-2">
            <div v-if="initialLoading" class="text-center py-4 text-gray-500">
                <i class="ti ti-loader animate-spin text-2xl mb-2"></i>
                <p>Chargement des données...</p>
            </div>

            <ul v-else class="space-y-1">
                <li v-for="country in filteredCountries" :key="country.id">
                    <div class="flex items-center gap-2 px-2 py-1 rounded cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700"
                        :class="{ 'bg-green-50 dark:bg-green-900/20': isSelected('country', country.id) }"
                        @click="selectNode('country', country)">
                        <button @click.stop="toggleNode('country', country.id)"
                            class="p-1 text-gray-500 hover:text-gray-700">
                            <i
                                :class="isExpanded('country', country.id) ? 'ti ti-chevron-down' : 'ti ti-chevron-right'"></i>
                        </button>
                        <span class="text-sm font-medium truncate flex items-center">
                            <img :src="`https://flagsapi.com/${country.code_iso}/flat/24.png`" alt=""
                                class="rounded-sm inline-block mr-2" />
                            {{ country.emoji }} {{ country.name }}
                        </span>
                    </div>

                    <!-- Districts -->
                    <ul v-if="isExpanded('country', country.id)"
                        class="pl-6 mt-1 space-y-1 border-l border-gray-200 dark:border-gray-700 ml-2">
                        <li v-for="district in getFilteredDistricts(country.id)" :key="district.id">
                            <div class="flex items-center gap-2 px-2 py-1 rounded cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700"
                                :class="{ 'bg-green-50 dark:bg-green-900/20': isSelected('district', district.id) }"
                                @click="selectNode('district', district)">
                                <button @click.stop="toggleNode('district', district.id)"
                                    class="p-1 text-gray-500 hover:text-gray-700">
                                    <i
                                        :class="isExpanded('district', district.id) ? 'ti ti-chevron-down' : 'ti ti-chevron-right'"></i>
                                </button>
                                <i class="ti ti-building-community text-gray-400"></i>
                                <span class="text-sm truncate">{{ district.name }}</span>
                            </div>

                            <!-- SubDistricts -->
                            <ul v-if="isExpanded('district', district.id)"
                                class="pl-6 mt-1 space-y-1 border-l border-gray-200 dark:border-gray-700 ml-2">
                                <li v-for="subDistrict in getFilteredSubDistricts(district.id)" :key="subDistrict.id">
                                    <div class="flex items-center gap-2 px-2 py-1 rounded cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700"
                                        :class="{ 'bg-green-50 dark:bg-green-900/20': isSelected('sub_district', subDistrict.id) }"
                                        @click="selectNode('sub_district', subDistrict)">
                                        <button @click.stop="toggleNode('sub_district', subDistrict.id)"
                                            class="p-1 text-gray-500 hover:text-gray-700">
                                            <i
                                                :class="isExpanded('sub_district', subDistrict.id) ? 'ti ti-chevron-down' : 'ti ti-chevron-right'"></i>
                                        </button>
                                        <i class="ti ti-building-cottage text-gray-400"></i>
                                        <span class="text-sm truncate">{{ subDistrict.name }}</span>
                                    </div>

                                    <!-- Villages -->
                                    <ul v-if="isExpanded('sub_district', subDistrict.id)"
                                        class="pl-6 mt-1 space-y-1 border-l border-gray-200 dark:border-gray-700 ml-2">
                                        <li v-for="village in getFilteredVillages(subDistrict.id)" :key="village.id">
                                            <div class="flex items-center gap-2 px-2 py-1 rounded cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700"
                                                :class="{ 'bg-green-50 dark:bg-green-900/20': isSelected('village', village.id) }"
                                                @click="selectNode('village', village)">
                                                <span class="w-4"></span> <!-- Spacer for alignment -->
                                                <i class="ti ti-home-2 text-gray-400"></i>
                                                <span class="text-sm truncate">{{ village.name }}</span>
                                            </div>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted, watch } from 'vue';
import { useGeoStore } from '../../stores/geoStore';

const props = defineProps({
    searchQuery: {
        type: String,
        default: ''
    }
});

const store = useGeoStore();

const initialLoading = computed(() => store.loading && !store.cache.countries);

onMounted(() => {
    store.fetchCountries();
});

const matchesQuery = (name) => {
    if (!props.searchQuery) return true;
    return name.toLowerCase().includes(props.searchQuery.toLowerCase());
};

const hasMatchingDescendant = (type, id) => {
    if (!props.searchQuery) return false;

    if (type === 'country') {
        const districts = store.cache.districts[id] || [];
        return districts.some(d => matchesQuery(d.name) || hasMatchingDescendant('district', d.id));
    }
    if (type === 'district') {
        const subDistricts = store.cache.subDistricts[id] || [];
        return subDistricts.some(sd => matchesQuery(sd.name) || hasMatchingDescendant('sub_district', sd.id));
    }
    if (type === 'sub_district') {
        const villages = store.cache.villages[id] || [];
        return villages.some(v => matchesQuery(v.name));
    }
    return false;
};

const filteredCountries = computed(() => {
    const countries = store.cache.countries || [];
    if (!props.searchQuery) return countries;

    return countries.filter(c => matchesQuery(c.name) || hasMatchingDescendant('country', c.id));
});

const getFilteredDistricts = (countryId) => {
    const districts = store.cache.districts[countryId] || [];
    if (!props.searchQuery) return districts;
    return districts.filter(d => matchesQuery(d.name) || hasMatchingDescendant('district', d.id));
};

const getFilteredSubDistricts = (districtId) => {
    const subDistricts = store.cache.subDistricts[districtId] || [];
    if (!props.searchQuery) return subDistricts;
    return subDistricts.filter(sd => matchesQuery(sd.name) || hasMatchingDescendant('sub_district', sd.id));
};

const getFilteredVillages = (subDistrictId) => {
    const villages = store.cache.villages[subDistrictId] || [];
    if (!props.searchQuery) return villages;
    return villages.filter(v => matchesQuery(v.name));
};

// Auto-expand nodes with matching children
watch(() => props.searchQuery, (newVal) => {
    if (!newVal) return;

    const countries = store.cache.countries || [];
    countries.forEach(c => {
        if (hasMatchingDescendant('country', c.id)) {
            store.expandedNodes.add(`country-${c.id}`);

            const districts = store.cache.districts[c.id] || [];
            districts.forEach(d => {
                if (hasMatchingDescendant('district', d.id)) {
                    store.expandedNodes.add(`district-${d.id}`);

                    const subDistricts = store.cache.subDistricts[d.id] || [];
                    subDistricts.forEach(sd => {
                        if (hasMatchingDescendant('sub_district', sd.id)) {
                            store.expandedNodes.add(`sub_district-${sd.id}`);
                        }
                    });
                }
            });
        }
    });
});

const isExpanded = (type, id) => store.expandedNodes.has(`${type}-${id}`);
const isSelected = (type, id) => store.selectedEntity?.type === type && store.selectedEntity?.id === id;

const toggleNode = (type, id) => store.toggleNode(type, id);
const selectNode = (type, entity) => store.selectEntity(type, entity);
</script>
