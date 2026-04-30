<template>
  <div class="detail-container">
    <div class="detail-card">
      <div v-if="loading" class="loading-state">
        <el-icon class="is-loading" :size="48"><Loading /></el-icon>
        <p class="mt-4">智能安全扫描中...</p>
      </div>

      <div v-else-if="error" class="error-state">
        <el-result icon="error" title="扫描遇到状况" :sub-title="error">
          <template #extra>
            <el-button type="primary" round @click="fetchScore">重新尝试</el-button>
          </template>
        </el-result>
      </div>

      <div v-else class="content-state">
        <header class="header">
          <h1 class="title">问卷安全分析</h1>
          <p class="subtitle">Wanhong 安全防护系统已为您完成全方位检测</p>
        </header>

        <div class="score-display">
          <el-progress
            type="dashboard"
            :percentage="score"
            :stroke-width="12"
            :color="scoreColor"
            :width="180"
          >
            <template #default="{ percentage }">
              <div class="score-inner">
                <span class="score-value">{{ percentage }}</span>
                <span class="score-label">信任分值</span>
              </div>
            </template>
          </el-progress>
        </div>

        <div class="status-box" :style="{ borderColor: scoreColor + '40', backgroundColor: scoreColor + '10' }">
          <div class="status-header">
            <el-tag :type="statusType" effect="dark" round class="status-tag">
              {{ statusText }}
            </el-tag>
          </div>
          <p class="status-desc">{{ statusDescription }}</p>
        </div>

        <div class="info-section">
          <div class="info-item">
            <span class="label">问卷 ID:</span>
            <span class="value">{{ surveyId || '未知' }}</span>
          </div>
          <div class="info-item">
            <span class="label">来源渠道:</span>
            <span class="value">{{ platform.toUpperCase() }}</span>
          </div>
        </div>

        <div class="action-footer">
          <el-button
            type="primary"
            class="main-action-btn"
            @click="proceed"
          >
            立即前往问卷
          </el-button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute } from 'vue-router';
import { ElMessage } from 'element-plus';
import { Loading } from '@element-plus/icons-vue';
import { useVisitorData } from '@fingerprint/vue';

const { isLoading, getData, data, error: fingerprintError } = useVisitorData({ immediate: false });

const route = useRoute();
const loading = ref(true);
const error = ref(null);
const score = ref(0);
const rawLink = ref('');
const surveyId = ref('');
const platform = ref('');

const scoreColor = computed(() => {
  if (score.value >= 80) return '#67C23A';
  if (score.value >= 50) return '#E6A23C';
  return '#F56C6C';
});

const statusType = computed(() => {
  if (score.value >= 80) return 'success';
  if (score.value >= 50) return 'warning';
  return 'danger';
});

const statusText = computed(() => {
  if (score.value >= 80) return '环境安全';
  if (score.value >= 50) return '中度异常';
  return '环境异常';
});

const statusDescription = computed(() => {
  if (score.value >= 80) return '当前答题环境非常安全，建议立即开始任务以获得奖励。';
  if (score.value >= 50) return '检测到轻微的网络环境异常，您可以尝试继续，但请注意答题规范。';
  return '我们的风控系统检测到您的网络环境存在较高风险，可能会影响问卷审核。';
});


const fetchScore = async () => {
  loading.value = true;
  error.value = null;

  let fingerprintFallback = 'blocked_or_failed';
  try {
    const result = await getData();
    fingerprintFallback = result.event_id || result.visitor_id;
  } catch (err) {
    console.warn('[Fingerprint] Identification blocked/failed.', err.message);
  }

  try {
    const payload = {
      fingerprintHash: fingerprintFallback,
      surveyId: surveyId.value,
      platform: platform.value,
      username: 'guest'
    };

    const response = await fetch("https://api.Wanhong.com/api/fraud/check", {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });

    if (!response.ok) throw new Error('Failed');

    const data = await response.json();
    score.value = data.score !== undefined ? data.score : 100;
    console.log(`[Fraud Check] Score: ${score.value}`);
  } catch (err) {
    console.error('[Fraud Check] Error:', err);
    // If fraud check fails, default to 50% as requested
    score.value = 50;
    error.value = null; 
  } finally {
    loading.value = false;
  }
};

const proceed = () => {
  if (rawLink.value) {
    window.location.href = rawLink.value;
  } else {
    ElMessage.error('问卷链接已失效');
  }
};

onMounted(() => {
  rawLink.value = route.query.link || '';
  surveyId.value = route.query.surveyId || '';
  platform.value = route.query.platform || '';

  if (!rawLink.value) {
    error.value = '问卷链接参数缺失';
    loading.value = false;
  } else {
    fetchScore();
  }
});
</script>

<style scoped lang="scss">
.detail-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: var(--t-background-color);
  padding: 24px;
  font-family: 'Poppins', 'Inter', 'PingFang SC', system-ui, sans-serif;
  color: var(--t-color);
}

.detail-card {
  width: 100%;
  max-width: 420px;
  background: var(--t-card-color);
  border: 1px solid var(--t-border-color);
  border-radius: 20px;
  padding: 32px 24px;
  box-shadow: 0 20px 50px var(--t-card-shadow-color);
  backdrop-filter: blur(10px);
  position: relative;
  overflow: hidden;

  &::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, var(--el-color-primary), #a855f7);
  }
}

.header {
  margin-bottom: 24px;
  text-align: center;
  
  .title {
    font-size: 22px;
    font-weight: 800;
    margin-bottom: 4px;
    letter-spacing: -0.5px;
    background: linear-gradient(to bottom, var(--t-color), var(--t-color-1));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }

  .subtitle {
    font-size: 12px;
    color: var(--t-color-1);
    opacity: 0.8;
  }
}

.score-display {
  display: flex;
  justify-content: center;
  margin-bottom: 24px;
}

.score-inner {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.score-value {
  font-size: 54px;
  font-weight: 800;
  line-height: 1;
  color: var(--t-color);
  margin-bottom: 4px;
}

.score-label {
  font-size: 14px;
  font-weight: 500;
  color: var(--t-color-2);
  text-transform: uppercase;
  letter-spacing: 1px;
}

.status-box {
  border-radius: 14px;
  padding: 16px;
  margin-bottom: 24px;
  border: 1px solid transparent;
  text-align: left;
  transition: all 0.3s ease;

  .status-header {
    margin-bottom: 12px;
  }

  .status-desc {
    font-size: 14px;
    line-height: 1.6;
    color: var(--t-color-1);
  }
}

.info-section {
  background: var(--t-background-color);
  border-radius: 12px;
  padding: 14px;
  margin-bottom: 24px;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;

  .info-item {
    display: flex;
    flex-direction: column;
    gap: 4px;

    .label {
      font-size: 11px;
      color: var(--t-color-2);
      text-transform: uppercase;
    }

    .value {
      font-size: 14px;
      font-weight: 600;
      color: var(--t-color);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
  }
}

.action-footer {
  .main-action-btn {
    width: 100%;
    height: 52px;
    font-size: 16px;
    font-weight: 700;
    border-radius: 14px;
    background: linear-gradient(135deg, var(--el-color-primary), #7c3aed);
    border: none;
    color: white;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3);

    &:hover {
      transform: translateY(-2px);
      box-shadow: 0 12px 25px rgba(99, 102, 241, 0.4);
      filter: brightness(1.1);
    }

    &:active {
      transform: translateY(0);
    }
  }
}

.loading-state {
  padding: 40px 0;
  text-align: center;
  color: var(--t-color-2);
}

:deep(.el-progress-circle__track) {
  stroke: var(--t-border-color);
  opacity: 0.3;
}
</style>
