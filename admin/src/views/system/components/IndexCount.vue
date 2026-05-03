<template>
  <el-row v-loading="loading" :gutter="10">
    <el-col v-for="(item, index) in datas" :key="index" :xs="24" :sm="8">
      <el-card :body-style="{ padding: '10px 0px' }" shadow="never">
        <template #header>
          <span>{{ item.name }}</span>
          <div style="display: flex; justify-content: space-between;margin-top: 10px;">$ {{ item.sydata.all_sy_usd || 0 }}  <span style="font-size: 12px;">{{item.team_name}}：$ {{ item.sydata.team_sy_usd || 0 }} / {{item.member_name}}：$ {{ item.sydata.member_sy_usd || 0 }}</span></div>
        </template>
        <div style="font-size: 12px;padding:10px 20px;">
          <span style="margin-right: 20px;">{{ item.offer_name }}：<strong>{{ item.offers || 0 }}</strong> 次</span>
          <span> {{ item.complete_name }}：<strong>{{ item.complete_offers || 0 }}</strong>个</span>
        </div>
      </el-card>
    </el-col>
  </el-row>
</template>

<script>
import { count } from '@/api/system/index'

export default {
  name: 'SystemIndexCount',
  data() {
    return {
      loading: false,
      datas: []
    }
  },
  computed: {
    name() {
      return this.$t('common.Count statistic')
    }
  },
  watch: {
    name() {
      this.count()
    }
  },
  created() {
    this.count()
  },
  methods: {
    count() {
      this.loading = true
      count()
        .then((res) => {
          this.datas = res.data.count
          this.loading = false
        })
        .catch(() => {
          this.loading = false
        })
    }
  }
}
</script>
