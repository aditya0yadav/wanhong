<template>
  <div class="app-container">
    <!-- 查询 -->
    <el-row>
      <el-col class="mb-2">
        <el-button type="primary" @click="add()">添加</el-button>
        <el-button type="danger" title="删除" @click="selectOpen('dele')">删除</el-button>
        <el-select v-model="query.search_field" class="ya-search-field ml-3 mr-2" placeholder="查询字段">
          <el-option :value="idkey" label="ID" />
          <el-option value="type" label="类型" />
          <el-option value="title" label="标题" />
        </el-select>
        <el-select v-model="query.search_exp" class="ya-search-exp mr-2">
          <el-option v-for="exp in exps" :key="exp.exp" :value="exp.exp" :label="exp.name" />
        </el-select>
        <el-select v-if="query.search_field === 'is_disable'" v-model="query.search_value" class="ya-search-value mr-2">
          <el-option :value="1" label="是" />
          <el-option :value="0" label="否" />
        </el-select>
        <el-select v-else-if="query.search_field === 'type'" v-model="query.search_value" class="ya-search-value mr-2">
          <el-option v-for="(item, index) in types" :key="index" :value="index" :label="item" />
        </el-select>
        <el-input v-else v-model="query.search_value" class="ya-search-value mr-2" placeholder="查询内容" clearable />
        <el-button type="primary" title="查询/刷新" @click="search()">查询</el-button>
        <el-button type="default" title="重置查询条件" @click="refresh()">重置</el-button>

      </el-col>
    </el-row>
    <el-dialog v-model="selectDialog" :title="selectTitle" :close-on-click-modal="false" :close-on-press-escape="false"
      top="20vh">
      <el-form ref="selectRef" label-width="120px">
        <el-form-item v-if="selectType === 'edittype'" label="类型">
          <el-select v-model="type">
            <el-option v-for="(item, index) in types" :key="index" :value="index" :label="item" />
          </el-select>
        </el-form-item>
        <el-form-item v-else-if="selectType === 'dele'">
          <span class="c-red">确定要删除选中的{{ name }}吗？</span>
        </el-form-item>
        <el-form-item :label="name + 'ID'">
          <el-input v-model="selectIds" type="textarea" autosize disabled />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button :loading="loading" @click="selectCancel">取消</el-button>
        <el-button :loading="loading" type="primary" @click="selectSubmit">提交</el-button>
      </template>
    </el-dialog>
    <!-- 列表 -->
    <el-table ref="table" v-loading="loading" :data="data" :height="height" @sort-change="sort"
      @selection-change="select">
      <el-table-column type="selection" width="42" title="全选/反选" />
      <el-table-column :prop="idkey" label="ID" width="80" />
      <el-table-column prop="type_name" label="类型" min-width="85" />
      <el-table-column prop="title" label="标题" min-width="220" show-overflow-tooltip>
        <template #default="scope">
          <span :style="{ color: scope.row.title_color }">{{ scope.row.title }}</span>
        </template>
      </el-table-column>
      <el-table-column prop="content" label="内容" min-width="220" show-overflow-tooltip>
        <template #default="scope">
          <span v-html="scope.row.content"></span>
        </template>
      </el-table-column>
      <el-table-column prop="members" label="推送用户" min-width="80">
        <template #default="scope">
          <div v-if="scope.row.members">
            <el-tag type="success" class="mr-1">{{ scope.row.members }}</el-tag>
          </div>
          <div v-else><el-tag type="success" class="mr-1">所有用户</el-tag></div>
        </template>
      </el-table-column>
      <el-table-column prop="status" label="状态" min-width="85">
        <template #default="scope">
          <el-tag v-if="scope.row.status == 1" type="success">已推送</el-tag>
          <el-tag v-if="scope.row.status == 0" type="warning">待推送</el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="create_time" label="添加时间" width="165" />
      <el-table-column prop="update_time" label="推送时间" width="165" />
      <el-table-column label="操作" width="95">
        <template #default="scope">
          <el-button type="success" size="small" :underline="false" @click="push(scope.row)"> 立即推送 </el-button>
        </template>
      </el-table-column>
    </el-table>
    <!-- 分页 -->
    <pagination v-show="count > 0" v-model:total="count" v-model:page="query.page" v-model:limit="query.limit"
      @pagination="list" />
    <!-- 添加修改 -->
    <el-dialog v-model="dialog" :title="dialogTitle" :close-on-click-modal="false" :close-on-press-escape="false"
      :before-close="cancel" top="5vh" width="800px">
      <el-form ref="ref" :model="model" :rules="rules" label-width="100px">
        <el-scrollbar native :height="height - 80">
          <el-form-item label="类型" prop="type">
            <el-radio-group v-model="model.type">
              <el-radio v-for="(item, index) in types" :key="index" :value="index" :label="item" />
            </el-radio-group>
          </el-form-item>
          <el-form-item v-if="model.type == 1" label="推送用户" prop="members">
            <el-select v-model="model.members" placeholder="请选择" clearable filterable multiple>
              <el-option v-for="item in members" :key="item.member_id" :value="item.member_id"
                :label="item.username + '(' + item.nickname + ')'" />
            </el-select>
          </el-form-item>
          <el-form-item label="标题" prop="title">
            <el-input v-model="model.title" placeholder="请输入标题" clearable />
          </el-form-item>
          <el-form-item label="内容" prop="content">
            <RichEditor size="small" v-model="model.content" />
          </el-form-item>
          <el-form-item v-if="model[idkey]" label="添加时间" prop="create_time">
            <el-input v-model="model.create_time" disabled />
          </el-form-item>
          <el-form-item v-if="model[idkey]" label="修改时间" prop="update_time">
            <el-input v-model="model.update_time" disabled />
          </el-form-item>
          <el-form-item v-if="model.delete_time" label="删除时间" prop="delete_time">
            <el-input v-model="model.delete_time" disabled />
          </el-form-item>
        </el-scrollbar>
      </el-form>
      <template #footer>
        <el-button :loading="loading" @click="cancel">取消</el-button>
        <el-button :loading="loading" type="primary" @click="submit">提交</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script>
import screenHeight from '@/utils/screen-height'
import Pagination from '@/components/Pagination/index.vue'
import RichEditor from '@/components/RichEditor/index.vue'
import { arrayColumn } from '@/utils/index'
import { getPageLimit } from '@/utils/settings'
import { list,push as pushNotice, info, add, edit, dele, edittype, datetime, disable } from '@/api/setting/notice'
import { listAll as memberList } from '@/api/member/member'
export default {
  name: 'SettingNotice',
  components: { Pagination, RichEditor },
  data() {
    return {
      name: '通告',
      height: 680,
      loading: false,
      idkey: 'notice_id',
      exps: [{ exp: 'like', name: '包含' }],
      query: {
        page: 1,
        limit: getPageLimit(),
        search_field: 'title',
        search_exp: 'like',
        date_field: 'create_time'
      },
      data: [],
      count: 0,
      dialog: false,
      dialogTitle: '',
      model: {
        notice_id: '',
        type: 0,
        title: '',
        content: '',
        members: '',
        sort: 250
      },
      rules: {
        title: [{ required: true, message: '请输入标题', trigger: 'blur' }],
        content: [{ required: true, message: '请输入内容', trigger: 'blur' }],
        members: [{ required: true, message: '请选择推送用户', trigger: 'blur' }]
      },
      types: [],
      selection: [],
      selectIds: '',
      selectTitle: '操作',
      selectDialog: false,
      selectType: '',
      type: 0,
      start_time: '',
      end_time: '',
      is_disable: 0
    }
  },
  created() {
    this.height = screenHeight()
    this.list()
    memberList().then((res) => {
      this.members = res.data;
    })
  },
  methods: {
    // 列表
    list() {
      this.loading = true
      list(this.query)
        .then((res) => {
          this.data = res.data.list
          this.count = res.data.count
          this.types = res.data.types
          this.exps = res.data.exps
          this.loading = false
        })
        .catch(() => {
          this.loading = false
        })
    },
    // 添加修改
    add() {
      this.dialog = true
      this.dialogTitle = this.name + '添加'
      this.reset()
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
    push(row) {
      ElMessageBox.confirm('是否推送?', '提示', {
        confirmButtonText: '立即推送',
        cancelButtonText: '取消',
        type: 'warning',
        beforeClose: (action, instance, done) => {
          if (action === 'confirm') {
            instance.confirmButtonLoading = true
            instance.confirmButtonText = '推送中...'
            pushNotice({ notice_id: row[this.idkey]}).then((res) => {
              this.list()
              done()
              ElMessage.success(res.msg)
              instance.confirmButtonLoading = false
            }).catch(() => {
              instance.confirmButtonLoading = false
              instance.confirmButtonText = '立即推送'
            })
          } else {
            instance.confirmButtonText = '立即推送'
            done()
          }
        },
      }).then(() => {
      })
    },
    submit() {
      this.$refs['ref'].validate((valid) => {
        if (valid) {
          this.loading = true
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
    // 操作
    select(selection) {
      this.selection = selection
      this.selectIds = this.selectGetIds(selection).toString()
    },
    selectGetIds(selection) {
      return arrayColumn(selection, this.idkey)
    },
    selectAlert() {
      ElMessageBox.alert('请选择需要操作的' + this.name, '提示', {
        type: 'warning',
        callback: () => { }
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
        if (selectType === 'edittype') {
          this.selectTitle = this.name + '修改类型'
        } else if (selectType === 'datetime') {
          this.selectTitle = this.name + '时间范围'
        } else if (selectType === 'disable') {
          this.selectTitle = this.name + '是否禁用'
        } else if (selectType === 'dele') {
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
        if (selectType === 'edittype') {
          this.edittype(this.selection)
        } else if (selectType === 'datetime') {
          this.datetime(this.selection)
        } else if (selectType === 'disable') {
          this.disable(this.selection, true)
        } else if (selectType === 'dele') {
          this.dele(this.selection)
        }
        this.selectDialog = false
      }
    },
    // 修改类型
    edittype(row) {
      if (!row.length) {
        this.selectAlert()
      } else {
        this.loading = true
        edittype({
          ids: this.selectGetIds(row),
          type: this.type
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
    // 时间范围
    datetime(row) {
      if (!row.length) {
        this.selectAlert()
      } else {
        this.loading = true
        datetime({
          ids: this.selectGetIds(row),
          start_time: this.start_time,
          end_time: this.end_time
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
    // 删除
    dele(row) {
      if (!row.length) {
        this.selectAlert()
      } else {
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
    }
  }
}
</script>
