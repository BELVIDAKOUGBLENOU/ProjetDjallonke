import { defineStore } from "pinia";
import axios from "axios";
import { ref } from "vue";

export const useGeoStore = defineStore("geo", () => {
    const selectedEntity = ref(null); // { type: 'country', ...entityData }
    const expandedNodes = ref(new Set());
    const cache = ref({
        countries: null,
        districts: {}, // by country_id
        subDistricts: {}, // by district_id
        villages: {}, // by sub_district_id
    });
    const loading = ref(false);

    // Actions
    const fetchCountries = async () => {
        if (cache.value.countries) return cache.value.countries;
        loading.value = true;
        try {
            const response = await axios.get(route("api.geo.countries.index"));
            cache.value.countries = response.data;
            return response.data;
        } finally {
            loading.value = false;
        }
    };

    const fetchDistricts = async (countryId) => {
        if (cache.value.districts[countryId])
            return cache.value.districts[countryId];
        loading.value = true;
        try {
            const response = await axios.get(
                route("api.geo.countries.districts", { country: countryId })
            );
            cache.value.districts[countryId] = response.data;
            return response.data;
        } finally {
            loading.value = false;
        }
    };

    const fetchSubDistricts = async (districtId) => {
        if (cache.value.subDistricts[districtId])
            return cache.value.subDistricts[districtId];
        loading.value = true;
        try {
            const response = await axios.get(
                route("api.geo.districts.sub-districts", {
                    district: districtId,
                })
            );
            cache.value.subDistricts[districtId] = response.data;
            return response.data;
        } finally {
            loading.value = false;
        }
    };

    const fetchVillages = async (subDistrictId) => {
        if (cache.value.villages[subDistrictId])
            return cache.value.villages[subDistrictId];
        loading.value = true;
        try {
            const response = await axios.get(
                route("api.geo.sub-districts.villages", {
                    subDistrict: subDistrictId,
                })
            );
            cache.value.villages[subDistrictId] = response.data;
            return response.data;
        } finally {
            loading.value = false;
        }
    };

    const selectEntity = (type, entity) => {
        selectedEntity.value = { type, ...entity };
    };

    const toggleNode = async (type, id) => {
        const key = `${type}-${id}`;
        if (expandedNodes.value.has(key)) {
            expandedNodes.value.delete(key);
        } else {
            expandedNodes.value.add(key);
            // Load children if needed
            if (type === "country") await fetchDistricts(id);
            if (type === "district") await fetchSubDistricts(id);
            if (type === "sub_district") await fetchVillages(id);
        }
    };

    const refreshCache = (type, parentId) => {
        if (type === "country") {
            cache.value.countries = null;
            fetchCountries();
        } else if (type === "district") {
            delete cache.value.districts[parentId];
            fetchDistricts(parentId);
        } else if (type === "sub_district") {
            delete cache.value.subDistricts[parentId];
            fetchSubDistricts(parentId);
        } else if (type === "village") {
            delete cache.value.villages[parentId];
            fetchVillages(parentId);
        }
    };

    return {
        selectedEntity,
        expandedNodes,
        cache,
        loading,
        fetchCountries,
        fetchDistricts,
        fetchSubDistricts,
        fetchVillages,
        selectEntity,
        toggleNode,
        refreshCache,
    };
});
