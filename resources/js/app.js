import './bootstrap';
import { createApp } from 'vue';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Initialize Vue for components
const app = createApp({});

// Register any global components here if needed
// app.component('example-component', ExampleComponent);

app.mount('#app');
