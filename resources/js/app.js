import './bootstrap';
import { createApp } from 'vue';
import App from './app.vue';
import router from './Router';

createApp(App)
    .use(router)
    .mount('#app');