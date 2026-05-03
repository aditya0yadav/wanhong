<template>
    <div>
        <div class="app-container" v-show="showDetail == 0">
            <!-- 查询 -->
            <el-row>
                <el-col class="mb-4">
                    <el-button type="primary" @click="add()">添加</el-button>
                    <el-button type="danger" title="删除" @click="selectOpen('dele')">删除</el-button>
                    <el-button type="warning" title="是否禁用" @click="selectOpen('disable')">禁用</el-button>
                    <el-select v-model="query.search_field" class="ya-search-field mr-2 ml-3" placeholder="查询字段">
                        <el-option :value="idkey" label="ID" />
                        <el-option value="platform_name" label="平台名称" />
                        <el-option value="platform_sign" label="平台标识" />
                    </el-select>
                    <el-select v-model="query.search_exp" class="ya-search-exp mr-2">
                        <el-option v-for="exp in exps" :key="exp.exp" :value="exp.exp" :label="exp.name" />
                    </el-select>
                    <el-input v-model="query.search_value" class="ya-search-value mr-2" placeholder="请输入查询内容"
                        clearable />
                    <el-button type="success" title="查询/刷新" @click="search()">查询</el-button>
                    <el-button type="default" title="重置查询条件" @click="refresh()">重置</el-button>
                </el-col>
            </el-row>
            <!-- 列表 -->
            <el-table ref="table" v-loading="loading" :data="data" :height="height" @sort-change="sort" stripe
                @selection-change="select">
                <el-table-column type="selection" width="42" title="全选/反选" />
                <el-table-column :prop="idkey" label="ID" width="80" sortable="custom" />
                <el-table-column prop="platform_name" label="平台名称" min-width="120" show-overflow-tooltip />
                <el-table-column prop="platform_sign" label="平台标识" min-width="120" show-overflow-tooltip />
                <el-table-column prop="logo_url" label="平台图片" min-width="120">
                    <template #default="scope">
                        <img :src="scope.row.logo_url" style="width: 100px;height:auto" />
                    </template>
                </el-table-column>
                <el-table-column prop="platform_level" label="平台评分" min-width="170">
                    <template #default="scope">
                        <el-rate v-model="scope.row.platform_level" disabled text-color="#ff9900" />
                    </template>
                </el-table-column>
                <el-table-column prop="projects_count" label="项目数量" min-width="170" show-overflow-tooltip />
                <el-table-column prop="is_disable" label="状态" min-width="80">
                    <template #default="scope">
                        <el-tag v-if="scope.row.is_disable == 1" type="danger">已禁用</el-tag>
                        <el-tag v-else type="success">启用</el-tag>
                    </template>
                </el-table-column>
                <el-table-column prop="sort" label="排序" width="85" sortable="custom" />
                <el-table-column label="操作" width="240">
                    <template #default="scope">
                        <el-button v-if="checkPermission(['admin/platform.Platform/detail'])" link type="primary"
                            @click="toDetail(scope.row)"> 管理
                        </el-button><el-divider v-if="checkPermission(['admin/platform.Platform/detail'])"
                            direction="vertical" />
                        <el-button v-if="checkPermission(['admin/platform.Platform/edit'])" link type="primary"
                            @click="edit(scope.row)"> 配置
                        </el-button><el-divider v-if="checkPermission(['admin/platform.Platform/edit'])"
                            direction="vertical" />
                        <el-button v-if="checkPermission(['admin/platform.Platform/dele'])" link type="primary"
                            @click="selectOpen('dele', [scope.row])"> 删除
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
            <!-- 分页 -->
            <pagination v-show="count > 0" v-model:total="count" v-model:page="query.page" v-model:limit="query.limit"
                @pagination="list" />
            <!-- 添加修改 -->
            <el-dialog v-model="dialog" :title="dialogTitle" :close-on-click-modal="false"
                :close-on-press-escape="false" :before-close="cancel" top="5vh">

                <el-form label-position="top" ref="ref" :model="model" :rules="rules" label-width="100px">

                    <el-form-item label="平台LOGO" prop="platform_image">
                        <FileImage v-model="model.platform_image" :file-url="model.logo_url" file-title="上传平台LOGO"
                            file-tip="图片小于200KB，jpg、png格式，宽高 1:1。" :height="100" upload />
                    </el-form-item>
                    <el-row :gutter="20">
                        <el-col :span="12">
                            <el-form-item label="平台名称" prop="platform_name">
                                <el-input key="platform_name" v-model="model.platform_name" placeholder="请输入平台名称"
                                    clearable />
                            </el-form-item>
                        </el-col>
                        <el-col :span="12">
                            <el-form-item label="平台标识【对接使用,不可重复】" prop="platform_sign">
                                <el-input key="platform_sign" v-model="model.platform_sign" placeholder="请输入平台标识"
                                    clearable />
                            </el-form-item>
                        </el-col>
                        <el-col :span="12">
                            <el-form-item label="平台背景色" prop="platform_color">
                                <el-input key="platform_color" v-model="model.platform_color" placeholder="请输入背景色"
                                    clearable />
                            </el-form-item>
                        </el-col>
                        <el-col :span="12">
                            <el-form-item label="平台货币" prop="platform_currency">
                                <el-select v-model="model.platform_currency" placeholder="请选择" clearable filterable>
                                    <el-option v-for="(item, index) in currencyAllData" :key="index"
                                        :label="item.currency_name" :value="item.currency_id" />
                                </el-select>
                            </el-form-item>
                        </el-col>
                        <el-col :span="12">
                            <el-form-item label="推荐星级" prop="platform_level">
                                <el-radio-group v-model="model.platform_level">
                                    <el-radio :label="1">一星</el-radio>
                                    <el-radio :label="2">二星</el-radio>
                                    <el-radio :label="3">三星</el-radio>
                                    <el-radio :label="4">四星</el-radio>
                                    <el-radio :label="5">五星</el-radio>
                                </el-radio-group>
                            </el-form-item>
                        </el-col>
                        <el-col :span="12">
                            <el-form-item label="项目对接地址" prop="platform_url">
                                <el-input key="platform_url" v-model="model.platform_url" placeholder="请输入项目对接地址"
                                    clearable />
                            </el-form-item>
                        </el-col>
                        <el-col :span="12">
                            <el-form-item label="配额对接地址" prop="platform_quota_url">
                                <el-input key="platform_quota_url" v-model="model.platform_quota_url"
                                    placeholder="请输入配额对接地址" clearable />
                            </el-form-item></el-col><el-col :span="12">
                            <el-form-item label="答题对接地址" prop="platform_click_url">
                                <el-input key="platform_click_url" v-model="model.platform_click_url"
                                    placeholder="请输入答题对接地址" clearable />
                            </el-form-item></el-col><el-col :span="12">
                            <el-form-item label="限制完成时间(分钟),0为不限" prop="limit_endtime">
                                <el-input type="number" key="limit_endtime" v-model="model.limit_endtime"
                                    placeholder="请输入限制完成分钟数" />
                            </el-form-item></el-col><el-col :span="12">
                            <el-form-item label="排序" prop="sort">
                                <el-input v-model="model.sort" type="number" />
                            </el-form-item>
                        </el-col><el-col :span="12">
                            <el-form-item v-for="(param, index) in model.params" :key="param.key"
                                :label="'对接参数 [' + (index + 1) + ']'" :prop="'params.' + index + '.name'">
                                <div class="flex w-full">
                                    <el-select class="mr-2 flex-1" v-model="param.name" placeholder="请选择对接参数名"
                                        filterable>
                                        <el-option value="app_id" label="AppID" />
                                        <el-option value="pub_id" label="PubID" />
                                        <el-option value="app_key" label="Appkey" />
                                        <el-option value="app_secret" label="AppSecret" />
                                        <el-option value="app_token" label="AppToken" />
                                        <el-option value="app_service_key" label="AppServiceKey" />
                                        <el-option value="app_service_secret" label="AppServiceSecret" />
                                    </el-select>
                                    <el-input class="mr-2 flex-1" v-model="param.value" placeholder="请输入对接参数值" />
                                    <el-button type="primary" plain @click.prevent="addParam">添加</el-button>
                                    <el-button type="danger" plain @click.prevent="removeParam(index)">删除</el-button>
                                </div>
                            </el-form-item>
                        </el-col><el-col :span="12">
                            <el-form-item v-for="(param, index) in model.project_params" :key="param.key"
                                :label="'项目参数 [' + (index + 1) + ']'" :prop="'project_params.' + index + '.name'">
                                <div class="flex w-full">
                                    <el-input class="mr-2 flex-1" v-model="param.name" placeholder="请输入项目参数名" />
                                    <el-input class="mr-2 flex-1" v-model="param.field" placeholder="请输入项目参数标识" />
                                    <el-button type="primary" plain @click.prevent="addProjectParam">添加</el-button>
                                    <el-button type="danger" plain
                                        @click.prevent="removeProjectParam(index)">删除</el-button>
                                </div>
                            </el-form-item></el-col>
                    </el-row>
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
                        <el-form-item v-if="selectType === 'dele'" label="删除">
                            <el-text size="default" type="danger">确定要删除选中的{{ name }}吗？删除平台将自动解绑所有授权,不可恢复！</el-text>
                        </el-form-item>
                        <el-form-item v-else-if="selectType === 'disable'" label="是否禁用">
                            <el-switch class="mr-2" v-model="is_disable" :active-value="1" :inactive-value="0" />
                            <el-text size="small" v-if="is_disable" type="danger"> 禁用后无法使用！</el-text>
                        </el-form-item>
                        <el-form-item :label="name + 'ID'">
                            <el-input v-model="selectIds" type="input" :rows="18" disabled />
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
import screenHeight from '@/utils/screen-height'
import { clipboard } from '@/utils/index'
import { getPageLimit } from '@/utils/settings'
import { arrayColumn } from '@/utils/index'
import { list, info, add, edit, dele, disable, currencyAllList } from '@/api/platform/platform'
import { useAppStoreHook } from '@/store/modules/app'
const appStore = useAppStoreHook()
export default {
    name: 'Platform',
    data() {
        return {
            loading: false,
            data: [],
            count: 0,
            dialog: false,
            dialogTitle: '',
            name: '平台',
            idkey: 'platform_id',
            is_disable: 0,
            query: {
                page: 1,
                limit: getPageLimit(),
                search_field: 'platform_name',
                search_exp: 'like',
                date_field: 'create_time',
            },
            rules: {
                platform_name: [{ required: true, message: '请输入平台名称', trigger: 'blur' }],
                platform_sign: [{ required: true, message: '请输入平台标识(标识唯一，仅支持数字与字母)', trigger: 'blur' }],
                platform_image: [{ required: true, message: '请上传平台logo', trigger: 'blur' }]
            },
            selection: [],
            selectIds: '',
            selectDialog: false,
            selectTitle: '',
            selectType: '',
            model: {
                platform_image: '',
                logo_url: '',
                platform_name: '',
                platform_sign: '',
                sort: 0,
                params: [],
                project_params: [],
            },
            exps: [],
            currencyAllData: [],
            height: 0,
        }
    },
    created() {
        appStore.changeDetail(0);
        this.list()
        currencyAllList().then((res) => {
            this.currencyAllData = res.data
        })
        this.height = screenHeight(220)
    },
    computed: {
        showDetail() {
            return appStore.showDetail
        }
    },
    methods: {
        checkPermission,
        clipboard,
        selectGetIds(selection) {
            return arrayColumn(selection, this.idkey)
        },
        removeParam(index) {
            if (this.model.params.length > 1) {
                this.model.params.splice(index, 1)
            } else {
                this.model.params[0].name = '';
                this.model.params[0].value = '';
            }
        },

        addParam() {
            let check = false
            this.model.params.forEach((item, index) => {
                if (!item.name || !item.value) {
                    check = true;
                }
            })
            if (check) {
                ElMessage.error('请完善已存在对接参数信息');
                return;
            }
            this.model.params.push({
                key: Date.now(),
                name: '',
                value: '',
            });
        },
        removeProjectParam(index) {
            if (this.model.project_params.length > 1) {
                this.model.project_params.splice(index, 1)
            } else {
                this.model.project_params[0].name = '';
                this.model.project_params[0].field = '';
            }
        },

        addProjectParam() {
            let check = false
            this.model.project_params.forEach((item, index) => {
                if (!item.name || !item.field) {
                    check = true;
                }
            })
            if (check) {
                ElMessage.error('请完善已存在项目参数信息');
                return;
            }
            this.model.project_params.push({
                key: Date.now(),
                name: '',
                value: '',
            });
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
        toDetail(row) {
            this.$router.push({ path: `/platform/detail/${row[this.idkey]}` })
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
                    this.exps = res.data.exps
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
            this.dialogTitle = this.name + '编辑'
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
                    const data = { ...this.model }
                    if (data[this.idkey]) {
                        edit(data)
                            .then((res) => {
                                this.list()
                                this.dialog = false
                                ElMessage.success(res.msg)
                            })
                            .catch(() => {
                                this.loading = false
                            })
                    } else {
                        add(data)
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
                if (this.model.params.length === 0) {
                    this.model.params = this.model.params_default;
                }
                if (this.model.project_params.length === 0) {
                    this.model.project_params = this.model.project_params_default;
                }
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