import './bootstrap';
import { createApp } from "vue";
import components from "./components";
import { createPinia } from "pinia";

const app = createApp({});
const pinia = createPinia();
app.use(pinia);

Object.entries(components).forEach(([name, component]) => {
    app.component(name, component);
});

app.mount('#app');
