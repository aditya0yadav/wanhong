<template>
  <div class="app-container">
    <el-card class="box-card shadow-sm border-0">
      <template #header>
        <div class="flex justify-between items-center">
          <span class="text-xl font-bold tracking-tight text-gray-800">业绩人设</span>
          <div class="flex gap-2">
            <el-button type="success" icon="CirclePlus">创建人设</el-button>
          </div>
        </div>
      </template>

      <div class="py-8 text-center" v-if="loading">
        <el-skeleton :rows="5" animated />
      </div>

      <div v-else class="text-center py-20 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
        <el-icon :size="60" class="text-gray-300 mb-4"><Avatar /></el-icon>
        <h3 class="text-lg font-medium text-gray-600">暂无人设模版</h3>
        <p class="text-gray-400 max-w-xs mx-auto mt-2">您还没有配置任何业绩人设模版。人设模版可以帮助您快速为团队成员分配业绩目标。</p>
      </div>
    </el-card>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Avatar, CirclePlus } from '@element-plus/icons-vue'

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
