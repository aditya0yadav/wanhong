<template>
    <div class="wrapper">
        <div class="v2-index-header" :style="{ top: '0px' }">
            <div class="v2-index-header-box">
                <!-- Logo -->
                <div class="header-logo-wrap">
                    <a href="/offers"><img src="@/assets/logo.png" class="v2-index-header-logo"></a>
                </div>

                <!-- Desktop Nav -->
                <div class="sidebar-content desktop-only" v-if="token">
                    <div :class="['item', route.name === 'offers' || route.meta.activeMenu === 'offers' ? 'active' : '']"
                        @click="turnToPage('offers')">
                        <img src="../assets/offers-choose.svg" class="nav-icon" />
                        <div class="tit">{{ $t('nav.offers') }}</div>
                    </div>
                    <div :class="['item', route.name === 'leaderboard' || route.meta.activeMenu === 'leaderboard' ? 'active' : '']"
                        @click="turnToPage('daily', true)">
                        <img src="../assets/leaderboard-choose.svg" class="nav-icon" />
                        <div class="tit">{{ $t('nav.leaderboard') }}</div>
                    </div>
                    <div :class="['item', route.name === 'statistics' ? 'active' : '']"
                        @click="turnToPage('statistics', true)">
                        <img src="../assets/stats.svg" class="nav-icon" />
                        <div class="tit">{{ $t('nav.statistics') }}</div>
                    </div>
                    <div :class="['item', route.name === 'teamStatistics' ? 'active' : '']"
                        @click="turnToPage('teamStatistics', true)">
                        <img src="../assets/advertise-choose.svg" class="nav-icon" />
                        <div class="tit">{{ $t('nav.teamStatistics') }}</div>
                    </div>
                    <div :class="['item', route.name === 'users' ? 'active' : '']" @click="turnToPage('users', true)">
                        <img src="../assets/affiliate-choose.svg" class="nav-icon" />
                        <div class="tit">{{ $t('nav.team') }}</div>
                    </div>
                </div>

                <!-- Right Side Controls -->
                <div class="v2-index-header-box-right">
                    <div class="v2-index-header-logo-right desktop-controls">
                        <el-switch style="margin-right: 15px;" v-model="theme" @change="toggleTheme" active-value="dark"
                            inactive-value="light" active-text="DARK" inactive-text="LIGHT" inline-prompt />
                        <el-dropdown trigger="click">
                            <img src="../assets/lang.png" style="width: 20px;cursor: pointer;" />
                            <template #dropdown>
                                <el-dropdown-menu>
                                    <el-dropdown-item @click="changeLang('en')">English</el-dropdown-item>
                                    <el-dropdown-item @click="changeLang('zhCn')">简体中文</el-dropdown-item>
                                </el-dropdown-menu>
                            </template>
                        </el-dropdown>
                        <el-dropdown>
                            <img src="../assets/hbchange.png" style="width: 20px;cursor: pointer;margin-left: 15px;" />
                            <template #dropdown>
                                <el-dropdown-menu>
                                    <el-dropdown-item @click="changeHb('usd')">{{ $t('statistics.usd') }}</el-dropdown-item>
                                    <el-dropdown-item @click="changeHb('coins')">{{ $t('statistics.coins') }}</el-dropdown-item>
                                </el-dropdown-menu>
                            </template>
                        </el-dropdown>
                        <div class="notice" style="padding-left:15px;cursor: pointer;color: #374E76;">
                            <el-badge v-if="noticeNum" :value="noticeNum" class="item" @click="openDrawer" size="small" style="margin-right: 10px;">
                                <el-icon size="20" color="#374E76"><BellFilled /></el-icon>
                            </el-badge>
                            <el-icon v-else size="20" @click="openDrawer" color="#374E76"><BellFilled /></el-icon>
                        </div>
                        <div v-if="hb === 'coins'" class="balance-chip">
                            {{ statisticsData['success'] }}<img src="../assets/coin.svg" alt="" style="width: 16px; margin-left: 5px;" />
                        </div>
                        <div v-if="hb === 'usd'" class="balance-chip">
                            $ {{ (statisticsData['success'] / 100).toFixed(2) }}
                        </div>
                        <div class="userinfo" v-if="token">
                            <div class="uinfo">
                                <el-avatar :size="24" fit="cover" :src="userInfo.avatar_url || ava" style="margin-right: 10px;" />
                                {{ userInfo.nickname }}
                                <el-icon style="margin-left: 5px"><ArrowDownBold /></el-icon>
                                <div class="pop" @click.stop>
                                    <div class="uitem" @click="turnToPage('profile', true)">{{ $t('user.profile') }}</div>
                                    <div class="uitem" @click="startLogout">{{ $t('user.logout') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="flex" style="margin-left:15px;" v-else>
                            <div class="item link-hover login font-weight-bold">Sign In</div>
                        </div>
                    </div>

                    <!-- Mobile: Notification + Hamburger -->
                    <div class="mobile-header-actions">
                        <div class="notice mobile-bell" style="cursor: pointer;margin-right: 12px;">
                            <el-badge v-if="noticeNum" :value="noticeNum" class="item" @click="openDrawer" size="small">
                                <el-icon size="22" color="#fff"><BellFilled /></el-icon>
                            </el-badge>
                            <el-icon v-else size="22" @click="openDrawer" color="#fff"><BellFilled /></el-icon>
                        </div>
                        <div class="mobile-menu-trigger" @click="toggleMobileMenu">
                            <el-icon size="26" color="#fff"><Menu /></el-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="v2-index-live-scroll" style="background-color: rgb(28,31,32);" v-if="rewards.length">
            <el-scrollbar class="hidden-scroll" ref="scrollArea" min-size="0">
                <ul>
                    <li v-for="item, index in rewards" @click="openBaseDetail(item)">
                        <span v-if="hb == 'coins'" :style="{ background: getColor(index) }">
                            <img src="../assets/coin.svg" alt="">{{ item.member_payout }}
                        </span>
                        <span v-if="hb == 'usd'" :style="{ background: getColor(index) }">
                            $ {{ (item.member_payout / sys.usd_rate).toFixed(2) }}
                        </span>
                        <span :style="{ background: getColor1(index) }">
                            <img :src="item.member_avatar" alt="">
                            {{ item.member_name }}</span>

                    </li>
                </ul>
            </el-scrollbar>
        </div>
        <main class="kaitai-main" :style="{ 'padding-top': rewards.length ? '109px' : '64px' }">
            <div class="main" :class="route.name === 'platform' ? 'v2-max1500' : 'v2-max1200'">
                <RouterView v-slot="{ Component }">
                    <transition name="fade-right" mode="out-in">
                        <component :is="Component" />
                    </transition>
                </RouterView>
            </div><!--
            <div class="recent-reward" :class="hideSide ? 'hide' : ''">
                <div class="title">
                    <div class="icon" @click="hideSide = !hideSide"><el-icon><Right v-if="!hideSide" /><Back v-else /></el-icon></div>
                    Recent Reward
                </div>
                <div class="reward-list">
                    <el-table :data="rewards" style="width: 100%" :height="height()">
                        <el-table-column prop="platform_name" :label="$t('statistics.platform')" />
                        <el-table-column prop="member_name" :label="$t('statistics.member')" />
                        <el-table-column prop="member_payout" :label="$t('statistics.rewardCoins')">
                            <template #default="scope">
                                <div class="money" v-if="hb == 'coins'"><img src="../assets/coin.svg" class="coin">{{ scope.row.member_payout }}</div>
                                <div class="money" v-if="hb == 'usd'">${{ (scope.row.member_payout / scope.row.usd_currency_coins).toFixed(2) }}</div>
                            </template>
                        </el-table-column>
                        <el-table-column prop="create_time" :label="$t('statistics.time')">
                            <template #default="scope">
                                {{ timeago(scope.row.create_time) }}
                            </template>
                        </el-table-column>
                    </el-table>
                </div>
            </div>-->
        </main>
        <el-dialog :title="$t('user.profile')" v-model="showbaseDetail" class="profile-dialog" width="1000px">
            <div class="flex justify-between mt20" v-loading="loading">
                <div class="coinpayu-profile">
                    <div class="imgbox">
                        <el-avatar :size="100" fit="cover" :src="baseUserInfo.avatar_url || ava"
                            style="margin-right: 10px;" />
                    </div>
                    <p style="font-size: 20px;text-align: center;color:var(--t-color-1)">{{ baseUserInfo.nickname }}</p>
                    <ul class="coinpayu-profile-list">
                        <li>
                            <p>{{ baseUserInfo.completed_offers }}</p>
                            <span>{{ $t('statistics.completedOffers') }}</span>
                        </li>
                        <li v-if="hb == 'coins'">
                            <p>{{ baseUserInfo.earned }}</p>
                            <span>{{ $t('user.coinsEarned') }}</span>
                        </li>
                        <li v-if="hb == 'usd'">
                            <p>{{ baseUserInfo.usd_earned ? baseUserInfo.usd_earned.member_payout : 0 }}</p>
                            <span>{{ $t('user.usdEarned') }}</span>
                        </li>
                    </ul>
                </div>
                <div class="earn">
                    <div style="border-bottom: 1px solid var(--t-background-color);display: flex;justify-content: space-between;
    padding: 20px 20px;"><span style="color:var(--t-color-1);font-size: 18px;">{{ $t('user.earnings') }}</span><span
                            style="font-size: 12px;color:var(--t-color-1)">{{ $t('user.last7Days') }}</span></div>

                    <div ref="sevenDom" v-if="baseUserInfo.seven_data" style="width:100%;height:300px;"></div>
                </div>
            </div>
            <div class="v2-h-ttile" style="margin: 20px 0;font-size:24px;">{{ $t('user.recentOffers') }}</div>
            <el-table class="white-space-nowrap" :data="baseUserInfo.rewards" style="width: 100%;margin-bottom:20px;">
                <el-table-column prop="platform.platform_name" :label="$t('statistics.platform')" />
                <el-table-column prop="project_pno" :label="$t('statistics.projectNo')" />
                <el-table-column prop="member_payout" :label="$t('statistics.rewardCoins')">
                    <template #default="scope">
                        <div class="money white-space-nowrap" v-if="hb == 'coins'"><img src="../assets/coin.svg"
                                class="coin">{{ scope.row.member_payout }}
                        </div>
                        <div class="money white-space-nowrap" v-if="hb == 'usd'">${{ (scope.row.member_payout /
                            scope.row.usd_currency_coins).toFixed(2) }}
                        </div>
                    </template>
                </el-table-column>
                <el-table-column prop="create_time" :label="$t('statistics.time')" />
            </el-table>
        </el-dialog>
        <el-drawer v-model="drawer" :size="400" :show-close="false" >
            <template #header="{ close, titleId, titleClass }">
                <h4>{{ $t('user.message') }}</h4>
                <el-link type="primary" @click="markAll" plain size="small" style="margin-right:10px;">
                    {{ $t('user.allread') }}
                </el-link>
            </template>
            <div v-for="item in noticeData" class="notice-item" :class="{ read: item.is_read == 1 }">
                <div style="flex:1;padding-right:10px;" @click="openNotice(item)">
                    <div class="title">{{ item.title }}</div>
                    <div class="time">{{ item.create_time }}</div>
                </div>
                <span class="mark" @click="markRead(item)">{{ $t('user.markread') }}</span>
            </div>
        </el-drawer>
        <el-drawer 
            v-model="mobileMenuVisible" 
            direction="ltr" 
            :size="300" 
            :with-header="false"
            :modal="true"
            class="mobile-nav-drawer"
        >
            <div class="mobile-nav-content" style="background: linear-gradient(160deg, #0f1e3a 0%, #182c4e 100%); min-height: 100%; display:flex; flex-direction:column; padding:0 0 24px;">
                <!-- Close button -->
                <div style="display:flex; justify-content:flex-end; padding:16px 16px 8px; cursor:pointer; color:rgba(255,255,255,0.5);" @click="mobileMenuVisible = false">
                    <el-icon size="22"><Close /></el-icon>
                </div>

                <!-- User Profile Section -->
                <div v-if="token" style="display:flex; align-items:center; padding:8px 20px 20px; gap:14px; border-bottom:1px solid rgba(255,255,255,0.07);">
                    <el-avatar :size="52" fit="cover" :src="userInfo.avatar_url || ava" style="border:2px solid rgba(255,255,255,0.2); flex-shrink:0;" />
                    <div style="flex:1; min-width:0;">
                        <div style="font-size:15px; font-weight:700; color:#fff; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ userInfo.nickname }}</div>
                        <div v-if="hb === 'coins'" style="display:flex; align-items:center; margin-top:4px; font-size:12px; color:rgba(255,255,255,0.6);">
                            <img src="../assets/coin.svg" style="width:13px;margin-right:4px;" />
                            {{ statisticsData['success'] }} Coins
                        </div>
                        <div v-if="hb === 'usd'" style="display:flex; align-items:center; margin-top:4px; font-size:12px; color:rgba(255,255,255,0.6);">
                            $ {{ (statisticsData['success'] / 100).toFixed(2) }} USD
                        </div>
                    </div>
                </div>

                <!-- Nav Items -->
                <div style="padding:10px 10px 6px;">
                    <div :class="['mobile-nav-item', route.name === 'offers' || route.meta?.activeMenu === 'offers' ? 'active' : '']"
                        @click="turnToPage('offers'); mobileMenuVisible = false">
                        <img src="../assets/offers-choose.svg" />
                        <span>{{ $t('nav.offers') }}</span>
                    </div>
                    <div :class="['mobile-nav-item', route.name === 'leaderboard' || route.meta?.activeMenu === 'leaderboard' ? 'active' : '']"
                        @click="turnToPage('daily', true); mobileMenuVisible = false">
                        <img src="../assets/leaderboard-choose.svg" />
                        <span>{{ $t('nav.leaderboard') }}</span>
                    </div>
                    <div :class="['mobile-nav-item', route.name === 'statistics' ? 'active' : '']"
                        @click="turnToPage('statistics', true); mobileMenuVisible = false">
                        <img src="../assets/history-choose.svg" />
                        <span>{{ $t('nav.statistics') }}</span>
                    </div>
                    <div :class="['mobile-nav-item', route.name === 'teamStatistics' ? 'active' : '']"
                        @click="turnToPage('teamStatistics', true); mobileMenuVisible = false">
                        <img src="../assets/advertise-choose.svg" />
                        <span>{{ $t('nav.teamStatistics') }}</span>
                    </div>
                    <div :class="['mobile-nav-item', route.name === 'users' ? 'active' : '']"
                        @click="turnToPage('users', true); mobileMenuVisible = false">
                        <img src="../assets/affiliate-choose.svg" />
                        <span>{{ $t('nav.team') }}</span>
                    </div>
                    <div class="mobile-nav-item" @click="turnToPage('profile', true); mobileMenuVisible = false">
                        <el-icon size="20" color="rgba(255,255,255,0.6)" style="margin-right:14px; flex-shrink:0;"><User /></el-icon>
                        <span>{{ $t('user.profile') }}</span>
                    </div>
                </div>

                <!-- Divider -->
                <div style="height:1px; background:rgba(255,255,255,0.07); margin:4px 0;"></div>

                <!-- Settings Section -->
                <div style="padding:8px 20px 4px;">
                    <div style="font-size:11px; font-weight:700; letter-spacing:0.08em; color:rgba(255,255,255,0.3); text-transform:uppercase; margin-bottom:8px;">Settings</div>
                    <div class="mobile-setting-row">
                        <span class="setting-label">Theme</span>
                        <el-switch v-model="theme" @change="toggleTheme" active-value="dark" inactive-value="light" active-text="Dark" inactive-text="Light" inline-prompt />
                    </div>
                    <div class="mobile-setting-row">
                        <span class="setting-label">Language</span>
                        <div class="setting-btn-group">
                            <div :class="['setting-chip', lang !== 'zhCn' ? 'chip-active' : '']" @click="changeLang('en')">EN</div>
                            <div :class="['setting-chip', lang === 'zhCn' ? 'chip-active' : '']" @click="changeLang('zhCn')">中文</div>
                        </div>
                    </div>
                    <div class="mobile-setting-row">
                        <span class="setting-label">Currency</span>
                        <div class="setting-btn-group">
                            <div :class="['setting-chip', hb === 'usd' ? 'chip-active' : '']" @click="changeHb('usd')">USD</div>
                            <div :class="['setting-chip', hb === 'coins' ? 'chip-active' : '']" @click="changeHb('coins')">Coins</div>
                        </div>
                    </div>
                </div>

                <!-- Spacer -->
                <div style="flex:1;"></div>

                <!-- Divider -->
                <div style="height:1px; background:rgba(255,255,255,0.07); margin:0 0 12px;"></div>

                <!-- Logout -->
                <div v-if="token" class="mobile-logout-btn" @click="startLogout(); mobileMenuVisible = false">
                    <el-icon size="17" style="margin-right:9px;"><SwitchButton /></el-icon>
                    {{ $t('user.logout') }}
                </div>
            </div>
        </el-drawer>
    </div>
</template>
<script setup>
import ava from '@/assets/ava.png'
import { ref, nextTick, onBeforeUnmount } from 'vue'
import { RouterView, useRoute, useRouter } from 'vue-router'
import { useUserStore } from '@/stores/modules/user'
import { User, Lock, Menu, Close, SwitchButton } from '@element-plus/icons-vue'
import { login, logout, getUserInfo, getLastRewards, getBaseDetail, getNotice, markNotice, unread } from '@/api/modules/user';
import { ElMessage, ElMessageBox } from "element-plus";
import { getMyStatistics } from '@/api/modules/platform'
import { storeToRefs } from 'pinia'
import * as echarts from 'echarts';
const route = useRoute()
const router = useRouter()
const userStore = useUserStore()
const drawer = ref(false)
const openDrawer = () => {
    drawer.value = true
    getNoticeData();
}
const activeName = ref('all');
const mobileMenuVisible = ref(false);
const toggleMobileMenu = () => {
    mobileMenuVisible.value = !mobileMenuVisible.value;
}
const { token, userInfo, showLogin, hideSide, sys, theme, lang, hb, noticeNum } = storeToRefs(userStore)
getUserInfo().then(res => {
    userInfo.value = res.data
})
const statisticsData = ref([])
getMyStatistics({}).then(res => {
    statisticsData.value = res.data
})
const noticeData = ref([])
const getNoticeData = () => {
    noticeData.value = [];
    getNotice().then(res => {
        noticeData.value = res.data.list;
    })
}

const getUnread = () => {
    unread().then(res => {
        noticeNum.value = res.data
    })
}
getUnread();
var noticetimer = setInterval(() => {
    getUnread();
}, 5000);
const markRead = (item) => {
    markNotice({ notice_data_id: item.notice_data_id }).then(res => {
        getNoticeData();
    })
}
const markAll = () => {
    markNotice({notice_data_id:-1}).then(res => {
        getNoticeData();
    })
}
const openNotice = (item) => {
    drawer.value = false;
    router.replace({ name: 'notice', params: { id: item.notice_data_id } })
}
onBeforeUnmount(() => {
    if (noticetimer) {
        clearInterval(noticetimer);
    }
})
const height = () => {
    var clientHeight = document.documentElement.clientHeight || document.body.clientHeight
    return clientHeight - 184;
}
const changeLang = (lang) => {
    userStore.SET_LOCALE(lang);
}
const changeHb = (hb) => {
    userStore.SET_HB(hb);
}
const toggleTheme = () => {
    document.documentElement.setAttribute('data-theme', theme.value);
}
const showbaseDetail = ref(false)
const baseUserInfo = ref({})
const sevenDom = ref(null);
const loading = ref(false)
const openBaseDetail = (item) => {
    showbaseDetail.value = true
    baseUserInfo.value = {}
    loading.value = true
    getBaseDetail(item).then(res => {

        baseUserInfo.value = res.data
        nextTick(() => {
            const sevenInstance = echarts.init(sevenDom.value);
            let sevenoption;
            sevenoption = {
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'cross'
                    }
                },
                grid: {
                    left: 60,
                    top: 30,
                    right: 40,
                    bottom: 40
                },
                xAxis: {
                    data: hb.value == 'coins' ? Object.keys(res.data.seven_data) : Object.keys(res.data.usd_seven_data),
                    nameTextStyle: {
                        color: '#a9a9ca'
                    },
                    axisLabel: {
                        color: '#a9a9ca'
                    },
                    axisLine: {
                        lineStyle: {
                            color: '#383838'
                        }
                    }, splitLine: {
                        lineStyle: {
                            color: '#383838'
                        }
                    }
                },
                yAxis: {
                    axisLabel: {
                        color: '#a9a9ca'
                    }, axisLine: {
                        lineStyle: {
                            color: '#383838'
                        }
                    }, splitLine: {
                        lineStyle: {
                            color: '#383838'
                        }
                    }
                },
                series: [{
                    name: hb.value == 'coins' ? 'completed coins' : 'completed usd',
                    type: 'line', // 这里可以是'line'、'bar'、'pie'等，根据图表类型选择
                    data: hb.value == 'coins' ? Object.values(res.data.seven_data) : Object.values(res.data.usd_seven_data),
                    smooth: true,
                    itemStyle: {
                        color: '#ffb535', // 线条颜色
                        borderWidth: 2, // 线条粗细
                        borderType: 'solid', // 线条类型
                        borderColor: '#fff', // 线条颜色
                        lineStyle: {
                            type: 'solid', // 线条类型
                            width: 2, // 线条粗细
                            color: '#ffb535' // 线条颜色
                        },
                        areaStyle: {
                            color: '#ffb535' // 填充颜色
                        },
                        symbol: 'circle', // 点的形状
                        symbolSize: 10 // 点的大小
                    }
                }]
            };
            sevenInstance.setOption(sevenoption);
            loading.value = false
        })

    })
}
const colorPalette = [
    '#3B82F6', // Blue
    '#8B5CF6', // Purple
    '#EC4899', // Pink
    '#F59E0B', // Amber
    '#10B981', // Emerald
    '#06B6D4', // Cyan
    '#6366F1', // Indigo
    '#EF4444', // Red
    '#14B8A6', // Teal
    '#F97316', // Orange
    '#7C3AED', // Violet
    '#D946EF', // Fuchsia
    '#0EA5E9', // Sky
    '#6B7280', // Gray
    '#06B6D4', // Cyan
    '#F43F5E', // Rose
    '#84CC16', // Lime
    '#3B82F6', // Blue 2
    '#8B5CF6', // Purple 2
    '#EC4899', // Pink 2
];
const arr = colorPalette;
const getColor = (index) => {
    return arr[index];
}
const getColor1 = (index) => {
    return modifiedColor(arr[index]);
}
const modifiedColor = (originalColor) => {
    // Convert hex to rgba with transparency
    const hex = originalColor.replace('#', '');
    const r = parseInt(hex.substring(0, 2), 16);
    const g = parseInt(hex.substring(2, 4), 16);
    const b = parseInt(hex.substring(4, 6), 16);
    return `rgba(${r}, ${g}, ${b}, 0.85)`;
}
const rewards = ref([])
getLastRewards().then(res => {
    rewards.value = res.data
    stopScroll();
    if (res.data.length > 0) {
        nextTick(() => {
            startScroll()
        })
    }

})
const scrollArea = ref(null)
let timer = null;
const SCROLL_SPEED = 1;
const startScroll = () => {
    if (timer) {
        clearInterval(timer);
    }
    timer = setInterval(() => {
        // 获取滚动容器
        const container = scrollArea.value.$el.querySelector('.el-scrollbar__wrap');
        // 判断是否已滚动到底部
        if (container.scrollWidth - (container.scrollLeft + container.clientWidth) <= 0) {
            // 滚动到顶部
            container.scrollLeft = 0;
        } else {
            container.scrollLeft += SCROLL_SPEED;
        }
    }, 30); // 根据需要调整滚动间隔
};

const stopScroll = () => {
    if (timer) {
        clearInterval(timer);
    }
};
const startLogout = async () => {
    const res = await logout()
    if (res.code == 200) {
        ElMessage({
            type: 'success',
            message: '退出成功'
        })

        token.value = ''
        userInfo.value = {}
        router.push({
            name: 'login'
        })
    }
}
const timeago = (time) => {
    var data = new Date(time);
    var dateTimeStamp = data.getTime()
    var minute = 1000 * 60;      //把分，时，天，周，半个月，一个月用毫秒表示
    var hour = minute * 60;
    var day = hour * 24;
    var week = day * 7;
    var month = day * 30;
    var year = month * 12;
    var now = new Date().getTime();   //获取当前时间毫秒
    var diffValue = now - dateTimeStamp;//时间差

    var result = "";
    if (diffValue < 0) {
        result = "" + "未来";
    }
    var minC = diffValue / minute;  //计算时间差的分，时，天，周，月
    var hourC = diffValue / hour;
    var dayC = diffValue / day;
    var weekC = diffValue / week;
    var monthC = diffValue / month;
    var yearC = diffValue / year;

    if (yearC >= 1) {
        result = " " + parseInt(yearC) + (lang.value == 'zhCn' ? " 年前" : " years ago")
    } else if (monthC >= 1 && monthC < 12) {
        result = " " + parseInt(monthC) + (lang.value == 'zhCn' ? " 月前" : " months ago")
    } else if (weekC >= 1 && weekC < 5 && dayC > 6 && monthC < 1) {
        result = " " + parseInt(weekC) + (lang.value == 'zhCn' ? " 周前" : " weeks ago")
    } else if (dayC >= 1 && dayC <= 6) {
        result = " " + parseInt(dayC) + (lang.value == 'zhCn' ? " 天前" : " days ago")
    } else if (hourC >= 1 && hourC <= 23) {
        result = " " + parseInt(hourC) + (lang.value == 'zhCn' ? " 小时前" : " hours ago")
    } else if (minC >= 1 && minC <= 59) {
        result = " " + parseInt(minC) + (lang.value == 'zhCn' ? " 分钟前" : " minutes ago")
    } else if (diffValue >= 0 && diffValue <= minute) {
        result = (lang.value == 'zhCn' ? "刚刚" : "just now")
    }

    console.log(result)
    return result
}
const turnToPage = (name, st) => {
    if (st && !token.value) {
        loginVisible.value = true;
        return;
    }
    router.push({
        name: name
    })
}
const showPop = ref(false)
</script>
<style scoped lang="scss">
.notice-item {
    border-left: 2px solid var(--el-color-primary);
    padding-left: 10px;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    cursor: pointer;

    .title {
        font-size: 14px;
        color: #000;

    }

    .time {
        margin-top: 10px;
        font-size: 12px;
        color: var(--t-color-1);
    }

    &.read {
        border-left: 2px solid var(--t-color-1);

        .title {
            color: var(--t-color-1);
        }
    }

    .mark {
        cursor: pointer;
        color: var(--el-color-primary);
        font-size: 12px;
        visibility: hidden;
    }

    &:hover {
        .mark {
            visibility: inherit;
        }
    }
}

.recent-reward {
    position: fixed;
    height: calc(100% - 124px);
    bottom: 0;
    right: 0;
    width: 380px;
    z-index: 999;
    background: var(--t-card-color);
    border-left: 1px solid var(--t-border-color);
    transition: width 0.3s ease-in-out;
    white-space: nowrap;

    &.hide {
        width: 0px;
    }

    .title {
        position: relative;
        padding: 20px;
        background-color: var(--t-background-color);
        border-bottom: 1px solid var(--t-border-color);

        .icon {
            cursor: pointer;
            position: absolute;
            top: 10px;
            left: -58px;
            padding: 10px 20px 10px 20px;
            background-color: var(--t-background-color);
            border-radius: 20px 0 0 20px;
            border: 1px solid var(--t-border-color);
        }
    }
}

.v2-index-live-scroll {
    overflow: hidden;
    width: 100%;
    height: 45px;
    position: fixed;
    top: 64px;
    z-index: 9;
    background-color: var(--t-background-color);

    ul {
        display: flex;
        margin: 6px 0;
        flex-wrap: nowrap;
        white-space: nowrap;

        li {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 32px;
            background: var(--t-card-color);
            box-shadow: 0px 0px 3px 0px #181a1b;
            border-radius: 6px;
            margin-right: 10px;
            flex-shrink: 1;
            cursor: pointer;
            transition: all 0.3s ease;

            &:hover {
              background: linear-gradient(135deg, #2a5a9b, #5aa08f);
              transform: translateY(-2px);
              box-shadow: 0px 4px 8px rgba(42, 90, 155, 0.4);
            }

            &:last-child {
                margin: 0;
            }

            span:first-child {
                padding: 4px 6px;
                border-radius: 6px 0 0 6px;
                font-size: 12px;
                display: flex;
                align-items: center;
                height: 24px;
                color: #fff;
                font-weight: bold;

                img {
                    width: 16px;
                    height: 16px;
                    margin-right: 4px;
                }
            }

            span:last-child {
                padding: 4px 10px;
                border-radius: 0 6px 6px 0;
                font-size: 12px;
                display: flex;
                align-items: center;
                height: 24px;
                color: #fff;

                img {
                    width: 16px;
                    height: 16px;
                    margin-right: 4px;
                }
            }
        }
    }
}

.u-header {
    display: grid;
    place-items: center;
    padding: 50px 0 30px;

    .avatar {
        display: flex;
        width: 90px;
        height: 90px;
        background-size: cover;
        border-radius: 50%;
        border: 2px solid #01d676;
        justify-content: center;
        align-items: center;
        overflow: hidden;

        img {
            width: 100%;
        }
    }
}

.u-cont {
    .profileTable {
        width: 100%;
        display: table;
        border-collapse: collapse;
        border-spacing: 0;
        margin-bottom: 40px;
    }

    .profileTable td {
        font-weight: 500;
        font-size: 12px;
        color: #a9a9ca;
        line-break: anywhere;
        padding: 10px 15px;
        line-height: 160%;
    }

    .profileTable tbody td:nth-child(1) {
        width: 60px;
    }

    .profileTable tbody td:nth-child(2) {
        text-align: left;
        max-width: 100px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .profileTable td:nth-child(2) {
        width: 40%;
    }

    .profileTable tbody tr:nth-child(odd) {
        background: var(--t-over-background-color);
    }

    .title {
        font-size: 16px;
        display: flex;
        align-items: center;

        img {
            margin-right: 10px;
        }
    }

    .statsBoxes {
        display: flex;
        justify-content: flex-start;
        gap: 15px;
        padding-bottom: 10px;
    }

    .statCard {
        font-family: "Poppins";
        font-style: normal;
        display: flex;
        justify-content: center;
        width: 235px;
        height: 70px;
        background: var(--t-over-background-color);
        border-radius: 6px;
        align-items: center;
        margin: 0.5rem 0 1rem 0;
    }

    .cardValue {
        font-weight: 700;
        font-size: 16px;
        text-align: center;
        letter-spacing: .01em;
        color: var(--t-color);
    }

    .cardValueDescription {
        font-weight: 500;
        font-size: 12px;
        text-align: center;
        color: var(--t-color);
        opacity: .6;
    }
}

.outer-container {
    width: 100%;
    /* 设置外部容器宽度 */
    overflow: hidden;
    /* 隐藏溢出部分 */
}

/* 定义动画 */
@keyframes scrollLeft {
    0% {
        transform: translateX(0);
    }

    100% {
        transform: translateX(calc(-100% - 10px));
        /* 加上一些额外的空白，以便滚动到最后一个元素时不会立即切换 */
    }
}

footer {
    background-color: var(--t-background-color);
    padding: 64px;
    display: flex;
    flex-direction: column;
    align-items: center;
    color: var(--t-color);

    .footer-container {
        display: flex;
        width: 100%;
        justify-content: space-between;
        max-width: 1100px;

        .logo {

            img {
                width: 100px;
            }

        }

        .item {


            .a1 {
                font-size: 20px;
            }

            p {
                margin-top: 10px;
            }
        }
    }
}

.achievement-item {
    display: flex;
    font-size: 12px;
    background: var(--t-background-color);
    border-radius: 10px;
    padding: 10px;
    align-items: center;
    white-space: nowrap;
    width: 200px;
    cursor: pointer;
    color: var(--t-color);

    .info {
        line-height: 1;
        padding: 0 10px;

        .nickname {
            color: rgb(169, 169, 202);
        }

        .platform {
            color: rgb(125, 125, 158);
        }
    }

    img {
        width: 24px;
        height: 24px;
        border-radius: 50%;
    }

    .price {
        background: var(--t-over-background-color);
        padding: 3px 10px;
        border-radius: 5px;
    }
}

.benan {
    font-size: 12px;

    a {
        color: #666;
        text-decoration: none;
    }
}

// 进入后和离开前保持原位
.fade-right-enter-to,
.fade-right-leave-from {
    opacity: 1;
    transform: none;
}

// 设置进入和离开过程中的动画时长0.5s
.fade-right-enter-active,
.fade-right-leave-active {
    transition: all 0.3s;
}

// 进入前和离开后为透明，并在右侧20px位置
.fade-right-enter-from,
.fade-right-leave-to {
    opacity: 0;
    transform: translateX(20px);
}


.logo-box {
    width: 240px;

    svg {
        display: none;
    }

    img {
        height: 30px;
    }
}

.loginbox-content {
    display: flex;
    background: var(--t-background-color);

    .left-img {
        width: 340px;
        background-size: 100% 100%;
        padding: 40px;

        img {
            width: 340px;
        }
    }

    .login {
        padding: 50px 30px 20px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        flex: 1;

        .logo {
            height: 60px;
        }

        .item {
            margin-top: 20px;
            width: 100%;

            .el-input {
                width: 100%;
                max-width: 100%;
                box-shadow: 0 0 1px rgb(239, 21, 178);
                border-radius: 10px;
                color: #fff;
            }

        }

        .forgot {
            display: flex;
            justify-content: flex-end;
            font-size: 16px;

            a {
                cursor: pointer;
            }
        }

        .types {
            display: flex;
            width: 100%;
            cursor: pointer;

            &.open-register {
                .type-item {
                    width: 50%;
                    height: 47.5px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-weight: bold;
                    border-radius: 20px;
                    color: #fff;
                    position: relative;
                    border: 1px solid rgb(239, 21, 178);

                    &.active {
                        background-color: rgb(239, 21, 178);
                        color: #fff;
                        z-index: 1;
                    }

                    &:first-child {
                        margin-left: 15px;
                    }

                    &:last-child {
                        margin-left: -30px;
                    }
                }
            }
        }

        .agree {
            display: flex;
            align-items: center;

            .el-checkbox {
                margin-right: 40px;
            }
        }

        .login-btn {
            background: rgb(239, 21, 178);
            text-align: center;
            color: #fff;
            font-size: 20px;
            padding: 10px;
            border-radius: 9px;
            width: 100%;
            box-sizing: border-box;
            border: none;
            cursor: pointer;
            margin-top: 20px;
        }

        .code {
            cursor: pointer;
            display: block;
            width: 80px;
            text-align: center;
        }

        .register-btn {
            margin-top: 50px;
            width: 220px;
            text-align: center;

            .line {
                margin: 0 10px;
            }
        }
    }
}

.sidebar-content {
    .nav-icon {
        width: 22px;
        height: 22px;
        filter: brightness(0) invert(1);
        opacity: 0.8;
        transition: all 0.3s ease;
    }

    .item {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 42px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        margin: 0 4px;
        border-radius: 8px;
        padding: 0 12px;

        .tit {
            max-width: 0;
            opacity: 0;
            overflow: hidden;
            white-space: nowrap;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin-left: 0;
        }

        &:hover, &.active {
            background: rgb(49, 64, 144);
            border-radius: 8px;
            padding: 0 16px;
            
            .nav-icon {
                opacity: 1;
                transform: scale(1.1);
                margin-right: 10px;
                margin-bottom: 0;
            }

            .tit {
                max-width: 150px;
                opacity: 1;
                margin-left: 5px;
                color: #fff;
            }
        }

        &.active::after {
            display: none;
        }
    }
}

.kaitai-main {
    width: 100%;
    display: flex;
    min-height: 100vh;
    background: var(--t-background-color);
    position: relative;
    padding-top: 124px;
    box-sizing: border-box;

    .main {
        width: 300px;
        flex: 1 1 0%;
        padding: 34px;
        background-color: var(--t-over-background-color);

        /* 垂直滚动条样式 */
        /* 宽度 */
        &::-webkit-scrollbar {
            width: 5px;
        }

        /* 水平滚动条样式 */
        /* 高度 */
        &::-webkit-scrollbar {
            height: 5px;
        }

        /* 背景色 */
        &::-webkit-scrollbar-track {
            background-color: #f5f5f5;
        }

        /* 滑块颜色 */
        &::-webkit-scrollbar-thumb {
            background-color: #666;
            border-radius: 10px;
        }
    }
}

/* ── Profile Dialog & Mobile Responsiveness ── */
.profile-dialog {
    max-width: 95%;
    border-radius: 16px !important;
    overflow: hidden;

    .el-dialog__header {
        margin-right: 0;
        padding: 20px 24px;
        border-bottom: 1px solid rgba(255,255,255,0.05);
    }

    .el-dialog__body {
        padding: 24px;
        background: var(--t-background-color);
    }
}

@media (max-width: 1024px) {
    :deep(.profile-dialog) {
        width: 90% !important;
    }
}

@media (max-width: 768px) {
    :deep(.profile-dialog) {
        width: 95% !important;
        margin-top: 5vh !important;
        
        .el-dialog__body {
            padding: 16px;
            max-height: 80vh;
            overflow-y: auto;
        }
    }

    .flex.justify-between.mt20 {
        flex-direction: column !important;
        gap: 16px;

        .coinpayu-profile {
            width: 100% !important;
            border-right: none !important;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            border-radius: 12px !important;
            padding-bottom: 20px;
        }

        .earn {
            width: 100% !important;
            border-radius: 12px !important;
        }
    }

    .white-space-nowrap {
        white-space: normal !important;
    }

    .v2-h-ttile {
        font-size: 20px !important;
        margin-top: 15px !important;
    }
}

@media (max-width: 480px) {
    .coinpayu-profile-list {
        li p { font-size: 18px !important; }
        li span { font-size: 10px !important; }
    }
}
</style>
