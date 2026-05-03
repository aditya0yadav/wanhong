<template>
    <main class="main-content nopd overbgcolor v2-max1000">
        <div v-if="!loading">

            <div v-if="walls.length">
                <div class="v2-h-title flex justify-between">
                    <div class="flex-1 flex items-center"><img style="width: 30px;margin-right: 5px;"
                            src="../assets/hot.svg" alt="">{{ $t('offers.wall') }}</div>

                </div>
                <div class="walls">
                    <div class="item" v-for="item, index in walls" @click="openIframe(item)">
                        <div class="cont" :style="{ background: item.platform_color }">
                            <div class="imgbox"><img :src="item.logo_url" />
                            </div>
                            <el-icon @click.stop="copyWallLink(item)"
                                style="position: absolute;right: 10px;top: 10px;color: var(--el-color-primary);cursor: pointer;"
                                size="24">
                                <CopyDocument />
                            </el-icon>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="lists.length">
                <div class="v2-h-title flex justify-between">
                    <div class="flex-1 flex items-center"><img style="width: 30px;margin-right: 5px;"
                            src="../assets/hot.svg" alt="">{{ $t('offers.partner') }}</div>

                </div>
                <div class="walls">
                    <div class="item" v-for="item in lists" @click="turnToPlatform(item)">
                        <div class="cont" :style="{ background: item.platform_color }">
                            <div class="imgbox"><img :src="item.logo_url" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <el-dialog width="1200" :title="iframeTitle.toUpperCase()" :close-on-press-escape="false"
                :close-on-click-modal="false" v-model="iframeVisible" align-center>
                <div>
                    <div style="height:20px;">
                        <el-progress stroke-width="2" v-if="iframeLoading" :percentage="80" :indeterminate="true"
                            :show-text="false" />
                    </div>
                    <iframe v-if="iframeVisible" :src='iframeUrl'
                        style='height: 700px; width: 100%; margin: 0; padding: 0; border: 0;'></iframe>
                </div>
            </el-dialog>
        </div>
        <div v-else class="v2-loading">
            <div class="loading">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
    </main>

</template>
<script setup>
import { ref, onMounted, nextTick, onUnmounted } from 'vue'
import { useUserStore } from '@/stores/modules/user'
import { storeToRefs } from 'pinia'
import { onBeforeRouteLeave, useRoute, useRouter } from 'vue-router'
import { getPlatformList, getWallLink, getOfferLink } from '@/api/modules/platform'
import { ElMessage, ElMessageBox } from 'element-plus'
import { copyText } from '@/api/helper/clipboard'
const { userInfo } = storeToRefs(useUserStore())
const rate = ref(5)
const lists = ref([])
const walls = ref([])
const route = useRoute()
const router = useRouter()
const loading = ref(true)
const wallRef = ref()
const listRef = ref()
let timer = null;

getPlatformList().then(res => {
    const data = res.data;
    console.log(res.data);
    data.forEach(item => {
        if (item.is_wall == 1) {
            walls.value.push(item)
        }
        if (item.is_list == 1) {
            lists.value.push(item)
        }
    });
    loading.value = false
}).catch(() => {
    loading.value = false
})

const turnToPlatform = (item) => {
    router.push({
        name: 'platform',
        params: {
            id: item.platform_id
        },
        query: {
            name: item.platform_name
        }
    })
}
onUnmounted(() => {
    clearInterval(timer);
})
onBeforeRouteLeave((to, from) => {
    clearInterval(timer);
})
const iframeLoading = ref(false);
const iframeVisible = ref(false);
const iframeTitle = ref('');
const iframeUrl = ref();
const getDetailUrl = (link, item, type = 'offer') => {
    // We use Wanhong.com as requested by user, falling back to current origin in dev
    const baseUrl = window.location.origin;
    const params = new URLSearchParams({
        link: link,
        surveyId: type === 'offer' ? item.project_pno : ('platform_' + item.platform_id),
        platform: type === 'offer' ? 'goweb' : (item.platform_name ? item.platform_name.toLowerCase() : 'goweb')
    });
    return `${baseUrl}/detail?${params.toString()}`;
}

const openIframe = (item) => {
    iframeVisible.value = true;
    iframeLoading.value = true;
    iframeUrl.value = '';
    iframeTitle.value = item.platform_name;
    getWallLink({ platform_id: item.platform_id }).then(res => {
        let link = res.data;
        if (typeof link === 'object' && link !== null) {
            link = link.link || link.url || JSON.stringify(link);
        }
        if (typeof link === 'string' && link.startsWith('/')) {
            link = window.location.origin + link;
        }

        const detailUrl = getDetailUrl(link, item, 'wall');
        iframeUrl.value = detailUrl; // Open the detail page in the iframe
        iframeLoading.value = false;
    })

}
const copyWallLink = (item) => {
    getWallLink({ platform_id: item.platform_id }).then(res => {
        let link = res.data;
        if (typeof link === 'object' && link !== null) {
            link = link.link || link.url || JSON.stringify(link);
        }
        if (typeof link === 'string' && link.startsWith('/')) {
            link = window.location.origin + link;
        }

        const detailUrl = getDetailUrl(link, item, 'wall');
        copyText(detailUrl).then(success => {
            if (success) {
                ElMessage.success('复制成功');
            }
        })
    })
}
const start = (item) => {
    getOfferLink({ project_pno: item.project_pno }).then(res => {
        let link = res.data;
        if (typeof link === 'object' && link !== null) {
            link = link.link || link.url || JSON.stringify(link);
        }
        if (typeof link === 'string' && link.startsWith('/')) {
            link = window.location.origin + link;
        }

        const detailUrl = getDetailUrl(link, item, 'offer');
        window.open(detailUrl);
    })
}
window.scrollTo(0, 0, 'smooth');
</script>
<style scoped lang="scss">


.v2-h-title {
    margin-top: 30px;
}

.v2-dashboard-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #fff;
    height: 58px;
    background: #FFFFFF;
    box-shadow: 0 1px 3px #e2d9d4;
    border-radius: 10px;
    position: relative;

    .v2-dashboard-top-left {
        display: flex;
        align-items: center;

        >div {
            width: 58px;
            height: 58px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            margin: 0 10px 0 20px;

            img {
                width: 76px;
                position: absolute;
                bottom: 0;
            }
        }

        p {
            display: flex;
            align-items: center;

            img {
                width: 24px;
                height: 24px;
                margin-left: 10px;
            }

            span {
                font-weight: 600;
                font-size: 18px;
                color: #433f3b;

                &:last-of-type {
                    color: #9c571f;
                    margin-left: 5px;
                }
            }
        }

    }

    .v2-dashboard-top-right {
        font-size: 14px;
        color: #77706a;
        margin-right: 34px;
        cursor: pointer;
        display: flex;
        align-items: center;
    }
}

.bt {
    width: 50px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #e8ecf7;
    border-radius: 2px;
    box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14), 0 3px 1px -2px rgba(0, 0, 0, 0.12), 0 1px 5px 0 rgba(0, 0, 0, 0.2);
}

.el-button+.el-button {
    margin-left: 3px;
}

.el-button+.el-button .bt {
    background: var(--t-btn-color);
    color: var(--t-btn-text-color);

}

.walls {
    display: flex;
    padding: 20px 0;
    margin: 0 3px;
    flex-wrap: wrap;

    .item {
        flex-shrink: 0;
        width: 180px;
        height: 190px;
        background: #FFFFFF;
        box-shadow: 0 0 3px #e2d9d4;
        border-radius: 10px;
        margin-right: 23px;
        cursor: pointer;
        transition: all .3s ease;
        margin-bottom: 20px;

        &:nth-child(5n) {
            margin-right: 0;
        }

        .cont {
            position: relative;
            height: 100%;
            border-radius: 8px;
            padding: 0 14px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        &:hover {
            transform: scale(1.02);
        }

        .imgbox {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        img {
            width: 80%;
        }

        p {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 15px;
            text-align: center;
            font-size: 16px;
            color: #312b26;
            margin: 0;
        }

    }
}
</style>