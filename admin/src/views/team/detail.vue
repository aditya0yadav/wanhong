<template>
  <div>
    <div class="p-2 bg-[--el-bg-color] dark:bg-[--menuBg] flex align-center"><span
        class="cursor-pointer hover:text-primary"><svg-icon icon-class="back" @click="back" size="1.5em" /></span> <span
        class="ml-2" v-if="model.team_name">{{ model.team_name }}的团队详情</span></div>
    <el-scrollbar native :height="height">
      <div class="app-container rounded-md">
        <div class="p-3 bg-[--el-bg-color-overlay]" v-loading="loading">
          <div class="font-size-3.5 pb-3 border-b border-[--cus-bg-color]">基础信息</div>
          <el-row :gutter="20" class="py-3">
            <el-col :span="6">
              <div class="p-3 flex align-center font-size-3 bg-[--cus-bg-color] rounded-md">
                <div class="mr-3">团队名称:</div>
                <div>{{ model.team_name }}</div>
              </div>
            </el-col>
            <el-col :span="6">
              <div class="p-3 flex align-center font-size-3 bg-[--cus-bg-color] rounded-md">
                <div class="mr-3">团队域名:</div>
                <div>{{ model.team_host }}</div>
              </div>
            </el-col>
            <el-col :span="6">
              <div class="p-3 flex align-center font-size-3 bg-[--cus-bg-color] rounded-md">
                <div class="mr-3">创建时间:</div>
                <div>{{ model.create_time }}</div>
              </div>
            </el-col>
            <el-col :span="6">
              <div class="p-3 flex align-center font-size-3 bg-[--cus-bg-color] rounded-md">
                <div class="mr-3">更新时间:</div>
                <div>{{ model.update_time }}</div>
              </div>
            </el-col>
            <el-col :span="6" class="mt-3">
              <div class="p-3 flex font-size-3 bg-[--cus-bg-color] rounded-md line-height-6">
                <span class="mr-3">授权成员数量:</span>
                <div>{{ model.auth_num }}</div>
              </div>
            </el-col>
            <el-col :span="6" class="mt-3">
              <div class="p-3 flex font-size-3 bg-[--cus-bg-color] rounded-md line-height-6">
                <span class="mr-3">抽佣比例:</span>
                <div>{{ model.commission_ratio }}%</div>
              </div>
            </el-col>
          </el-row>
        </div>
        <div class="p-3 bg-[--el-bg-color-overlay] mt-3">
          <el-tabs ref="tabsContent" v-model="activeName" @tab-change="handleTabChange">
            <el-tab-pane name="auth" label="已授权平台" lazy>
              <el-row>
                <el-col class="mb-4">
                  <el-button plain type="primary" @click="platAdd()">添加授权</el-button>
                  <el-input v-model="teamAuthQuery.search_value" class="ya-search-value mr-2 ml-3" placeholder="请输入平台名"
                    clearable />
                  <el-button type="primary" title="查询" @click="teamAuthSearch()">查询</el-button>
                  <el-button type="default" title="重置" @click="teamAuthRefresh()">重置</el-button>
                </el-col>
              </el-row>
              <el-table ref="teamAuthTable" v-loading="authloading" :data="teamAuthData" >
                <el-table-column prop="platform_name" label="平台名称" min-width="170" sortable="custom"
                  show-overflow-tooltip />
                <el-table-column prop="platform_sign" label="平台标识" min-width="170" sortable="custom"
                  show-overflow-tooltip />
                <el-table-column prop="auth_rate" label="抽成比例(%)" min-width="170" sortable="custom"
                  show-overflow-tooltip />
                <el-table-column label="操作" width="240">
                  <template #default="scope">
                    <el-button  link type="primary" :underline="false"
                      @click="platAdd('edit', scope.row)">编辑授权
                    </el-button>
                    <el-button  link type="primary" :underline="false" @click="todelAuth(scope.row)"> 解除授权
                    </el-button>
                  </template>
                </el-table-column>
              </el-table>
              <!-- 分页 -->
              <pagination v-show="teamAuthCount > 0" v-model:total="teamAuthCount" v-model:page="teamAuthQuery.page"
                v-model:limit="teamAuthQuery.limit"  @pagination="teamAuthList" />
            </el-tab-pane>
            <el-tab-pane name="member" label="团队成员" lazy>
              <el-row>
                <el-col class="mb-4">
                  <el-button plain type="primary" @click="userAdd()">添加成员</el-button>
                  <el-input v-model="userQuery.search_value" class="ya-search-value mr-2 ml-3" placeholder="请输入用户名"
                    clearable />
                  <el-button type="primary" title="查询" @click="userSearch()">查询</el-button>
                  <el-button type="default" title="重置" @click="userRefresh()">重置</el-button>
                </el-col>
              </el-row>
              <!-- 列表 -->
              <el-table ref="userTable" v-loading="userLoading" :data="userData">
                <el-table-column :prop="idkey" label="ID" width="80" sortable="custom" />
                <el-table-column prop="avatar_id" label="头像" min-width="62">
                  <template #default="scope">
                    <FileImage :file-url="scope.row.avatar_url" avatar lazy />
                  </template>
                </el-table-column>
                <el-table-column prop="nickname" label="昵称" min-width="170" sortable="custom" show-overflow-tooltip />
                <el-table-column prop="username" label="用户名" min-width="170" sortable="custom" show-overflow-tooltip />
                <el-table-column prop="phone" label="手机" min-width="112" sortable="custom" show-overflow-tooltip />
                <el-table-column prop="email" label="邮箱" min-width="200" sortable="custom" show-overflow-tooltip />
                <el-table-column prop="tag_names" label="标签" min-width="130" show-overflow-tooltip />
                <el-table-column prop="group_names" label="分组" min-width="135" show-overflow-tooltip />
                <el-table-column prop="create_time" label="注册时间" width="165" sortable="custom" />
                <el-table-column label="操作" width="120">
                  <template #default="scope">
                    <el-button type="primary"  link @click="userEdit(scope.row)"> 编辑 </el-button>
                    <el-divider direction="vertical" />
                    <el-button type="primary"  link @click="userDele(scope.row)"> 删除 </el-button>
                  </template>
                </el-table-column>
              </el-table>
              <!-- 分页 -->
              <pagination v-show="userCount > 0" v-model:total="userCount" v-model:page="userQuery.page"
                v-model:limit="userQuery.limit"  @pagination="userList" />
            </el-tab-pane>
            <el-tab-pane name="reward" label="业绩记录" lazy>
              <!-- 查询 -->
              <el-row>
                <el-col class="mb-4">
                  <el-select style="width: 120px;" v-model="rewardQuery.platform_id" placeholder="归属平台" class="mr-2"
                    clearable filterable>
                    <el-option v-for="(item, index) in platData" :key="index" :label="item.platform_name"
                      :value="item.platform_id" />
                  </el-select>
                  <el-select style="width: 120px;" v-model="rewardQuery.reward_status" placeholder="业绩状态" class="mr-2"
                    clearable filterable>
                    <el-option v-for="(item, index) in rewardStatus" :key="index" :label="item.label"
                      :value="item.value" />
                  </el-select>
                  <el-input v-model="rewardQuery.member" class="ya-search-value mr-2" placeholder="请输入会员账号/昵称/ID"
                    clearable />
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
              <!-- 列表 -->
              <el-table ref="rewardTable" v-loading="rewardLoading" :data="rewardData" @sort-change="rewardSort">
                <el-table-column type="selection" width="42" title="全选/反选" />
                <el-table-column prop="reward_id" label="ID" width="80" sortable="custom" />
                <el-table-column prop="uuid" min-width="150" label="UUID" show-overflow-tooltip />
                <el-table-column prop="project_pno" min-width="120" label="PID" />
                <el-table-column prop="project_no" min-width="150" label="项目编号" />
                <el-table-column prop="project_name" min-width="150" label="项目名称" />
                <el-table-column prop="platform.platform_name" label="归属平台" min-width="120" />
                <el-table-column prop="member.nickname" label="归属用户" />
                <el-table-column prop="payout" label="奖励金币(总价/团队/个人)" min-width="220">
                  <template #default="scope">
                    {{ scope.row.payout }} / {{ scope.row.team_payout }} / {{ scope.row.member_payout }}
                  </template>
                </el-table-column>
                <el-table-column prop="reward_status" label="业绩状态">
                  <template #default="scope">
                    <el-text :type="rewardStatusFilter(scope.row.reward_status)['type']">{{
                      rewardStatusFilter(scope.row.reward_status)['label'] }}</el-text>
                  </template>
                </el-table-column>
                <el-table-column prop="ip" min-width="150" label="IP" show-overflow-tooltip />
                <el-table-column prop="create_time" min-width="120" label="用时">
                  <template #default="scope">
                    <el-text>{{ timeDifference(scope.row.start_time, scope.row.create_time) }}</el-text>
                  </template>
                </el-table-column>
                <el-table-column prop="start_time" width="180" label="开始时间" />
                <el-table-column prop="create_time" width="180" label="完成时间" />


                <el-table-column label="人设信息" width="180" fixed="right">
                  <template #default="scope">
                    <el-button :disabled="!scope.row.front_rs" :type="!scope.row.front_rs ? 'default' : 'success'"
                       link @click="lookPersona(scope.row, 'front_rs')"> 前置人设 </el-button>
                    <el-button :disabled="!scope.row.backend_rs" :type="!scope.row.backend_rs ? 'default' : 'success'"
                       link @click="lookPersona(scope.row, 'backend_rs')"> 后置人设 </el-button>
                  </template>
                </el-table-column>
              </el-table>
              <!-- 分页 -->
              <pagination v-model:total="rewardCount" v-model:page="rewardQuery.page" v-model:limit="rewardQuery.limit"
                 @pagination="rewardList" />
            </el-tab-pane><!--
            <el-tab-pane name="static" label="数据看板" lazy>
              <el-row>
                <el-col class="mb-4">
                  <el-select style="width: 120px;" v-model="staticQuery.platform_id" placeholder="归属平台" class="mr-2"
                    clearable filterable>
                    <el-option v-for="(item, index) in platData" :key="index" :label="item.platform_name"
                      :value="item.platform_id" />
                  </el-select>
                  <el-input v-model="staticQuery.member" class="ya-search-value mr-2" placeholder="请输入会员账号/昵称/ID"
                    clearable />
                  <el-input style="width: 150px;" v-model="staticQuery.search_value" class="mr-2" placeholder="请输入项目名称"
                    clearable />
                  <el-date-picker v-model="staticQuery.date_value" type="datetimerange" class="ya-date-value mr-2"
                    start-placeholder="开始日期" end-placeholder="结束日期" value-format="YYYY-MM-DD HH:mm:ss"
                    :default-time="[new Date(2024, 1, 1, 0, 0, 0), new Date(2024, 1, 1, 23, 59, 59)]" />
                  <el-button type="primary" title="查询/刷新" @click="staticSearch()">查询</el-button>
                  <el-button type="default" title="重置查询条件" @click="staticRefresh()">重置</el-button>
                </el-col>
              </el-row>
              <div v-if="statisticData">
                <p>统计指标</p>
                <el-row :gutter="20" class="mb-4">
                  <el-col :span="12">
                    <div class="statistic-item c5">
                      <div class="tit">成功率</div>
                      <div class="cont">{{ statisticData.success_rate }}%</div>
                    </div>
                  </el-col>
                  <el-col :span="12">
                    <div class="statistic-item c5">
                      <div class="tit">团队抽成</div>
                      <div class="cont">${{ (parseFloat(statisticData.success_team_payout) +
                        parseFloat(statisticData.pendding_team_payout) - parseFloat(statisticData.success_member_payout)
                        -
                        parseFloat(statisticData.pendding_member_payout)).toFixed(2) }}</div>
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
                      <div class="tit">待审核数量</div>
                      <div class="cont">{{ statisticData.pendding_count }}次</div>
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
                      <div class="tit">团队完成业绩</div>
                      <div class="cont">${{ (parseFloat(statisticData.success_team_payout) +
                        parseFloat(statisticData.pendding_team_payout)).toFixed(2)}}</div>
                    </div>
                  </el-col>
                  <el-col :span="6">
                    <div class="statistic-item c2">
                      <div class="tit">团队待审核业绩</div>
                      <div class="cont">${{ statisticData.pendding_team_payout }}</div>
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
                      <div class="cont">${{ (parseFloat(statisticData.success_member_payout) +
                        parseFloat(statisticData.pendding_member_payout)).toFixed(2)}}</div>
                    </div>
                  </el-col>
                  <el-col :span="6">
                    <div class="statistic-item c2">
                      <div class="tit">会员待审核业绩</div>
                      <div class="cont">${{ statisticData.pendding_member_payout }}</div>
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
              <el-empty v-else description="暂无数据" v-loading="statisticLoading" />
            </el-tab-pane>
            -->
          </el-tabs>
        </div>
      </div>
    </el-scrollbar>
    <!-- 授权平台 -->
    <el-dialog v-model="platDialog" :title="platType == 'add' ? '添加授权平台' : '编辑授权平台'" :close-on-click-modal="false"
      :close-on-press-escape="false" :before-close="platCancel" top="20vh" width="600px">
      <el-form ref="platRef" :model="platModel" :rules="platRules" label-width="100px">

        <el-form-item label="选择平台" prop="platform_id">
          <el-select v-model="platModel.platform_id" class="w-full" clearable filterable>
            <el-option v-for="item in platData" :key="item.platform_id" :label="item.platform_name"
              :value="item.platform_id" />
          </el-select>
        </el-form-item>
        <el-form-item label="抽成比例" prop="auth_rate">
          <el-input v-model="platModel.auth_rate" type="number" min="0" max="100" placeholder="请输入抽成比例" />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button :loading="loading" @click="platCancel">取消</el-button>
        <el-button :loading="loading" type="primary" @click="platSubmit">提交</el-button>
      </template>
    </el-dialog>
    <!-- 添加修改团队用户 -->
    <el-dialog v-model="userDialog" :title="userDialogTitle" :close-on-click-modal="false"
      :close-on-press-escape="false" :before-close="userCancel" top="5vh">
      <el-form ref="userRef" :model="userModel" :rules="userRules" label-width="100px">
        <el-tabs>
          <el-tab-pane label="基础信息">
            <el-scrollbar native :height="height - 80">
              <el-form-item label="头像" prop="avatar_id">
                <FileImage v-model="userModel.avatar_id" :file-url="userModel.avatar_url" file-title="上传头像"
                  file-tip="图片小于200KB，jpg、png格式，宽高 1:1。" :height="100" avatar upload />
              </el-form-item>
              <el-form-item label="昵称" prop="nickname">
                <el-input key="nickname" v-model="userModel.nickname" placeholder="请输入昵称" clearable />
              </el-form-item>
              <el-form-item label="用户名" prop="username">
                <el-input key="username" v-model="userModel.username" placeholder="请输入用户名" clearable />
              </el-form-item>
              <el-form-item v-if="userModel.member_id == ''" label="密码" prop="password">
                <el-input key="password" v-model="userModel.password" placeholder="请输入密码" clearable show-password />
              </el-form-item>
              <el-form-item label="手机" prop="phone">
                <el-input v-model="userModel.phone" clearable />
              </el-form-item>
              <el-form-item label="邮箱" prop="email">
                <el-input v-model="userModel.email" clearable />
              </el-form-item>
              <el-form-item label="姓名" prop="name">
                <el-input v-model="userModel.name" clearable />
              </el-form-item>
              <el-form-item label="性别" prop="gender">
                <el-select v-model="userModel.gender">
                  <el-option v-for="(item, index) in genders" :key="index" :label="item" :value="index" />
                </el-select>
              </el-form-item>
              <el-form-item label="所在地" prop="region_id">
                <el-cascader v-model="userModel.region_id" class="w-full" :options="regionData" :props="regionProps"
                  clearable />
              </el-form-item>
              <el-form-item label="排序" prop="sort">
                <el-input v-model="userModel.sort" type="number" />
              </el-form-item>
              <el-form-item label="备注" prop="remark">
                <el-input v-model="userModel.remark" clearable />
              </el-form-item>
            </el-scrollbar>
          </el-tab-pane>
          <el-tab-pane label="权限信息">
            <el-scrollbar native :height="height - 80">
              <el-form-item label="标签" prop="tag_ids">
                <el-select v-model="userModel.tag_ids" class="w-full" multiple clearable filterable>
                  <el-option v-for="item in tagData" :key="item.tag_id" :label="item.tag_name" :value="item.tag_id" />
                </el-select>
              </el-form-item>
              <el-form-item label="分组(角色)" prop="group_ids">
                <el-select v-model="userModel.group_ids" class="w-full" clearable filterable>
                  <el-option v-for="item in groupData" :key="item.group_id" :label="item.group_name"
                    :value="item.group_id" />
                </el-select>
              </el-form-item>
            </el-scrollbar>
          </el-tab-pane>
          <el-tab-pane label="登录注册">
            <el-scrollbar native :height="height - 80">
              <el-form-item v-if="userModel[idkey]" label="登录IP" prop="login_ip">
                <el-input v-model="userModel.login_ip" disabled />
              </el-form-item>
              <el-form-item v-if="userModel[idkey]" label="登录地区" prop="login_region">
                <el-input v-model="userModel.login_region" disabled />
              </el-form-item>
              <el-form-item v-if="userModel[idkey]" label="登录时间" prop="login_time">
                <el-input v-model="userModel.login_time" disabled />
              </el-form-item>
              <el-form-item v-if="userModel[idkey]" label="登录次数" prop="login_num">
                <el-input v-model="userModel.login_num" disabled />
              </el-form-item>
              <el-form-item v-if="userModel[idkey]" label="注册平台" prop="platform_name">
                <el-input v-model="userModel.platform_name" disabled />
              </el-form-item>
              <el-form-item v-if="userModel[idkey]" label="注册应用" prop="application_name">
                <el-input v-model="userModel.application_name" disabled />
              </el-form-item>
              <el-form-item v-if="userModel[idkey]" label="注册时间" prop="create_time">
                <el-input v-model="userModel.create_time" disabled />
              </el-form-item>
              <el-form-item v-if="userModel[idkey]" label="修改时间" prop="update_time">
                <el-input v-model="userModel.update_time" disabled />
              </el-form-item>
              <el-form-item v-if="userModel[idkey]" label="退出时间" prop="logout_time">
                <el-input v-model="userModel.logout_time" disabled />
              </el-form-item>
              <el-form-item v-if="userModel.delete_time" label="删除时间" prop="delete_time">
                <el-input v-model="userModel.delete_time" disabled />
              </el-form-item>
            </el-scrollbar>
          </el-tab-pane>
        </el-tabs>
      </el-form>
      <template #footer>
        <el-button :loading="userLoading" @click="userCancel">取消</el-button>
        <el-button :loading="userLoading" type="primary" @click="userSubmit">提交</el-button>
      </template>
    </el-dialog>
    <el-dialog v-model="rsDialog" title="人设信息" :close-on-click-modal="false" :close-on-press-escape="false" top="5vh">
      <el-scrollbar native :height="500">
        <div v-if="rsType == 0">
          {{ rsContent }}
        </div>
        <div v-if="rsType == 1">
          <div v-for="(item, index) in rsContent" :key="index"
            style="border:1px solid #ebeef5;margin-bottom: 10px;margin-right: 10px;">
            <div style="padding:10px;background-color: #f5f5f5;">{{ item.name }}</div>
            <div style="padding:10px;">{{ item.value }}</div>
          </div>
        </div>
      </el-scrollbar>
    </el-dialog>
  </div>
</template>
<script>
import checkPermission from '@/utils/permission'
import screenHeight from '@/utils/screen-height'
import { clipboard } from '@/utils/index'
import { getPageLimit } from '@/utils/settings'
import { info, edit, teamAuth,statistic } from '@/api/team/team'
import { add as addMember, edit as editMember, info as infoMember, list as listMember, dele as deleMember } from '@/api/member/member'
import { list as listReward } from '@/api/team/reward'
import { platlist, platformAuth, platformEditAuth, deleAuth } from '@/api/platform/platform'
import { useAppStoreHook } from '@/store/modules/app'
import RewardExport from './components/RewardExport.vue'
const appStore = useAppStoreHook();
export default {
  name: 'teamDetail',
  components: { RewardExport },
  data() {
    return {
      activeName: 'auth',
      loading: true,
      authloading: false,
      userLoading: false,
      switchLoading: false,
      model: {
        team_name: '',
        team_sign: '',
        is_disable: 0,
        create_time: '',
        update_time: '',
      },
      idkey: 'member_id',
      userDialog: false,
      userDialogTitle: '',
      userModel: {
        member_id: '',
        team_id: '',
        avatar_id: 0,
        avatar_url: '',
        tag_ids: [],
        group_ids: [],
        nickname: '',
        username: '',
        password: '',
        phone: '',
        email: '',
        name: '',
        gender: 0,
        region_id: '',
        remark: '',
        sort: 250,
        api_ids: [],
        thirds: []
      },
      platRules: {
        platform_id: [{ required: true, message: '请选择平台', trigger: 'change' }],
        auth_rate: [{ required: true, message: '输入抽成比例', trigger: 'change' }],
      },
      userRules: {
        nickname: [{ required: true, message: '请输入昵称', trigger: 'blur' }],
        username: [{ required: true, message: '请输入用户名', trigger: 'blur' }],
        password: [{ required: true, message: '请输入密码', trigger: 'blur' }],
      },
      platDialog: false,
      platData: [],
      teamAuthData: [],
      teamAuthCount: 0,
      platModel: {
        platform_id: '',
        platform_name: '',
        auth_rate: 0
      },
      teamAuthQuery: {
        page: 1,
        limit: getPageLimit(),
        search_field: 'platform_name',
        search_exp: 'like',
        date_field: 'create_time'
      },
      genders: [],
      platforms: [],
      applications: [],
      regionData: [],
      regionProps: {
        checkStrictly: true,
        value: 'region_id',
        label: 'region_name',
        emitPath: false
      },
      regionQueryProps: {
        checkStrictly: true,
        value: 'region_id',
        label: 'region_name',
        multiple: true,
        emitPath: false
      },
      tagData: [],
      groupData: [],
      userData: [],
      userCount: 0,
      userQuery: {
        page: 1,
        limit: getPageLimit(),
        search_field: 'username',
        search_exp: 'like',
        date_field: 'create_time'
      },
      rewardLoading: false,
      rewardData: [],
      rewardCount: 0,
      rewardDialog: false,
      rewardDialogTitle: '',
      rewardQuery: {
        page: 1,
        limit: getPageLimit(),
        search_field: 'project_name',
        search_exp: '=',
        date_field: 'create_time',
        platform_id: '',
        team_id: '',
        reward_status: '',
        member: '',
      },
      rewardRules: {
      },
      rewardStatus: [{
        value: 6,
        label: '核减',
        type: 'warning',
      },
      {
        value: 1,
        label: '成功',
        type: 'success',
      },
      {
        value: 2,
        label: '失败',
        type: 'danger',
      },
      {
        value: 3,
        label: '超限',
        type: 'danger',
      },
      {
        value: 4,
        label: '终止',
        type: 'danger',
      }
      ],
      rewardModel: {
        team_id: '',
        platform_id: '',
        user_id: '',
        txn_id: '',
        payout: '',
        team_payout: '',
        user_payout: '',
      },
      rewardExps: [],
      height: 680,
      platType: 'add',
      rsDialog: false,
      rsContent: '',
      rsType: 0,
      staticQuery: {
        platform_id: '',
        member: '',
        search_value: '',
        date_value: []
      },
      statisticData:null,
      statisticLoading:false
    }
  },
  created() {
    appStore.changeDetail(1);
    this.getInfo(this.$route.params.id)
    platlist().then((res) => {
      this.platData = res.data
    })
    this.height = screenHeight(170)
  },
  methods: {
    checkPermission,
    clipboard,
    handleTabChange(val) {
      if (val === 'auth') {
        if (this.teamAuthData.length === 0) this.teamAuthList()
      } else if (val === 'member') {
        if (this.userData.length === 0) this.userList()
      } else if (val === 'reward') {
        if (this.rewardData.length === 0) this.rewardList()
      } else if (val === 'static') {
        if (!this.statisticData) this.staticSearch()
      }
    },
    staticSearch() {
      this.statisticData = null
      this.statisticLoading = true
      const param = {
        ...this.staticQuery,
        team_id:this.model.team_id
      }
      statistic(param)
        .then((res) => {
          this.statisticData = res.data[0]
          this.statisticLoading = false
      }).catch(()=>{
        this.statisticLoading = false
      })
    },
    staticRefresh() {
      this.staticQuery = this.$options.data().staticQuery;
      this.staticSearch();
    },
    lookPersona(row, key = 'front_rs') {
      if (!row[key]) {
        ElMessage.error('人设信息为空');
        return
      }
      this.rsDialog = true;
      try {
        this.rsContent = JSON.parse(row[key]);
        this.rsType = 1;
      } catch (e) {
        this.rsContent = row[key];
        this.rsType = 0;
      }
    },
    async getInfo(id) {
      this.loading = true
      await info({ team_id: id })
        .then((res) => {
          this.loading = false
          this.reset(res.data)
          this.rewardQuery.team_id = res.data.team_id
        })
        .catch(() => { console.log('error') })
      this.teamAuthList()
    },
    userAdd() {
      this.userDialog = true
      this.userDialogTitle = '团队用户添加'
      this.userReset()
    },
    userEdit(row) {
      this.userDialog = true
      this.userDialogTitle = '团队用户编辑：' + row[this.idkey]
      var id = {}
      id[this.idkey] = row[this.idkey]
      infoMember(id)
        .then((res) => {
          this.userReset(res.data)
        })
        .catch(() => { })
    },
    userDele(row) {
      ElMessageBox.confirm(
        '确定删除该用户吗?',
        '解除授权',
        {
          confirmButtonText: '确认',
          cancelButtonText: '取消',
          type: 'warning',
        }
      )
        .then(() => {
          deleMember({ ids: [row.member_id] }).then((res) => {
            ElMessage.success(res.msg)
            this.userList()
          })
        })
        .catch(() => {
        })

    },
    // 重置
    userReset(row) {
      if (row) {
        this.userModel = row
      } else {
        this.userModel = this.$options.data().userModel
      }
      if (this.$refs['userRef'] !== undefined) {
        try {
          this.$refs['userRef'].resetFields()
          this.$refs['userRef'].clearValidate()
        } catch (error) { }
      }
    },
    // 查询
    userSearch() {
      this.userQuery.page = 1
      this.userList()
    },
    userCancel() {
      this.userDialog = false
    },
    // 重置查询
    userRefresh() {
      const limit = this.userQuery.limit
      this.userQuery = this.$options.data().userQuery
      this.$refs['userTable'].clearSort()
      this.userQuery.limit = limit
      this.userList()
    },
    // 查询
    teamAuthSearch() {
      this.teamAuthQuery.page = 1
      this.teamAuthList()
    },
    // 重置查询
    teamAuthRefresh() {
      const limit = this.teamAuthQuery.limit
      this.teamAuthQuery = this.$options.data().teamAuthQuery
      this.$refs['teamAuthTable'].clearSort()
      this.teamAuthQuery.limit = limit
      this.teamAuthList()
    },
    // 列表
    userList() {
      this.userLoading = true
      listMember(this.userQuery)
        .then((res) => {
          this.userData = res.data.list
          this.userCount = res.data.count
          this.genders = res.data.genders
          this.platforms = res.data.platforms
          this.applications = res.data.applications
          this.regionData = res.data.region
          this.tagData = res.data.tag
          this.groupData = res.data.group
          this.exps = res.data.exps
          this.userLoading = false
        })
        .catch(() => {
          this.userLoading = false
        })
    },
    userSubmit() {
      this.$refs['userRef'].validate((valid) => {
        if (valid) {
          this.userLoading = true
          this.userModel.api_tree = []
          this.userModel.thirds = []
          this.userModel.team_id = this.model.team_id
          if (this.userModel[this.idkey]) {
            editMember(this.userModel)
              .then((res) => {
                ElMessage.success(res.msg)
                this.userDialog = false
                this.userLoading = false
                this.userList()
              })
              .catch(() => {
                this.userLoading = false
              })
          } else {
            addMember(this.userModel)
              .then((res) => {
                ElMessage.success(res.msg)
                this.userDialog = false
                this.userLoading = false
                this.userList()
              })
              .catch(() => {
                this.userLoading = false
              })
          }
        } else {
          ElMessage.error('请完善必填项（带红色星号*）')
        }
      })
    },
    teamAuthList() {
      this.authloading = true
      const data = {
        team_id: this.model.team_id,
        ...this.teamAuthQuery
      }
      teamAuth(data)
        .then((res) => {
          this.teamAuthData = res.data.list
          this.teamAuthCount = res.data.count
          this.authloading = false
        })
        .catch(() => {
          this.teamAuthData = []
          this.teamAuthCount = 0
          this.authloading = false
        })
    },
    changeStatus() {
      if (!this.model.team_id) return;
      this.switchLoading = true
      edit(this.model)
        .then((res) => {
          ElMessage.success(res.msg)
          this.switchLoading = false
        })
        .catch(() => {
          this.switchLoading = false
        })
    },
    back() {
      appStore.changeDetail(0);
      this.$nextTick(() => {
        this.$router.push({ path: '/team/team' })
      })

    },
    todelAuth(row) {
      ElMessageBox.confirm(
        '确定解除该平台的授权?',
        '解除授权',
        {
          confirmButtonText: '确认',
          cancelButtonText: '取消',
          type: 'warning',
        }
      )
        .then(() => {
          deleAuth({ platform_auth_id: row.platform_auth_id }).then((res) => {
            ElMessage.success(res.msg)
            this.teamAuthList()
          })
        })
        .catch(() => {
        })
    },
    // 重置
    reset(row) {
      if (row) {
        this.model = row
      } else {
        this.model = this.$options.data().model
      }
      this.userQuery.team_id = this.model.team_id
    },
    platAdd(type = 'add', row) {
      this.platType = type
      if (type == 'edit') {
        this.platModel = { ...row }
      } else {
        this.platModel = this.$options.data().platModel
      }
      this.platDialog = true
    },
    platCancel() {
      this.platDialog = false
    },
    platSubmit() {
      this.$refs['platRef'].validate((valid) => {
        if (valid) {
          const data = { ...this.platModel }
          data.team_id = this.model.team_id
          if (this.platType == 'edit') {
            platformEditAuth(data).then((res) => {
              ElMessage.success(res.msg)
              this.platDialog = false
              this.teamAuthList()
            })
          } else {
            platformAuth(data).then((res) => {
              ElMessage.success(res.msg)
              this.platDialog = false
              this.teamAuthList()
            })
          }
        } else {
          ElMessage.error('请完善必填项（带红色星号*）')
        }
      })
    },
    rewardStatusFilter(status) {
      return this.rewardStatus.filter((item) => item.value === status)[0];
    },
    timeDifference(startDate, endDate) {
      // 将时间字符串转换为Date对象
      const start = new Date(startDate.replace(/-/g, '/'));
      const end = new Date(endDate.replace(/-/g, '/'));

      // 计算时间差（毫秒）
      const diff = end.getTime() - start.getTime();
      console.log(start, end)
      // 转换为秒
      const seconds = Math.floor(diff / 1000);

      // 计算分钟和秒数
      const minutes = Math.floor(seconds / 60);
      const secondsLeft = seconds % 60;

      // 返回格式化的时间差字符串
      return `${minutes}分钟${secondsLeft}秒`;
    },
    // 列表
    rewardList() {
      this.rewardLoading = true
      const params = { team_id: this.model.team_id, ...this.rewardQuery }
      listReward(params)
        .then((res) => {
          this.rewardData = res.data.list
          this.rewardCount = res.data.count
          this.rewardExps = res.data.exps
          this.rewardLoading = false
        })
        .catch(() => {
          this.rewardLoading = false
        })
    },
    // 重置
    rewardReset(row) {
      if (row) {
        this.rewardModel = row
      } else {
        this.rewardModel = this.$options.data().rewardModel
      }
      if (this.$refs['rewardRef'] !== undefined) {
        try {
          this.$refs['rewardRef'].resetFields()
          this.$refs['rewardRef'].clearValidate()
        } catch (error) { }
      }
    },
    // 查询
    rewardSearch() {
      this.rewardQuery.page = 1
      this.rewardList()
    },
    // 重置查询
    rewardRefresh() {
      const limit = this.rewardQuery.limit
      this.rewardQuery = this.$options.data().rewardQuery
      this.$refs['rewardTable'].clearSort()
      this.rewardQuery.limit = limit
      this.rewardList()
    },
    // 排序
    rewardSort(sort) {
      this.query.sort_field = sort.prop
      this.query.sort_value = ''
      if (sort.order === 'ascending') {
        this.query.sort_value = 'asc'
        this.rewardList()
      }
      if (sort.order === 'descending') {
        this.query.sort_value = 'desc'
        this.rewardList()
      }
    },
  }
}
</script>