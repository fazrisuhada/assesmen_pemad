import './assets/main.css';
import { createApp, markRaw } from 'vue';
import App from './App.vue';
import router from './router';

// prime vue
import PrimeVue from 'primevue/config';
import Aura from '@primevue/themes/aura';
import 'primeicons/primeicons.css';
// import 'primeflex/primeflex.css';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import ToastService from 'primevue/toastservice';
import SplitButton from 'primevue/splitbutton';
import Avatar from 'primevue/avatar';
import AutoComplete from 'primevue/autocomplete';
import Tooltip from 'primevue/tooltip'
import Card from 'primevue/card';
import InputNumber from 'primevue/inputnumber';
import ProgressSpinner from 'primevue/progressspinner';
import Tag from 'primevue/tag';
import Rating from 'primevue/rating';
import Paginator from 'primevue/paginator';
import Dropdown from 'primevue/dropdown';


// pinia
import { createPinia } from 'pinia';

const app = createApp(App);
const pinia = createPinia();

app.use(pinia);
app.use(router);
app.use(PrimeVue, {
    theme: {
        preset: Aura,
    }
});
app.use(ToastService);

app.component('InputText', InputText);
app.component('Button', Button);
app.component('Dialog', Dialog);
app.component('SplitButton', SplitButton);
app.component('Avatar', Avatar);
app.component('AutoComplete', AutoComplete);
app.directive('tooltip', Tooltip);
app.component('Card', Card);
app.component('InputNumber', InputNumber);
app.component('ProgressSpinner', ProgressSpinner);
app.component('Tag', Tag);
app.component('Rating', Rating);
app.component('Paginator', Paginator);
app.component('Dropdown', Dropdown); 

pinia.use(({ store }) => {
    store.$router = markRaw(router);
})

app.mount('#app')
