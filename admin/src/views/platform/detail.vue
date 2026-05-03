<template>
    <div>
        <div class="p-2 bg-[--el-bg-color] dark:bg-[--menuBg] flex align-center"><span
                class="cursor-pointer hover:text-primary"><svg-icon icon-class="back" @click="toPlatform"
                    size="1.5em" /></span> <span class="ml-2" v-if="model.platform_name">{{ model.platform_name
                    }}的平台详情</span></div>
        <el-scrollbar native :height="height">
            <div class="app-container rounded-md">
                <div class="p-3 bg-[--el-bg-color-overlay] rounded-lg">
                    <div class="font-size-3.5 pb-3 border-b border-[--cus-bg-color]">基础信息</div>
                    <el-row :gutter="20" class="py-3">
                        <el-col :span="6">
                            <div class="p-3 flex align-center font-size-3 bg-[--cus-bg-color] rounded-md">
                                <div class="mr-3">平台名称:</div>
                                <div>{{ model.platform_name }}</div>
                            </div>
                        </el-col>
                        <el-col :span="6">
                            <div class="p-3 flex align-center font-size-3 bg-[--cus-bg-color] rounded-md">
                                <div class="mr-3">平台标识:</div>
                                <div>{{ model.platform_sign }}</div>
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
                    </el-row>
                </div>
                <div class="p-3 bg-[--el-bg-color-overlay] mt-3  rounded-lg">
                    <el-tabs v-model="activeName" @tab-change="handleTabChange">
                        <el-tab-pane name="info" label="平台设置" lazy>
                            <div style="font-size:14px;">基础配置</div>
                            <el-row :gutter="20" v-loading="loading">
                                <el-col :span="6" class="mt-3">
                                    <div class="p-3 flex font-size-3 bg-[--cus-bg-color] rounded-md line-height-6">
                                        <span class="mr-3">开启状态:</span>
                                        <el-switch :loading="switchLoading" size="small"
                                            @change="($event) => changeStatus($event, 'is_disable')"
                                            v-model="model.is_disable" :active-value="0" :inactive-value="1" />
                                    </div>
                                </el-col>
                                <el-col :span="6" class="mt-3">
                                    <div class="p-3 flex font-size-3 bg-[--cus-bg-color] rounded-md line-height-6">
                                        <span class="mr-3">启用列表:</span>
                                        <el-switch :loading="switchLoading" size="small"
                                            @change="($event) => changeStatus($event, 'is_list')"
                                            v-model="model.is_list" :active-value="1" :inactive-value="0" />
                                    </div>
                                </el-col>
                                <el-col :span="6" class="mt-3">
                                    <div class="p-3 flex font-size-3 bg-[--cus-bg-color] rounded-md line-height-6">
                                        <span class="mr-3">启用优惠:</span>
                                        <el-switch :loading="switchLoading" size="small"
                                            @change="($event) => changeStatus($event, 'is_wall')"
                                            v-model="model.is_wall" :active-value="1" :inactive-value="0" />
                                    </div>
                                </el-col>
                                <el-col :span="6" class="mt-3">
                                    <div class="p-3 flex font-size-3 bg-[--cus-bg-color] rounded-md line-height-6">
                                        <span class="mr-3">展示攻略:</span>
                                        <el-switch :loading="switchLoading" size="small"
                                            @change="($event) => changeStatus($event, 'is_quota')"
                                            v-model="model.is_quota" :active-value="1" :inactive-value="0" />
                                    </div>
                                </el-col>
                                <el-col :span="6" class="mt-3">
                                    <div class="p-3 flex font-size-3 bg-[--cus-bg-color] rounded-md line-height-6">
                                        <span class="mr-3">展示调查ID:</span>
                                        <el-switch :loading="switchLoading" size="small"
                                            @change="($event) => changeStatus($event, 'show_no')"
                                            v-model="model.show_no" :active-value="1" :inactive-value="0" />
                                    </div>
                                </el-col>
                                <el-col :span="6" class="mt-3">
                                    <div class="p-3 flex font-size-3 bg-[--cus-bg-color] rounded-md line-height-6">
                                        <span class="mr-3">展示调查名称:</span>
                                        <el-switch :loading="switchLoading" size="small"
                                            @change="($event) => changeStatus($event, 'show_name')"
                                            v-model="model.show_name" :active-value="1" :inactive-value="0" />
                                    </div>
                                </el-col>
                                <el-col :span="6" class="mt-3">
                                    <div class="p-3 flex font-size-3 bg-[--cus-bg-color] rounded-md line-height-6">
                                        <span class="mr-3">展示点击量:</span>
                                        <el-switch :loading="switchLoading" size="small"
                                            @change="($event) => changeStatus($event, 'show_click')"
                                            v-model="model.show_click" :active-value="1" :inactive-value="0" />
                                    </div>
                                </el-col>
                                <el-col :span="6" class="mt-3">
                                    <div class="p-3 flex font-size-3 bg-[--cus-bg-color] rounded-md line-height-6">
                                        <span class="mr-3">展示完成量:</span>
                                        <el-switch :loading="switchLoading" size="small"
                                            @change="($event) => changeStatus($event, 'show_complete')"
                                            v-model="model.show_complete" :active-value="1" :inactive-value="0" />
                                    </div>
                                </el-col>
                                <el-col :span="6" class="mt-3">
                                    <div class="p-3 flex font-size-3 bg-[--cus-bg-color] rounded-md line-height-6">
                                        <span class="mr-3">展示配额量:</span>
                                        <el-switch :loading="switchLoading" size="small"
                                            @change="($event) => changeStatus($event, 'show_quota')"
                                            v-model="model.show_quota" :active-value="1" :inactive-value="0" />
                                    </div>
                                </el-col>
                                <el-col :span="6" class="mt-3">
                                    <div class="p-3 flex font-size-3 bg-[--cus-bg-color] rounded-md line-height-6">
                                        <span class="mr-3">展示Loi:</span>
                                        <el-switch :loading="switchLoading" size="small"
                                            @change="($event) => changeStatus($event, 'show_loi')"
                                            v-model="model.show_loi" :active-value="1" :inactive-value="0" />
                                    </div>
                                </el-col>
                                <el-col :span="6" class="mt-3">
                                    <div class="p-3 flex font-size-3 bg-[--cus-bg-color] rounded-md line-height-6">
                                        <span class="mr-3">展示Ir:</span>
                                        <el-switch :loading="switchLoading" size="small"
                                            @change="($event) => changeStatus($event, 'show_ir')"
                                            v-model="model.show_ir" :active-value="1" :inactive-value="0" />
                                    </div>
                                </el-col>
                                <el-col :span="6" class="mt-3">
                                    <div class="p-3 flex font-size-3 bg-[--cus-bg-color] rounded-md line-height-6">
                                        <span class="mr-3">项目类型:</span>
                                        <el-radio-group size="small" v-model="model.is_custom" :loading="switchLoading"
                                            @change="($event) => changeStatus($event, 'is_custom')">
                                            <el-radio class="model-mr" :value="0">手动创建</el-radio>
                                            <el-radio :value="1">API创建</el-radio>
                                        </el-radio-group>
                                    </div>
                                </el-col>
                                <el-col :span="6" class="mt-3">
                                    <div class="p-3 flex font-size-3 bg-[--cus-bg-color] rounded-md line-height-6">
                                        <span class="mr-3">回调模型:</span>
                                        <el-radio-group size="small" v-model="model.model_type" :loading="switchLoading"
                                            @change="($event) => changeStatus($event, 'model_type')">
                                            <el-radio class="model-mr" :value="0">通用模型</el-radio>
                                            <el-radio :value="1">自定义模型</el-radio>
                                        </el-radio-group>
                                    </div>
                                </el-col>
                                <el-col :span="6" class="mt-3">
                                    <div class="p-3 flex font-size-3 bg-[--cus-bg-color] rounded-md line-height-6">
                                        <span class="mr-3">回调奖励:</span>
                                        <el-radio-group size="small" v-model="model.pay_type" :loading="switchLoading"
                                            @change="($event) => changeStatus($event, 'pay_type')">
                                            <el-radio class="model-mr" :value="0">货币</el-radio>
                                            <el-radio :value="1">金币</el-radio>
                                        </el-radio-group>
                                    </div>
                                </el-col>
                                <el-col :span="6" class="mt-3">
                                    <div class="p-3 flex font-size-3 bg-[--cus-bg-color] rounded-md line-height-6">
                                        <span class="mr-3">异常回调:</span>
                                        <el-radio-group size="small" v-model="model.is_accept_error"
                                            :loading="switchLoading"
                                            @change="($event) => changeStatus($event, 'is_accept_error')">
                                            <el-radio class="model-mr" :value="0">不接收</el-radio>
                                            <el-radio :value="1">接收</el-radio>
                                        </el-radio-group>
                                    </div>
                                </el-col>
                            </el-row>
                            <div style="margin-top:20px;font-size:14px;">人设模版</div>
                            <el-row :gutter="20">
                                <el-col :span="6" class="mt-3">
                                    <div
                                        class="p-3 flex font-size-3 bg-[--cus-bg-color] rounded-md line-height-6 items-center">
                                        <span class="mr-3 whitespace-nowrap">前置人设模版:</span>
                                        <el-select
                                            @change="($event) => changeStatus($event, 'platform_persona_template')"
                                            size="small" v-model="model.platform_persona_template" class="w-full">
                                            <el-option label="不启用前置人设" :value="0" />
                                            <el-option v-for="item in frontPersonaData"
                                                :label="item.persona_name + ' -【前置】'" :value="item.persona_id" />
                                        </el-select>
                                    </div>
                                </el-col>
                                <el-col :span="6" class="mt-3">
                                    <div
                                        class="p-3 flex font-size-3 bg-[--cus-bg-color] rounded-md line-height-6 items-center">
                                        <span class="mr-3 whitespace-nowrap">后置人设模版:</span>
                                        <el-select
                                            @change="($event) => changeStatus($event, 'platform_persona_backend')"
                                            size="small" v-model="model.platform_persona_backend" class="w-full">
                                            <el-option label="不启用后置人设" :value="0" />
                                            <el-option v-for="item in backendPersonaData"
                                                :label="item.persona_name + ' -【后置】'" :value="item.persona_id" />
                                        </el-select>
                                    </div>
                                </el-col>
                            </el-row>
                        </el-tab-pane>
                        <el-tab-pane name="auth" label="已授权团队" lazy>
                            <el-table ref="platformAuthTable" v-loading="authloading" :data="platformAuthData"  :height="height - 320">
                                <el-table-column prop="team_name" label="团队名称" min-width="170" sortable="custom"
                                    show-overflow-tooltip />
                                <el-table-column prop="team_host" label="团队域名" min-width="170" sortable="custom"
                                    show-overflow-tooltip />
                                <el-table-column prop="auth_num" label="授权成员数量" min-width="170" sortable="custom"
                                    show-overflow-tooltip />
                                <el-table-column label="操作" width="240">
                                    <template #default="scope">
                                        <el-button size="small" link type="primary" :underline="false"
                                            @click="todelAuth(scope.row)"> 解除授权 </el-button>
                                    </template>
                                </el-table-column>
                            </el-table>
                            <!-- 分页 -->
                            <pagination v-show="platformAuthCount > 0" v-model:total="platformAuthCount"
                                v-model:page="platformAuthQuery.page" v-model:limit="platformAuthQuery.limit"
                                size="small" @pagination="platformAuthDataList" />
                        </el-tab-pane>
                        <el-tab-pane name="project" label="项目列表" lazy>
                            <el-row>
                                <el-col class="mb-4">
                                    <el-button v-if="model.is_custom == 0" plain type="primary"
                                        @click="openProjectAdd()">添加项目</el-button>
                                    <el-button v-else plain v-loading.fullscreen.lock="pullLoading" type="primary"
                                        @click="openProjectPull">拉取项目</el-button>
                                    <el-button plain v-loading.fullscreen.lock="pullLoading" type="warning"
                                        @click="copyProject">克隆项目</el-button>
                                    <el-button type="danger"
                                        @click="disAllProject">一键暂停</el-button>
                                    <el-button type="danger"
                                        @click="delAllProject">一键删除</el-button>
                                    <el-select v-model="projectQuery.search_field" class="ya-search-field mr-2 ml-3"
                                        placeholder="查询字段">
                                        <el-option value="project_pno" label="PID" />
                                        <el-option value="project_no" label="项目编号" />
                                        <el-option value="project_name" label="项目名称" />
                                        <el-option value="project_code" label="国家代码" />
                                    </el-select>
                                    <el-input v-model="projectQuery.search_value" class="ya-search-value mr-2"
                                        placeholder="请输入查询内容" clearable />
                                    <el-button type="primary" title="查询" @click="openProjectSearch()">查询</el-button>
                                    <el-button type="default" title="重置" @click="openProjectRefresh()">重置</el-button>
                                </el-col>
                            </el-row>
                            <!-- 列表 -->
                            <el-table ref="projectTable" v-loading="projectloading" :data="projectData"
                                @sort-change="projectSort" stripe @selection-change="projectSelect" :height="height - 370">
                                <el-table-column type="selection" width="42" title="全选/反选" />
                                <el-table-column prop="project_pno" label="PID" width="150" fixed="left">
                                    <template #default="scope">
                                        <span>{{ scope.row.project_pno }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column prop="project_no" label="项目编号" width="180">
                                    <template #default="scope">
                                        <span>{{ scope.row.project_no }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column prop="project_code" label="国家代码" width="120"><template
                                        #default="scope">
                                        <span>{{ scope.row.project_code }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column prop="project_name" label="项目名称" width="180"><template
                                        #default="scope">
                                        <span>{{ scope.row.project_name }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column prop="project_cpi" label="项目CPI" width="100"><template
                                        #default="scope">
                                        <span>{{ scope.row.project_cpi }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column prop="currency.currency_name" label="项目货币" width="100"><template
                                        #default="scope">
                                        <span>{{ scope.row.currency.currency_name }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column label="项目金币" width="100">
                                    <template #default="scope">

                                        <span>{{(currencyAllData.find((item) => item.currency_id ==
                                            scope.row.project_currency).currency_coins *
                                            scope.row.project_cpi).toFixed(2)}}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column prop="project_loi" label="项目LOI" width="100">
                                    <template #default="scope">
                                        <span>{{ scope.row.project_loi }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column prop="project_ir" label="项目IR" width="100">
                                    <template #default="scope">
                                        <span>{{ scope.row.project_ir }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column v-for="(item, index) in model.project_params" :label="item.name"
                                    width="180">
                                    <template #default="scope">
                                        <span>{{ scope.row.project_params ?
                                            scope.row.project_params[index]['value'] : '' }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column prop="create_time" label="添加时间" width="180">
                                    <template #default="scope">
                                        <span>{{ scope.row.create_time }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column prop="update_time" label="更新时间" width="180">
                                    <template #default="scope">
                                        <span>{{ scope.row.update_time }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column prop="is_disable_name" label="是否禁用" width="120">
                                    <template #default="scope">
                                        <span>{{ scope.row.is_disable_name }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column label="操作" width="280" fixed="right">
                                    <template #default="scope">
                                        <el-button size="small" link type="primary" :underline="false"
                                            @click="openProjectEdit(scope.row)"> 编辑 </el-button>
                                        <el-divider direction="vertical" />
                                        <el-button size="small" link type="primary" :underline="false"
                                            @click="openProjectDisable(scope.row)"> {{
                                                scope.row.is_disable ? '启用' :
                                                    '禁用' }}
                                        </el-button>
                                        <el-divider direction="vertical" />
                                        <el-button size="small" link type="primary" :underline="false"
                                            @click="openProjectDele(scope.row, 0)"> 删除 </el-button>
                                    </template>
                                </el-table-column>
                            </el-table>
                            <!-- 分页 -->
                            <pagination v-show="projectCount > 0" v-model:total="projectCount"
                                v-model:page="projectQuery.page" v-model:limit="projectQuery.limit" size="small"
                                @pagination="projectDataList" />
                        </el-tab-pane>
                        <el-tab-pane name="dashboard" label="数据看板" lazy>
                            <div v-loading="statisticLoading">
                                <el-row>
                                    <el-col class="mb-4">
                                        <el-date-picker v-model="statisticDate" type="datetimerange" class="mr-2"
                                            start-placeholder="开始日期" end-placeholder="结束日期"
                                            value-format="YYYY-MM-DD HH:mm:ss"
                                            :default-time="[new Date(2024, 1, 1, 0, 0, 0), new Date(2024, 1, 1, 23, 59, 59)]" />
                                        <el-button type="primary" @click="platformStatisticData">查询</el-button>
                                    </el-col>
                                </el-row>
                                <div id="numberEchart" :style="{ height: '500px' }"></div>
                            </div>
                        </el-tab-pane>
                        <el-tab-pane name="reclycle" label="回收站" lazy>
                            <el-alert title="手动删除的项目,API拉取后不存在的项目将自动进入回收站,可手动进行恢复和彻底删除" type="error" />
                            <el-row class="mt-4">
                                <el-col class="mb-4">
                                    <el-button type="danger" title="清空" @click="handleClearReclycle()">清空回收站</el-button>
                                    <el-select v-model="projectQuery.search_field" class="ya-search-field mr-2 ml-3"
                                        placeholder="查询字段">
                                        <el-option value="project_pno" label="PID" />
                                        <el-option value="project_no" label="项目编号" />
                                        <el-option value="project_name" label="项目名称" />
                                        <el-option value="project_code" label="国家代码" />
                                    </el-select>
                                    <el-input v-model="projectQuery.search_value" class="ya-search-value mr-2"
                                        placeholder="请输入查询内容" clearable />
                                    <el-button type="primary" title="查询" @click="openProjectSearch()">查询</el-button>
                                    <el-button type="default" title="重置" @click="openProjectRefresh()">重置</el-button>
                                </el-col>
                            </el-row>
                            <!-- 列表 -->
                            <el-table ref="projectTable" v-loading="projectloading" :data="projectData"
                                @sort-change="projectSort" stripe @selection-change="projectSelect"  :height="height - 430">
                                <el-table-column type="selection" width="42" title="全选/反选" />
                                <el-table-column prop="project_pno" label="PID" width="150" fixed="left">
                                    <template #default="scope">
                                        <span>{{ scope.row.project_pno }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column prop="project_no" label="项目编号" width="180">
                                    <template #default="scope">
                                        <span>{{ scope.row.project_no }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column prop="project_code" label="国家代码" width="120"><template
                                        #default="scope">
                                        <span>{{ scope.row.project_code }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column prop="project_name" label="项目名称" width="180"><template
                                        #default="scope">
                                        <span>{{ scope.row.project_name }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column prop="project_cpi" label="项目CPI" width="100"><template
                                        #default="scope">
                                        <span>{{ scope.row.project_cpi }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column prop="currency.currency_name" label="项目货币" width="100"><template
                                        #default="scope">
                                        <span>{{ scope.row.currency.currency_name }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column label="项目金币" width="100">
                                    <template #default="scope">
                                        <span>{{(currencyAllData.find((item) => item.currency_id ==
                                            scope.row.project_currency).currency_coins *
                                            scope.row.project_cpi).toFixed(2)}}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column prop="project_loi" label="项目LOI" width="100">
                                    <template #default="scope">
                                        <span>{{ scope.row.project_loi }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column prop="project_ir" label="项目IR" width="100">
                                    <template #default="scope">
                                        <span>{{ scope.row.project_ir }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column v-for="(item, index) in model.project_params" :label="item.name"
                                    width="180">
                                    <template #default="scope">
                                        <span>{{ scope.row.project_params ?
                                            scope.row.project_params[index]['value'] : '' }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column prop="delete_time" label="删除时间" width="180">
                                    <template #default="scope">
                                        <span>{{ scope.row.delete_time }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column prop="is_disable_name" label="是否禁用" width="120">
                                    <template #default="scope">
                                        <span>{{ scope.row.is_disable_name }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column label="操作" width="280" fixed="right">
                                    <template #default="scope">
                                        <el-button size="small" link type="success" :underline="false"
                                            @click="unDele(scope.row)"> 恢复 </el-button>
                                        <el-divider direction="vertical" />
                                        <el-button size="small" link type="danger" :underline="false"
                                            @click="openProjectDele(scope.row, 1)"> 彻底删除 </el-button>
                                    </template>
                                </el-table-column>
                            </el-table>
                            <!-- 分页 -->
                            <pagination v-show="projectCount > 0" v-model:total="projectCount"
                                v-model:page="projectQuery.page" v-model:limit="projectQuery.limit" size="small"
                                @pagination="projectDataList" />
                        </el-tab-pane>
                    </el-tabs>
                </div>
            </div>
        </el-scrollbar>
        <!-- 添加、编辑 -->
        <el-dialog v-model="projectDialog" :title="projectDialogTitle" :close-on-click-modal="false"
            :close-on-press-escape="false" :before-close="projectCancel" top="5vh" width="1200px">
            <el-form v-if="projectDialog" v-loading="editloading" ref="projectForm" :model="projectModel"
                :rules="projectRules" label-width="80px">
                <el-tabs>
                    <el-tab-pane label="基础信息">
                        <el-form-item label="项目编号" prop="project_no">
                            <el-input v-model="projectModel.project_no" clearable />
                        </el-form-item>
                        <el-form-item label="项目名称" prop="project_name">
                            <el-input v-model="projectModel.project_name" clearable />
                        </el-form-item>
                        <el-form-item label="Cpi" prop="project_cpi">
                            <el-input-number style="width: 100%" :precision="2" controls-position="right" :min="0.01"
                                :max="10000" :step="0.01" v-model="projectModel.project_cpi" clearable />
                        </el-form-item>
                        <el-form-item label="Loi" prop="project_quota">
                            <el-input-number style="width: 100%" controls-position="right" :min="0" :max="10000"
                                :step="1" v-model="projectModel.project_loi" clearable />
                        </el-form-item>
                        <el-form-item label="Ir" prop="project_quota">
                            <el-input-number style="width: 100%" controls-position="right" :min="0" :max="10000"
                                :step="1" v-model="projectModel.project_ir" clearable />
                        </el-form-item>
                        <el-form-item label="项目货币" prop="project_currency">
                            <el-select v-model="projectModel.project_currency" placeholder="请选择" clearable filterable>
                                <el-option v-for="(item, index) in currencyAllData" :key="index"
                                    :label="item.currency_name" :value="item.currency_id" />
                            </el-select>
                        </el-form-item>
                        <el-form-item label="国家代码" prop="project_code">
                            <el-input v-model="projectModel.project_code" clearable />
                        </el-form-item>
                        <el-form-item label="项目链接" prop="project_click_url">
                            <el-input v-model="projectModel.project_click_url" clearable />
                        </el-form-item>
                        <el-form-item v-if="model.show_quota" label="完成次数" prop="project_complete">
                            <el-input-number style="width: 100%" controls-position="right" :min="0" :max="10000"
                                :step="1" v-model="projectModel.project_complete" clearable />
                        </el-form-item>
                        <el-form-item v-if="model.show_quota" label="项目配额" prop="project_quota">
                            <el-input-number style="width: 100%" controls-position="right" :min="0" :max="10000"
                                :step="1" v-model="projectModel.project_quota" clearable />
                        </el-form-item>
                        <el-form-item label="热门推荐" prop="is_recommend">
                            <el-radio-group v-model="projectModel.is_recommend">
                                <el-radio class="model-mr" :value="0">不推荐</el-radio>
                                <el-radio class="model-mr" :value="1">推荐</el-radio>
                            </el-radio-group>
                        </el-form-item>
                        <el-form-item label="前置人设">
                            <el-select v-model="projectModel.project_persona_template" class="w-full">
                                <el-option label="不启用人设" :value="0" />
                                <el-option v-for="item in frontPersonaData"
                                    :label="item.persona_name + ' -【' + (item.persona_type == 0 ? '前置' : '后置') + '】'"
                                    :value="item.persona_id" />
                            </el-select>
                            <el-text type="danger">项目前置人设模版,优先级高于平台前置人设模版。</el-text>
                        </el-form-item>
                        <el-form-item label="后置人设">
                            <el-select v-model="projectModel.project_persona_backend" class="w-full">
                                <el-option label="不启用人设" :value="0" />
                                <el-option v-for="item in backendPersonaData"
                                    :label="item.persona_name + ' -【' + (item.persona_type == 0 ? '前置' : '后置') + '】'"
                                    :value="item.persona_id" />
                            </el-select>
                            <el-text type="danger">项目后置人设模版,优先级高于平台后置人设模版。</el-text>
                        </el-form-item>
                        <el-form-item v-for="(param, index) in projectModel.project_params" :key="param.key"
                            :label="param.name" :prop="'project_params.' + index + '.name'">
                            <div class="flex w-full">
                                <el-input class="mr-2 flex-1" v-model="param.value" :placeholder="'请输入' + param.name" />
                            </div>
                        </el-form-item>
                    </el-tab-pane>
                    <el-tab-pane label="文档信息">
                        <el-form-item label="内容" prop="project_content">
                            <vue-ueditor-wrap v-model="projectModel.project_content" editor-id="editor"
                                :config="editorConfig" :editorDependencies="['ueditor.config.js', 'ueditor.all.js']"
                                />
                        </el-form-item>
                        <el-form-item label="文件" prop="words">
                            <FileImage v-model="projectModel.project_file_id" fileType="word" file-title="上传项目文档"
                                :file-url="projectModel.project_file_url" file-tip="项目文档优先级大于内容,上传后将优先显示" :height="120"
                                upload />
                        </el-form-item>
                    </el-tab-pane>
                </el-tabs>
            </el-form>
            <template #footer>
                <el-button :loading="projectLoading" @click="projectCancel">取消</el-button>
                <el-button :loading="projectLoading" type="primary" @click="projectSubmit">提交</el-button>
            </template>
        </el-dialog>
        <el-dialog v-model="fileDialog" :title="fileTitle" :close-on-click-modal="false" :close-on-press-escape="false"
            top="1vh" width="80%" append-to-body>
            <FileManage :file-type="fileType" @file-cancel="fileCancel" @file-submit="fileSubmit" />
        </el-dialog>
    </div>
</template>
<script>
import checkPermission from '@/utils/permission'
import { clipboard } from '@/utils/index'
import { getPageLimit } from '@/utils/settings'
import RichEditor from '@/components/RichEditor/index.vue'
import { pull, info, edit, platformAuthList, deleAuth, currencyAllList, platformStatistic } from '@/api/platform/platform'
import { list as projectList, info as projectInfo, add as projectAdd, edit as projectEdit, dele as projectDele, disable as projectDisable, copy, restore, clearReclycle,disall,delall } from '@/api/project/project'
import { useAppStoreHook } from '@/store/modules/app'
import { list as personaList } from '@/api/platform/persona'
import screenHeight from '@/utils/screen-height'
import { arrayColumn } from '@/utils/index'
import { ElMessage, ElMessageBox } from 'element-plus'
import FileManage from '@/components/FileManage/index.vue'
import * as echarts from 'echarts/core'
// 引入图表，图表后缀都为 Chart
import { LineChart, BarChart } from 'echarts/charts'
// 引入组件，组件后缀都为 Component
import { TitleComponent, LegendComponent, GridComponent, TooltipComponent, ToolboxComponent } from 'echarts/components'
// 引入 Canvas 渲染器，注意引入 CanvasRenderer 或者 SVGRenderer 是必须的一步
import { CanvasRenderer } from 'echarts/renderers'
import { set } from 'nprogress'
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
const appStore = useAppStoreHook();
export default {
    name: 'PlatformDetail',
    components: { FileManage },
    data() {
        return {
            activeName: 'info',
            loading: true,
            switchLoading: false,
            editloading: false,
            model: {
                platform_name: '',
                platform_sign: '',
                is_disable: 0,
                create_time: '',
                update_time: '',
            },
            personaVisible: false,
            platformAuthData: [],
            platformAuthCount: 0,
            authloading: false,
            platformAuthQuery: {
                page: 1,
                limit: getPageLimit(),
                search_field: 'platform_name',
                search_exp: 'like',
                date_field: 'create_time'
            },
            projectData: [],
            projectCount: 0,
            projectloading: false,
            projectDialog: false,
            projectDialogTitle: '',
            projectQuery: {
                page: 1,
                limit: getPageLimit(),
                search_field: 'project_pno',
                search_exp: 'like',
                date_field: 'create_time'
            },
            projectModel: {
                project_no: '',
                project_name: '',
                project_content: '',
                sort: 0,
                project_params: [],
            },
            projectRules: {
                project_no: [{ required: true, message: '请输入项目ID', trigger: 'blur' }],
                project_name: [{ required: true, message: '请输入项目名称', trigger: 'blur' }],
                project_cpi: [{ required: true, message: '请输入项目金额', trigger: 'blur' }]
            },
            currencyAllData: [],
            height: 680,
            pullLoading: false,
            selection: [],
            fileModel: {
                file_url: '',
                file_name: '',
                file_ext: ''
            },
            statisticData: [],
            statisticDate: [],
            statisticLoading: false,
            projectType: 0,
            personaData: [],
            editorConfig: {
                serverUrl: import.meta.env.VITE_APP_BASE_URL + '/admin/file.File/ueditor_add',
                autoHeightEnabled: false,
                // 初始容器高度
                initialFrameHeight: 400,
                serverHeaders: {
                    AdminToken: localStorage.getItem('admin_AdminToken'),
                },
                // 初始容器宽度
                initialFrameWidth: '100%',
                // 配置UEditorPlus的惊天资源
                UEDITOR_HOME_URL: '/admin/public/ueditor/',
                imageActionName: "uploadimage",
                imageFieldName: "file",
                imageUrlPrefix: "",
                imageMaxSize: 2048000,
                imageAllowFiles: [".png", ".jpg", ".jpeg", ".gif"],
                toolbarCallback: (cmd, editor) => {
                    console.log('toolbarCallback', cmd, editor);
                    switch (cmd) {
                        case 'insertimage':
                            this.fileAction = 'insertimage'
                            this.fileDialog = true
                            this.fileType = 'image'
                            return true;
                        case 'insertvideo':
                            this.fileAction = 'insertvideo'
                            this.fileDialog = true
                            this.fileType = 'video'
                            return true;
                        case 'insertaudio':
                            this.fileAction = 'insertaudio'
                            this.fileDialog = true
                            this.fileType = 'audio'
                            return true;
                        case 'attachment':
                            this.fileAction = 'attachment'
                            this.fileDialog = true
                            this.fileType = 'attachment'
                            return true;
                        case 'insertframe':
                            ElMessageBox.prompt('请输入网络地址', '插入Iframe', {
                                confirmButtonText: 'OK',
                                cancelButtonText: 'Cancel',
                            })
                                .then(({ value }) => {
                                    this.editor.execCommand('insertHtml', '<p><iframe style="width:100%;height:300px" src="'+value+'" /></p>');
                                })
                                .catch(() => {
                                })
                            
                            return true;
                    }
                }

            },
            fileDialog: false,
            fileTitle: '',
            fileType: '',
            fileAction: '',
        }
    },
    async created() {
        appStore.changeDetail(1);
        await personaList({ page: 1, limit: 10000 }).then(res => {
            this.personaData = res.data.list
        })
        this.getInfo(this.$route.params.id)
        currencyAllList().then((res) => {
            this.currencyAllData = res.data
        })
        this.height = screenHeight(100)
    },
    computed: {
        frontPersonaData() {
            return this.personaData.filter(item => item.persona_type == 0)
        },
        backendPersonaData() {
            return this.personaData.filter(item => item.persona_type == 1)
        }
    },
    methods: {
        checkPermission,
        clipboard,
        disAllProject() {
            ElMessageBox.confirm(
                '确定禁用全部项目吗?',
                '禁用全部项目',
                {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning',
                },
            ).then(() => {
                disall({platform_id: this.model.platform_id}).then((res) => {
                    ElMessage.success('禁用成功')
                    this.projectDataList()
                })
            })
        },
        delAllProject() {
            ElMessageBox.confirm(
                '确定删除全部项目吗?',
                '删除全部项目',
                {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning',
                },
            ).then(() => {
                delall({platform_id: this.model.platform_id}).then((res) => {
                    ElMessage.success('删除成功')
                    this.projectDataList()
                })
            })
        },
        async getInfo(id) {
            this.loading = true
            await info({ platform_id: id })
                .then((res) => {
                    this.loading = false
                    this.reset(res.data)
                })
                .catch(() => { console.log('error') })
            this.platformAuthDataList()
        },
        fileCancel() {
            this.fileDialog = false
        },
        fileSubmit(fileList) {
            this.fileDialog = false
            const fileLength = fileList.length
            if (fileLength) {
                const index = fileLength - 1
                if (this.fileAction === 'insertimage') {
                    this.editor.execCommand('insertimage', {
                        src: fileList[index]['file_url'],
                        width: '100%', // 可选样式
                    });
                }
                if (this.fileAction === 'insertaudio') {
                    this.editor.execCommand('insertHtml', '<p><audio src="' + fileList[index]['file_url'] + '" /></p>');
                }
                if (this.fileAction === 'insertvideo') {
                    this.editor.execCommand('insertHtml', '<p><video controls><source src="' + fileList[index]['file_url'] + '" type="video/mp4"></video></p>');
                }
                if (this.fileAction === 'attachment') {
                    this.editor.execCommand('insertHtml', '<p><a href="' + fileList[index]['file_url'] + '">' + fileList[index]['file_name'] + '</a></p>');
                }
            }
        },
        handleEditorReady(editor) {
            this.editor = editor
        },
        projectSelect(selection) {
            this.selection = selection
            this.selectIds = this.selectGetIds(selection).toString()
        },
        selectGetIds(selection) {
            return arrayColumn(selection, 'project_id')
        },
        copyProject() {
            if (this.selection.length === 0) {
                ElMessage.error('请选择要复制的项目');
                return;
            }
            if (this.selection.length > 1) {
                ElMessage.error('只能选择一个项目进行复制');
                return;
            }
            this.editloading = true
            this.projectDialog = true
            this.projectDialogTitle = '添加项目'
            projectInfo({project_id: this.selection[0].project_id})
                .then((res) => {
                    this.projectModel = {
                        ...res.data,
                        project_id: '',
                        project_click:0,
                        project_complete:0
                    }
                    this.editloading = false;
                })
                .catch(() => { })

        },
        handleTabChange(val) {
            if (val === 'auth') {
                this.platformAuthDataList()
            } else if (val === 'project') {
                this.projectType = 0
                this.projectQuery = this.$options.data().projectQuery
                this.projectDataList()
            } else if (val === 'dashboard') {
                this.platformStatisticData();
            } else if (val === 'reclycle') {
                this.projectType = 1
                this.projectQuery = this.$options.data().projectQuery
                this.projectDataList()
            }
        },
        platformStatisticData() {
            this.statisticLoading = true
            platformStatistic({ platform_id: this.model.platform_id, date_value: this.statisticDate }).then((res) => {
                this.statisticDate = [res.data.start, res.data.end];
                this.statisticLoading = false
                this.dateEchart(res.data, 'numberEchart')
            }).catch(() => {
                this.statisticLoading = false
            })
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
                            show: true
                        }
                    }
                },
                series: data.series
            }
            echart.setOption(option)
        },
        openProjectPull() {
            this.pullLoading = true
            pull({ platform_id: this.model.platform_id }).then((res) => {
                this.projectQuery.page = 1
                this.pullLoading = false
                ElMessage.success(res.msg);
            })
        },
        projectSort(sort) {
            // 排序
            this.projectQuery.sort_field = sort.prop
            this.projectQuery.sort_value = ''
            if (sort.order === 'ascending') {
                this.projectQuery.sort_value = 'asc'
                this.projectDataList()
            }
            if (sort.order === 'descending') {
                this.projectQuery.sort_value = 'desc'
                this.projectDataList()
            }
        },
        unDele(row) {
            restore({ project_id: row.project_id }).then((res) => {
                ElMessage.success('恢复成功');
                this.projectDataList()
            })
        },
        handleClearReclycle() {
            ElMessageBox.confirm(
                '确定清空回收站吗?',
                '清空回收站',
                {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning',
                },
            ).then(() => {
                clearReclycle({ platform_id: this.model.platform_id }).then((res) => {
                    ElMessage.success('清理成功');
                    this.projectDataList()
                })
            })
        },
        openProjectAdd() {
            this.projectDialog = true
            this.projectDialogTitle = '添加项目'
            this.projectReset()
        },
        openProjectSearch() {
            this.projectQuery.page = 1
            this.projectDataList()
        },
        openProjectRefresh() {
            const limit = this.projectQuery.limit
            this.projectQuery = this.$options.data().projectQuery
            this.projectQuery.limit = limit
            this.projectDataList()
        },
        openProjectEdit(row) {
            this.projectDialog = true
            this.projectDialogTitle = '编辑项目'
            var id = {}
            id['project_id'] = row['project_id']
            this.editloading = true
            projectInfo(id)
                .then((res) => {

                    this.projectReset(res.data)
                    this.editloading = false;
                })
                .catch(() => { })
        },
        openProjectDisable(row) {
            ElMessageBox.confirm(
                '禁用后用户无法使用该项目,确定' + (row.is_disable ? '启用' : '禁用') + '该项目吗?',
                (row.is_disable ? '启用' : '禁用') + '项目',
                {
                    confirmButtonText: '确认',
                    cancelButtonText: '取消',
                    type: 'warning',
                }
            )
                .then(() => {
                    projectDisable({ ids: [row.project_id], is_disable: row.is_disable == 1 ? 0 : 1 }).then((res) => {
                        ElMessage.success(res.msg)
                        this.projectDataList()
                    })
                })
                .catch(() => {
                })
        },
        // 重置
        projectReset(row) {

            if (row) {
                this.projectModel = row
                if (this.projectModel.project_file_id > 0) {
                    this.fileModel = this.projectModel.project_file
                } else {
                    this.fileModel = this.$options.data().fileModel
                }
            } else {
                this.projectModel = this.$options.data().projectModel
                this.projectModel['project_params'] = this.model['project_params'] || [];
                this.projectModel['project_currency'] = this.model['platform_currency'];
            }
            if (this.$refs['projectRef'] !== undefined) {
                try {
                    this.$refs['projectRef'].resetFields()
                    this.$refs['projectRef'].clearValidate()
                } catch (error) { }
            }
            console.log(this.projectModel)
        },
        openProjectDele(row, st) {
            ElMessageBox.confirm(
                st == 1 ? '确定彻底删除该项目吗?彻底删除项目不可恢复' : '确定删除该项目吗?',
                '删除项目',
                {
                    confirmButtonText: '确认',
                    cancelButtonText: '取消',
                    type: 'warning',
                }
            )
                .then(() => {
                    projectDele({ ids: [row.project_id], st: st }).then((res) => {
                        ElMessage.success(res.msg)
                        this.projectDataList()
                    })
                })
                .catch(() => {
                })
        },
        projectCancel() {
            this.projectDialog = false
        },
        projectSubmit() {
            this.$refs.projectForm.validate((valid) => {
                if (valid) {
                    this.projectLoading = true
                    this.projectModel.platform_id = this.model.platform_id
                    if (this.projectModel['project_id']) {
                        projectEdit(this.projectModel)
                            .then((res) => {
                                ElMessage.success(res.msg)
                                this.projectDialog = false
                                this.projectLoading = false
                                this.projectDataList()
                            })
                            .catch(() => {
                                this.projectLoading = false
                            })
                    } else {
                        projectAdd(this.projectModel)
                            .then((res) => {
                                ElMessage.success(res.msg)
                                this.projectDialog = false
                                this.projectLoading = false
                                this.projectDataList()
                            })
                            .catch(() => {
                                this.projectLoading = false
                            })
                    }
                } else {
                    ElMessage.error('请完善必填项（带红色星号*）')
                }
            })
        },
        projectDataList() {
            this.projectloading = true
            const data = {
                platform_id: this.model.platform_id,
                ...this.projectQuery,
                projectType: this.projectType
            }
            projectList(data)
                .then((res) => {
                    this.projectData = res.data.list
                    this.projectData.forEach((item) => {
                        if (item.project_params) {
                            item.project_params = JSON.parse(item.project_params)
                        }
                    })
                    this.projectCount = res.data.count
                    this.projectloading = false
                })
                .catch(() => {
                    this.projectData = []
                    this.projectCount = 0
                    this.projectloading = false
                })

        },
        todelAuth(row) {
            ElMessageBox.confirm(
                '确定解除对该团队的授权?',
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
                        this.platformAuthDataList()
                    })
                })
                .catch(() => {
                })
        },
        platformAuthDataList() {
            this.authloading = true
            const data = {
                platform_id: this.model.platform_id,
                ...this.platformAuthQuery
            }
            platformAuthList(data)
                .then((res) => {
                    this.platformAuthData = res.data.list
                    this.platformAuthCount = res.data.count
                    this.authloading = false
                })
                .catch(() => {
                    this.platformAuthData = []
                    this.platformAuthCount = 0
                    this.authloading = false
                })
        },
        changeStatus(e, field) {
            if (!this.model.platform_id) return;
            this.switchLoading = true
            this.loading = true
            edit(this.model)
                .then((res) => {
                    ElMessage.success(res.msg)
                    this.switchLoading = false
                    this.loading = false
                })
                .catch(() => {
                    this.switchLoading = false
                    this.loading = false
                    this.model[field] = e == 1 ? 0 : 1
                })
        },
        toPlatform() {
            appStore.changeDetail(0);
            this.$nextTick(() => {
                this.$router.push({ path: '/platform/platform' })
            })

        },
        // 重置
        reset(row) {
            if (row) {
                this.model = row
            } else {

                this.model = this.$options.data().model
            }
        },
    }
}
</script>
<style lang="scss" scoped>
::v-deep(.el-input-number .el-input__inner) {
    text-align: left;
}

::v-deep(.el-input-number.is-controls-right .el-input__wrapper) {
    padding-left: 10px;
}

.model-mr {
    margin-right: 15px;
}
</style>