<template>
    <div class="v2-dashboard-main-section v2-max1000">
        <div class="v2-h-title">{{ $t('user.profile') }}</div>
        <div class="flex justify-between mt20">
            <div class="coinpayu-profile">
                <div class="imgbox">
                    <el-avatar :size="100" fit="cover" :src="userInfo.avatar_url || ava" style="margin-right: 10px;" />
                    <el-upload ref="uploadRef" class="upload-demo" action="#" @change="handleUploadChange"
                        :auto-upload="false" :show-file-list="false" accept=".png,.jpg,.jpeg">
                        <template #trigger>
                            <div class="file">
                                <img src="../assets/photo.svg" style="width: 16px;height: 16px;" />
                            </div>
                        </template>
                    </el-upload>
                </div>
                <p style="font-size: 20px;text-align: center;color:var(--t-color-1)">{{ userInfo.nickname }}</p>
                <ul class="coinpayu-profile-list">
                    <li>
                        <p>{{ userInfo.completed_offers }}</p>
                        <span>{{ $t('statistics.completedOffers') }}</span>
                    </li>
                    <li v-if="hb == 'coins'">
                        <p>{{ userInfo.earned }}</p>
                        <span>{{ $t('user.coinsEarned') }}</span>
                    </li>
                    <li v-if="hb == 'usd'">
                        <p>{{ userInfo.usd_earned.member_payout }}</p>
                        <span>{{ $t('user.usdEarned') }}</span>
                    </li>
                </ul>
            </div>
            <div class="earn">
                <div style="border-bottom: 1px solid var(--t-background-color);display: flex;justify-content: space-between;
    padding: 20px 20px;"><span style="color:var(--t-color-1);font-size: 18px;">{{$t('user.earnings')}}</span><span style="font-size: 12px;color:var(--t-color-1)">{{$t('user.last7Days')}}</span></div>

                <div ref="sevenDom" style="width:100%;height:300px;"></div>
            </div>
        </div>
        <div class="flex justify-between">
            <div class="info">
                <div class="v2-h-title">{{ $t('user.yourInfo') }}</div>
                <ul>
                    <li>
                        <p>{{ $t('user.memberId') }}</p>
                        <span>{{ userInfo.member_id }}</span>
                    </li>
                    <li>
                        <p>{{ $t('user.group') }}</p>
                        <span>{{ userInfo.group_names }}</span>
                    </li>
                    <li>
                        <p>{{ $t('user.team') }}</p>
                        <span>{{ userInfo.team_name }}</span>
                    </li>
                    <li>
                        <p>{{ $t('user.loginIp') }}</p>
                        <span>{{ userInfo.login_ip }}</span>
                    </li>
                    <li>
                        <p>{{ $t('user.loginRegion') }}</p>
                        <span>{{ userInfo.login_region }}</span>
                    </li>
                    <li>
                        <p>{{ $t('user.email') }}</p>
                        <span>{{ userInfo.email || '--' }}</span>
                    </li>
                </ul>
            </div>
            <div class="setting">
                <div class="v2-h-title">{{ $t('user.settings') }}</div>
                <p style="color: var(--t-color-1);font-size: 20px;margin-top: 25px;">{{ $t('user.changeDisplayName') }}</p>
                <p style="color: var(--t-color-1);font-size: 14px;margin-top: 20px;">{{ $t('user.editDisplayName') }}:</p>
                <div class="flex mt20"><el-input v-model="nickname" size="large" style="width: 210px;margin-right: 10px;" /><el-button type="primary"
                        @click="change">{{ $t('user.change') }}</el-button></div>
            </div>
        </div>
    </div>
</template>
<script setup>
import ava from '@/assets/ava.png'
import { handleError, ref,watch,nextTick } from 'vue'
import { useUserStore } from '@/stores/modules/user'
import { avatar, changeNickname, getUserInfo } from '@/api/modules/user'
import { storeToRefs } from 'pinia'
import { ElMessageBox, ElMessage } from 'element-plus'
import * as echarts from 'echarts';
const { userInfo,sys,hb } = storeToRefs(useUserStore())
const userRewards = ref([])
const usertotal = ref(0)
const currentPage = ref(1)
const uploadRef = ref()
const handleUploadChange = (file) => {
    const formData = new FormData();
    formData.append('file', file.raw);
    avatar(formData).then(res => {
        userInfo.value.avatar_url = res.data.file_url;
    })
}
watch(hb, () => {
    getUserData()
})

const nickname = ref()
const sevenDom = ref(null);
let sevenInstance;
nextTick(() => { 
    sevenInstance = echarts.init(sevenDom.value);
})
const getUserData = () => {
    getUserInfo().then(res => {
    userInfo.value = res.data
    nickname.value = res.data.nickname
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
            data: hb.value =='coins' ? Object.keys(res.data.seven_data) : Object.keys(res.data.usd_seven_data),
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
                    color: '#ebebeb'
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
            name: hb.value =='coins'? 'complete coins' : 'complete usd',
            type: 'line', // 这里可以是'line'、'bar'、'pie'等，根据图表类型选择
            data: hb.value =='coins' ? Object.values(res.data.seven_data) : Object.values(res.data.usd_seven_data),
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
})
}
getUserData()
const change = () => {
    changeNickname({ nickname: nickname.value }).then(res => {
        userInfo.value.nickname = nickname.value;
    })
}
</script>
<style scoped lang="scss">
/* ── Profile Card ── */
.coinpayu-profile {
    background: var(--t-card-color);
    box-shadow: 0 2px 5px var(--t-card-shadow-color);
    overflow: hidden;
    width: 42%;
    border-right: 1px solid rgba(255,255,255,0.08);
    border-radius: 12px 0 0 12px;

    .imgbox {
        width: 100px;
        height: 100px;
        position: relative;
        margin: 34px auto 20px;

        .file {
            width: 28px;
            height: 28px;
            background: var(--el-color-primary);
            border-radius: 50%;
            overflow: hidden;
            position: absolute;
            bottom: 0;
            right: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: opacity 0.2s;
            &:hover { opacity: 0.85; }
        }
    }
}

.coinpayu-profile-list {
    display: flex;
    padding: 20px 0 0;
    margin-top: 30px;
    border-top: 1px solid rgba(255,255,255,0.07);

    li {
        flex: 1;
        text-align: center;
        padding: 0;

        p {
            font-size: 22px;
            font-weight: 700;
            color: var(--t-color);
            margin-bottom: 6px;
        }

        span {
            font-size: 12px;
            color: var(--t-color-2);
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
    }
}

/* ── Earnings Chart ── */
.earn {
    background: var(--t-card-color);
    box-shadow: 0 2px 5px var(--t-card-shadow-color);
    overflow: hidden;
    flex: 1;
    border-radius: 0 12px 12px 0;
}

/* ── Info Section ── */
.info {
    background: var(--t-card-color);
    box-shadow: 0 2px 5px var(--t-card-shadow-color);
    overflow: hidden;
    padding: 24px 32px;
    flex: 1;
    margin-top: 20px;
    margin-right: 1%;
    border-radius: 12px;

    ul {
        display: flex;
        flex-wrap: wrap;
        margin-top: 16px;
        gap: 4px 0;

        li {
            width: 50%;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255,255,255,0.05);

            p {
                font-size: 11px;
                color: var(--t-color-1);
                text-transform: uppercase;
                letter-spacing: 0.05em;
                font-weight: 600;
                margin-bottom: 4px;
            }

            span {
                font-size: 15px;
                color: var(--t-color);
                display: block;
                font-weight: 500;
            }
        }
    }
}

/* ── Settings Section ── */
.setting {
    background: var(--t-card-color);
    box-shadow: 0 2px 5px var(--t-card-shadow-color);
    overflow: hidden;
    padding: 24px 32px;
    flex: 1;
    margin-top: 20px;
    margin-left: 1%;
    border-radius: 12px;

    .flex.mt20 {
        flex-wrap: wrap;
        gap: 10px;
    }
}

/* ── Mobile ── */
@media (max-width: 768px) {
    .v2-dashboard-main-section {
        padding: 10px;
    }

    .v2-h-title {
        font-size: 20px;
        margin-bottom: 15px;
    }

    /* Top section: profile + chart stack */
    .flex.justify-between.mt20 {
        flex-direction: column !important;
        gap: 16px;
        margin-top: 10px;

        .coinpayu-profile {
            width: 100% !important;
            border-right: none !important;
            border-radius: 12px !important;
            padding-bottom: 10px;
            box-sizing: border-box;
        }

        .earn {
            width: 100%;
            border-radius: 12px !important;
            box-sizing: border-box;
        }
    }

    /* Bottom section: info + settings stack */
    .flex.justify-between:not(.mt20) {
        flex-direction: column !important;
        gap: 16px;
        margin-top: 16px;

        .info {
            width: 100%;
            margin-right: 0 !important;
            margin-top: 0;
            border-radius: 12px !important;
            padding: 20px !important;
            box-sizing: border-box;

            ul {
                margin-top: 10px;
                li { 
                    width: 100%; 
                    padding: 10px 0;
                }
            }
        }

        .setting {
            width: 100%;
            margin-left: 0 !important;
            margin-top: 0;
            border-radius: 12px !important;
            padding: 20px !important;
            box-sizing: border-box;

            /* Nickname input + button full width */
            .flex.mt20 {
                flex-direction: column;
                width: 100%;
                .el-input { width: 100% !important; margin-right: 0 !important; }
                .el-button { width: 100%; margin-top: 5px; }
            }
        }
    }

    /* Profile stat list — compact */
    .coinpayu-profile-list {
        margin-top: 16px;
        padding-top: 15px;
        li p { font-size: 18px; }
        li span { font-size: 10px; }
    }

    .imgbox {
        margin: 20px auto 10px !important;
    }
}

@media (max-width: 480px) {
    .info ul li { width: 100%; }
    .v2-h-title { font-size: 18px; }
}
</style>