<template>
    <div class="leaderboard">
        <div v-if="!rloading">
            <div class="leaderboard-top">
                <span>{{$t('leaderboard.dailyLeaderboard')}}</span>
                <div class="leaderboard-type">
                    <div class="type-item" v-for="item in leaderboardTypeList" :key="item.value"
                        :class="leaderboardType == item.value ? 'active' : ''" @click="getLeaderboard(item.value)">
                        {{ $t('leaderboard.' + item.value) }}</div>
                </div>
            </div>
            <div class="leaderboard-crown">
                <div class="leaderboardBox leaderboardTopBox">
                    <div class="userInfoContain">
                        <div class="position">1st</div>
                        <a @click="openBaseDetail(rankData[0])" href="javaScript:void(0)" class="gainProfileLink" data-gainid="374425">
                            <div class="avaContain">
                                <img :src="rankData[0] ? rankData[0].avatar_url : ava">
                            </div>
                        </a>
                        <a href="javaScript:void(0)" class="gainProfileLink" data-gainid="374425">
                            <h3 class="username truncate">{{ rankData[0] ? rankData[0].nickname : '--' }}</h3>
                        </a>
                    </div>

                    <div class="statsContain">
                        <div class="statsSection leftStatsSection">
                            <div class="statsInnerSection">
                                <h6 v-if="hb == 'coins'" class="coinsFigure">{{ rankData[0] ?
            rankData[0].total_member_payout : '--' }}</h6>
                                <h6 v-if="hb == 'usd'" class="coinsFigure">{{ rankData[0] ?
            rankData[0].usd_total_member_payout : '--' }}</h6>
                                <div class="coinsDescription">{{hb == 'usd' ? $t('statistics.usd') : $t('statistics.coins')}}</div>
                            </div>
                                
                        </div>
                        <div class="statsSection">
                            <div class="statsInnerSection">
                                <h6 class="coinsFigure">{{ rankData[0] ?
            rankData[0].total_member_offers : '--' }}</h6>
                                <div class="coinsDescription">{{$t('statistics.completedOffers')}}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="leaderboardBox leaderboardTopBox">
                    <div class="userInfoContain">
                        <div class="position">2nd</div>
                        <a @click="openBaseDetail(rankData[1])" href="javaScript:void(0)" class="gainProfileLink" data-gainid="374425">
                            <div class="avaContain">
                                <img :src="rankData[1] ? rankData[1].avatar_url : ava">
                            </div>
                        </a>
                        <a href="javaScript:void(0)" class="gainProfileLink" data-gainid="374425">
                            <h3 class="username truncate">{{ rankData[1] ? rankData[1].nickname : '--' }}</h3>
                        </a>
                    </div>

                    <div class="statsContain">
                        <div class="statsSection leftStatsSection">
                            <div class="statsInnerSection">
                                <h6 v-if="hb == 'coins'" class="coinsFigure">{{ rankData[1] ?
            rankData[1].total_member_payout : '--' }}</h6>
                                <h6 v-if="hb == 'usd'" class="coinsFigure">{{ rankData[1] ?
            rankData[1].usd_total_member_payout : '--' }}</h6>
                                <div class="coinsDescription">{{hb == 'usd' ? $t('statistics.usd') : $t('statistics.coins')}}</div>
                            </div>

                        </div>
                        <div class="statsSection">
                            <div class="statsInnerSection">
                                <h6 class="coinsFigure">{{ rankData[1] ?
            rankData[1].total_member_offers : '--' }}</h6>
                                <div class="coinsDescription">{{$t('statistics.completedOffers')}}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="leaderboardBox leaderboardTopBox">
                    <div class="userInfoContain">
                        <div class="position">3rd</div>
                        <a @click="openBaseDetail(rankData[2])" href="javaScript:void(0)" class="gainProfileLink" data-gainid="374425">
                            <div class="avaContain">
                                <img :src="rankData[2]?rankData[2].avatar_url:ava">
                            </div>
                        </a>
                        <a href="javaScript:void(0)" class="gainProfileLink" data-gainid="374425">
                            <h3 class="username truncate">{{ rankData[2] ? rankData[2].nickname : '--' }}</h3>
                        </a>
                    </div>

                    <div class="statsContain">
                        <div class="statsSection leftStatsSection">
                            <div class="statsInnerSection">
                                <h6 v-if="hb == 'coins'" class="coinsFigure">{{ rankData[2] ?
            rankData[2].total_member_payout : '--' }}</h6>
                                <h6 v-if="hb == 'usd'" class="coinsFigure">{{ rankData[2] ?
            rankData[2].usd_total_member_payout : '--' }}</h6>
                                <div class="coinsDescription">{{hb == 'usd' ? $t('statistics.usd') : $t('statistics.coins')}}</div>
                            </div>

                        </div>
                        <div class="statsSection">
                            <div class="statsInnerSection">
                                <h6 class="coinsFigure">{{ rankData[2] ?
            rankData[2].total_member_offers : '--' }}</h6>
                                <div class="coinsDescription">{{$t('statistics.completedOffers')}}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="leaderboard-table">
                <ul>
                    <li v-for="item, index in rankData" >
                        <div v-if="index > 2" class="leaderboardBox valign-wrapper">
                            <div class="userInfoContain valign-wrapper">
                                <div class="position">{{ index + 1 }}</div>
                                <div class="avaContain">
                                    <a href="javaScript:void(0)" @click="openBaseDetail(item)"><img :src="item.avatar_url"></a>
                                </div>
                                <div class="username truncate">{{ item.nickname }}</div>
                            </div>

                            <div class="statsContain">
                                <div class="statsSection">
                                    <div class="statsInnerSection">
                                        <h6 class="coinsFigure" v-if="hb == 'coins'">{{ item.total_member_payout }}</h6>
                                        <h6 class="coinsFigure" v-if="hb == 'usd'">{{
                                            item.usd_total_member_payout }}</h6>
                                        <div class="coinsDescription">{{hb == 'usd' ? $t('statistics.usd') : $t('statistics.coins')}}</div>
                                    </div>
                                </div>
                                <div class="statsSection">
                                    <div class="statsInnerSection">
                                        <h6 class="coinsFigure">{{ item.total_member_offers}}</h6>
                                        <div class="coinsDescription">{{$t('statistics.completedOffers')}}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </li>
                </ul>
            </div>
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
        <el-dialog :title="$t('user.profile')" v-model="showbaseDetail" class="profile-dialog"
            width="1000px">
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
                            <span>{{ $t('user.earned') }}</span>
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
            <el-table :data="baseUserInfo.rewards" style="width: 100%;margin-bottom:20px;">
                <el-table-column prop="platform.platform_name" :label="$t('statistics.platform')" />
                <el-table-column prop="project_pno" :label="$t('statistics.projectNo')" />
                <el-table-column prop="member_payout" :label="$t('statistics.rewardCoins') ">
                    <template #default="scope">
                        <div class="money" v-if="hb == 'coins'"><img src="../assets/coin.svg" class="coin">{{ scope.row.member_payout }}
                        </div>
                        <div class="money" v-if="hb == 'usd'">${{ (scope.row.member_payout / scope.row.usd_currency_coins).toFixed(2) }}
                        </div>
                    </template>
                </el-table-column>
                <el-table-column prop="create_time" :label="$t('statistics.time')" />
            </el-table>
        </el-dialog>
    </div>
</template>
<script setup>
import { computed, ref,nextTick } from 'vue'
import { useRoute, useRouter } from 'vue-router';
import { getRanking } from '@/api/modules/platform';
import { getBaseDetail } from '@/api/modules/user';
import * as echarts from 'echarts';
import ava from '@/assets/ava.png'

import { useUserStore } from '@/stores/modules/user'
import { storeToRefs } from 'pinia'
const { hb } = storeToRefs(useUserStore())
const route = useRoute()
const router = useRouter()
const leaderboardType = ref('daily')
const leaderboardTypeList = [{
    value: 'daily', label: 'Daily', key: 'today'
}, { value: 'weekly', label: 'Weekly', key: 'this week' }, { value: 'monthly', label: 'Monthly', key: 'this month' }]
const getLeaderboard = (type) => {
    router.push({ name: type })
}
const leaderboardkey = computed(() => {
    return leaderboardTypeList.find(item => item.value === leaderboardType.value).key
})
const rankData = ref([])
const rloading = ref(true)
getRanking({ type: leaderboardType.value }).then(res => {
    rankData.value = res.data
    rloading.value = false
})
const showbaseDetail = ref(false)
const baseUserInfo = ref({})
const sevenDom = ref(null);
const loading = ref(false)
const openBaseDetail = (item) => {
    if(!item) return
    showbaseDetail.value = true
    baseUserInfo.value = {}
    loading.value = true
    getBaseDetail(item).then(res => {
        baseUserInfo.value = res.data
        nextTick(() => {
            const sevenInstance = echarts.init(sevenDom.value);
            let sevenoption = {
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
                    type: 'line',
                    data: hb.value == 'coins' ? Object.values(res.data.seven_data) : Object.values(res.data.usd_seven_data),
                    smooth: true,
                    itemStyle: {
                        color: '#ffb535',
                        borderWidth: 2,
                        borderType: 'solid',
                        borderColor: '#fff',
                        lineStyle: {
                            type: 'solid',
                            width: 2,
                            color: '#ffb535'
                        },
                        areaStyle: {
                            color: '#ffb535'
                        },
                        symbol: 'circle',
                        symbolSize: 10
                    }
                }]
            };
            sevenInstance.setOption(sevenoption);
            loading.value = false
        })
        
    })
}
window.scrollTo(0, 0, 'smooth');
</script>

<style scoped lang="scss">
.leaderboard {
    max-width: 1000px;
    width: 95%;
    margin: 20px auto;

    .leaderboard-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;

        >span {
            color: var(--t-color-1);
            font-size: 25px;
            font-weight: 500;
        }

        .leaderboard-type {
            display: flex;
            overflow-x: auto;
            padding-bottom: 5px;
            -webkit-overflow-scrolling: touch;
            &::-webkit-scrollbar {
                height: 0px;
            }
        }

        .type-item {
            cursor: pointer;
            flex-shrink: 0;
            width: 100px;
            text-align: center;
            height: 38px;
            line-height: 38px;
            border: 1px solid rgba(113, 105, 93, 0.4);
            font-size: 14px;
            border-radius: 35px;
            margin-right: 8px;
            color: #988f82;
            transition: all 0.3s;

            &.active {
                border-color: var(--main-color);
                color: var(--main-color);
                background: rgba(var(--main-color-rgb), 0.1);
            }
        }
    }
}

.leaderboard-table {
    margin-top: 10px;
    .leaderboardBox {
        background-color: var(--t-card-color);
        border-radius: 16px;
        min-height: 80px;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        padding: 10px;
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .userInfoContain {
        flex: 1;
        display: flex;
        align-items: center;
        min-width: 0;

        .position {
            min-width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            font-weight: 600;
            font-size: 12px;
            margin-left: 5px;
            background-color: var(--t-background-color);
            color: #cecac3;
        }

        .avaContain {
            margin-left: 12px;
            flex-shrink: 0;

            img {
                height: 48px;
                width: 48px;
                border-radius: 50%;
                object-fit: cover;
                border: 2px solid rgba(255, 255, 255, 0.1);
            }
        }

        .username {
            font-size: 16px;
            font-weight: 500;
            margin: 0;
            margin-left: 12px;
            color: var(--t-color-1);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    }

    .statsContain {
        display: flex;
        gap: 15px;
        margin-right: 10px;

        .statsSection {
            text-align: center;

            .coinsFigure {
                color: var(--t-color-1);
                font-size: 16px;
                font-weight: 600;
                margin: 0;
            }

            .coinsDescription {
                color: #888;
                font-size: 11px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
        }
    }
}

.leaderboard-crown {
    display: flex;
    justify-content: space-between;
    align-items: stretch;
    margin: 30px 0;
    gap: 15px;

    .leaderboardBox {
        background-color: var(--t-card-color);
        flex: 1;
        border-radius: 20px;
        padding-bottom: 20px;
        border: 1px solid rgba(255, 255, 255, 0.05);
        display: flex;
        flex-direction: column;
        position: relative;
        overflow: hidden;

        .position {
            background-color: var(--t-background-color);
            color: #69a5ed;
            border-radius: 6px;
            padding: 4px 10px;
            font-weight: 700;
            font-size: 12px;
            position: absolute;
            top: 15px;
            left: 15px;
            z-index: 1;
        }

        .avaContain {
            text-align: center;
            padding-top: 40px;
            margin-bottom: 10px;

            img {
                height: 80px;
                width: 80px;
                border-radius: 50%;
                border: 3px solid rgba(255, 255, 255, 0.1);
                box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            }
        }

        .username {
            text-align: center;
            font-size: 18px;
            font-weight: 600;
            margin: 5px 10px 15px;
            color: var(--t-color-1);
        }

        .statsContain {
            display: flex;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            margin-top: auto;
        }

        .leftStatsSection {
            border-right: 1px solid rgba(255, 255, 255, 0.05);
        }

        .statsSection {
            flex: 1;
            padding: 15px 5px;
            text-align: center;
        }

        .coinsFigure {
            font-size: 18px;
            color: var(--t-color-1);
            font-weight: 700;
            margin: 0;
        }

        .coinsDescription {
            color: #888;
            font-size: 10px;
            text-transform: uppercase;
            margin-top: 4px;
        }
    }
}

@media (max-width: 768px) {
    .leaderboard {
        margin: 10px auto;
        width: 92%;
    }

    .leaderboard-top {
        flex-direction: column;
        align-items: flex-start !important;
        
        >span {
            font-size: 20px;
        }
    }

    .leaderboard-crown {
        flex-direction: column;
        
        .leaderboardBox {
            width: 100%;
            flex: none;
            
            .avaContain img {
                height: 70px;
                width: 70px;
            }
        }
    }

    .leaderboard-table {
        .leaderboardBox {
            flex-direction: column;
            align-items: flex-start;
            padding: 15px;
            height: auto;
        }

        .userInfoContain {
            width: 100%;
            margin-bottom: 15px;
        }

        .statsContain {
            width: 100%;
            justify-content: space-around;
            margin-right: 0;
            padding-top: 10px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }
    }
}

@media (max-width: 768px) {
    /* Profile Dialog Mobile Overrides */
    :deep(.profile-dialog) {
        width: 95% !important;
        margin-top: 5vh !important;
        
        .el-dialog__body {
            padding: 16px;
            max-height: 80vh;
            overflow-y: auto;
        }
    }

    .leaderboard .flex.justify-between.mt20 {
        flex-direction: column !important;
        gap: 16px;

        .coinpayu-profile {
            width: 100% !important;
            border-right: none !important;
            border-radius: 12px !important;
            padding-bottom: 20px;
        }

        .earn {
            width: 100% !important;
            border-radius: 12px !important;
        }
    }

    .v2-h-ttile {
        font-size: 20px !important;
        margin-top: 15px !important;
    }
}
</style>