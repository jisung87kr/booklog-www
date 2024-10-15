import {createApp} from "vue";
import ExampleComponent from "../components/ExampleComponent.vue";
createApp({
  components: {
        "example-component": ExampleComponent,
    },
}).mount("#app");
