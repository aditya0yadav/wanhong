<template>
  <el-dropdown @command="handleCommand">
    <el-button class="mr-3">导出文件</el-button>
    <template #dropdown>
      <el-dropdown-menu>
        <el-dropdown-item command="csv">csv</el-dropdown-item>
        <el-dropdown-item command="xlsx">xlsx</el-dropdown-item>
        <el-dropdown-item command="xls">xls</el-dropdown-item>
      </el-dropdown-menu>
    </template>
  </el-dropdown>
  <el-dialog
    v-model="dialog"
    :title="name"
    top="20vh"
    :before-close="cancel"
    :close-on-click-modal="false"
    :close-on-press-escape="false"
    draggable
  >
    <el-form label-width="120px">
      <el-form-item label="导出备注">
        <el-input v-model="model.export_remark" type="text" placeholder="请输入备注" clearable />
      </el-form-item>
      <el-form-item label="">
        <el-col><el-text>导出结果可在【导出文件】查看下载</el-text></el-col>
      </el-form-item>
    </el-form>
    <template #footer>
      <el-button :loading="loading" @click="cancel">取消</el-button>
      <el-button :loading="loading" type="primary" @click="submit">提交</el-button>
    </template>
  </el-dialog>
</template>

<script>
import { exports } from '@/api/team/reward'

export default {
  name: 'RewardExport',
  components: {},
  props: {
    query: Object
  },
  data() {
    return {
      name: '业绩导出',
      loading: false,
      dialog: false,
      model: { export_remark: '', export_type: '' },
    }
  },
  created() {},
  methods: {
    handleCommand(command) {
      this.show(command)
    },
    show(type) {
      this.model.export_type = type
      this.dialog = true
    },
    cancel() {
      this.dialog = false
    },
    submit() {
      this.loading = true
      exports(Object.assign({}, this.query, this.model))
        .then((res) => {
          this.loading = false
          ElMessage.success(res.msg)
          exports(
            {
              file_path: res.data.file_path,
              file_name: res.data.file_name
            },
            'get'
          )
          this.dialog = false
        })
        .catch(() => {
          this.loading = false
        })
    }
  }
}
</script>
