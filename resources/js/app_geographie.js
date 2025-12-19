import { createApp } from "vue";
import { createPinia } from "pinia";
import { ZiggyVue } from "ziggy-js";
import AppGeoHome from "./components/Geographic/AppGeoHome.vue";

const app = createApp(AppGeoHome);
const pinia = createPinia();

app.use(pinia);
app.use(ZiggyVue);
app.mount("#appGeo");
