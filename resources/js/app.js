import './bootstrap';
import { createApp } from "vue";
import AppLayout from './Layouts/AppLayout.vue';
import { router } from './router';

const app = createApp(AppLayout);

app.use(router);

router.isReady().then(() => {
    app.mount('#app');
});
