import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import './styles/bootstrap/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/css/bootstrap.css';
// import BootstrapVue3 from 'bootstrap-vue-3';
import en from './locales/en.json';
import du from './locales/du.json';
import { createI18n } from 'vue-i18n';


const i18n = createI18n({
    messages: {
        en: en,
        du: du
    },
    fallbackLocale: 'en',
    silentFallbackWarn: true
        // fallbackLocale: ''
});

const app = createApp(App);
app.use(i18n);
app.use(router).mount('#app');
// app.use(BootstrapVue3)

app.config.globalProperties.$hostName = 'http://localhost:8000/api';
// app.config.globalProperties.$hostName = 'http://10.10.20.208/HR_Engine/api';
// app.config.globalProperties.$hostName = 'https://theportal.scala-apac.org/HR_Engine/api';
