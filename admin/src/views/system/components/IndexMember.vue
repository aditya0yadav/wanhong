<template>
  <el-card v-loading="loading" shadow="never">
    <el-row>
      <el-col>
        <el-tabs v-model="date_type" @tab-change="handleTabChange">
          <el-tab-pane label="7日统计" name="week"></el-tab-pane>
          <el-tab-pane label="30天统计" name="month"></el-tab-pane>
          <el-tab-pane label="年度统计" name="year"></el-tab-pane>
          <el-tab-pane label="自定义查询" name="custom"></el-tab-pane>
        </el-tabs>
      </el-col>
      <el-col v-if="date_type == 'custom'">
        <!-- 查询 -->
        <el-row>
          <el-col class="mb-4">
            <el-select style="width: 120px;" v-model="rewardQuery.platform_id" placeholder="归属平台" class="mr-2" clearable
              filterable>
              <el-option v-for="(item, index) in platforms" :key="index" :label="item.platform_name"
                :value="item.platform_id" />
            </el-select>
            <el-select style="width: 120px;" v-model="rewardQuery.team_id" placeholder="归属团队" class="mr-2" clearable
              filterable>
              <el-option v-for="(item, index) in teams" :key="index" :label="item.team_name" :value="item.team_id" />
            </el-select>
            <el-input v-model="rewardQuery.member" class="ya-search-value mr-2" placeholder="请输入会员账号/昵称/ID" clearable />
            <el-input style="width: 150px;" v-model="rewardQuery.search_value" class="mr-2" placeholder="请输入项目名称"
              clearable />
            <el-date-picker v-model="rewardQuery.date_value" type="datetimerange" class="ya-date-value mr-2"
              start-placeholder="开始日期" end-placeholder="结束日期" value-format="YYYY-MM-DD HH:mm:ss"
              :default-time="[new Date(2024, 1, 1, 0, 0, 0), new Date(2024, 1, 1, 23, 59, 59)]" />
            <el-button type="primary" title="查询/刷新" @click="rewardSearch()">查询</el-button>
            <el-button type="default" title="重置查询条件" @click="rewardRefresh()">重置</el-button>
            <RewardExport :query="rewardQuery" />
          </el-col>
        </el-row>
        <div v-if="statisticData">
           <p>统计指标</p>
           <el-row :gutter="20" class="mb-4">
            <el-col :span="24">
                <div class="statistic-item c5">
                  <div class="tit">成功率</div>
                  <div class="cont">{{ statisticData.success_rate }}%</div>
                </div>
            </el-col>
          </el-row>
           <p>参与统计</p>
           <el-row :gutter="20" class="mb-4">
            <el-col :span="6">
                <div class="statistic-item c1">
                  <div class="tit">参与数量</div>
                  <div class="cont">{{ statisticData.total }}次</div>
                </div>
            </el-col>
            <el-col :span="6">
                <div class="statistic-item c2">
                  <div class="tit">核减数量</div>
                  <div class="cont">{{ statisticData.deduction_count }}次</div>
                </div>
            </el-col>
            <el-col :span="6">
                <div class="statistic-item c3">
                  <div class="tit">成功数量</div>
                  <div class="cont">{{ statisticData.success_count }}次</div>
                </div>
            </el-col>
            <el-col :span="6">
                <div class="statistic-item c4">
                  <div class="tit">失败数量</div>
                  <div class="cont">{{ statisticData.failed_count }}次</div>
                </div>
            </el-col>
           </el-row>
           <p>业绩统计</p>
           <el-row :gutter="20" class="mb-4">
            <el-col :span="6">
                <div class="statistic-item c1">
                  <div class="tit">系统完成业绩</div>
                  <div class="cont">${{ parseFloat(statisticData.success_payout).toFixed(2)}}</div>
                </div>
            </el-col>
            <el-col :span="6">
                <div class="statistic-item c2">
                  <div class="tit">系统核减业绩</div>
                  <div class="cont">${{ statisticData.deduction_payout }}</div>
                </div>
            </el-col>
            <el-col :span="6">
                <div class="statistic-item c3">
                  <div class="tit">系统成功业绩</div>
                  <div class="cont">${{ statisticData.success_payout }}</div>
                </div>
            </el-col>
            <el-col :span="6">
                <div class="statistic-item c4">
                  <div class="tit">系统失败业绩</div>
                  <div class="cont">${{ statisticData.failed_payout }}</div>
                </div>
            </el-col>
            <el-col :span="6">
                <div class="statistic-item c1">
                  <div class="tit">团队完成业绩</div>
                  <div class="cont">${{ parseFloat(statisticData.success_team_payout).toFixed(2)}}</div>
                </div>
            </el-col>
            <el-col :span="6">
                <div class="statistic-item c2">
                  <div class="tit">团队核减业绩</div>
                  <div class="cont">${{ statisticData.deduction_team_payout }}</div>
                </div>
            </el-col>
            <el-col :span="6">
                <div class="statistic-item c3">
                  <div class="tit">团队成功业绩</div>
                  <div class="cont">${{ statisticData.success_team_payout }}</div>
                </div>
            </el-col>
            <el-col :span="6">
                <div class="statistic-item c4">
                  <div class="tit">团队失败业绩</div>
                  <div class="cont">${{ statisticData.failed_team_payout }}</div>
                </div>
            </el-col>
            <el-col :span="6">
                <div class="statistic-item c1">
                  <div class="tit">会员完成业绩</div>
                  <div class="cont">${{ parseFloat(statisticData.success_member_payout).toFixed(2)}}</div>
                </div>
            </el-col>
            <el-col :span="6">
                <div class="statistic-item c2">
                  <div class="tit">会员核减业绩</div>
                  <div class="cont">${{ statisticData.deduction_member_payout }}</div>
                </div>
            </el-col>
            <el-col :span="6">
                <div class="statistic-item c3">
                  <div class="tit">会员成功业绩</div>
                  <div class="cont">${{ statisticData.success_member_payout }}</div>
                </div>
            </el-col>
            <el-col :span="6">
                <div class="statistic-item c4">
                  <div class="tit">会员失败业绩</div>
                  <div class="cont">${{ statisticData.failed_member_payout }}</div>
                </div>
            </el-col>
           </el-row>
        </div>
        <el-empty v-else description="暂无数据" :loading="statisticLoading" />
      </el-col>
      <el-col v-else>
        <div id="numberEchart" :style="{ height: height + 'px' }"></div>
      </el-col>
    </el-row>
  </el-card>
</template>

<script>
// ECharts
// 引入 echarts 核心模块，核心模块提供了 echarts 使用必须要的接口
import * as echarts from 'echarts/core'
// 引入图表，图表后缀都为 Chart
import { LineChart, BarChart } from 'echarts/charts'
// 引入组件，组件后缀都为 Component
import { TitleComponent, LegendComponent, GridComponent, TooltipComponent, ToolboxComponent } from 'echarts/components'
// 引入 Canvas 渲染器，注意引入 CanvasRenderer 或者 SVGRenderer 是必须的一步
import { CanvasRenderer } from 'echarts/renderers'
// 注册必须的组件
echarts.use([
  LineChart,
  BarChart,
  TitleComponent,
  LegendComponent,
  GridComponent,
  TooltipComponent,
  ToolboxComponent,
  CanvasRenderer
])

import { reward as stat,statistic } from '@/api/system/index'
import { platlist } from '@/api/platform/platform'
import { teamlist } from '@/api/team/team'
export default {
  name: 'SystemIndexMember',
  data() {
    return {
      height: 500,
      loading: false,
      date_type: 'week',
      date_range: [],
      date_ptype: 'monthrange',
      date_format: 'YYYY-MM',
      rewardQuery: {
        platform_id: '',
        team_id: '',
        member: '',
        search_value: '',
        date_value: []
      },
      statisticData:null,
      statisticLoading:false
    }
  },
  computed: {
    name() {
      return this.$t('member.Member statistic')
    }
  },
  watch: {
    name() {
      this.stat()
    }
  },
  created() {
    this.stat()
    platlist().then((res) => {
      this.platforms = res.data
    })
    teamlist().then((res) => {
      this.teams = res.data
    })
  },
  methods: {
    stat() {
      this.loading = true
      stat({
        type: this.date_type,
        date: this.date_range
      })
        .then((res) => {
          this.date_type = res.data.type
          this.dateEchart(res.data, 'numberEchart')
          this.dateOptions()
          this.loading = false
        })
        .catch(() => {
          this.loading = false
        })
    },
    rewardSearch() {
      this.statisticData = null
      this.loading = true
      statistic(this.rewardQuery)
        .then((res) => {
          this.statisticData = res.data[0]
          this.loading = false
      }).catch(()=>{
        this.loading = false
      })
    },
    handleTabChange(val) {
      this.date_type = val
      if (val == 'custom') {
        this.rewardSearch();
      } else {
        this.stat()
      }
    },
    typeChange() {
      this.dateOptions()
      this.date_range = []
    },
    dateOptions() {
      const type = this.date_type
      if (type === 'day') {
        this.date_ptype = 'daterange'
        this.date_format = 'YYYY-MM-DD'
      } else if (type === 'month') {
        this.date_ptype = 'monthrange'
        this.date_format = 'YYYY-MM'
      }
    },
    dateChange() {
      this.stat()
    },
    dateEchart(data, id) {
      var echart = echarts.init(document.getElementById(id))

      var option = {
        title: {
          left: 'center'
        },
        legend: {
          top: '20px',
          data: data.legend,
          selected: data.selected
        },
        grid: {
          top: '80px',
          left: '1%',
          right: '3%',
          bottom: '3%',
          containLabel: true
        },
        xAxis: {
          type: 'category',
          boundaryGap: false,
          data: data.xAxis
        },
        yAxis: {
          type: 'value'
        },
        tooltip: {
          trigger: 'axis',
          textStyle: {
            align: 'left'
          },
        },
        toolbox: {
          feature: {
            magicType: { show: true, type: ['line', 'bar'] },
            dataView: { show: true, readOnly: true },
            saveAsImage: {
              show: true,
              name: this.name + data.date[0] + '-' + data.date[1]
            }
          }
        },
        series: data.series
      }
      echart.setOption(option)
    }
  }
}
</script>
