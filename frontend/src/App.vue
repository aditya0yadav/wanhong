<script setup>
import { RouterView } from 'vue-router'
import { useUserStore } from '@/stores/modules/user'
import { storeToRefs } from 'pinia';
import { site } from '@/api/modules/user';
import { ElConfigProvider } from 'element-plus'
import zhCn from 'element-plus/es/locale/lang/zh-cn'
import en from 'element-plus/es/locale/lang/en'
const userStore = useUserStore()
const { sys, theme,lang } = storeToRefs(userStore)
site().then(res => {
    sys.value = res.data;
    window.document.title = res.data.name
    window.document.querySelector('meta[name="description"]').setAttribute('content', res.data.description)
    window.document.querySelector('meta[name="keywords"]').setAttribute('content', res.data.keywords)
})
userStore.SET_LOCALE(lang.value);
document.documentElement.setAttribute('data-theme', theme.value);
</script>
<template>
<el-config-provider :locale="lang == 'zhCn'? zhCn : en">
  <RouterView />
</el-config-provider>
</template>

<style scoped></style>
