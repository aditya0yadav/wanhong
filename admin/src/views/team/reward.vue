<template>
    <div>
        <el-scrollbar native :height="height">
            <div class="app-container">
                <!-- 查询 -->
                <el-row>
                    <el-col class="mb-4">
                        <el-button type="primary" title="批量导入UUID审核" @click="rewardSelectOpen('authlist')">导入审核</el-button>
                        <el-button  type="warning" title="批量核减" @click="rewardSelectOpen('auth')">批量核减</el-button>
                        <el-button type="danger" title="删除"
                            @click="rewardSelectOpen('dele')">删除</el-button>
                        <el-select style="width: 120px;" v-model="rewardQuery.platform_id" placeholder="归属平台"
                            class="mr-2 ml-2" clearable filterable>
                            <el-option v-for="(item, index) in platforms" :key="index" :label="item.platform_name"
                                :value="item.platform_id" />
                        </el-select>
                        <el-select style="width: 120px;" v-model="rewardQuery.team_id" placeholder="归属团队" class="mr-2"
                            clearable filterable>
                            <el-option v-for="(item, index) in teams" :key="index" :label="item.team_name"
                                :value="item.team_id" />
                        </el-select>
                        <el-select style="width: 120px;" v-model="rewardQuery.reward_status" placeholder="业绩状态"
                            class="mr-2" clearable filterable>
                            <el-option v-for="(item, index) in rewardStatus" :key="index" :label="item.label"
                                :value="item.value" />
                        </el-select>
                        
                        <el-input style="max-width: 150px;" v-model="rewardQuery.member" class="ya-search-value mr-2" placeholder="请输入账号/昵称/ID"
                            clearable />
                        <el-select v-model="rewardQuery.search_field" class="ya-search-field mr-2" placeholder="查询字段">
                            <el-option value="uuid" label="UUID" />
                            <el-option value="project_pno" label="PID" />
                            <el-option value="project_no" label="项目编号" />
                            <el-option value="project_name" label="项目名称" />
                        </el-select>
                        <el-input style="width: 150px;" v-model="rewardQuery.search_value" class="mr-2"
                            placeholder="请输入查询内容" clearable />
                        <el-date-picker v-model="rewardQuery.date_value" type="datetimerange" class="ya-date-value mr-2"
                            start-placeholder="开始日期" end-placeholder="结束日期" value-format="YYYY-MM-DD HH:mm:ss"
                            :default-time="[new Date(2024, 1, 1, 0, 0, 0), new Date(2024, 1, 1, 23, 59, 59)]" />
                        <el-button type="success" title="查询/刷新" @click="rewardSearch()">查询</el-button>
                        <el-button class="mr-2" type="default" title="重置查询条件" @click="rewardRefresh()">重置</el-button>
                        <RewardExport :query="rewardQuery" />
                    </el-col>
                </el-row>
                <!-- 列表 -->
                <el-table ref="table" v-loading="rewardLoading" :data="rewardData" :height="height - 130"
                    @sort-change="rewardSort" @selection-change="rewardSelect">
                    <el-table-column type="selection" width="42" title="全选/反选" />
                    <el-table-column prop="reward_id" min-width="80" label="ID" />
                    <el-table-column prop="uuid" label="UUID" min-width="150" />
                    <el-table-column prop="project_pno" min-width="120" label="PID" />
                    <el-table-column prop="project_no" min-width="100" label="项目编号" />
                    <el-table-column prop="project_name" min-width="120" label="项目名称" show-overflow-tooltip />
                    <el-table-column prop="platform.platform_name" label="归属平台" />
                    <el-table-column prop="team.team_name" label="归属团队" />
                    <el-table-column prop="member.nickname" label="归属用户" />
                    <el-table-column prop="payout" label="奖励(平台)"  min-width="90">
                        <template #default="scope">
                            <span v-if="scope.row.reward_status == 6" style="color: red">-{{ scope.row.payout }}</span><span v-else>{{ scope.row.payout }}</span>
                        </template>
                    </el-table-column>
                    <el-table-column prop="team_payout" label="奖励(团队)" min-width="90" >
                        <template #default="scope">
                            <span v-if="scope.row.reward_status == 6" style="color: red">-{{ scope.row.team_payout }}</span><span v-else>{{ scope.row.team_payout }}</span>
                        </template>
                    </el-table-column>
                    <el-table-column prop="member_payout" label="奖励(个人)" min-width="90">
                        <template #default="scope">
                            <span v-if="scope.row.reward_status == 6" style="color: red">-{{ scope.row.member_payout }}</span><span v-else>{{ scope.row.member_payout }}</span>
                        </template>
                    </el-table-column>
                    <el-table-column prop="reward_status" label="业绩状态">
                        <template #default="scope">
                            <el-text :type="rewardStatusFilter(scope.row.reward_status)['type']">{{
                                rewardStatusFilter(scope.row.reward_status)['label'] }}</el-text>
                        </template>
                    </el-table-column>

                    <el-table-column prop="create_time" min-width="120" label="用时">
                        <template #default="scope">
                            <el-text>{{ timeDifference(scope.row.start_time, scope.row.create_time) }}</el-text>
                        </template>
                    </el-table-column>
                    <el-table-column prop="ip" min-width="130" label="IP" />
                    <el-table-column prop="start_time" min-width="160" label="开始时间" />
                    <el-table-column prop="create_time" min-width="160" label="完成时间" />
                    <el-table-column prop="auth_time" min-width="160" label="审核时间" />
                    
                    <el-table-column label="操作" width="120" fixed="right">
                        <template #default="scope">
                            <el-button size="small" link type="primary" @click="rewardEdit(scope.row)"> 编辑
                            </el-button><el-divider direction="vertical" />
                            <el-button size="small" link type="primary" @click="rewardDele(scope.row)"> 删除
                            </el-button>
                        </template>
                    </el-table-column>
                </el-table>
                <!-- 分页 -->
                <pagination v-show="rewardCount > 0" v-model:total="rewardCount" v-model:page="rewardQuery.page"
                    v-model:limit="rewardQuery.limit" size="small" @pagination="rewardList" />
                <!-- 添加修改 -->
                <el-dialog v-model="rewardDialog" :title="rewardDialogTitle" :close-on-click-modal="false"
                    :close-on-press-escape="false" :before-close="cancel" top="5vh" width="700px">
                    <el-form label-position="top" ref="rewardRef" :model="rewardModel" :rules="rules"
                        label-width="100px">
                        <el-form-item label="业绩ID" prop="platform_id" v-if="rewardModel.reward_id">
                            <el-input key="reward_id" :value="rewardModel.reward_id" disabled />
                        </el-form-item>
                        <el-form-item label="团队ID" prop="team_id">
                            <el-input key="team_id" v-model="rewardModel.team_id" />
                        </el-form-item>
                        <el-form-item label="平台ID" prop="team_id">
                            <el-input key="platform_id" v-model="rewardModel.platform_id" />
                        </el-form-item>
                        <el-form-item label="用户ID" prop="member_id">
                            <el-input key="member_id" v-model="rewardModel.member_id" />
                        </el-form-item>
                        <el-form-item label="流水ID" prop="txn_id">
                            <el-input key="txn_id" v-model="rewardModel.txn_id" />
                        </el-form-item>
                        <el-form-item label="全价奖励" prop="payout">
                            <el-input key="payout" v-model="rewardModel.payout" />
                        </el-form-item>
                        <el-form-item label="团队奖励" prop="team_payout">
                            <el-input key="team_payout" v-model="rewardModel.team_payout" />
                        </el-form-item>
                        <el-form-item label="用户奖励" prop="member_payout">
                            <el-input key="member_payout" v-model="rewardModel.member_payout" />
                        </el-form-item>
                    </el-form>
                    <template #footer>
                        <el-button :loading="loading" @click="rewardCancel">取消</el-button>
                        <el-button :loading="loading" type="primary" @click="rewardSubmit">提交</el-button>
                    </template>
                </el-dialog>
                <el-dialog v-model="selectDialog" :title="selectTitle" :close-on-click-modal="false"
                    :close-on-press-escape="false" top="5vh">
                    <el-scrollbar native>
                        <el-form label-width="120px">
                            <el-form-item label="删除" v-if="selectType === 'dele'">
                                <el-text size="default" type="danger">确定要删除选中的业绩吗？</el-text>
                            </el-form-item>
                            <el-form-item label="ID" v-if="selectType === 'dele'">
                                <el-input v-model="selectIds" type="textarea" :rows="1" disabled readonly/>
                            </el-form-item>
                            <el-form-item label="审核" v-if="selectType === 'auth'">
                                <el-text size="default" type="danger">确定要核减选中的业绩吗？</el-text>
                            </el-form-item>
                            <el-form-item label="UUID" v-if="selectType === 'auth'">
                                <el-input :value="selectionUuids" type="textarea" :rows="selection.length"  disabled readonly/>
                            </el-form-item>
                            <el-form-item label="业绩类型" v-if="selectType === 'authlist'">
                                <el-radio-group v-model="authType">
                                    <el-radio class="model-mr" :value="0">失败业绩</el-radio>
                                    <el-radio :value="1">成功业绩</el-radio>
                                </el-radio-group>
                            </el-form-item>
                            <el-form-item label="同步操作" v-if="selectType === 'authlist'">
                                <el-radio-group v-model="authSync">
                                    <el-radio class="model-mr" :value="0">不同步</el-radio>
                                    <el-radio :value="1">同步</el-radio>
                                </el-radio-group>
                            </el-form-item>
                            <el-form-item label="限制起止时间" v-if="selectType === 'authlist'">
                                <el-date-picker v-model="authDate" type="datetimerange" class="ya-date-value"
                                    start-placeholder="开始日期" end-placeholder="结束日期" value-format="YYYY-MM-DD HH:mm:ss"
                                    :default-time="[new Date(2024, 1, 1, 0, 0, 0), new Date(2024, 1, 1, 23, 59, 59)]" />
                            </el-form-item>
                            <el-form-item label="UUID数据" v-if="selectType === 'authlist'">
                                <el-input v-model="authUuids" type="textarea" :rows="10" placeholder="请输入UUID数据" />
                            </el-form-item>
                        </el-form>
                    </el-scrollbar>
                    <template #footer>
                        <el-button :loading="rewardLoading" @click="selectCancel">取消</el-button>
                        <el-button :loading="rewardLoading" type="primary" @click="selectSubmit">提交</el-button>
                    </template>
                </el-dialog>
                <el-dialog v-model="rsDialog" title="人设信息" :close-on-click-modal="false" :close-on-press-escape="false"
                    top="5vh">
                    <el-scrollbar native :height="500">
                        <div v-if="rsType == 0">
                            {{ rsContent }}
                        </div>
                        <div v-if="rsType == 1">
                            <div v-for="(item, index) in rsContent" :key="index"
                                style="border:1px solid #ebeef5;margin-bottom: 10px;margin-right: 10px;">
                                <div style="padding:10px;background-color: #f5f5f5;">{{ item.name }}</div>
                                <div style="padding:10px;">{{ item.value }}</div>
                            </div>
                        </div>
                    </el-scrollbar>
                </el-dialog>
            </div>
        </el-scrollbar>
    </div>
</template>
<script>
import checkPermission from '@/utils/permission'

import screenHeight from '@/utils/screen-height'
import { clipboard } from '@/utils/index'
import { getPageLimit } from '@/utils/settings'
import { arrayColumn } from '@/utils/index'
import { list, info, add, edit, dele, batchAudit,uuidAudit } from '@/api/team/reward'
import { platlist } from '@/api/platform/platform'
import { teamlist } from '@/api/team/team'
import { useAppStoreHook } from '@/store/modules/app'
import RewardExport from './components/RewardExport.vue'
import { ElMessage } from 'element-plus'
const appStore = useAppStoreHook()
export default {
    name: 'Reward',
    components: { RewardExport },
    data() {
        return {
            rewardLoading: false,
            rewardData: [],
            rewardCount: 0,
            rewardDialog: false,
            rewardDialogTitle: '',
            authType: 1,
            authSync: 0,
            authDate: '',
            authUuids: '',
            rewardQuery: {
                page: 1,
                limit: getPageLimit(),
                search_field: 'uuid',
                search_exp: '=',
                date_field: 'create_time',
                platform_id: '',
                team_id: '',
                reward_status: '',
                member: '',
            },
            rewardRules: {
            },
            rewardStatus: [{
                value: 6,
                label: '核减',
                type: 'warning',
            },
            {
                value: 1,
                label: '成功',
                type: 'success',
            },
            {
                value: 2,
                label: '失败',
                type: 'danger',
            },
            {
                value: 3,
                label: '超限',
                type: 'danger',
            },
            {
                value: 4,
                label: '终止',
                type: 'danger',
            }
            ],
            rewardModel: {
                team_id: '',
                platform_id: '',
                user_id: '',
                txn_id: '',
                payout: '',
                team_payout: '',
                user_payout: '',
            },
            rewardExps: [],
            platforms: [],
            teams: [],
            height: 0,
            selection: [],
            selectIds: '',
            selectTitle: '操作',
            selectDialog: false,
            selectType: '',
            rsDialog: false,
            rsContent: '',
            rsType: 0
        }
    },
    created() {
        this.rewardList()
        platlist().then((res) => {
            this.platforms = res.data
        })
        teamlist().then((res) => {
            this.teams = res.data
        })
        this.height = screenHeight(80)
    },
    computed: {
        selectionUuids() {
            return arrayColumn(this.selection, 'uuid').join('\n')
        }
    },
    methods: {
        checkPermission,
        clipboard,
        rewardStatusFilter(status) {
            return this.rewardStatus.filter((item) => item.value === status)[0];
        },
        lookRs(row, key = 'front_rs') {
            if (!row[key]) {
                ElMessage.error('人设信息为空');
                return
            }
            this.rsDialog = true;
            try {
                this.rsContent = JSON.parse(row[key]);
                this.rsType = 1;
            } catch (e) {
                this.rsContent = row[key];
                this.rsType = 0;
            }
        },
        getRs(row, key = 'front_rs'){
            var rsContent = '';
            try {
                var data = JSON.parse(row[key]);
                for (var i = 0; i < data.length; i++) {
                    if(data[i].value){
                        if( data[i].type=='image'){
                            rsContent += '<br />'+data[i].name + ':' + '<img style="width:100px;" src="' + data[i].value + '"/>';
                        }else{
                            rsContent += data[i].name + ':' + data[i].value + ';'; 
                        }
                        
                    }
                }
            } catch (e) {
                rsContent = row[key];
            }
            return rsContent;
        },
        timeDifference(startDate, endDate) {
            // 将时间字符串转换为Date对象
            const start = new Date(startDate.replace(/-/g, '/'));
            const end = new Date(endDate.replace(/-/g, '/'));

            // 计算时间差（毫秒）
            const diff = end.getTime() - start.getTime();
            console.log(start, end)
            // 转换为秒
            const seconds = Math.floor(diff / 1000);

            // 计算分钟和秒数
            const minutes = Math.floor(seconds / 60);
            const secondsLeft = seconds % 60;

            // 返回格式化的时间差字符串
            return `${minutes}分钟${secondsLeft}秒`;
        },
        // 列表
        rewardList() {
            this.rewardLoading = true
            list(this.rewardQuery)
                .then((res) => {
                    this.rewardData = res.data.list
                    this.rewardCount = res.data.count
                    this.rewardExps = res.data.exps
                    this.rewardLoading = false
                })
                .catch(() => {
                    this.rewardLoading = false
                })
        },
        // 删除
        rewardDele(row) {
            ElMessageBox.confirm(
                '确定删除该业绩吗?',
                '解除授权',
                {
                    confirmButtonText: '确认',
                    cancelButtonText: '取消',
                    type: 'warning',
                }
            )
                .then(() => {
                    this.rewardLoading = true
                    dele({
                        ids: [row.reward_id]
                    })
                        .then((res) => {
                            this.rewardList()
                            ElMessage.success(res.msg)
                        })
                        .catch(() => {
                            this.rewardLoading = false
                        })
                })
        },
        // 添加修改
        rewardAdd() {
            this.rewardDialog = true
            this.rewardDialogTitle = '业绩添加'
            this.rewardReset()
        },
        rewardEdit(row) {
            this.rewardDialog = true
            this.rewardDialogTitle = '业绩编辑'
            info({ reward_id: row.reward_id })
                .then((res) => {
                    this.rewardReset(res.data)
                })
                .catch(() => { })
        },
        rewardCancel() {
            this.rewardDialog = false
            this.rewardReset()
        },
        rewardSubmit() {
            this.$refs['rewardRef'].validate((valid) => {
                if (valid) {
                    this.rewardLoading = true
                    const data = { ...this.rewardModel }
                    if (data['reward_id']) {
                        edit(data)
                            .then((res) => {
                                this.rewardList()
                                this.rewardDialog = false
                                ElMessage.success(res.msg)
                            })
                            .catch(() => {
                                this.rewardLoading = false
                            })
                    } else {
                        console.log(data)
                        add(data)
                            .then((res) => {
                                this.rewardList()
                                this.rewardDialog = false
                                ElMessage.success(res.msg)
                            })
                            .catch(() => {
                                this.rewardLoading = false
                            })
                    }
                } else {
                    ElMessage.error('请完善必填项（带红色星号*）')
                }
            })
        },
        // 重置
        rewardReset(row) {
            if (row) {
                this.rewardModel = row
            } else {
                this.rewardModel = this.$options.data().rewardModel
            }
            if (this.$refs['rewardRef'] !== undefined) {
                try {
                    this.$refs['rewardRef'].resetFields()
                    this.$refs['rewardRef'].clearValidate()
                } catch (error) { }
            }
        },
        // 查询
        rewardSearch() {
            this.rewardQuery.page = 1
            this.rewardList()
        },
        // 重置查询
        rewardRefresh() {
            const limit = this.rewardQuery.limit
            this.rewardQuery = this.$options.data().rewardQuery
            this.$refs['rewardTable'].clearSort()
            this.rewardQuery.limit = limit
            this.rewardList()
        },
        // 排序
        rewardSort(sort) {
            this.query.sort_field = sort.prop
            this.query.sort_value = ''
            if (sort.order === 'ascending') {
                this.query.sort_value = 'asc'
                this.rewardList()
            }
            if (sort.order === 'descending') {
                this.query.sort_value = 'desc'
                this.rewardList()
            }
        },
        rewardSelectOpen(selectType, selectRow = '') {
            if (selectRow) {
                this.$refs['table'].clearSelection()
                const selectRowLen = selectRow.length
                for (let i = 0; i < selectRowLen; i++) {
                    this.$refs['table'].toggleRowSelection(selectRow[i], true)
                }
            }
            if (!this.selection.length && selectType !== 'authlist') {
                ElMessageBox.alert('请选择需要操作的业绩', '提示', {
                    type: 'warning',
                    callback: () => { }
                })
            } else {
                if (selectType === 'authlist') {
                    this.selectTitle = '批量导入UUID审核'
                } else if (selectType === 'dele') {
                    this.selectTitle = '批量删除'
                } else if (selectType === 'auth') {
                    this.selectTitle = '批量核减'
                }
                this.selectDialog = true
                this.selectType = selectType
            }
        },
        // 操作
        rewardSelect(selection) {
            this.selection = selection
            this.selectIds = this.rewardSelectGetIds(selection).toString()
        },
        rewardSelectGetIds(selection) {
            return arrayColumn(selection, 'reward_id')
        },
        selectCancel() {
            this.selectDialog = false
        },
        selectSubmit() {
            if (!this.selection.length && this.selectType !== 'authlist') {
                this.selectAlert()
            } else {
                this.rewardLoading = true
                if (this.selectType === 'authlist') {
                    batchAudit({
                        auth_type: this.authType,
                        auth_sync: this.authSync,
                        auth_date: this.authDate,
                        auth_uuids: this.authUuids,
                    }).then((res) => {
                        this.rewardLoading = false
                        this.rewardList()
                        ElMessage.success(res.msg)
                        this.selectDialog = false
                    }).catch(() => {
                        this.rewardLoading = false
                    })
                } else if (this.selectType === 'auth') {
                    
                    uuidAudit({
                        auth_type: this.authType,
                        ids: this.rewardSelectGetIds(this.selection),
                    }).then((res) => {
                        this.rewardLoading = false
                        this.rewardList()
                        ElMessage.success(res.msg)
                        this.selectDialog = false
                    }).catch(() => {
                        this.rewardLoading = false
                    })
                } else if (this.selectType === 'dele') {
                    dele({
                        ids: this.rewardSelectGetIds(this.selection)
                    })
                        .then((res) => {
                            this.rewardLoading = false
                            this.rewardList()
                            ElMessage.success(res.msg)
                            this.selectDialog = false
                        })
                        .catch(() => {
                            this.rewardLoading = false
                        })
                }

            }
        },
    }
}
</script>