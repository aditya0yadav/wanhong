<template>
    <div>
        <div class="app-container" v-show="showDetail == 0">
            <!-- 查询 -->
            <el-row>
                <el-col class="mb-4">
                    <el-button type="primary" @click="add()">添加</el-button>
                    <el-button type="warning" @click="copyPersona">克隆模版</el-button>
                    <el-input v-model="query.search_value" class="ya-search-value mr-2 ml-2" placeholder="请输入查询内容"
                        clearable />
                    <el-button type="primary" title="查询/刷新" @click="search()">查询</el-button>
                    <el-button type="default" title="重置查询条件" @click="refresh()">重置</el-button>
                </el-col>

            </el-row>
            <!-- 列表 -->
            <el-table ref="table" v-loading="loading" :data="data" :height="height" @sort-change="sort" stripe
                @selection-change="select">
                <el-table-column type="selection" width="42" title="全选/反选" />
                <el-table-column :prop="idkey" label="ID" width="80" sortable="custom" />
                <el-table-column prop="persona_name" label="模版名称" />
                <el-table-column prop="persona_type" label="模版类型">
                    <template #default="scope">
                        <span v-if="scope.row.persona_type == 0">前置</span><span v-else>后置</span>
                    </template>
                </el-table-column>
                <el-table-column prop="sort" label="排序" width="85" sortable="custom" />
                <el-table-column label="操作" width="240">
                    <template #default="scope">
                        <el-button link type="primary" @click="toDetail(scope.row)"> 内容管理
                        </el-button><el-divider direction="vertical" />
                        <el-button link type="primary" @click="edit(scope.row)"> 编辑
                        </el-button><el-divider direction="vertical" />
                        <el-button size="small" link type="primary" @click="selectOpen('dele', [scope.row])"> 删除
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
            <!-- 分页 -->
            <pagination v-show="count > 0" v-model:total="count" v-model:page="query.page" v-model:limit="query.limit"
                size="small" @pagination="list" />
            <!-- 添加修改 -->
            <el-dialog v-model="dialog" :title="dialogTitle" :close-on-click-modal="false"
                :close-on-press-escape="false" :before-close="cancel" top="5vh" width="700px">
                <el-form label-position="top" ref="ref" :model="model" :rules="rules" label-width="100px">
                    <el-form-item label="模版名称" prop="persona_name">
                        <el-input key="platform_name" v-model="model.persona_name" placeholder="请输入模版名称" clearable />
                    </el-form-item>
                    <el-form-item label="模版类型" prop="persona_type">
                        <el-select placeholder="请选择模版类型" v-model="model.persona_type">
                            <el-option label="前置" :value="0"></el-option>
                            <el-option label="后置" :value="1"></el-option>
                        </el-select>
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
                        <el-form-item v-if="selectType === 'dele'" label="删除">
                            <el-text size="default" type="danger">确定要删除选中的{{ name }}吗？</el-text>
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
            <el-dialog v-model="personaDialog" :title="activePersona.persona_name + '-【人设模版内容管理】'"
                :close-on-click-modal="false" :close-on-press-escape="false" top="20vh" width="1000px">
                <el-scrollbar native :height="height - 200">
                    <el-row>
                        <el-col class="mb-4">
                            <el-button plain type="primary" @click="addData()">添加</el-button>
                        </el-col>

                    </el-row>
                    <!-- 列表 -->
                    <el-table ref="datatable" v-loading="loading" :data="rsData" :height="400">
                        <el-table-column prop="persona_data_id" label="ID" width="80" sortable="custom" />
                        <el-table-column prop="persona_data_name" label="人设问题" min-width="170" />
                        <el-table-column label="类型">
                            <template #default="scope">
                                <span v-if="scope.row.persona_data_type == 'input'">输入框</span>
                                <span v-if="scope.row.persona_data_type == 'radio'">单选框</span>
                                <span v-if="scope.row.persona_data_type == 'select'">多选框</span>
                                <span v-if="scope.row.persona_data_type == 'date'">日期框</span>
                                <span v-if="scope.row.persona_data_type == 'image'">图片</span>
                            </template>
                        </el-table-column>
                        <el-table-column prop="persona_data_holder" label="提示文字" min-width="170" />
                        <el-table-column label="是否必填">
                            <template #default="scope">
                                <span v-if="scope.row.persona_data_must == 1">是</span><span v-else>否</span>
                            </template>
                        </el-table-column>
                        <el-table-column prop="sort" label="排序" width="85" sortable="custom" />
                        <el-table-column label="操作" width="120">
                            <template #default="scope">
                                <el-button link type="primary" @click="editData(scope.row)"> 编辑
                                </el-button>
                                <el-button link type="danger" @click="deleData(scope.row)"> 删除
                                </el-button>
                            </template>
                        </el-table-column>
                    </el-table>
                </el-scrollbar>
            </el-dialog>
            <!-- 添加修改 -->
            <el-dialog v-model="dataDialog" :title="dataDialogTitle" :close-on-click-modal="false"
                :close-on-press-escape="false" :before-close="cancelData" top="5vh" width="500px">
                <el-form label-position="top" ref="dataRef" :model="dataModel" :rules="dataRules" label-width="100px">
                    <el-form-item label="人设问题" prop="persona_data_name">
                        <el-input key="persona_data_name" v-model="dataModel.persona_data_name" placeholder="请输入人设问题"
                            clearable />
                    </el-form-item>
                    <el-form-item label="人设类型" prop="persona_data_type">
                        <el-select v-model="dataModel.persona_data_type" placeholder="请选择人设类型">
                            <el-option label="输入框" value="input" />
                            <el-option label="单选框" value="radio" />
                            <el-option label="多选框" value="select" />
                            <el-option label="日期框" value="date" />
                            <el-option label="图片" value="image" />
                        </el-select>
                    </el-form-item>
                    <el-form-item
                        v-if="dataModel.persona_data_type === 'radio' || dataModel.persona_data_type === 'select'"
                        v-for="(param, index) in dataModel.persona_data_values" :key="param.key"
                        :label="'答案选项 [' + (index + 1) + ']'" :prop="'persona_data_values.' + index + '.value'">
                        <div class="flex w-full">
                            <el-input class="mr-2 flex-1" v-model="param.value" placeholder="请输入答案选项" />
                            <el-button type="danger" plain @click.prevent="removeParam(index)">删除</el-button>
                        </div>
                    </el-form-item>
                    <el-form-item label="提示文字" prop="persona_data_holder">
                        <el-input key="persona_data_holder" v-model="dataModel.persona_data_holder" placeholder="请输入提示文字,不填使用默认方式"
                            clearable />
                    </el-form-item>
                    <el-button class="mb-4"
                        v-if="dataModel.persona_data_type === 'radio' || dataModel.persona_data_type === 'select'"
                        type="primary" plain @click.prevent="addParam">添加答案选项</el-button>
                    <el-form-item label="是否必填" prop="persona_data_must">
                        <el-select v-model="dataModel.persona_data_must" placeholder="是否必填">
                            <el-option label="否" :value="0" />
                            <el-option label="是" :value="1" />
                        </el-select>
                    </el-form-item>
                    <el-form-item label="排序" prop="sort">
                        <el-input v-model="dataModel.sort" type="number" />
                    </el-form-item>

                </el-form>
                <template #footer>
                    <el-button :loading="loading" @click="cancelData">取消</el-button>
                    <el-button :loading="loading" type="primary" @click="submitData">提交</el-button>
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
import { list, info, add, edit, dele, dataList, dataInfo, dataAdd, dataEdit, dataDele,copy } from '@/api/platform/persona'
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
            name: '人设模版',
            idkey: 'persona_id',
            is_disable: 0,
            activePersona: {},
            personaDialog: false,
            dataDialog: false,
            dataDialogTitle: '',
            query: {
                page: 1,
                limit: getPageLimit(),
                search_field: 'persona_name',
                search_exp: 'like',
                date_field: 'create_time',
            },
            rules: {
                persona_name: [{ required: true, message: '请输入平台名称', trigger: 'blur' }]
            },
            selection: [],
            selectIds: '',
            selectDialog: false,
            selectTitle: '',
            selectType: '',
            model: {
                persona_name: '',
                persona_type:0,
                sort: 0,
            },
            dataModel: {
                persona_data_name: '',
                persona_data_type: '',
                persona_data_holder:'',
                persona_data_must: 0,
                sort: 0,
                persona_data_values: []
            },
            dataRules: {
                persona_data_name: [{ required: true, message: '请输入人设问题', trigger: 'blur' }],
                persona_data_type: [{ required: true, message: '请选择人设类型', trigger: 'blur' }],
                persona_data_must: [{ required: true, message: '请选择是否必填', trigger: 'blur' }],
            },
            exps: [],
            currencyAllData: [],
            rsData: []
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
        select(selection) {
            this.selection = selection
            this.selectIds = this.selectGetIds(selection).toString()
        },
        copyPersona() {
            if (this.selection.length === 0) {
                ElMessage.error('请选择要复制的项目');
                return;
            }
            this.loading = true
            copy({ ids: this.selectIds }).then((res) => {
                this.query.page = 1
                this.loading = false
                ElMessage.success('克隆完成');
                this.list()
            }) 
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
            this.activePersona = { ...row }
            this.personaDialog = true;
            this.getDatalist();
        },
        editData(row) {
            this.dataModel = { ...row }
            this.dataDialog = true
            if (row.persona_data_values) {
                this.dataModel.persona_data_values = JSON.parse(row.persona_data_values)
            } else {
                this.dataModel.persona_data_values = [];
            }
            this.dataDialogTitle = '人设编辑'
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
        async getDatalist() {
            this.loading = true
            const res = await dataList({ persona_id: this.activePersona.persona_id })
            this.rsData = res.data.list
            this.loading = false
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
        addData() {
            this.dataDialog = true
            this.dataModel = {
                persona_id: this.activePersona.persona_id,
                persona_data_name: '',
                persona_data_type: '',
                persona_data_holder: '',
                persona_data_must: 0,
                sort: 0,
                persona_data_values: []
            }
            this.dataDialogTitle = '人设添加'
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
        cancelData() {
            this.dataDialog = false
            this.resetData()
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
        submitData() {
            this.$refs['dataRef'].validate((valid) => {
                if (valid) {
                    const data = { ...this.dataModel }
                    if ((data['persona_data_type'] == 'select' || data['persona_data_type'] == 'radio') && data['persona_data_values'].length === 0) {
                        ElMessage.error('单选/多选类型的选项不能为空')
                        return;
                    }
                    if (data['persona_data_type'] == 'select' || data['persona_data_type'] == 'radio') {
                        data['persona_data_values'] = JSON.stringify(data['persona_data_values']);
                    } else {
                        data['persona_data_values'] = ''
                    }
                    data['persona_id'] = this.activePersona.persona_id
                    this.loading = true
                    if (data['persona_data_id']) {
                        dataEdit(data)
                            .then((res) => {
                                this.getDatalist()
                                this.loading = false
                                this.dataDialog = false
                                ElMessage.success(res.msg)
                            })
                            .catch(() => {
                                this.loading = false
                            })
                    } else {
                        dataAdd(data).then((res) => {
                            this.getDatalist()
                            this.dataDialog = false
                            ElMessage.success(res.msg)
                        }).catch(() => {
                            this.loading = false
                        })
                    }
                }
            })
        },
        deleData(row) {
            dataDele({ ids: [row.persona_data_id] }).then((res) => {
                this.getDatalist();
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
        resetData() {
            if (this.$refs['dataRef'] !== undefined) {
                try {
                    this.$refs['dataRef'].resetFields()
                    this.$refs['dataRef'].clearValidate()
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
        removeParam(index) {
            this.dataModel.persona_data_values.splice(index, 1);
        },

        addParam() {
            this.dataModel.persona_data_values.push({
                key: Date.now(),
                value: '',
            });
        },
    }
}
</script>