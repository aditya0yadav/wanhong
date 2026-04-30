import { createApp } from 'vue'
import { createPinia } from 'pinia'
import ElementPlus from 'element-plus'
import 'element-plus/dist/index.css'
// reset style sheet
import "@/styles/reset.scss";
// CSS common style sheet
import "@/styles/common.scss";
import "@/styles/element.scss";
import "@/icons/iconfont.css"
import App from './App.vue'
import router from './router'
// pinia store
import pinia from "./stores"
import Vue3ColorPicker from "vue3-colorpicker";
import "vue3-colorpicker/style.css";
import * as ElementPlusIconsVue from '@element-plus/icons-vue'
import "./assets/font-awesome/css/font-awesome.min.css"
import i18n from './i18n';
import { FingerprintPlugin } from '@fingerprint/vue';

const app = createApp(App)
for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
    app.component(key, component)
}
app.use(FingerprintPlugin, {
    apiKey: '4jld2XJVg9Xsh8csXfFK',
    region: 'us',
});

app.use(createPinia())
app.use(Vue3ColorPicker).use(pinia).use(i18n).use(router).use(ElementPlus, { size: 'large' }).mount('#app')

