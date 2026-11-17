import './bootstrap';
import { createApp } from 'vue/dist/vue.esm-bundler.js';
import OrderTracker from './components/OrderTracker.vue';

const app = createApp({});
app.component('order-tracker', OrderTracker);
app.mount('#app');