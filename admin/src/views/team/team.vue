<template>
    <div>
    <div class="app-container"  v-show="showDetail == 0">
        <!-- 查询 -->
        <el-row>
            <el-col class="mb-4">
                <el-button type="primary" @click="add()">添加</el-button>
                <el-button type="danger" title="删除" @click="selectOpen('dele')">删除</el-button>
                <el-button type="warning" title="是否禁用" @click="selectOpen('disable')">禁用</el-button>
                <el-input v-model="query.search_value" class="ya-search-value mr-2 ml-3" placeholder="请输入团队名称"
                    clearable />
                <el-button type="primary" title="查询/刷新" @click="search()">查询</el-button>
                <el-button type="default" title="重置查询条件" @click="refresh()">重置</el-button>
            </el-col>
        </el-row>
        <!-- 列表 -->
        <el-table ref="table" v-loading="loading" :data="data" :height="height" @sort-change="sort"
            @selection-change="select">
            <el-table-column type="selection" width="42" title="全选/反选" />
            <el-table-column :prop="idkey" label="ID" width="80" sortable="custom" />
            <el-table-column prop="team_name" label="团队名称" min-width="170" sortable="custom" show-overflow-tooltip />
            <el-table-column prop="team_host" label="团队域名" min-width="170" sortable="custom" show-overflow-tooltip />
            <el-table-column prop="auth_num" label="授权成员数量" min-width="200" sortable="custom" show-overflow-tooltip />
            <el-table-column prop="commission_ratio" label="抽佣比例" min-width="130" show-overflow-tooltip />
            <el-table-column prop="is_disable_name" label="禁用" min-width="80" sortable="custom" />
            <el-table-column prop="sort" label="排序" width="85" sortable="custom" />
            <el-table-column label="操作" width="240">
                <template #default="scope">
                    <el-button link type="primary" :underline="false" @click="toDetail(scope.row)"> 管理
                    </el-button><el-divider direction="vertical" />
                    <el-button link type="primary" :underline="false" @click="edit(scope.row)"> 编辑
                    </el-button><el-divider direction="vertical" />
                    <el-button link type="primary" :underline="false"
                        @click="selectOpen('dele', [scope.row])"> 删除 </el-button>
                </template>
            </el-table-column>
        </el-table>
        <!-- 分页 -->
        <pagination v-show="count > 0" v-model:total="count" v-model:page="query.page" v-model:limit="query.limit"
            size="small" @pagination="list" />
        <!-- 添加修改 -->
        <el-dialog v-model="dialog" :title="dialogTitle" :close-on-click-modal="false" :close-on-press-escape="false"
            :before-close="cancel" top="5vh">
            <el-form ref="ref" :model="model" :rules="rules" label-width="100px">

                <el-form-item label="团队名称" prop="team_name">
                    <el-input key="team_name" v-model="model.team_name" placeholder="请输入团队名称" clearable />
                </el-form-item>
                <el-form-item label="团队LOGO" prop="team_logo">
                        <FileImage v-model="model.team_logo" :file-url="model.logo_url" file-title="上传团队LOGO"
                            file-tip="图片小于200KB，jpg、png格式，宽高 1:1。" :height="100" upload />
                </el-form-item>
                <el-form-item label="团队域名" prop="team_host">
                    <el-input key="team_host" v-model="model.team_host" placeholder="请输入团队域名" clearable />
                </el-form-item>
                <el-form-item label="授权成员数量" prop="auth_num">
                    <el-input key="auth_num" v-model="model.auth_num" placeholder="请输入授权成员数量" clearable />
                </el-form-item>
                <el-form-item label="抽佣比例" prop="commission_ratio">
                    <el-input key="commission_ratio" v-model="model.commission_ratio" placeholder="请输入抽佣比例" clearable />
                </el-form-item>
                
                <el-form-item label="排序" prop="sort">
                    <el-input v-model="model.sort" type="number" />
                </el-form-item>
            </el-form>
            <template #footer>
                <el-button :loading="loading" @click="cancel">取消</el-button>
                <el-button :loading="loading" type="primary" @click="submit">提交</el-button>
            </template>
        </el-dialog>
        <el-dialog v-model="selectDialog" :title="selectTitle" :close-on-click-modal="false"
            :close-on-press-escape="false" top="20vh" width="500px">
            <el-scrollbar native :height="height - 200">
                <el-form label-width="120px">
                    <el-form-item :label="name + 'ID'">
                        <el-input v-model="selectIds" type="input" :rows="18" disabled />
                    </el-form-item>
                    <el-form-item v-if="selectType === 'dele'" label="删除">
                        <el-text size="default" type="danger">确定要删除选中的{{ name }}吗？</el-text>
                    </el-form-item>
                    <el-form-item v-else-if="selectType === 'disable'" label="是否禁用">
                        <el-switch class="mr-2" v-model="is_disable" :active-value="1" :inactive-value="0" />
                        <el-text size="small" v-if="is_disable" type="danger"> 禁用后无法使用！</el-text>
                    </el-form-item>
                </el-form>
            </el-scrollbar>
            <template #footer>
                <el-button :loading="loading" @click="selectCancel">取消</el-button>
                <el-button :loading="loading" type="primary" @click="selectSubmit">提交</el-button>
            </template>
        </el-dialog>
    </div>
    <RouterView></RouterView>
    </div>
</template>
<script>
import checkPermission from '@/utils/permission'
import { clipboard } from '@/utils/index'
import { getPageLimit } from '@/utils/settings'
import { arrayColumn } from '@/utils/index'
import { list, info, add, edit, dele, disable } from '@/api/team/team'
import { useAppStoreHook } from '@/store/modules/app'
const appStore = useAppStoreHook()
export default {
    name: 'Team',
    data() {
        return {
            loading: false,
            data: [],
            count: 0,
            dialog: false,
            dialogTitle: '',
            name: '团队',
            idkey: 'team_id',
            is_disable: 0,
            query: {
                page: 1,
                limit: getPageLimit(),
                search_field: 'team_name',
                search_exp: 'like',
                date_field: 'create_time',
            },
            selection: [],
            selectIds: '',
            selectDialog: false,
            selectTitle: '',
            selectType: '',
            model: {
                team_name: '',
                team_host: '',
                team_logo: '',
                auth_num: '',
                commission_ratio: '',
                sort: 0
            },
        }
    },
    created() {
        appStore.changeDetail(0);
        this.list()
    },
    computed: {
        showDetail() {
            return appStore.showDetail
        }
    },
    methods: {
        checkPermission,
        clipboard,
        toDetail(row) {
            this.$router.push({ name: 'TeamDetail', params: { id: row[this.idkey] } })
        },
        selectGetIds(selection) {
            return arrayColumn(selection, this.idkey)
        },
        select(selection) {
            this.selection = selection
            this.selectIds = this.selectGetIds(selection).toString()
        },
        selectOpen(selectType, selectRow = '') {
            if (selectRow) {
                this.$refs['table'].clearSelection()
                const selectRowLen = selectRow.length
                for (let i = 0; i < selectRowLen; i++) {
                    this.$refs['table'].toggleRowSelection(selectRow[i], true)
                }
            }
            if (!this.selection.length) {
                this.selectAlert()
            } else {
                this.selectTitle = '操作'
                if (selectType === 'dele') {
                    this.selectTitle = this.name + '删除'
                }
                this.selectDialog = true
                this.selectType = selectType
            }
        },
        selectCancel() {
            this.selectDialog = false
        },
        selectSubmit() {
            if (!this.selection.length) {
                this.selectAlert()
            } else {
                const selectType = this.selectType
                if (selectType === 'disable') {
                    this.disable(this.selection, true)
                } else if (selectType === 'dele') {
                    this.dele(this.selection)
                }
                this.selectDialog = false
            }
        },
        // 列表
        list() {
            this.loading = true
            list(this.query)
                .then((res) => {
                    this.data = res.data.list
                    this.count = res.data.count
                    this.loading = false
                })
                .catch(() => {
                    this.loading = false
                })
        },
        selectAlert() {
            ElMessageBox.alert('请选择需要操作的' + this.name, '提示', {
                type: 'warning',
                callback: () => { }
            })
        },
        selectGetIds(selection) {
            return arrayColumn(selection, this.idkey)
        },
        // 删除
        dele(row) {
            if (!row.length) {
                this.selectAlert()
            } else {
                this.loading = true
                dele({
                    ids: this.selectGetIds(row)
                })
                    .then((res) => {
                        this.list()
                        ElMessage.success(res.msg)
                    })
                    .catch(() => {
                        this.loading = false
                    })
            }
        },
        // 添加修改
        add() {
            this.dialog = true
            this.dialogTitle = this.name + '添加'
            this.reset()
        },
        // 是否禁用
        disable(row, select = false) {
            if (!row.length) {
                this.selectAlert()
            } else {
                this.loading = true
                var is_disable = row[0].is_disable
                if (select) {
                    is_disable = this.is_disable
                }
                disable({
                    ids: this.selectGetIds(row),
                    is_disable: is_disable
                })
                    .then((res) => {
                        this.list()
                        ElMessage.success(res.msg)
                    })
                    .catch(() => {
                        this.list()
                    })
            }
        },
        edit(row) {
            this.dialog = true
            this.dialogTitle = this.name + '修改：' + row[this.idkey]
            var id = {}
            id[this.idkey] = row[this.idkey]
            info(id)
                .then((res) => {
                    this.reset(res.data)
                })
                .catch(() => { })
        },
        cancel() {
            this.dialog = false
            this.reset()
        },
        submit() {
            this.$refs['ref'].validate((valid) => {
                if (valid) {
                    this.loading = true
                    this.model.api_tree = []
                    this.model.thirds = []
                    if (this.model[this.idkey]) {
                        edit(this.model)
                            .then((res) => {
                                this.list()
                                this.dialog = false
                                ElMessage.success(res.msg)
                            })
                            .catch(() => {
                                this.loading = false
                            })
                    } else {
                        add(this.model)
                            .then((res) => {
                                this.list()
                                this.dialog = false
                                ElMessage.success(res.msg)
                            })
                            .catch(() => {
                                this.loading = false
                            })
                    }
                } else {
                    ElMessage.error('请完善必填项（带红色星号*）')
                }
            })
        },
        // 重置
        reset(row) {
            if (row) {
                this.model = row
            } else {
                this.model = this.$options.data().model
            }
            this.apiExpandAll = false
            if (this.$refs['ref'] !== undefined) {
                try {
                    this.$refs['ref'].resetFields()
                    this.$refs['ref'].clearValidate()
                } catch (error) { }
            }
        },
        // 查询
        search() {
            this.query.page = 1
            this.list()
        },
        // 重置查询
        refresh() {
            const limit = this.query.limit
            this.query = this.$options.data().query
            this.$refs['table'].clearSort()
            this.query.limit = limit
            this.list()
        },
        // 排序
        sort(sort) {
            this.query.sort_field = sort.prop
            this.query.sort_value = ''
            if (sort.order === 'ascending') {
                this.query.sort_value = 'asc'
                this.list()
            }
            if (sort.order === 'descending') {
                this.query.sort_value = 'desc'
                this.list()
            }
        },
    }
}
</script>