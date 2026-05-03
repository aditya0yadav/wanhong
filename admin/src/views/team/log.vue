<template>
  <div class="app-container">
    <el-card class="box-card shadow-sm border-0">
      <template #header>
        <div class="flex justify-between items-center">
          <span class="text-xl font-bold tracking-tight text-gray-800">团队操作日志</span>
          <el-button icon="Search" plain>筛选日志</el-button>
        </div>
      </template>

      <div class="py-8 text-center" v-if="loading">
        <el-skeleton :rows="5" animated />
      </div>

      <div v-else class="text-center py-20 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
        <el-icon :size="60" class="text-gray-300 mb-4"><Document /></el-icon>
        <h3 class="text-lg font-medium text-gray-600">暂无操作日志</h3>
        <p class="text-gray-400 max-w-xs mx-auto mt-2">目前没有任何团队相关的操作记录。所有的团队变更、成员添加和业绩修改都将记录在此处。</p>
      </div>
    </el-card>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Document, Search } from '@element-plus/icons-vue'

const loading = ref(true)

onMounted(() => {
  setTimeout(() => {
    loading.value = false
  }, 800)
})
</script>

<style scoped>
.box-card {
  border-radius: 12px;
}
</style>
