<template>
    <div>
        <el-scrollbar native :height="height">
            <div class="app-container rounded-md">
                <div class="bg-[--el-bg-color-overlay]  rounded-lg">
                    <el-tabs v-model="activeName" @tab-change="handleTabChange" type="border-card">
                        <el-tab-pane name="project" label="项目列表" lazy>
                            <el-row>
                                <el-col class="mb-4">
                                    <el-button type="primary" @click="openProjectAdd()">添加项目</el-button>
                                    <el-button v-loading.fullscreen.lock="pullLoading" type="warning"
                                        @click="copyProject">克隆项目</el-button>
                                    <el-button type="danger"
                                        @click="disAllProject">一键暂停</el-button>
                                    <el-button type="danger"
                                        @click="delAllProject">一键删除</el-button>
                                    <el-select v-model="projectQuery.search_field" class="ya-search-field mr-2 ml-3"
                                        placeholder="查询字段">
                                        <el-option value="project_pno" label="PID" />
                                        <el-option value="project_no" label="项目编号" />
                                        <el-option value="project_name" label="项目名称" />
                                        <el-option value="project_code" label="国家代码" />
                                    </el-select>
                                    <el-input v-model="projectQuery.search_value" class="ya-search-value mr-2"
                                        placeholder="请输入查询内容" clearable />
                                    <el-button type="success" title="查询" @click="openProjectSearch()">查询</el-button>
                                    <el-button type="default" title="重置" @click="openProjectRefresh()">重置</el-button>
                                </el-col>
                            </el-row>
                            <!-- 列表 -->
                            <el-table ref="projectTable" v-loading="projectloading" :data="projectData"
                                @sort-change="projectSort" stripe @selection-change="projectSelect" :height="height - 200">
                                <el-table-column type="selection" width="42" title="全选/反选" />
                                <el-table-column prop="project_pno" label="PID" min-width="100" fixed="left">
                                    <template #default="scope">
                                        <span>{{ scope.row.project_pno }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column prop="project_no" label="项目编号" width="100">
                                    <template #default="scope">
                                        {{ scope.row.project_no }}
                                    </template>
                                </el-table-column>
                                <el-table-column prop="project_code" label="国家代码">
                                </el-table-column>
                                <el-table-column prop="project_name" label="项目名称"><template
                                        #default="scope">
                                        <span>{{ scope.row.project_name }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column prop="currency.currency_name" label="项目货币"><template
                                        #default="scope">
                                        <span>{{ scope.row.currency.currency_name }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column prop="project_cpi" label="项目CPI"><template
                                        #default="scope">
                                        <span>{{ scope.row.project_cpi }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column prop="project_loi" label="项目LOI">
                                    <template #default="scope">
                                        <span>{{ scope.row.project_loi }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column prop="project_ir" label="项目IR">
                                    <template #default="scope">
                                        <span>{{ scope.row.project_ir }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column prop="project_ir" min-width="120" label="点击/完成/配额">
                                    <template #default="scope">
                                        <span>{{ scope.row.project_click }}/{{ scope.row.project_complete }}/{{ scope.row.project_quota }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column prop="create_time" min-width="160" label="添加时间">
                                    <template #default="scope">
                                        <span>{{ scope.row.create_time }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column prop="update_time" min-width="160" label="更新时间">
                                    <template #default="scope">
                                        <span>{{ scope.row.update_time }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column prop="is_disable_name" label="是否禁用">
                                    <template #default="scope">
                                        <span>{{ scope.row.is_disable_name }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column label="操作" width="180" fixed="right">
                                    <template #default="scope">
                                        <el-button size="small" link type="primary" :underline="false"
                                            @click="openProjectEdit(scope.row)"> 编辑 </el-button>
                                        <el-divider direction="vertical" />
                                        <el-button size="small" link type="primary" :underline="false"
                                            @click="openProjectDisable(scope.row)"> {{
                                                scope.row.is_disable ? '启用' :
                                                    '禁用' }}
                                        </el-button>
                                        <el-divider direction="vertical" />
                                        <el-button size="small" link type="primary" :underline="false"
                                            @click="openProjectDele(scope.row, 0)"> 删除 </el-button>
                                    </template>
                                </el-table-column>
                            </el-table>
                            <!-- 分页 -->
                            <pagination v-show="projectCount > 0" v-model:total="projectCount"
                                v-model:page="projectQuery.page" v-model:limit="projectQuery.limit" size="small"
                                @pagination="projectDataList" />
                        </el-tab-pane>
                        <el-tab-pane name="reclycle" label="回收站" lazy>
                            <el-alert title="手动删除的项目,API拉取后不存在的项目将自动进入回收站,可手动进行恢复和彻底删除" type="error" />
                            <el-row class="mt-4">
                                <el-col class="mb-4">
                                    <el-button type="danger" title="清空" @click="handleClearReclycle()">清空回收站</el-button>
                                    <el-select v-model="projectQuery.search_field" class="ya-search-field mr-2 ml-3"
                                        placeholder="查询字段">
                                        <el-option value="project_pno" label="PID" />
                                        <el-option value="project_no" label="项目编号" />
                                        <el-option value="project_name" label="项目名称" />
                                        <el-option value="project_code" label="国家代码" />
                                    </el-select>
                                    <el-input v-model="projectQuery.search_value" class="ya-search-value mr-2"
                                        placeholder="请输入查询内容" clearable />
                                    <el-button type="primary" title="查询" @click="openProjectSearch()">查询</el-button>
                                    <el-button type="default" title="重置" @click="openProjectRefresh()">重置</el-button>
                                </el-col>
                            </el-row>
                            <!-- 列表 -->
                            <el-table ref="projectTable" v-loading="projectloading" :data="projectData"
                                @sort-change="projectSort" stripe @selection-change="projectSelect"  :height="height - 250">
                                <el-table-column type="selection" width="42" title="全选/反选" />
                                <el-table-column prop="project_pno" label="PID" min-width="100" fixed="left">
                                    <template #default="scope">
                                        <span>{{ scope.row.project_pno }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column prop="project_no" label="项目编号" min-width="120">
                                    <template #default="scope">
                                        <span>{{ scope.row.project_no }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column prop="project_code" label="国家代码"><template
                                        #default="scope">
                                        <span>{{ scope.row.project_code }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column prop="project_name" label="项目名称"><template
                                        #default="scope">
                                        <span>{{ scope.row.project_name }}</span>
                                    </template>
                                </el-table-column>
                        
                                <el-table-column prop="currency.currency_name" label="项目货币"><template
                                        #default="scope">
                                        <span>{{ scope.row.currency.currency_name }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column prop="project_cpi" label="项目CPI"><template
                                        #default="scope">
                                        <span>{{ scope.row.project_cpi }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column prop="project_loi" label="项目LOI">
                                    <template #default="scope">
                                        <span>{{ scope.row.project_loi }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column prop="project_ir" label="项目IR">
                                    <template #default="scope">
                                        <span>{{ scope.row.project_ir }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column prop="project_ir" min-width="120" label="点击/完成/配额">
                                    <template #default="scope">
                                        <span>{{ scope.row.project_click }}/{{ scope.row.project_complete }}/{{ scope.row.project_quota }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column prop="delete_time" label="删除时间">
                                    <template #default="scope">
                                        <span>{{ scope.row.delete_time }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column prop="is_disable_name" label="是否禁用">
                                    <template #default="scope">
                                        <span>{{ scope.row.is_disable_name }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column label="操作" width="280" fixed="right">
                                    <template #default="scope">
                                        <el-button size="small" link type="success" :underline="false"
                                            @click="unDele(scope.row)"> 恢复 </el-button>
                                        <el-divider direction="vertical" />
                                        <el-button size="small" link type="danger" :underline="false"
                                            @click="openProjectDele(scope.row, 1)"> 彻底删除 </el-button>
                                    </template>
                                </el-table-column>
                            </el-table>
                            <!-- 分页 -->
                            <pagination v-show="projectCount > 0" v-model:total="projectCount"
                                v-model:page="projectQuery.page" v-model:limit="projectQuery.limit" size="small"
                                @pagination="projectDataList" />
                        </el-tab-pane>
                    </el-tabs>
                </div>
            </div>
        </el-scrollbar>
        <!-- 添加、编辑 -->
        <el-dialog v-model="projectDialog" :title="projectDialogTitle" :close-on-click-modal="false"
            :close-on-press-escape="false" :before-close="projectCancel" top="5vh" width="1200px">
            <el-form v-if="projectDialog" v-loading="editloading" ref="projectForm" :model="projectModel"
                :rules="projectRules" label-width="80px">
                <el-tabs>
                    <el-tab-pane label="基础信息">
                        <el-form-item label="所属平台" prop="platform_id">
                            <el-select v-model="projectModel.platform_id" class="w-full" clearable filterable>
                                <el-option v-for="item in platforms" :key="item.platform_id" :label="item.platform_name"
                                    :value="item.platform_id" />
                            </el-select>
                        </el-form-item>
                        <el-form-item label="项目编号" prop="project_no">
                            <el-input v-model="projectModel.project_no" clearable />
                        </el-form-item>
                        <el-form-item label="项目名称" prop="project_name">
                            <el-input v-model="projectModel.project_name" clearable />
                        </el-form-item>
                        <el-form-item label="项目配额" prop="project_quota">
                            <el-input-number style="width: 100%" controls-position="right" :min="0" :max="10000"
                                :step="1" v-model="projectModel.project_quota" clearable />
                        </el-form-item>
                        <el-form-item label="Cpi" prop="project_cpi">
                            <el-input-number style="width: 100%" :precision="2" controls-position="right" :min="0.01"
                                :max="10000" :step="0.01" v-model="projectModel.project_cpi" clearable />
                        </el-form-item>
                        <el-form-item label="Loi" prop="project_quota">
                            <el-input-number style="width: 100%" controls-position="right" :min="0" :max="10000"
                                :step="1" v-model="projectModel.project_loi" clearable />
                        </el-form-item>
                        <el-form-item label="Ir" prop="project_quota">
                            <el-input-number style="width: 100%" controls-position="right" :min="0" :max="10000"
                                :step="1" v-model="projectModel.project_ir" clearable />
                        </el-form-item>
                        <el-form-item label="项目货币" prop="project_currency">
                            <el-select v-model="projectModel.project_currency" placeholder="请选择" clearable filterable>
                                <el-option v-for="(item, index) in currencyAllData" :key="index"
                                    :label="item.currency_name" :value="item.currency_id" />
                            </el-select>
                        </el-form-item>
                        <el-form-item label="国家代码" prop="project_code">
                            <el-input v-model="projectModel.project_code" clearable />
                        </el-form-item>
                        <el-form-item label="项目链接" prop="project_click_url">
                            <el-input v-model="projectModel.project_click_url" clearable />
                        </el-form-item>
                        
                        <!--
                        <el-form-item label="热门推荐" prop="is_recommend">
                            <el-radio-group v-model="projectModel.is_recommend">
                                <el-radio class="model-mr" :value="0">不推荐</el-radio>
                                <el-radio class="model-mr" :value="1">推荐</el-radio>
                            </el-radio-group>
                        </el-form-item>
                        <el-form-item label="前置人设">
                            <el-select v-model="projectModel.project_persona_template" class="w-full">
                                <el-option label="不启用人设" :value="0" />
                                <el-option v-for="item in frontPersonaData"
                                    :label="item.persona_name + ' -【' + (item.persona_type == 0 ? '前置' : '后置') + '】'"
                                    :value="item.persona_id" />
                            </el-select>
                            <el-text type="danger">项目前置人设模版,优先级高于平台前置人设模版。</el-text>
                        </el-form-item>
                        <el-form-item label="后置人设">
                            <el-select v-model="projectModel.project_persona_backend" class="w-full">
                                <el-option label="不启用人设" :value="0" />
                                <el-option v-for="item in backendPersonaData"
                                    :label="item.persona_name + ' -【' + (item.persona_type == 0 ? '前置' : '后置') + '】'"
                                    :value="item.persona_id" />
                            </el-select>
                            <el-text type="danger">项目后置人设模版,优先级高于平台后置人设模版。</el-text>
                        </el-form-item>-->
                        <el-form-item v-for="(param, index) in projectModel.project_params" :key="param.key"
                            :label="param.name" :prop="'project_params.' + index + '.name'">
                            <div class="flex w-full">
                                <el-input class="mr-2 flex-1" v-model="param.value" :placeholder="'请输入' + param.name" />
                            </div>
                        </el-form-item>
                    </el-tab-pane>
                    <el-tab-pane label="文档信息">
                        <el-form-item label="内容" prop="project_content">
                            <vue-ueditor-wrap v-model="projectModel.project_content" editor-id="editor"
                                :config="editorConfig" :editorDependencies="['ueditor.config.js', 'ueditor.all.js']"
                                @ready="handleEditorReady" />
                        </el-form-item>
                        <el-form-item label="文件" prop="words">
                            <FileImage v-model="projectModel.project_file_id" fileType="word" file-title="上传项目文档"
                                :file-url="projectModel.project_file_url" file-tip="项目文档优先级大于内容,上传后将优先显示" :height="120"
                                upload />
                        </el-form-item>
                    </el-tab-pane>
                </el-tabs>
            </el-form>
            <template #footer>
                <el-button :loading="projectLoading" @click="projectCancel">取消</el-button>
                <el-button :loading="projectLoading" type="primary" @click="projectSubmit">提交</el-button>
            </template>
        </el-dialog>
        <el-dialog v-model="fileDialog" :title="fileTitle" :close-on-click-modal="false" :close-on-press-escape="false"
            top="1vh" width="80%" append-to-body>
            <FileManage :file-type="fileType" @file-cancel="fileCancel" @file-submit="fileSubmit" />
        </el-dialog>
    </div>
</template>
<script>
import checkPermission from '@/utils/permission'
import { clipboard } from '@/utils/index'
import { getPageLimit } from '@/utils/settings'
import { platlist, currencyAllList } from '@/api/platform/platform'
import { list as projectList, info as projectInfo, add as projectAdd, edit as projectEdit, dele as projectDele, disable as projectDisable, copy, restore, clearReclycle,disall,delall } from '@/api/project/project'
import screenHeight from '@/utils/screen-height'
import { arrayColumn } from '@/utils/index'
import { ElMessage, ElMessageBox } from 'element-plus'
import FileImage from '@/components/FileManage/FileImage.vue'
import FileManage from '@/components/FileManage/index.vue'
export default {
    name: 'PlatformDetail',
    components: { FileManage },
    data() {
        return {
            activeName: 'project',
            fileDialog: false,
            fileTitle: '',
            fileType: '',
            fileAction: '',
            loading: true,
            editloading: false,
            projectData: [],
            projectCount: 0,
            projectloading: false,
            projectDialog: false,
            projectDialogTitle: '',
            projectQuery: {
                page: 1,
                limit: getPageLimit(),
                search_field: 'project_pno',
                search_exp: 'like',
                date_field: 'create_time'
            },
            projectModel: {
                project_no: '',
                project_name: '',
                project_content: '',
                sort: 0,
                project_params: [],
            },
            projectRules: {
                platform_id: [{ required: true, message: '请选择平台', trigger: 'blur' }],
                project_no: [{ required: true, message: '请输入项目ID', trigger: 'blur' }],
                project_name: [{ required: true, message: '请输入项目名称', trigger: 'blur' }],
                project_cpi: [{ required: true, message: '请输入项目金额', trigger: 'blur' }],
                project_currency: [{ required: true, message: '请选择项目货币', trigger: 'blur' }],
                project_cpi: [{ required: true, message: '请输入项目CPI', trigger: 'blur' }],
                project_quota: [{ required: true, message: '请输入项目配额', trigger: 'blur' }],
                project_loi: [{ required: true, message: '请输入项目LOI', trigger: 'blur' }],
                project_ir: [{ required: true, message: '请输入项目IR', trigger: 'blur' }],
                project_code: [{ required: true, message: '请输入项目国家代码', trigger: 'blur' }],
                project_click_url: [{ required: true, message: '请输入项目链接', trigger: 'blur' }],
            },
            currencyAllData: [],
            height: 680,
            pullLoading: false,
            selection: [],
            projectType: 0,
            editorConfig: {
                serverUrl: import.meta.env.VITE_APP_BASE_URL + '/admin/file.File/ueditor_add',
                autoHeightEnabled: false,
                // 初始容器高度
                initialFrameHeight: 400,
                serverHeaders: {
                    AdminToken: localStorage.getItem('admin_AdminToken'),
                },
                // 初始容器宽度
                initialFrameWidth: '100%',
                // 配置UEditorPlus的惊天资源
                UEDITOR_HOME_URL: '/admin/public/ueditor/',
                imageActionName: "uploadimage",
                imageFieldName: "file",
                imageUrlPrefix: "",
                imageMaxSize: 2048000,
                imageAllowFiles: [".png", ".jpg", ".jpeg", ".gif"],
                toolbarCallback: (cmd, editor) => {
                    console.log('toolbarCallback', cmd, editor);
                    switch (cmd) {
                        case 'insertimage':
                            this.fileAction = 'insertimage'
                            this.fileDialog = true
                            this.fileType = 'image'
                            return true;
                        case 'insertvideo':
                            this.fileAction = 'insertvideo'
                            this.fileDialog = true
                            this.fileType = 'video'
                            return true;
                        case 'insertaudio':
                            this.fileAction = 'insertaudio'
                            this.fileDialog = true
                            this.fileType = 'audio'
                            return true;
                        case 'attachment':
                            this.fileAction = 'attachment'
                            this.fileDialog = true
                            this.fileType = 'attachment'
                            return true;
                        case 'insertframe':
                            ElMessageBox.prompt('请输入网络地址', '插入Iframe', {
                                confirmButtonText: 'OK',
                                cancelButtonText: 'Cancel',
                            })
                                .then(({ value }) => {
                                    this.editor.execCommand('insertHtml', '<p><iframe style="width:100%;height:300px" src="'+value+'" /></p>');
                                })
                                .catch(() => {
                                })
                            
                            return true;
                    }
                }

            },
            platforms: [],
        }
    },
    async created() {
        await currencyAllList().then((res) => {
            this.currencyAllData = res.data
        })
        platlist().then((res) => {
            this.platforms = res.data
        })
        this.projectDataList()
        this.height = screenHeight(100)
    },
    computed: {

    },
    methods: {
        checkPermission,
        clipboard,
        fileCancel() {
            this.fileDialog = false
        },
        fileSubmit(fileList) {
            this.fileDialog = false
            const fileLength = fileList.length
            if (fileLength) {
                const index = fileLength - 1
                if (this.fileAction === 'insertimage') {
                    this.editor.execCommand('insertimage', {
                        src: fileList[index]['file_url'],
                        width: '100%', // 可选样式
                    });
                }
                if (this.fileAction === 'insertaudio') {
                    this.editor.execCommand('insertHtml', '<p><audio src="' + fileList[index]['file_url'] + '" /></p>');
                }
                if (this.fileAction === 'insertvideo') {
                    this.editor.execCommand('insertHtml', '<p><video controls><source src="' + fileList[index]['file_url'] + '" type="video/mp4"></video></p>');
                }
                if (this.fileAction === 'attachment') {
                    this.editor.execCommand('insertHtml', '<p><a href="' + fileList[index]['file_url'] + '">' + fileList[index]['file_name'] + '</a></p>');
                }
            }
        },
        disAllProject() {
            ElMessageBox.confirm(
                '确定禁用全部项目吗?',
                '禁用全部项目',
                {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning',
                },
            ).then(() => {
                disall().then((res) => {
                    ElMessage.success('禁用成功')
                    this.projectDataList()
                })
            })
        },
        delAllProject() {
            ElMessageBox.confirm(
                '确定删除全部项目吗?',
                '删除全部项目',
                {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning',
                },
            ).then(() => {
                delall().then((res) => {
                    ElMessage.success('删除成功')
                    this.projectDataList()
                })
            })
        },
        handleEditorReady(editor) {
            this.editor = editor
        },
        projectSelect(selection) {
            this.selection = selection
            this.selectIds = this.selectGetIds(selection).toString()
        },
        selectGetIds(selection) {
            return arrayColumn(selection, 'project_id')
        },
        copyProject() {
            if (this.selection.length === 0) {
                ElMessage.error('请选择要复制的项目');
                return;
            }
            if (this.selection.length > 1) {
                ElMessage.error('只能选择一个项目进行复制');
                return;
            }
            this.editloading = true
            this.projectDialog = true
            this.projectDialogTitle = '添加项目'
            projectInfo({project_id: this.selection[0].project_id})
                .then((res) => {
                    this.projectModel = {
                        ...res.data,
                        project_id: '',
                        project_click:0,
                        project_complete:0
                    }
                    this.editloading = false;
                })
                .catch(() => { })

        },
        handleTabChange(val) {
            if (val === 'project') {
                this.projectType = 0
                this.projectQuery = this.$options.data().projectQuery
                this.projectDataList()
            } else if (val === 'reclycle') {
                this.projectType = 1
                this.projectQuery = this.$options.data().projectQuery
                this.projectDataList()
            }
        },
        projectSort(sort) {
            // 排序
            this.projectQuery.sort_field = sort.prop
            this.projectQuery.sort_value = ''
            if (sort.order === 'ascending') {
                this.projectQuery.sort_value = 'asc'
                this.projectDataList()
            }
            if (sort.order === 'descending') {
                this.projectQuery.sort_value = 'desc'
                this.projectDataList()
            }
        },
        unDele(row) {
            restore({ project_id: row.project_id }).then((res) => {
                ElMessage.success('恢复成功');
                this.projectDataList()
            })
        },
        handleClearReclycle() {
            ElMessageBox.confirm(
                '确定清空回收站吗?',
                '清空回收站',
                {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning',
                },
            ).then(() => {
                clearReclycle().then((res) => {
                    ElMessage.success('清理成功');
                    this.projectDataList()
                })
            })
        },
        openProjectAdd() {
            this.projectDialog = true
            this.projectDialogTitle = '添加项目'
            this.projectReset()
        },
        openProjectSearch() {
            this.projectQuery.page = 1
            this.projectDataList()
        },
        openProjectRefresh() {
            const limit = this.projectQuery.limit
            this.projectQuery = this.$options.data().projectQuery
            this.projectQuery.limit = limit
            this.projectDataList()
        },
        openProjectEdit(row) {
            this.projectDialog = true
            this.projectDialogTitle = '编辑项目'
            var id = {}
            id['project_id'] = row['project_id']
            this.editloading = true
            projectInfo(id)
                .then((res) => {

                    this.projectReset(res.data)
                    this.editloading = false;
                })
                .catch(() => { })
        },
        openProjectDisable(row) {
            ElMessageBox.confirm(
                '禁用后用户无法使用该项目,确定' + (row.is_disable ? '启用' : '禁用') + '该项目吗?',
                (row.is_disable ? '启用' : '禁用') + '项目',
                {
                    confirmButtonText: '确认',
                    cancelButtonText: '取消',
                    type: 'warning',
                }
            )
                .then(() => {
                    projectDisable({ ids: [row.project_id], is_disable: row.is_disable == 1 ? 0 : 1 }).then((res) => {
                        ElMessage.success(res.msg)
                        this.projectDataList()
                    })
                })
                .catch(() => {
                })
        },
        // 重置
        projectReset(row) {

            if (row) {
                this.projectModel = row
                if (this.projectModel.project_file_id > 0) {
                    this.fileModel = this.projectModel.project_file
                } else {
                    this.fileModel = this.$options.data().fileModel
                }
            } else {
                this.projectModel = this.$options.data().projectModel
            }
            if (this.$refs['projectRef'] !== undefined) {
                try {
                    this.$refs['projectRef'].resetFields()
                    this.$refs['projectRef'].clearValidate()
                } catch (error) { }
            }
            console.log(this.projectModel)
        },
        openProjectDele(row, st) {
            ElMessageBox.confirm(
                st == 1 ? '确定彻底删除该项目吗?彻底删除项目不可恢复' : '确定删除该项目吗?',
                '删除项目',
                {
                    confirmButtonText: '确认',
                    cancelButtonText: '取消',
                    type: 'warning',
                }
            )
                .then(() => {
                    projectDele({ ids: [row.project_id], st: st }).then((res) => {
                        ElMessage.success(res.msg)
                        this.projectDataList()
                    })
                })
                .catch(() => {
                })
        },
        projectCancel() {
            this.projectDialog = false
        },
        projectSubmit() {
            this.$refs.projectForm.validate((valid) => {
                if (valid) {
                    this.projectLoading = true
                    if (this.projectModel['project_id']) {
                        projectEdit(this.projectModel)
                            .then((res) => {
                                ElMessage.success(res.msg)
                                this.projectDialog = false
                                this.projectLoading = false
                                this.projectDataList()
                            })
                            .catch(() => {
                                this.projectLoading = false
                            })
                    } else {
                        projectAdd(this.projectModel)
                            .then((res) => {
                                ElMessage.success(res.msg)
                                this.projectDialog = false
                                this.projectLoading = false
                                this.projectDataList()
                            })
                            .catch(() => {
                                this.projectLoading = false
                            })
                    }
                } else {
                    ElMessage.error('请完善必填项（带红色星号*）')
                }
            })
        },
        projectDataList() {
            this.projectloading = true
            const data = {
                ...this.projectQuery,
                projectType: this.projectType
            }
            projectList(data)
                .then((res) => {
                    this.projectData = res.data.list
                    this.projectData.forEach((item) => {
                        if (item.project_params) {
                            item.project_params = JSON.parse(item.project_params)
                        }
                    })
                    this.projectCount = res.data.count
                    this.projectloading = false
                })
                .catch(() => {
                    this.projectData = []
                    this.projectCount = 0
                    this.projectloading = false
                })

        },
        // 重置
        reset(row) {
            if (row) {
                this.model = row
            } else {

                this.model = this.$options.data().model
            }
        },
    }
}
</script>
<style lang="scss" scoped>
::v-deep(.el-input-number .el-input__inner) {
    text-align: left;
}

::v-deep(.el-input-number.is-controls-right .el-input__wrapper) {
    padding-left: 10px;
}

.model-mr {
    margin-right: 15px;
}
</style>