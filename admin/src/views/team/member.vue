<template>
  <div class="app-container">
    <el-card class="box-card shadow-sm border-0">
      <template #header>
        <div class="flex justify-between items-center">
          <span class="text-xl font-bold tracking-tight text-gray-800">全部成员</span>
          <div class="flex gap-2">
            <el-button type="primary" icon="Plus">添加成员</el-button>
            <el-button icon="Refresh" @click="fetchData">刷新</el-button>
          </div>
        </div>
      </template>

      <div class="py-8 text-center" v-if="loading">
        <el-skeleton :rows="5" animated />
      </div>

      <div v-else class="text-center py-20 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
        <el-icon :size="60" class="text-gray-300 mb-4"><User /></el-icon>
        <h3 class="text-lg font-medium text-gray-600">暂无成员数据</h3>
        <p class="text-gray-400 max-w-xs mx-auto mt-2">目前该团队下尚未绑定任何成员，点击上方按钮开始添加。</p>
      </div>
    </el-card>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { User, Plus, Refresh } from '@element-plus/icons-vue'

const loading = ref(true)

const fetchData = () => {
  loading.value = true
  setTimeout(() => {
    loading.value = false
  }, 800)
}

onMounted(() => {
  fetchData()
})
</script>

<style scoped>
.box-card {
  border-radius: 12px;
}
</style>
