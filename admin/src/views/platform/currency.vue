<template>
    <div>
        <div class="app-container">
            <!-- 查询 -->
            <el-row>
                <el-col class="mb-4">
                    <el-button type="primary" @click="openCurrencyAdd()">添加</el-button>
                    <el-input v-model="currencyQuery.search_value" class="ya-search-value mr-2 ml-2" placeholder="请输入查询内容"
                        clearable />
                    <el-button type="success" title="查询/刷新" @click="openCurrencySearch()">查询</el-button>
                    <el-button type="default" title="重置查询条件" @click="openCurrencyRefresh()">重置</el-button>
                </el-col>
            </el-row>
            <!-- 列表 -->
            <el-table ref="currencyTable" v-loading="currencyLoading" :data="currencyData">
                <el-table-column prop="currency_id" label="ID" width="80" sortable="custom" />
                <el-table-column prop="currency_name" label="币种名称" min-width="170" sortable="custom"
                    show-overflow-tooltip />
                <el-table-column prop="currency_code" label="币种代码" min-width="170" sortable="custom"
                    show-overflow-tooltip />
                <el-table-column prop="currency_coins" label="单位金币" min-width="170" sortable="custom"
                    show-overflow-tooltip />
                <el-table-column label="操作" width="240">
                    <template #default="scope">
                        <el-button size="small" link type="primary" @click="openCurrencyEdit(scope.row)"> 编辑
                        </el-button><el-divider direction="vertical" />
                        <el-button size="small" link type="primary" @click="openCurrencyDele(scope.row)"> 删除
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
            <!-- 分页 -->
            <pagination v-show="currencyCount > 0" v-model:total="currencyCount" v-model:page="currencyQuery.page" v-model:limit="currencyQuery.limit"
                 @pagination="currencyDataList" />
            <!-- 添加修改 -->
            <el-dialog v-model="currencyDialog" :title="currencyTitle" :close-on-click-modal="false"
                :close-on-press-escape="false" :before-close="cancel" top="5vh" width="700px">
                <el-form label-position="top" ref="currencyRef" :model="currencyModel" :rules="rules" label-width="100px">
                    <el-form-item label="货币名称" prop="currency_name">
                        <el-input key="currency_name" v-model="currencyModel.currency_name" placeholder="请输入货币名称" clearable />
                    </el-form-item>
                    <el-form-item label="货币代码" prop="currency_code">
                        <el-input key="currency_code" v-model="currencyModel.currency_code" placeholder="请输入货币代码" />
                    </el-form-item>
                    <el-form-item label="单位金币(1货币兑金币数量)" prop="currency_coins">
                        <el-input key="currency_coins" v-model="currencyModel.currency_coins" placeholder="请输入单位货币兑金币比例" clearable />
                    </el-form-item>
                </el-form>
                <template #footer>
                    <el-button :loading="currencyLoading" @click="currencyCancel">取消</el-button>
                    <el-button :loading="currencyLoading" type="primary" @click="currencySubmit">提交</el-button>
                </template>
            </el-dialog>
        </div>
    </div>
</template>
<script>
import checkPermission from '@/utils/permission'
import { clipboard } from '@/utils/index'
import { getPageLimit } from '@/utils/settings'
import { arrayColumn } from '@/utils/index'
import { currencyList,currencyAdd,currencyEdit,currencyDele } from '@/api/platform/platform'
import { useAppStoreHook } from '@/store/modules/app'
const appStore = useAppStoreHook()
export default {
    name: 'Currency',
    data() {
        return {
            currencyQuery: {
                page: 1,
                limit: getPageLimit(),
                search_field: 'currency_name',
                search_exp: 'like',
                date_field: 'create_time',
            },
            currencyLoading: false,
            currencyData: [],
            currencyDount: 0,
            currencyModel: {
                currency_name: '',
                currency_code: '',
                currency_coins: '',
            },
            currencyDialog: false,
            currencyDialogTitle: '',
        }
    },
    created() {
        this.currencyDataList()
    },
    methods: {
        currencyDataList() {
            this.currencyLoading = true
            currencyList(this.currencyQuery).then(res => {
                this.currencyData = res.data.list
                this.currencyCount = res.data.count
                this.currencyLoading = false
            }).catch(() => {
                this.currencyLoading = false
            })
        },
        currencyCancel() {
            this.currencyDialog = false
            this.currencyModel = {
                currency_name: '',
                currency_code: '',
                currency_coins: '',
            }
        },
        currencySubmit() {
            this.$refs.currencyRef.validate(valid => {
                if (valid) {
                    this.currencyLoading = true
                    if (this.currencyModel.currency_id) {
                        currencyEdit(this.currencyModel).then(res => {
                            this.currencyLoading = false
                            this.currencyCancel()
                            this.currencyDataList()
                            ElMessage.success(res.msg)
                        }).catch(() => {
                            this.currencyLoading = false
                        })
                    } else {
                         currencyAdd(this.currencyModel).then(res => {
                            this.currencyLoading = false
                            this.currencyCancel()
                            this.currencyDataList()
                            ElMessage.success(res.msg)
                        }).catch(() => {
                            this.currencyLoading = false
                         })   
                        
                    }
                }
            })
        },
        openCurrencyAdd() {
            this.currencyDialog = true
            this.currencyTitle = '添加币种'
            this.currencyReset()
        },
        openCurrencyEdit(row) {
            this.currencyDialog = true
            this.currencyTitle = '编辑币种'
            this.currencyReset(row)
        },
        currencyReset(row) {
            if (row) {
                this.currencyModel = { ...row}
            } else {
                this.currencyModel = this.$options.data().currencyModel
            }
            if (this.$refs['currencyRef'] !== undefined) {
                try {
                    this.$refs['currencyRef'].resetFields()
                    this.$refs['currencyRef'].clearValidate()
                } catch (error) { }
            }
        },
        openCurrencyDele(row) {
            ElMessageBox.confirm('此操作将删除该币种, 是否继续?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning',
            }).then(() => {
                currencyDele({currency_id: row.currency_id}).then(res => {
                    ElMessage.success(res.msg)
                    this.currencyDataList()
                })
            }).catch(() => {
            })
        },
    }
}
</script>