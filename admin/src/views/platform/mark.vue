<template>
    <div>
        <div class="app-container">
            <!-- 查询 -->
            <el-row>
                <el-col class="mb-4">
                    <el-button type="primary" @click="openMarkAdd()">添加</el-button>
                    <el-input v-model="MarkQuery.search_value" class="ya-search-value mr-2 ml-2" placeholder="请输入查询内容"
                        clearable />
                    <el-button type="success" title="查询/刷新" @click="openMarkSearch()">查询</el-button>
                    <el-button type="default" title="重置查询条件" @click="openMarkRefresh()">重置</el-button>
                </el-col>
            </el-row>
            <!-- 列表 -->
            <el-table ref="MarkTable" v-loading="MarkLoading" :data="MarkData">
                <el-table-column prop="mark_id" label="ID" width="80" sortable="custom" />
                <el-table-column prop="mark_name" label="标记名称"  />
                <el-table-column prop="mark_ename" label="标记英文名称" />
                    <el-table-column prop="sort" label="排序" />
                <el-table-column label="操作" width="240">
                    <template #default="scope">
                        <el-button link type="primary" @click="toDetail(scope.row)"> 标记记录
                        </el-button><el-divider direction="vertical" />
                        <el-button link type="primary" @click="openMarkEdit(scope.row)"> 编辑
                        </el-button><el-divider direction="vertical" />
                        <el-button link type="primary" @click="openMarkDele(scope.row)"> 删除
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
            <!-- 分页 -->
            <pagination v-show="MarkCount > 0" v-model:total="MarkCount" v-model:page="MarkQuery.page" v-model:limit="MarkQuery.limit"
                 @pagination="MarkDataList" />
            <!-- 添加修改 -->
            <el-dialog v-model="MarkDialog" :title="MarkTitle" :close-on-click-modal="false"
                :close-on-press-escape="false" :before-close="cancel" top="5vh" width="700px">
                <el-form label-position="top" ref="MarkRef" :model="MarkModel" :rules="rules" label-width="100px">
                    <el-form-item label="标记名称" prop="mark_name">
                        <el-input key="mark_name" v-model="MarkModel.mark_name" placeholder="请输入标记名称" clearable />
                    </el-form-item>
                    <el-form-item label="标记名称英文" prop="mark_name">
                        <el-input key="mark_ename" v-model="MarkModel.mark_ename" placeholder="请输入标记英文名称" clearable />
                    </el-form-item>
                    <el-form-item label="排序" prop="sort">
                        <el-input key="sort" v-model="MarkModel.sort" type="number" clearable />
                    </el-form-item>
                </el-form>
                <template #footer>
                    <el-button :loading="MarkLoading" @click="MarkCancel">取消</el-button>
                    <el-button :loading="MarkLoading" type="primary" @click="MarkSubmit">提交</el-button>
                </template>
            </el-dialog>
            <el-dialog v-model="detailDialog" title="标记记录" :close-on-click-modal="false"
                :close-on-press-escape="false" :before-close="cancelDetail" top="5vh" width="1000px">
                <el-table height="500" ref="detailTable" v-loading="detailLoading" :data="detailData">
                    <el-table-column prop="mark_detail_id" label="ID" width="80" />
                    <el-table-column prop="mark_user_id" label="标记人"
                        show-overflow-tooltip />
                    <el-table-column prop="mark.mark_name" label="标记内容"
                        show-overflow-tooltip />
                    <el-table-column prop="mark_project_pno" label="标记项目PID"
                        show-overflow-tooltip />
                    <el-table-column prop="mark_project_no" label="标记项目编号"
                        show-overflow-tooltip />
                    <el-table-column prop="create_time" label="标记时间" width="165" sortable="custom" />
                    <el-table-column label="操作" width="240">
                        <template #default="scope">
                            <el-button  link type="primary" @click="openDetailDele(scope.row)"> 删除
                            </el-button>
                        </template>
                    </el-table-column>
                </el-table>
                <pagination v-show="detailCount > 0" v-model:total="detailCount" v-model:page="detailQuery.page" v-model:limit="detailQuery.limit"
                     @pagination="MarkDetailDataList" />
            </el-dialog>
        </div>
    </div>
</template>
<script>
import checkPermission from '@/utils/permission'
import { clipboard } from '@/utils/index'
import { getPageLimit } from '@/utils/settings'
import { arrayColumn } from '@/utils/index'
import { MarkList,MarkAdd,MarkEdit,MarkDele,MarkDetailList,MarkDetailDele } from '@/api/platform/mark'
import { useAppStoreHook } from '@/store/modules/app'
const appStore = useAppStoreHook()
export default {
    name: 'Mark',
    data() {
        return {
            MarkQuery: {
                page: 1,
                limit: getPageLimit(),
                search_field: 'mark_name',
                search_exp: 'like',
                date_field: 'create_time',
            },
            detailQuery: {
                page: 1,
                limit: getPageLimit(),
            },
            MarkLoading: false,
            MarkData: [],
            MarkDount: 0,
            MarkModel: {
                mark_name: '',
                sort:0
            },
            MarkDialog: false,
            MarkDialogTitle: '',
            detailDialog: false,
            detailModel: {},
            detailLoading: false,
            detailData: [],
            detailCount: 0,
        }
    },
    created() {
        this.MarkDataList()
    },
    methods: {
        MarkDataList() {
            this.MarkLoading = true
            MarkList(this.MarkQuery).then(res => {
                this.MarkData = res.data.list
                this.MarkCount = res.data.count
                this.MarkLoading = false
            }).catch(() => {
                this.MarkLoading = false
            })
        },
        MarkCancel() {
            this.MarkDialog = false
            this.MarkModel = {
                Mark_name: '',
                Mark_code: '',
                Mark_coins: '',
            }
        },
        toDetail(row) {
            this.detailDialog = true
            this.detailModel = { ...row }
            setTimeout(() => {
                this.MarkDetailDataList()
            },200)
        },
        cancelDetail() {
            this.detailDialog = false
        },
        MarkSubmit() {
            this.$refs.MarkRef.validate(valid => {
                if (valid) {
                    this.MarkLoading = true
                    if (this.MarkModel.mark_id) {
                        MarkEdit(this.MarkModel).then(res => {
                            this.MarkLoading = false
                            this.MarkCancel()
                            this.MarkDataList()
                            ElMessage.success(res.msg)
                        }).catch(() => {
                            this.MarkLoading = false
                        })
                    } else {
                         MarkAdd(this.MarkModel).then(res => {
                            this.MarkLoading = false
                            this.MarkCancel()
                            this.MarkDataList()
                            ElMessage.success(res.msg)
                        }).catch(() => {
                            this.MarkLoading = false
                         })   
                        
                    }
                }
            })
        },
        openMarkAdd() {
            this.MarkDialog = true
            this.MarkTitle = '添加标记'
            this.MarkReset()
        },
        openMarkEdit(row) {
            this.MarkDialog = true
            this.MarkTitle = '编辑标记'
            this.MarkReset(row)
        },
        MarkReset(row) {
            if (row) {
                this.MarkModel = { ...row}
            } else {
                this.MarkModel = this.$options.data().MarkModel
            }
            if (this.$refs['MarkRef'] !== undefined) {
                try {
                    this.$refs['MarkRef'].resetFields()
                    this.$refs['MarkRef'].clearValidate()
                } catch (error) { }
            }
        },
        openMarkDele(row) {
            ElMessageBox.confirm('此操作将删除该标记, 是否继续?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning',
            }).then(() => {
                MarkDele({ids: [row.mark_id]}).then(res => {
                    ElMessage.success(res.msg)
                    this.MarkDataList()
                })
            }).catch(() => {
            })
        },
        openDetailDele(row) {
            ElMessageBox.confirm('此操作将删除该标记, 是否继续?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning',
            }).then(() => {
                MarkDetailDele({ids: [row.mark_detail_id]}).then(res => {
                    ElMessage.success(res.msg)
                    this.MarkDetailDataList()
                })
            }).catch(() => {
            })
        },
        MarkDetailDataList() {
            this.detailLoading = true
            MarkDetailList({mark_id:this.detailModel.mark_id,...this.detailQuery}).then(res => {
                this.detailData = res.data.list
                this.detailCount = res.data.count
                this.detailLoading = false
            }).catch(() => {
                this.detailLoading = false
            })
        },
    }
}
</script>