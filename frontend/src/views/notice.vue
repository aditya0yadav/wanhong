<template>
    <div style="box-shadow: var(--t-card-shadow-color) 0px 1px 3px;
    background: var(--t-card-color);padding: 30px;border-radius: 10px;max-width: 1000px;">
        <div class="v2-h-title" style="margin-bottom: 20px;font-size:20px;">{{ detail.title }}</div>
        <div v-html="detail.content"></div>
    </div>
</template>
<script setup>
import { useRoute } from 'vue-router'
import { noticeDetail,unread } from '@/api/modules/user'
import { ref,watch } from 'vue'
import { useUserStore } from '@/stores/modules/user'
import { storeToRefs } from 'pinia'
const userStore = useUserStore()
const route = useRoute()
const loading = ref(true)
const detail = ref({})
const { noticeNum } = storeToRefs(userStore)
noticeDetail({notice_data_id:route.params.id}).then(res => {
    detail.value = res.data;
    loading.value = false;
    unread().then(res => {
            noticeNum.value = res.data
        })
})
watch(() => route.params.id, () => {
    detail.value = {};
    noticeDetail({notice_data_id:route.params.id}).then(res => {
        detail.value = res.data;
        loading.value = false;
        unread().then(res => {
            noticeNum.value = res.data
        })
    })
})
</script>