export const ENTITY_CONFIG = {
    country: {
        label: "Pays",
        children: "districts",
        addLabel: "Nouveau district",
        fields: [
            { name: "name", label: "Nom", type: "text", required: true },
            {
                name: "code_iso",
                label: "Code ISO",
                type: "text",
                required: true,
            },
            { name: "emoji", label: "Emoji", type: "text" },
            { name: "is_active", label: "Actif", type: "checkbox" },
        ],
        apiRoute: "countries",
        childType: "district",
    },
    district: {
        label: "District",
        children: "sub_districts",
        addLabel: "Nouveau sous-district",
        fields: [{ name: "name", label: "Nom", type: "text", required: true }],
        apiRoute: "districts",
        childType: "sub_district",
    },
    sub_district: {
        label: "Sous-district",
        children: "villages",
        addLabel: "Nouveau village",
        fields: [{ name: "name", label: "Nom", type: "text", required: true }],
        apiRoute: "sub-districts",
        childType: "village",
    },
    village: {
        label: "Village",
        children: null,
        addLabel: null,
        fields: [
            { name: "name", label: "Nom", type: "text", required: true },
            { name: "local_code", label: "Code Local", type: "text" },
        ],
        apiRoute: "villages",
        childType: null,
    },
};
