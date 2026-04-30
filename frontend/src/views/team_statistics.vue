<template>
    <main class="main-content">
        <div v-if="!loading">
            <div class="rewards-top">
                <div class="v2-h-title">
                    {{ $t('statistics.overview') }}
                </div>
                <ul>
                    <li><span>{{$t('statistics.completedOffers')}}</span>
                        <p>{{ statisticsData['offers'] }}</p>
                    </li>
                    <li><span>{{ $t('statistics.teamEarnings') }} / {{$t('statistics.memberEarnings')}}</span>
                        <p v-if="hb == 'coins'"><img src="../assets/coin.svg" alt="" style="width: 25px; margin-right: 5px;">
                            {{ statisticsData['teamsuccess'] }} / {{ statisticsData['membersuccess'] }}</p>
                        <p v-if="hb == 'usd'">$ {{ (statisticsData['teamsuccess'] / 100).toFixed(2) }} / $ {{ (statisticsData['membersuccess'] / 100).toFixed(2) }}</p>
                    </li>
                    <li><span>{{$t('statistics.teamDeduction')}} / {{$t('statistics.memberDeduction')}}</span>
                        <p v-if="hb == 'coins'"><img src="../assets/coin.svg" alt="" style="width: 25px; margin-right: 5px;">
                            {{ statisticsData['teamdeduction'] || 0 }} / {{ statisticsData['memberdeduction'] || 0 }}</p>
                        <p v-if="hb == 'usd'">$ {{  (statisticsData['teamdeduction'] / 100).toFixed(2) || 0 }} / $ {{ (statisticsData['memberdeduction'] / 100).toFixed(2) || 0 }}</p> 
                        
                    </li>
                    <li><span>{{$t('statistics.teamFailed')}} / {{$t('statistics.memberFailed')}}</span>
                        <p v-if="hb == 'coins'"><img src="../assets/coin.svg" alt="" style="width: 25px; margin-right: 5px;">
                            {{ statisticsData['teamfailed'] }} / {{ statisticsData['memberfailed'] }}</p>
                        <p v-if="hb == 'usd'">$ {{ (statisticsData['teamfailed']/100).toFixed(2) }} / $ {{ (statisticsData['memberfailed']/100).toFixed(2) }}</p>
                    </li>
                </ul>
            </div>
            <div class="v2-h-title flex items-center justify-content-between">
                <div class="flex items-center">
                    <el-select style="width: 150px;" v-model="query.platform_id" class="ya-search-value" clearable
                        filterable :placeholder="$t('statistics.platform')">
                        <el-option v-for="item in plaformData" :key="item.platform_id" :label="item.platform_name"
                            :value="item.platform_id" />
                    </el-select>
                    <el-select style="width: 150px;" v-model="query.reward_status" class="ya-search-value ml-2"
                        clearable filterable :placeholder="$t('statistics.status')">
                        <el-option v-for="item in rewardStatusList" :key="item.value" :label="$t('statistics.' + item.label)"
                            :value="item.value" />
                    </el-select>
                    <el-input style="width: 200px;" v-model="query.member_nickname" class="ml-2" :placeholder="$t('statistics.nickname')"
                        clearable />
                    <el-date-picker class="date-value ml-2" v-model="query.date_value" type="datetimerange"
                        :start-placeholder="$t('statistics.startDate')" :end-placeholder="$t('statistics.endDate')" value-format="YYYY-MM-DD HH:mm:ss"
                        :default-time="[new Date(2024, 1, 1, 0, 0, 0), new Date(2024, 1, 1, 23, 59, 59)]" />
                    <el-button class="ml-2" type="primary" title="Search" @click="search">{{$t('statistics.search')}}</el-button>
                    <el-button class="ml-2" type="primary" title="Search" @click="exportData">{{$t('statistics.export')}}</el-button>
                </div>
            </div>
            <div class="rewards-content">
                <el-table :data="tableData" style="width: 100%;margin-top: 20px;">
                    <el-table-column prop="platform.platform_name" :label="$t('statistics.platform')" />
                    <el-table-column prop="project_pno" :label="$t('statistics.projectNo')" width="200" />
                    <el-table-column prop="member.nickname" :label="$t('statistics.nickname')" />
                    <el-table-column prop="ip" label="Ip" />
                    <el-table-column :label="$t('statistics.teamRewardCoins')">
                        <template #default="scope">
                            <div class="money" v-if="hb == 'coins'"><img src="../assets/coin.svg" class="coin">{{ scope.row.team_payout }}
                            </div>
                            <div class="money" v-if="hb == 'usd'">${{ (scope.row.team_payout / scope.row.usd_currency_coins).toFixed(2) }}
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column :label="$t('statistics.memberRewardCoins')">
                        <template #default="scope">
                            <div class="money" v-if="hb == 'coins'"><img src="../assets/coin.svg" class="coin">{{ scope.row.member_payout }}
                            </div>
                            <div class="money" v-if="hb == 'usd'">${{ (scope.row.member_payout / scope.row.usd_currency_coins).toFixed(2) }}
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column prop="reward_status" :label="$t('statistics.status')">
                        <template #default="scope">
                            <el-text :type="scope.row.is_mark == 1 ? 'default' : 'success'" v-if="scope.row.reward_status == 1">{{$t('statistics.success')}}<el-tooltip v-if="scope.row.is_mark == 1" effect="dark" :content="$t('statistics.tooshort')" placement="top"><el-icon szie="large" style="margin-left: 5px;"><InfoFilled /></el-icon></el-tooltip></el-text>
                            <el-text type="danger" v-if="scope.row.reward_status == 2">{{ $t('statistics.failure') }}</el-text>
                            <el-text type="danger" v-if="scope.row.reward_status == 3">{{$t('statistics.overQuota')}}</el-text>
                            <el-text type="danger" v-if="scope.row.reward_status == 4">{{$t('statistics.terminate')}}</el-text>
                            <el-text type="danger" v-if="scope.row.reward_status == 5">{{ $t('statistics.unknown') }}</el-text>
                            <el-text type="danger" v-if="scope.row.reward_status == 6">{{ $t('statistics.deduction') }}</el-text>
                        </template>
                    </el-table-column>
                    <el-table-column prop="create_time" label="Time" />
                </el-table>

                <!-- Mobile Card List -->
                <div class="mobile-stat-list">
                    <div class="stat-row-card" v-for="row in tableData" :key="row.id">
                        <div class="src-platform">{{ row.platform?.platform_name }}</div>
                        <div class="src-pno">#{{ row.project_pno }}</div>
                        <div class="src-row">
                            <span class="src-label">{{ row.member?.nickname }}</span>
                            <span class="src-label" style="font-family:monospace;">{{ row.ip }}</span>
                        </div>
                        <div class="src-row">
                            <span class="src-label">{{ $t('statistics.teamRewardCoins') }}</span>
                            <span class="src-value">
                                <img v-if="hb=='coins'" src="../assets/coin.svg" style="width:13px;" />
                                {{ hb=='coins' ? row.team_payout : ('$'+(row.team_payout/row.usd_currency_coins).toFixed(2)) }}
                            </span>
                        </div>
                        <div class="src-row">
                            <span class="src-label">{{ $t('statistics.memberRewardCoins') }}</span>
                            <span class="src-value">
                                <img v-if="hb=='coins'" src="../assets/coin.svg" style="width:13px;" />
                                {{ hb=='coins' ? row.member_payout : ('$'+(row.member_payout/row.usd_currency_coins).toFixed(2)) }}
                            </span>
                        </div>
                        <div class="src-row">
                            <span class="src-label">{{ $t('statistics.time') }}</span>
                            <span class="src-label">{{ row.create_time }}</span>
                        </div>
                        <div class="src-row">
                            <span class="src-label">Status</span>
                            <span :class="['src-badge', row.reward_status == 1 ? 'success' : 'danger']">
                                {{ row.reward_status == 1 ? $t('statistics.success') : row.reward_status == 2 ? $t('statistics.failure') : row.reward_status == 3 ? $t('statistics.overQuota') : row.reward_status == 6 ? $t('statistics.deduction') : $t('statistics.terminate') }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex justify-content-end mt20">
                    <el-pagination :default-page-size="query.limit" @current-change="changeCurrent" small background
                        layout="prev, pager, next" :total="total" :current-page="query.page" class="mt-4" />
                </div>
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
    </main>
</template>
<script setup>
import { ref } from 'vue';
import { useUserStore } from '@/stores/modules/user'
import { storeToRefs } from 'pinia'
import { getPlatformList, getTeamRewards, getTeamStatistics, exportTeamRewards } from '@/api/modules/platform'
const { userInfo,hb,sys } = storeToRefs(useUserStore())
const loading = ref(false)
const tableData = ref([])
const total = ref(0)
const statisticsData = ref([])

const getUserRewards = (params) => {
    loading.value = true
    getTeamRewards(query.value).then(res => {
        tableData.value = res.data.list;
        total.value = res.data.count;
        loading.value = false
    })
    getTeamStatistics(query.value).then(res => {
        statisticsData.value = res.data
    })
}
const query = ref({
    page: 1,
    limit: 20,
    search_field: '',
    search_exp: 'like',
    date_field: 'create_time',
    sort_field: 'create_time',
    sort_value: 'desc',
    platform_id: '',
    reward_status: '',
    member_nickname: '',
})
const rewardStatusList = ref([{
    value: 6,
    label: 'deduction',
    type: 'danger',
},
{
    value: 1,
    label: 'success',
    type: 'success',
},
{
    value: 2,
    label: 'failure',
    type: 'danger',
},
{
    value: 3,
    label: 'overQuota',
    type: 'danger',
},
{
    value: 4,
    label: 'terminate',
    type: 'danger',
}
])
const plaformData = ref([])
getPlatformList().then(res => {
    plaformData.value = res.data
})
getUserRewards()
const changeCurrent = (e) => {
    query.value.page = e
    getUserRewards()
}
const search = () => {
    query.value.page = 1
    getUserRewards()
}

const exportData = () => {
    exportTeamRewards(query.value).then(res => {
        exportTeamRewards(
            {
                file_path: res.data.file_path,
                file_name: res.data.file_name
            },
            'get'
        )
    })
}
</script>
<style scoped lang="scss">
.rewards-top {
    ul {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        box-shadow: var(--t-card-shadow-color) 0px 1px 3px;
        background: var(--t-card-color);
        border-radius: 14px;
        padding: 20px 0;
        margin: 20px 0;

        li {
            padding: 12px 20px;
            border-right: 1px solid rgba(255,255,255,0.06);
            &:last-child { border-right: none; }

            > span {
                font-size: 12px;
                color: var(--t-color-1);
                text-transform: uppercase;
                letter-spacing: 0.05em;
                font-weight: 500;
            }

            p {
                font-size: 20px;
                font-weight: 700;
                color: var(--t-color);
                display: flex;
                align-items: center;
                margin: 8px 0 0;
            }
        }
    }
}

.mobile-stat-list { display: none; }
.rewards-content .el-table { display: block; }

@media (max-width: 768px) {
    .rewards-top ul {
        grid-template-columns: repeat(2, 1fr);
        li { border-right: none; border-bottom: 1px solid rgba(255,255,255,0.06); }
        li:nth-child(odd) { border-right: 1px solid rgba(255,255,255,0.06); }
        li:last-child { border-bottom: none; }
    }

    .rewards-content .el-table { display: none !important; }
    .mobile-stat-list { display: flex !important; flex-direction: column; gap: 10px; margin-top: 16px; }

    .stat-row-card {
        background: var(--t-card-color);
        border-radius: 12px;
        padding: 14px 16px;
        border: 1px solid rgba(255,255,255,0.06);
        display: flex;
        flex-direction: column;
        gap: 6px;

        .src-platform { font-size: 11px; color: var(--t-color-1); font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; }
        .src-pno { font-size: 13px; color: var(--t-color); font-family: monospace; margin-bottom: 4px; }
        .src-row { display: flex; justify-content: space-between; align-items: center; }
        .src-label { font-size: 12px; color: var(--t-color-1); }
        .src-value { font-size: 13px; font-weight: 600; color: var(--t-color); display: flex; align-items: center; gap: 4px; }
        .src-badge {
            font-size: 11px; font-weight: 700; padding: 2px 9px; border-radius: 20px;
            &.success { background: rgba(14,255,78,0.12); color: rgba(14,255,78,0.9); }
            &.danger { background: rgba(248,113,113,0.12); color: #f87171; }
        }
    }
}
</style>