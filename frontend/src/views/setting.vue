<template>
    <main class="main-content">
        <div class="settings">
            <div class="item" v-for="item in settings" :key="item.id">
                <div class="cont flex align-items-center">
                    <div class="imgbox">
                        <img :src="$imgUrl(item.image)" />
                    </div>
                    <div class="info">
                        <div class="title">{{ item.name }}</div>
                        <div class="desc">{{ item.api_name }}</div>
                        <div class="count flex justify-content-between">
                            <div>
                                <div class="c-title">Total offers</div>
                                <div class="c-desc c-number">{{ item.offers }}</div>
                            </div>
                            <div>
                                <div class="c-title">Last updatetime</div>
                                <div class="c-desc c-date">{{ item.updatetime_txt }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="btns">
                    <div class="btn" @click="openSetting(item)"><el-icon>
                            <Setting />
                        </el-icon>Setting</div>
                    <div class="btn" @click="openPull(item)"><el-icon>
                            <SetUp />
                        </el-icon>Pull Data</div>
                </div>
            </div>
        </div>
        <el-dialog class="settingbox" v-model="settingVisible" width="600" align-center>
            <div>
                <div class="setting-header flex align-items-center justify-content-center">
                    <div class="imgbox" style="width:120px;height:60px;overflow:hidden;display:flex;justify-content:center;align-items:center">
                        <img :src="$imgUrl(activeSetting.image)" />
                    </div>
                    <div class="info">
                        <div class="title">{{ activeSetting.name }}</div>
                        <div class="desc">{{ activeSetting.api_name }}</div>
                    </div>
                </div>
                <div class="notik" v-if="activeSetting.name == 'notik'">
                    <view class="item">
                        <div class="title">Status:</div>
                        <el-radio-group v-model="activeSetting.status">
                            <el-radio :value="1">Enable</el-radio>
                            <el-radio :value="0">Disable</el-radio>
                        </el-radio-group>
                    </view>
                    <view class="item">
                        <div class="title">app_id:</div>
                        <el-input size="large" v-model="activeSetting.app_id" style="width: 240px"
                            placeholder="Please input here" />
                    </view>
                    <view class="item">
                        <div class="title">pub_id:</div>
                        <el-input size="large" v-model="activeSetting.pub_id" style="width: 240px"
                            placeholder="Please input here" />
                    </view>
                    <view class="item">
                        <div class="title">api_name:</div>
                        <el-input size="large" v-model="activeSetting.api_name" style="width: 240px"
                            placeholder="Please input here" />
                    </view>
                    <view class="item">
                        <div class="title">api_key:</div>
                        <el-input size="large" v-model="activeSetting.api_key" style="width: 240px"
                            placeholder="Please input here" />
                    </view>
                    <view class="item">
                        <div class="title">api_secret:</div>
                        <el-input size="large" v-model="activeSetting.api_secret" style="width: 240px"
                            placeholder="Please input here" />
                    </view>
                    <view class="item">
                        <div class="title">callback url:</div>
                        <el-input size="large" v-model="activeSetting.callback" style="width: 240px"
                            placeholder="Please input here" />
                    </view>
                    <view class="item">
                        <div class="title">percent(%):</div>
                        <el-input size="large" v-model="activeSetting.rate" style="width: 240px"
                            placeholder="Please input here"></el-input>
                    </view>
                </div>
                <div class="notik" v-else-if="activeSetting.name == 'tctask'">
                    <view class="item">
                        <div class="title">Status:</div>
                        <el-radio-group v-model="activeSetting.status">
                            <el-radio :value="1">Enable</el-radio>
                            <el-radio :value="0">Disable</el-radio>
                        </el-radio-group>
                    </view>
                    <view class="item">
                        <div class="title">coopid:</div>
                        <el-input size="large" v-model="activeSetting.app_id" style="width: 240px"
                            placeholder="Please input here" />
                    </view>
                    <view class="item">
                        <div class="title">token:</div>
                        <el-input size="large" v-model="activeSetting.pub_id" style="width: 240px"
                            placeholder="Please input here" />
                    </view>
                    <view class="item">
                        <div class="title">api_name:</div>
                        <el-input size="large" v-model="activeSetting.api_name" style="width: 240px"
                            placeholder="Please input here" />
                    </view>
                    <view class="item">
                        <div class="title">api_key:</div>
                        <el-input size="large" v-model="activeSetting.api_key" style="width: 240px"
                            placeholder="Please input here" />
                    </view>
                    <view class="item">
                        <div class="title">callback url:</div>
                        <el-input size="large" v-model="activeSetting.callback" style="width: 240px"
                            placeholder="Please input here" />
                    </view>
                    <view class="item">
                        <div class="title">percent(%):</div>
                        <el-input size="large" v-model="activeSetting.rate" style="width: 240px"
                            placeholder="Please input here"></el-input>
                    </view>
                </div>
                <div class="notik" v-else-if="activeSetting.name == 'innomoment'">
                    <view class="item">
                        <div class="title">Status:</div>
                        <el-radio-group v-model="activeSetting.status">
                            <el-radio :value="1">Enable</el-radio>
                            <el-radio :value="0">Disable</el-radio>
                        </el-radio-group>
                    </view>
                    <view class="item">
                        <div class="title">api_name:</div>
                        <el-input size="large" v-model="activeSetting.api_name" style="width: 240px"
                            placeholder="Please input here" />
                    </view>
                    <view class="item">
                        <div class="title">mid:</div>
                        <el-input size="large" v-model="activeSetting.app_id" style="width: 240px"
                            placeholder="Please input here" />
                    </view>
                    <view class="item">
                        <div class="title">api_key:</div>
                        <el-input size="large" v-model="activeSetting.api_key" style="width: 240px"
                            placeholder="Please input here" />
                    </view>
                    <view class="item">
                        <div class="title">api_secret:</div>
                        <el-input size="large" v-model="activeSetting.api_secret" style="width: 240px"
                            placeholder="Please input here" />
                    </view>
                    <view class="item">
                        <div class="title">callback url:</div>
                        <el-input size="large" v-model="activeSetting.callback" style="width: 240px"
                            placeholder="Please input here" />
                    </view>
                    <view class="item">
                        <div class="title">percent(%):</div>
                        <el-input size="large" v-model="activeSetting.rate" style="width: 240px"
                            placeholder="Please input here"></el-input>
                    </view>
                </div>
                <div class="notik" v-else>
                    <view class="item">
                        <div class="title">Status:</div>
                        <el-radio-group v-model="activeSetting.status">
                            <el-radio :value="1">Enable</el-radio>
                            <el-radio :value="0">Disable</el-radio>
                        </el-radio-group>
                    </view>
                    <view class="item">
                        <div class="title">api_name:</div>
                        <el-input size="large" v-model="activeSetting.api_name" style="width: 240px"
                            placeholder="Please input here" />
                    </view>
                    <view class="item">
                        <div class="title">api_key:</div>
                        <el-input size="large" v-model="activeSetting.api_key" style="width: 240px"
                            placeholder="Please input here" />
                    </view>
                    <view class="item">
                        <div class="title">api_secret:</div>
                        <el-input size="large" v-model="activeSetting.api_secret" style="width: 240px"
                            placeholder="Please input here" />
                    </view>
                    <view class="item">
                        <div class="title">callback url:</div>
                        <el-input size="large" v-model="activeSetting.callback" style="width: 240px"
                            placeholder="Please input here" />
                    </view>
                    <view class="item">
                        <div class="title">percent(%):</div>
                        <el-input size="large" v-model="activeSetting.rate" style="width: 240px"
                            placeholder="Please input here"></el-input>
                    </view>
                </div>
                <div class="setting-btn">
                    <el-button size="large" type="success" @click="savePlat">Edit Platform</el-button>
                </div>
            </div>
        </el-dialog>
        <el-dialog :close-on-press-escape="false" :close-on-click-modal="false" class="settingbox" v-model="pullVisible"
            width="600" align-center>
            <el-progress :show-text="false" :percentage="30" :stroke-width="15" status="success" striped striped-flow />
        </el-dialog>
    </main>
</template>
<script setup>
import { ref } from 'vue'
import { getSettings, runPull,saveSetting } from '@/api/modules/user'
import { ElMessage, ElMessageBox } from 'element-plus'
const settings = ref([])
const settingVisible = ref(false)
const pullVisible = ref(false)
const activeSetting = ref({})
getSettings().then(res => {
    settings.value = res.data
})
const openSetting = (item) => {
    activeSetting.value = item;
    settingVisible.value = true;
}
const openPull = (item) => {
    ElMessageBox.confirm(
        'Are you sure you want to pull the data again from ' + item.name + '?',
        'Warning',
        {
            confirmButtonText: 'OK',
            cancelButtonText: 'Cancel',
            type: 'warning',
        }
    )
        .then(() => {
            activeSetting.value = item;
            pullVisible.value = true;
            runPull({ id: activeSetting.value.id }).then(res => {
                pullVisible.value = false;
                if (res.code == 1) {
                    ElMessage({
                        type: 'success',
                        message: 'Pull data successfully',
                    })
                    getSettings().then(res => {
                        settings.value = res.data
                    })
                }
            }).catch(() => {
                pullVisible.value = false;
            })

        })
        .catch(() => {
            ElMessage({
                type: 'info',
                message: 'Delete canceled',
            })
        })
}
const savePlat = () => {
    saveSetting({...activeSetting.value}).then(res => {
        if (res.code == 1) {
            ElMessage({
                type: 'success',
                message: 'Edit platform successfully',
            })
            getSettings().then(res => {
                settings.value = res.data
            })
        }
    })
}

</script>
<style scoped lang="scss">
.notik {
    padding: 0 40px 30px;

    .item {
        display: flex;
        margin-bottom: 10px;
        background-color: rgb(255, 255, 255,.1);
        align-items: center;
        border:1px solid rgba(14, 255, 78, 0.2509803922);
        
        border-radius: 10px;
        overflow: hidden;
        padding:2px 20px;
        .el-select {
            flex: 1;
            background-color: rgba(0,0,0,0);
        }

        .title {
            width: 100px;
            font-weight: bold;
            border-right:1px solid rgba(14, 255, 78, 0.2509803922);
            margin-right:20px;
            font-size:16px;
            span {
                color: red;
                display: inline-flex;
                align-items: center;
            }
        }

        .el-input {
            flex: 1;
        }
    }
}

.setting-btn {
    display: flex;
    padding: 0 40px 40px;
    justify-content: center;

    .el-button {
        flex: 1;
    }
}

.settings {
    display: grid;
    gap: 12px;
    width: 100%;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    .item {
        background: rgba(255, 255, 255, .1);
        padding: 20px;
        border-radius: 20px;
        .cont {
            flex: 1;
        }

        .imgbox {
            width: 200px;
            display: flex;
            height: 150px;
            align-items: center;
            justify-content: center;
            border-radius: 20px;

            img {
                width: 80%;
            }
        }

        .info {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding-left: 20px;

            .title {
                font-size: 30px;
                font-weight: bold;
            }

            .count {
                .c-title {
                    font-weight: bold;
                    padding-top: 10px;
                }

                .c-number {
                    font-size: 30px;
                }

                .c-date {
                    font-size: 16px;
                }

                .c-desc {
                    height: 40px;
                    display: flex;
                    align-items: center;
                }
            }
        }

        .btns {
            display: flex;
            padding-top: 30px;

            .btn {
                padding: 10px 0;
                flex: 1;
                background-color: rgba(14, 255, 78, 0.5);
                color: #fff;
                border-radius: 10px;
                cursor: pointer;
                margin-right: 20px;
                display: flex;
                align-items: center;
                justify-content: center;

                &:last-child {
                    margin-right: 0;
                    background-color: #fff;
                    color: #666;
                }

                .el-icon {
                    margin-right: 5px;
                }
            }
        }
    }
}

.setting-header {
    padding: 60px 40px 30px;

    img {
        width: 100px;
    }

    .info {
        padding-left: 10px;

        .title {
            font-size: 30px;
            font-weight: bold;
            line-height: 1

        }
    }
}
</style>