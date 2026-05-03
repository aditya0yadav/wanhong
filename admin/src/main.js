import { createApp } from 'vue'
import App from './App.vue'
import router from '@/router'
import { setupStore } from '@/store'
import 'default-passive-events'

import '@/permission'

import 'virtual:svg-icons-register'

import i18n from '@/lang/index'

import 'element-plus/theme-chalk/dark/css-vars.css'
import '@/styles/index.scss'
import 'uno.css'
import SvgIcon from '@/components/SvgIcon/index.vue'
import FileImage from '@/components/FileManage/FileImage.vue'
import FileUpload from '@/components/FileManage/FileUpload.vue'
import Pagination from '@/components/Pagination/index.vue'
import hamburger from '@/components/Hamburger/index.vue'
import VueUeditorWrap from 'vue-ueditor-wrap';
const app = createApp(App)
app.component('SvgIcon', SvgIcon)
app.component('FileImage', FileImage)
app.component('FileUpload', FileUpload)
app.component('Pagination', Pagination)
app.component('Hamburger', hamburger)
setupStore(app)
app.use(router)
app.use(i18n)
app.use(VueUeditorWrap)
app.mount('#app')
