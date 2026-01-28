import './bootstrap';
import { createApp } from 'vue';
import App from './App.vue';

// AdminLTE assets
import 'admin-lte/dist/css/adminlte.min.css'
import 'admin-lte/plugins/fontawesome-free/css/all.min.css'
import 'admin-lte/dist/js/adminlte.min.js'

import 'bootstrap/dist/js/bootstrap.bundle.min.js'


const app = createApp(App);
// app.use(router);
app.mount('#app');