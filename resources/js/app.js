import './bootstrap';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import router from './router';
import App from './App.vue';

// Import CSS
import '../css/app.css';

// Create Vue app
const app = createApp(App);

// Plugins
app.use(createPinia());
app.use(router);

// Mount
app.mount('#app');
