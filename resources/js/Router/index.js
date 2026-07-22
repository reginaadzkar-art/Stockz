import { createRouter, createWebHistory } from 'vue-router';
import Dashboard from '../Pages/Dashboard.vue';
import InventoryList from '../Pages/InventoryList.vue';

const routes = [
    {
        path: '/',
        name: 'Dashboard',
        component: Dashboard
    },
    {
        path: '/inventory',
        name: 'Inventory',
        component: InventoryList
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;