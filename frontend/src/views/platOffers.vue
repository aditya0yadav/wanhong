<template>
    <main class="main-content">
        <div v-if="!loading">
            <div class="plat-toolbar">
                <div class="plat-toolbar-top">
                    <div class="plat-title">{{ (route.query.name + ' Offers').toUpperCase() }}</div>
                    <div class="sort-wrap" @click="showOrder = !showOrder">
                        <el-icon>
                            <Star />
                        </el-icon>
                        <span>{{ $t('offers.' + getOrderName) }}</span>
                        <el-icon>
                            <ArrowUpBold v-if="showOrder" />
                            <ArrowDownBold v-else />
                        </el-icon>
                        <div class="orders" v-if="showOrder" @click.stop>
                            <div v-for="item in orders" class="order-item" @click="getDataByOrder(item)">
                                <component class="icons" :is="item.icon"></component>
                                <span class="tit">{{ $t('offers.' + item.name) }}</span>
                                <el-icon size="20" v-if="order == item.id" style="color:rgba(14,255,78,0.85);">
                                    <SuccessFilled />
                                </el-icon>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="plat-toolbar-search">
                    <el-input class="search" v-model="query.code" size="large"
                        :placeholder="$t('offers.countryCode')" />
                    <el-input class="search" v-model="query.project_pno" size="large" :placeholder="$t('offers.pid')" />
                    <el-input class="search" v-model="query.project_name" size="large"
                        :placeholder="lang == 'en' ? 'Project name' : '项目名称'" />
                    <el-button type="primary" size="large" @click="getData">{{ $t('statistics.search') }}</el-button>
                </div>
            </div>
            <div class="regional_table">
                <!-- Desktop Table -->
                <div class="desktop-table">
                    <el-table :data="tableData" style="width: 100%;margin-bottom:20px;">
                        <el-table-column prop="project_pno" :label="$t('offers.pid')" width="170" />
                        <el-table-column v-if="showName" prop="project_name" :label="$t('offers.name')" />
                        <el-table-column prop="project_code" :label="$t('offers.country')" width="100"
                            show-overflow-tooltip />
                        <el-table-column v-if="showLoi" prop="project_loi" label="Loi" width="80" />
                        <el-table-column v-if="showIr" prop="project_ir" label="Ir" width="80" />
                        <el-table-column prop="project_quota" min-width="130" label="Complete/Quota" v-if="showQuota">
                            <template #default="scope">
                                {{ scope.row.project_complete }} / {{ scope.row.project_quota }}
                            </template>
                        </el-table-column>
                        <el-table-column v-for="item in customParams" :label="item">
                            <template #default="scope">
                                {{scope.row['project_params'].filter(param => param.name == item)[0].value}}
                            </template>
                        </el-table-column>
                        <el-table-column prop="project_cpi" :label="$t('offers.rewardCoins')">
                            <template #default="scope">
                                <div class="money" v-if="hb == 'coins'"><img src="../assets/coin.svg" class="coin">{{
                                    scope.row.project_cpi }}</div>
                                <div class="money" v-if="hb == 'usd'">$ {{ (scope.row.project_cpi /
                                    sys.usd_rate).toFixed(2) }}</div>
                            </template>
                        </el-table-column>
                        <el-table-column prop="create_time" :label="$t('offers.createTime')" />
                        <el-table-column prop="update_time" :label="$t('offers.updateTime')" />
                        <el-table-column :label="$t('offers.operate')" width="240">
                            <template #default="scope">
                                <div style="display:flex;align-items:center;">
                                    <el-dropdown @command="handleMarkCommand" placement="bottom-start" trigger="click">
                                        <a href="javascript:;" style="margin-right:10px;font-size:14px;">{{
                                            $t('offers.mark') }}</a>
                                        <template #dropdown>
                                            <el-dropdown-menu>
                                                <el-dropdown-item :command="{ type: 'view', ...scope.row }">{{ lang ==
                                                    'en' ? 'View' : '查看' }}</el-dropdown-item>
                                                <el-dropdown-item :command="{ type: 'edit', ...scope.row }">{{ lang ==
                                                    'en' ? 'Edit' : '编辑' }}</el-dropdown-item>
                                            </el-dropdown-menu>
                                        </template>
                                    </el-dropdown>
                                    <a href="javascript:;" v-if="scope.row.platform.is_quota == 1"
                                        @click="toQuota(scope.row)" style="margin-right:10px;font-size:14px;">{{
                                        $t('offers.quota') }}</a>
                                    <a href="javascript:;" @click="start(scope.row)"
                                        style="margin-right:10px;font-size:14px;">{{ $t('offers.start')
                                        }}</a>
                                    <a href="javascript:;" @click="copy(scope.row)"
                                        style="margin-right:10px;font-size:14px;">{{ $t('offers.copy')
                                        }}</a>
                                </div>
                            </template>
                        </el-table-column>
                    </el-table>
                </div>

                <!-- Mobile Card List -->
                <div class="mobile-offer-list">
                    <div class="offer-card" v-for="row in tableData" :key="row.project_pno">
                        <div class="offer-card-header">
                            <div class="offer-card-pid">#{{ row.project_pno }}</div>
                            <div class="offer-card-reward">
                                <template v-if="hb == 'coins'">
                                    <img src="../assets/coin.svg" style="width:14px;margin-right:3px;" />{{
                                    row.project_cpi }}
                                </template>
                                <template v-else>$ {{ (row.project_cpi / sys.usd_rate).toFixed(2) }}</template>
                            </div>
                        </div>
                        <div class="offer-card-name" v-if="showName">{{ row.project_name }}</div>
                        <div class="offer-card-meta">
                            <span class="meta-chip">🌍 {{ row.project_code }}</span>
                            <span class="meta-chip" v-if="showLoi">LOI: {{ row.project_loi }}</span>
                            <span class="meta-chip" v-if="showIr">IR: {{ row.project_ir }}</span>
                            <span class="meta-chip" v-if="showQuota">{{ row.project_complete }}/{{ row.project_quota
                                }}</span>
                        </div>
                        <div class="offer-card-actions">
                            <button class="oc-btn" @click="start(row)">{{ $t('offers.start') }}</button>
                            <button class="oc-btn secondary" @click="copy(row)">{{ $t('offers.copy') }}</button>
                            <button class="oc-btn secondary" v-if="row.platform.is_quota == 1" @click="toQuota(row)">{{
                                $t('offers.quota') }}</button>
                        </div>
                    </div>
                </div>

                <div class="flex justify-content-end">
                    <el-pagination :default-page-size="query.limit" @current-change="changeCurrent" small background
                        layout="prev, pager, next" :current-page="query.page" :total="total" class="mt-4" />
                </div>
            </div>
            <el-dialog :title="quotaTitle" :close-on-press-escape="false" :close-on-click-modal="false" class="offerbox quota-dialog"
                v-model="quotaVisible" width="900" align-center>

                <div style="height:3px;">
                    <el-progress stroke-width="3" v-if="quotaLoading" :percentage="80" :indeterminate="true"
                        :show-text="false" />
                </div>

                <!-- Structured View -->
                <div v-if="quotaType == 'structured' && quotaVisible" class="quota-container">
                    <div class="quota-scroll-area">

                        <!-- Stat Cards -->
                        <div class="stat-row">
                            <div class="stat-card">
                                <div class="stat-label">{{ lang === 'en' ? 'Completed' : '已完成' }}</div>
                                <div class="stat-value">{{ structuredQuota?.complete ?? 0 }}</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-label">{{ lang === 'en' ? 'Total Quota' : '总配额' }}</div>
                                <div class="stat-value">{{ structuredQuota?.total ?? 0 }}</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-label">{{ lang === 'en' ? 'Remaining' : '剩余' }}</div>
                                <div class="stat-value accent">{{ structuredQuota?.remain ?? 0 }}</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-label">{{ lang === 'en' ? 'Reward' : '奖励' }}</div>
                                <div class="stat-value positive">
                                    <div v-if="hb == 'coins'"
                                        style="display:flex;align-items:center;justify-content: center;">
                                        <img src="../assets/coin.svg" style="width:20px;margin-right:5px;">{{
                                        structuredQuota?.surveyCPI }}
                                    </div>
                                    <div v-if="hb == 'usd'">$ {{ structuredQuota?.surveyCPI }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Meta Bar -->
                        <div class="meta-bar" v-if="structuredQuota?.surveyID">
                            <div class="meta-tag">ID: {{ structuredQuota.surveyID }}</div>
                            <div class="meta-desc" v-if="structuredQuota?.projectBrief">{{ structuredQuota.projectBrief
                                }}</div>
                        </div>

                        <!-- Progress -->
                        <div class="progress-section">
                            <div class="section-label">{{ lang === 'en' ? 'Overall Progress' : '总进度' }}</div>
                            <div class="progress-info-row">
                                <span>{{ structuredQuota?.complete ?? 0 }} {{ lang === 'en' ? 'of' : '/' }} {{
                                    structuredQuota?.total ?? 0 }} {{ lang === 'en' ? 'completes' : '完成' }}</span>
                                <span class="pct">{{ Math.round(((structuredQuota?.complete ?? 0) /
                                    (structuredQuota?.total || 1)) *
                                    100) }}%</span>
                            </div>
                            <div class="progress-track">
                                <div class="progress-bar"
                                    :style="{ width: Math.round(((structuredQuota?.complete ?? 0) / (structuredQuota?.total || 1)) * 100) + '%' }">
                                </div>
                            </div>
                        </div>

                        <!-- Two Column: Targeting + Quota Table -->
                        <div class="two-col">
                            <!-- Targeting Criteria -->
                            <div v-if="structuredQuota?.qualifications?.length">
                                <div class="section-label">{{ lang === 'en' ? 'Targeting Criteria' : '定向要求' }}</div>
                                <div class="panel">
                                    <div class="criteria-list">
                                        <div v-for="q in structuredQuota.qualifications" :key="q.category"
                                            class="criteria-item">
                                            <div class="c-key">{{ q.category }}</div>
                                            <div class="c-val">{{ q.value }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Quota Breakdown -->
                            <div v-if="structuredQuota?.quotaBuckets?.length">
                                <div class="section-label">{{ lang === 'en' ? 'Quota Breakdown' : '配额类别' }}</div>
                                <div class="panel">
                                    <table class="quota-table">
                                        <thead>
                                            <tr>
                                                <th>{{ lang === 'en' ? 'Category' : '类别' }}</th>
                                                <th>{{ lang === 'en' ? 'Values' : '允许值' }}</th>
                                                <th style="text-align:right;">{{ lang === 'en' ? 'Left' : '剩余' }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(item, i) in structuredQuota.quotaBuckets" :key="i">
                                                <td>{{ item.category }}</td>
                                                <td>{{ item.value }}</td>
                                                <td style="text-align:right;">
                                                    <span
                                                        :class="item.remaining == 0 ? 'remaining-zero' : 'remaining-ok'">
                                                        {{ item.remaining }}
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div v-if="!structuredQuota?.quotaBuckets?.length && !structuredQuota?.qualifications?.length"
                            class="empty-notice">
                            {{ lang === 'en' ? 'No additional requirements for this survey.' : '该问卷暂无额外要求。' }}
                        </div>

                    </div>
                </div>

                <!-- HTML View -->
                <div v-if="quotaType == 'html' && quotaVisible" class="quota-html-body">
                    <div class="html" v-html="quota"></div>
                </div>

                <!-- Iframe View -->
                <iframe class="quota-iframe" v-if="quotaType == 'link' && quotaVisible" :src="quota"
                    style="height:550px;border:0;margin:0;width:100%;display:block;">
                </iframe>

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
import { ref, computed, nextTick } from 'vue'
import { getOffers, getOfferLink, getQuota, getMarkDetailList, getMarkList, markProject } from '@/api/modules/platform'
import { useRoute, useRouter } from 'vue-router'
import { useUserStore } from '@/stores/modules/user'
import { storeToRefs } from 'pinia'
import { ElMessage, ElMessageBox } from 'element-plus'
import { copyText } from '@/api/helper/clipboard'
import { useVisitorData } from '@fingerprint/vue';

const { isLoading, getData, data, error: fingerprintError } = useVisitorData({ immediate: false });
const { token, showLogin, userInfo, hb, sys, lang } = storeToRefs(useUserStore())
const route = useRoute()
const router = useRouter()
const tableData = ref([])
const total = ref(0)
const query = ref({
    page: 1,
    limit: 20,
    search_field: 'project_name',
    search_exp: 'like',
    date_field: 'create_time',
    platform_id: route.params.id,
    code: '',
    project_pno: '',
    project_name: '',
    sort_field: 'update_time',
    sort_value: 'desc'
})
const customParams = ref([])
const search = ref(null);
const page = ref(1);
const order = ref(1);
const orders = [{
    name: 'recentUpdate',
    field: 'update_time',
    value: 'desc',
    icon: 'Star',
    id: 1
}, {
    name: 'recentCreate',
    field: 'create_time',
    value: 'desc',
    icon: 'Star',
    id: 2
}, {
    name: 'highestReward',
    field: 'project_cpi',
    value: 'desc',
    icon: 'Bottom',
    id: 3
}, {
    name: 'lowestReward',
    field: 'project_cpi',
    value: 'asc',
    icon: 'Top',
    id: 4
}]
const showOrder = ref(false)
const getOrderName = computed(() => {
    return orders.filter(item => item.id == order.value)[0].name;
})
const getOrderValue = computed(() => {
    return orders.filter(item => item.id == order.value)[0].value;
})

const getDataByOrder = (item) => {
    query.value.sort_field = item.field;
    query.value.sort_value = item.value;
    order.value = item.id;
    fetchOffers();
    showOrder.value = false;
}
const changeCurrent = (e) => {
    tableData.value = [];
    query.value.page = e
    fetchOffers();
}
const customFields = ref([]);
const quota = ref(null);
const quotaVisible = ref(false)
const quotaType = ref('')
const quotaLoading = ref(false);
const quotaTitle = ref('');
const structuredQuota = ref(null);

const fetchProxyQuota = (item) => {
    getQuota({ project_pno: item.project_pno }).then(res => {
        const data = res.data || {};
        quotaLoading.value = false;

        // Reset state
        structuredQuota.value = null;
        quota.value = '';

        // 1. Process Structured Data
        let surveyInfoArray = null;
        let isZamplia = false;
        let zampliaPayload = null;

        // Detect Zamplia structure (often returned directly with TotalQuota, Quotas, or result.Quotas)
        if (data.TotalQuota !== undefined || Array.isArray(data.Quotas) || (data.result && Array.isArray(data.result.Quotas))) {
            isZamplia = true;
            zampliaPayload = data.result || data;
        } else if (Array.isArray(data.surveyInfo) && data.surveyInfo.length > 0) {
            surveyInfoArray = data.surveyInfo;
        } else if (data.surveyInfo && Array.isArray(data.surveyInfo.surveyInfo) && data.surveyInfo.surveyInfo.length > 0) {
            surveyInfoArray = data.surveyInfo.surveyInfo;
        }

        if (isZamplia && zampliaPayload) {
            // Process Zamplia payload structurally
            const raw = zampliaPayload;
            const total = raw.TotalQuota ?? 0;
            const complete = raw.Completes ?? 0;
            const processed = {
                total,
                complete,
                remain: total - complete,
                surveyCPI: hb.value == 'usd' ? (item.project_cpi / sys.value.usd_rate).toFixed(2) : item.project_cpi,
                surveyID: raw.SurveyId ?? item.project_pno,
                projectBrief: raw.Name ?? raw.ProjectName ?? '',
                quotaBuckets: [],
                qualifications: []
            };

            // Zamplia Quota Breakdown
            const quotas = raw.Quotas || [];
            quotas.forEach(q => {
                const category = q.Name || 'Target Group';
                const totalQ = q.TotalQuota || 0;
                const compQ = q.Completes || 0;
                const remaining = totalQ - compQ;

                let optionTexts = '-';
                if (q.Conditions && Array.isArray(q.Conditions)) {
                    optionTexts = q.Conditions.map(c => `[${c.ConditionId}]`).join(', ');
                }

                processed.quotaBuckets.push({
                    category,
                    value: optionTexts,
                    remaining
                });
            });

            // Zamplia Qualifications (if available)
            const qualifications = raw.Qualifications || [];
            qualifications.forEach(q => {
                const category = q.Name || `QID: ${q.QuestionId}`;
                const value = Array.isArray(q.ValidOptions) ? q.ValidOptions.join(', ') : (q.Value || '-');
                processed.qualifications.push({
                    category,
                    value
                });
            });

            structuredQuota.value = processed;

        } else if (surveyInfoArray) {
            // Process original GoWebSurveys / legacy payload
            const raw = surveyInfoArray[0];
            const total = raw.surveyTargetCount ?? raw.TotalQuota ?? 0;
            const complete = raw.allocatedQuota ?? raw.Complete ?? 0;
            const processed = {
                total,
                complete,
                remain: total - complete,
                surveyCPI: hb.value == 'usd' ? (item.project_cpi / sys.value.usd_rate).toFixed(2) : item.project_cpi,
                surveyID: raw.surveyID,
                projectBrief: raw.projectBrief,
                quotaBuckets: [],
                qualifications: []
            };

            const quotaDetails = raw.Quota || {};
            Object.values(quotaDetails).forEach(q => {
                if (q.conditions && Array.isArray(q.conditions)) {
                    const category = q.conditions[0]?.profileQuestionKey || 'Other';
                    const optionTexts = q.conditions
                        .map(c => {
                            if (c.OptionText && c.OptionText.trim() !== '') return c.OptionText;
                            if (c.min !== null && c.max !== null && c.min !== undefined && c.max !== undefined) return `${c.min} - ${c.max}`;
                            if (c.min !== null && c.min !== undefined) return `≥ ${c.min}`;
                            if (c.max !== null && c.max !== undefined) return `≤ ${c.max}`;
                            return null;
                        })
                        .filter(t => t !== null)
                        .join(', ') || '-';
                    const remaining = q.remainingQuota ? q.remainingQuota[0] : '-';
                    processed.quotaBuckets.push({ category, value: optionTexts, remaining });
                }
            });

            // 1.1 Process Qualifications / Targeting from Backend (if available)
            const qualData = data.qualData || {};
            const targeting = qualData.targeting || {};
            Object.entries(targeting).forEach(([key, options]) => {
                if (Array.isArray(options)) {
                    const value = options.map(o => {
                        if (o.OptionText && o.OptionText.trim()) return o.OptionText;
                        if (o.min !== undefined && o.max !== undefined) return `${o.min}-${o.max}`;
                        if (o.min !== undefined) return `≥ ${o.min}`;
                        if (o.max !== undefined) return `≤ ${o.max}`;
                        return '-';
                    }).join(', ');
                    processed.qualifications.push({
                        category: key,
                        value
                    });
                }
            });

            structuredQuota.value = processed;
        }

        // 2. Process HTML Content
        if (data.content && data.content.trim() !== '') {
            quota.value = data.content;
        }

        // 3. Determine Layout Type
        if (structuredQuota.value) {
            quotaType.value = 'structured';
        } else if (quota.value !== '') {
            quotaType.value = 'html';
        } else {
            quotaType.value = 'html';
            quota.value = lang.value === 'en' ? 'No additional requirements for this survey.' : '该问卷暂无额外要求。';
        }

        if (data.type === 'link' && data.content) {
            quotaType.value = 'link';
            quota.value = data.content;
        }
    }).catch(err => {
        console.error(`[Quota] Failed to fetch quota for ${item.project_pno}:`, err);
        quotaLoading.value = false;
    });
};

const toQuota = (item) => {
    activeOffer.value = item;
    quotaVisible.value = true;
    quotaLoading.value = true;
    quotaTitle.value = item.project_name;
    quota.value = '';
    structuredQuota.value = null;

    fetchProxyQuota(item);
}
const loading = ref(true)
const showQuota = ref(false)
const showName = ref(false)
const showLoi = ref(false)
const showIr = ref(false)
const showComplete = ref(false)
const showClick = ref(false)
const fetchOffers = () => {
    tableData.value = [];
    loading.value = true;
    getOffers(query.value).then(res => {
        const data = res.data.list;
        showQuota.value = res.data.show_quota ? true : false;
        showName.value = res.data.show_name ? true : false;
        showLoi.value = res.data.show_loi ? true : false;
        showIr.value = res.data.show_ir ? true : false;
        showComplete.value = res.data.show_complete ? true : false;
        showClick.value = res.data.show_click ? true : false;
        customParams.value = [];
        data.forEach((item, index) => {
            item.project_params = item.project_params ? JSON.parse(item.project_params) : [];
            if (index == 0) {
                item.project_params.forEach(param => {
                    customParams.value.push(param.name);
                })
            }
        })

        tableData.value = data;
        total.value = res.data.count;
        loading.value = false;
    }).catch(err => {
        loading.value = false;
    });
}
const onChange = () => {
    page.value = 1;
    fetchOffers();
}

const activeOffer = ref({})
const offerVisible = ref(false)

const sendFraudCheck = async (surveyId) => {
    let fingerprintFallback = 'blocked_or_failed';
    try {
        const result = await getData();
        fingerprintFallback = result.event_id || result.visitor_id;
    } catch (err) {
        console.warn('[Fingerprint] Identification failed/blocked.', err.message);
    }

    try {
        const payload = {
            fingerprintHash: fingerprintFallback,
            surveyId: surveyId,
            platform: route.query.name ? route.query.name.toLowerCase() : 'goweb'
        };

        fetch(`${window.location.origin}/api/fraud/check`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        }).then(async response => {
            if (response.ok) {
                const data = await response.json();
                console.log(`[Fraud Check] Survey: ${surveyId}, Score: ${data.score}`);
            } else {
                console.warn(`[Fraud Check] API returned non-OK status: ${response.status}`);
            }
        }).catch(err => {
            console.error('[Fraud Check] Network or API error occurred:', err);
        });
    } catch (err) {
        console.error('[Fraud Check] Unexpected error in flow:', err);
    }
};


const getDetailUrl = (link, item) => {
    const baseUrl = window.location.origin;
    const params = new URLSearchParams({
        link: link,
        surveyId: item.project_pno,
        platform: route.query.name ? route.query.name.toLowerCase() : 'goweb'
    });
    return `${baseUrl}/detail?${params.toString()}`;
}

const copy = (item) => {
    sendFraudCheck(item.project_pno);
    getOfferLink({ project_pno: item.project_pno }).then(res => {
        let link = res.data;
        if (typeof link === 'object' && link !== null) {
            link = link.link || link.url || JSON.stringify(link);
        }
        if (typeof link === 'string' && link.startsWith('/')) {
            link = window.location.origin + link;
        }

        const detailUrl = getDetailUrl(link, item);
        copyText(detailUrl);
        ElMessage.success('Copied successfully');
    })

}
const start = async (item) => {
    await sendFraudCheck(item.project_pno);
    getOfferLink({ project_pno: item.project_pno }).then(res => {
        let link = res.data;
        if (typeof link === 'object' && link !== null) {
            link = link.link || link.url || JSON.stringify(link);
        }
        if (typeof link === 'string' && link.startsWith('/')) {
            link = window.location.origin + link;
        }

        const detailUrl = getDetailUrl(link, item);
        window.open(detailUrl);
    })
}
const markDialog = ref(false)
const markData = ref([])
const markLoading = ref(false)
const mark = (item) => {
    activeOffer.value = item;
    markDialog.value = true;
    markLoading.value = true;
    markData.value = [];
    getMarkDetailList({ project_pno: item.project_pno }).then(res => {
        markLoading.value = false;
        markData.value = res.data;
    })
}
const markVisible = ref(false)
const handleMarkCommand = (command) => {
    if (command.type == 'edit') {
        markVisible.value = true;
        activeOffer.value = command;
    } else {
        mark(command);
    }
}
fetchOffers();
const markList = ref([])
getMarkList().then(res => {
    markList.value = res.data
});
const handleCommand = (command) => {
    ElMessageBox.confirm(lang.value == 'en' ? 'Are you sure you want to mark this project?' : '立即标记该项目?', lang.value == 'en' ? 'Warning' : '提示', {
        confirmButtonText: lang.value == 'en' ? 'OK' : '确定',
        cancelButtonText: lang.value == 'en' ? 'Cancel' : '取消',
        cancelButtonType: 'primary',
        type: 'warning',
    }).then(() => {
        markProject({ project_pno: activeOffer.value.project_pno, mark_id: command.mark_id }).then(res => {
            markVisible.value = false;
            ElMessage.success(lang.value == 'en' ? 'Mark successfully' : '标记成功');
        })
    }).catch(() => {
    })

}
</script>
<style lang="scss" scoped>
table {
    max-width: 100%;
}

.orders {
    position: absolute;
    left: 0;
    top: 100%;
    width: 100%;
    right: 0;
    background-color: var(--t-card-color);
    box-shadow: 0 0 3px var(--t-card-shadow-color);
    z-index: 2;
    border-radius: 10px;
    padding: 10px 20px;
    margin-top: 2px;
    box-sizing: border-box;

    .order-item {
        display: flex;
        padding: 10px 0;
        align-items: center;

        .tit {
            margin: 0 10px;
        }
    }

    .icons {
        width: 16px;
        height: 16px;
    }
}


/* 垂直滚动条样式 */
/* 宽度 */
::-webkit-scrollbar {
    width: 0px;
}

/* 水平滚动条样式 */
/* 高度 */
::-webkit-scrollbar {
    height: 0px;
}

/* 背景色 */
::-webkit-scrollbar-track {
    background-color: #f5f5f5;
}

/* 滑块颜色 */
::-webkit-scrollbar-thumb {
    background-color: #666;
    border-radius: 10px;
}

.h-title {
    font-family: Poppins;
    font-weight: bold;
    color: currentcolor;
    line-height: 1.33;
    display: flex;
    font-size: 24px;
    margin: 0px;
    white-space: nowrap;
    padding-bottom: 20px;
    border-bottom: 1px solid rgba(255, 255, 255, .05);
    margin-bottom: 20px;
    color: var(--t-color);

    .search {
        width: 240px;
        background: #1e1e2e;
        border-radius: 10px;
        margin-left: 20px;

        &:focus {
            box-shadow: none;
        }

    }

}

.offer-header {
    padding: 40px 40px 20px;
    font-size: 24px;
    font-weight: bold;
    columns: #fff;

    img {
        width: 100px;
    }

    .info {
        padding-left: 20px;
        line-height: 1.1;
    }

    .money {
        color: rgba(218, 80, 218, .8);
    }
}

.offer-btn {
    background: rgba(14, 255, 78, 0.8509803922);
    text-align: center;
    color: #fff;
    font-size: 20px;
    padding: 10px;
    border-radius: 999px;
    box-sizing: border-box;
    border: none;
    cursor: pointer;
    margin: 40px;

    &:hover {
        background: rgba(14, 255, 78, 0.9509803922);
    }
}

.offer-content {
    margin: 0 40px 40px;
    padding: 20px;
    background-color: rgba(255, 255, 255, .1);
    border-radius: 20px;

    .title {
        font-size: 18px;
        font-weight: bold;
    }



    .os {
        margin-top: 40px;
        border-radius: 14px;
        padding: 3px 7px;
        display: flex;
        justify-content: center;

        svg {
            width: 28px;
            height: 36px;
            display: inline-block;
            line-height: 1em;
            flex-shrink: 0;
            color: #019ca1;
            vertical-align: middle;
            fill: none;
            margin-right: 20px;
        }
    }
}

.list-inline {
    padding-left: 0;
    margin-left: -5px;
    list-style: none;

    li {
        width: 41px;
        height: 42px;
        font-size: 18px;
        display: inline-block;
        cursor: pointer;

        &.active {
            border: 1px solid #ff9900;
        }
    }
}

// Premium Quota Dialog Styles (Navy Blue Theme)
.quota-dialog-body {
    max-height: 65vh;
    overflow-y: auto;
    padding: 24px;
    background: #161821;

    &.is-loading {
        opacity: 0.5;
        pointer-events: none;
    }
}

:deep(.el-dialog.offerbox) {
    border-radius: 12px;
    overflow: hidden;
    max-height: 92vh;
    display: flex;
    flex-direction: column;

    .el-dialog__header {
        padding: 22px 28px 18px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.07);
        margin: 0;
        flex-shrink: 0;

        .el-dialog__title {
            font-size: 17px;
            font-weight: 600;
            color: var(--t-color);
        }
    }

    .el-dialog__body {
        padding: 0;
        flex: 1;
        overflow: hidden;
    }
}

.quota-container {
    color: var(--t-color);

    .quota-scroll-area {
        max-height: 72vh;
        overflow-y: auto;
        padding: 24px 28px;
        display: flex;
        flex-direction: column;
        gap: 22px;

        &::-webkit-scrollbar {
            width: 0;
        }
    }
}

// Stat Cards
.stat-row {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 10px;

    @media (max-width: 600px) {
        grid-template-columns: repeat(2, 1fr);
    }
}

.stat-card {
    background: var(--t-card-color);
    border-radius: 10px;
    padding: 14px 16px;
    border: 1px solid rgba(255, 255, 255, 0.05);
}

.stat-label {
    font-size: 11px;
    font-weight: 500;
    color: #888;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    margin-bottom: 8px;
}

.stat-value {
    font-size: 22px;
    font-weight: 600;
    color: var(--t-color);
    line-height: 1;

    &.accent {
        color: #f59e0b;
    }

    &.positive {
        color: rgba(14, 255, 78, 0.85);
    }
}

// Meta Bar
.meta-bar {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.meta-tag {
    font-size: 12px;
    font-weight: 600;
    color: #a9a9ca;
    background: rgba(169, 169, 202, 0.12);
    padding: 4px 10px;
    border-radius: 6px;
    border: 1px solid rgba(169, 169, 202, 0.2);
}

.meta-desc {
    font-size: 13px;
    color: #888;
    font-style: italic;
}

// Progress
.section-label {
    font-size: 11px;
    font-weight: 500;
    color: #888;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    margin-bottom: 10px;
}

.progress-section {
    display: flex;
    flex-direction: column;
    gap: 0;
}

.progress-info-row {
    display: flex;
    justify-content: space-between;
    font-size: 13px;
    color: #888;
    margin-bottom: 8px;

    .pct {
        font-weight: 600;
        color: rgba(14, 255, 78, 0.85);
    }
}

.progress-track {
    height: 5px;
    background: rgba(255, 255, 255, 0.07);
    border-radius: 99px;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    background: rgba(14, 255, 78, 0.6);
    border-radius: 99px;
    transition: width 0.6s ease;
}

// Two Column Layout
.two-col {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    align-items: start;

    @media (max-width: 600px) {
        grid-template-columns: 1fr;
    }
}

// Panel (shared wrapper for both columns)
.panel {
    background: var(--t-card-color);
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: 10px;
    overflow: hidden;
}

// Targeting Criteria
.criteria-list {
    padding: 4px 0;
}

.criteria-item {
    display: flex;
    flex-direction: column;
    gap: 3px;
    padding: 10px 16px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.04);

    &:last-child {
        border-bottom: none;
    }
}

.c-key {
    font-size: 10px;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    font-weight: 500;
}

.c-val {
    font-size: 13px;
    color: var(--t-color);
    line-height: 1.4;
}

// Quota Table
.quota-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;

    th {
        text-align: left;
        padding: 10px 16px;
        color: #888;
        font-weight: 500;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        background: rgba(255, 255, 255, 0.02);
        white-space: nowrap;
    }

    td {
        padding: 10px 16px;
        color: var(--t-color);
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        vertical-align: top;

        &:last-child {
            white-space: nowrap;
        }
    }

    tr:last-child td {
        border-bottom: none;
    }
}

.remaining-zero {
    font-weight: 600;
    color: #f87171;
}

.remaining-ok {
    font-weight: 600;
    color: rgba(14, 255, 78, 0.85);
}

// Empty State
.empty-notice {
    text-align: center;
    padding: 40px 20px;
    color: #666;
    font-size: 14px;
}

// HTML View
.quota-html-body {
    max-height: 65vh;
    overflow-y: auto;
    padding: 24px 28px;

    .html {
        color: var(--t-color);

        table {
            width: 100% !important;
            border-collapse: collapse;

            th,
            tr:first-of-type td {
                background: rgba(255, 255, 255, 0.05) !important;
                color: var(--t-color) !important;
                font-weight: 600;
                padding: 10px 14px !important;
                border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
            }

            td {
                padding: 10px 14px !important;
                border-bottom: 1px solid rgba(255, 255, 255, 0.04) !important;
                color: var(--t-color) !important;
            }
        }
    }
}

// Iframe View
.quota-iframe {
    width: 100%;
    height: 550px;
    border: none;
    display: block;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

// =============================================
// RESPONSIVE TOOLBAR
// =============================================
.plat-toolbar {
    padding-bottom: 16px;
    border-bottom: 1px solid rgba(255, 255, 255, .05);
    margin-bottom: 20px;
}

.plat-toolbar-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;
}

.plat-title {
    font-family: Poppins;
    font-weight: bold;
    font-size: 22px;
    color: var(--t-color);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    flex: 1;
    margin-right: 12px;
}

.sort-wrap {
    position: relative;
    display: flex;
    align-items: center;
    gap: 6px;
    background: var(--t-card-color);
    padding: 9px 16px;
    border-radius: 20px;
    cursor: pointer;
    font-size: 13px;
    color: var(--t-color-1);
    white-space: nowrap;
    flex-shrink: 0;
}

.plat-toolbar-search {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;

    .search {
        flex: 1;
        min-width: 120px;
        background: #1e1e2e;
        border-radius: 10px;
    }

    .el-button {
        flex-shrink: 0;
    }
}

// =============================================
// TABLE / CARD SWITCHING
// =============================================
.desktop-table {
    display: block;
}

.mobile-offer-list {
    display: none;
    flex-direction: column;
    gap: 12px;
    margin-bottom: 20px;
}

@media (max-width: 768px) {
    .desktop-table {
        display: none !important;
    }

    .mobile-offer-list {
        display: flex !important;
    }

    .plat-toolbar-search {
        .search {
            width: 100%;
            min-width: 0;
            flex: 1 1 calc(50% - 5px);
        }
    }
}



.offer-card {
    background: var(--t-card-color);
    border-radius: 14px;
    padding: 16px;
    border: 1px solid rgba(255, 255, 255, 0.06);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    transition: transform 0.15s, box-shadow 0.15s;

    &:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.28);
    }
}

.offer-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.offer-card-pid {
    font-size: 12px;
    color: rgba(255, 255, 255, 0.4);
    font-family: monospace;
    font-weight: 600;
}

.offer-card-reward {
    display: flex;
    align-items: center;
    font-size: 16px;
    font-weight: 700;
    color: rgba(14, 255, 78, 0.9);
}

.offer-card-name {
    font-size: 14px;
    font-weight: 600;
    color: var(--t-color);
    margin-bottom: 10px;
    line-height: 1.4;
}

.offer-card-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-bottom: 14px;
}

.meta-chip {
    display: inline-flex;
    align-items: center;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    background: rgba(255, 255, 255, 0.06);
    color: rgba(255, 255, 255, 0.55);
    border: 1px solid rgba(255, 255, 255, 0.08);
}

.offer-card-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.oc-btn {
    flex: 1;
    padding: 9px 12px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.18s;
    min-width: 60px;
    background: var(--el-color-primary);
    color: #fff;

    &:hover {
        opacity: 0.88;
    }

    &.secondary {
        background: rgba(255, 255, 255, 0.07);
        color: var(--t-color-1);
        border: 1px solid rgba(255, 255, 255, 0.1);

        &:hover {
            background: rgba(255, 255, 255, 0.12);
            color: #fff;
        }
    }
}

/* ── Quota Dialog Mobile Responsiveness ── */
.quota-dialog {
    max-width: 95%;
    border-radius: 16px !important;
}

@media (max-width: 900px) {
    :deep(.quota-dialog) {
        width: 95% !important;
        margin-top: 5vh !important;
        
        .el-dialog__body {
            padding: 16px;
            max-height: 85vh;
            overflow-y: auto;
        }
    }

    .quota-container {
        padding: 0 !important;
    }

    .stat-row {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 10px !important;
    }

    .two-col {
        grid-template-columns: 1fr !important;
        gap: 20px !important;
    }

    .meta-bar {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 8px;
    }
}

@media (max-width: 480px) {
    .stat-row {
        grid-template-columns: 1fr !important;
    }
}
</style>