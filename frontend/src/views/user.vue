<template>
    <main class="main-content">
        <div v-if="!loading">
            <div class="h-title">
                <el-button type="primary" @click="openAdd">
                    + {{ $t('user.addUser') }}
                </el-button>
                <el-input class="search" v-model="userQuery.search_value" size="large" :placeholder="$t('user.searchByNickname')"
                     />
                <el-button style="margin-left:10px;" type="primary" size="large" @click="onChange">{{ $t('statistics.search') }}</el-button>
                
            </div>
            <div class="regional_table">
                <el-table :data="tableData" style="width: 100%;margin-bottom:20px;">
                    <el-table-column prop="member_id" label="ID" />
                    <el-table-column prop="group_names" :label="$t('user.role')" />
                    <el-table-column prop="username" :label="$t('user.username')">
                    </el-table-column>
                    <el-table-column prop="nickname" :label="$t('user.nickname')" />
                    <el-table-column prop="rate" :label="$t('user.commissionRatio')">
                        <template #default="scope">
                            {{ scope.row.rate }}%
                        </template>
                    </el-table-column>
                    <el-table-column prop="login_ip" :label="$t('user.loginIp')" />
                    <el-table-column prop="login_time" :label="$t('user.loginTime')" />
                    <el-table-column :label="$t('user.operate')">
                        <template #default="scope">
                            <a href="javascript:;" @click="openEdit(scope.row)">{{ $t('user.edit') }}</a>
                        </template>
                    </el-table-column>
                </el-table>
                <div class="flex justify-content-end">
                    <el-pagination :default-page-size="userQuery.limit" @current-change="changeCurrent" small background
                        layout="prev, pager, next" :current-page="userQuery.page" :total="total" class="mt-4" />
                </div>
            </div>
            <el-dialog :title="$t('user.editUser')" :close-on-press-escape="false" :close-on-click-modal="false"
                v-model="editVisible" width="600" align-center>
                <div class="editbox">
                    <div class="item">
                        <div class="title">{{ $t('user.username') }}:</div>
                        <el-input size="large" v-model="activeUser.username" style="width: 240px"
                             />
                    </div>
                    <div class="item">
                        <div class="title">{{ $t('user.nickname') }}:</div>
                        <el-input size="large" v-model="activeUser.nickname" style="width: 240px"
                             />
                    </div>
                    <div class="item">
                        <div class="title">{{ $t('user.commissionRatio') }}:</div>
                        <el-input size="large" v-model="activeUser.rate" style="width: 240px"
                             />
                    </div>
                    <div class="item">
                        <div class="title">{{ $t('user.password') }}:</div>
                        <el-input size="large" v-model="activeUser.password" style="width: 240px"
                            :placeholder="$t('user.notFill')" />
                    </div>
                </div>
                <div class="edit-btn">
                    <el-button type="primary" @click="submitEdit" size="large">{{ $t('user.save') }}</el-button>
                </div>
            </el-dialog>
            <el-dialog :title="$t('user.addUser')" :close-on-press-escape="false" :close-on-click-modal="false"
                v-model="addVisible" width="600" align-center>
                <div class="editbox">
                    <div class="item">
                        <div class="title">{{ $t('user.username') }}:</div>
                        <el-input size="large" v-model="addUser.username" style="width: 240px"
                         />
                    </div>
                    <div class="item">
                        <div class="title">{{ $t('user.nickname') }}:</div>
                        <el-input size="large" v-model="addUser.nickname" style="width: 240px"
                         />
                    </div>
                    <div class="item">
                        <div class="title">{{ $t('user.password') }}:</div>
                        <el-input size="large" v-model="addUser.password" style="width: 240px"
                         />
                    </div>
                    <div class="item">
                        <div class="title">{{ $t('user.commissionRatio') }}:</div>
                        <el-input size="large" v-model="addUser.rate" style="width: 240px"
                          />
                    </div>
                </div>
                <div class="edit-btn">
                    <el-button type="primary" @click="submitAdd" size="large" style="width: 100%;">{{ $t('user.submit') }}</el-button>
                </div>
            </el-dialog>
        </div>
        <div v-else class="v2-loading">
            <div class="loading">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
    </main>
</template>
<script setup>
import { add, list, edit } from '@/api/modules/user'
import { ref } from 'vue';
import { ElMessage, ElMessageBox, genFileId } from 'element-plus'
import { useUserStore } from '@/stores/modules/user'
import { storeToRefs } from 'pinia'
const { userInfo } = storeToRefs(useUserStore())
const tableData = ref([]);
const currentPage = ref(1);
const total = ref(0);
const editVisible = ref(false);
const addVisible = ref(false);
const activeUser = ref();
const search = ref(null);
const loading = ref(true);
const addUser = ref({
    nickname: '',
    password: '',
    username: '',
    rate: 0
})
const userQuery = ref({
    page: 1,
    limit: 20,
    search_field: 'nickname',
    search_exp: 'like',
    search_value: '',
    date_field: 'create_time'
})
const getUserList = () => {
    loading.value = true;
    list(userQuery.value).then(res => {
        tableData.value = res.data.list;
        total.value = res.data.pages;
        loading.value = false;
    }).catch(() => {
        loading.value = false
    })
}
getUserList()
const openEdit = (row) => {
    activeUser.value = { ...row };
    editVisible.value = true;
}
const openAdd = () => {
    addVisible.value = true;
    addUser.value = {
        nickname: '',
        password: '',
        username: '',
        rate: 0
    }
}
const submitAdd = () => {
    add(addUser.value).then(res => {
        addVisible.value = false;
        getUserList();
    })
}
const changeCurrent = (e) => {
    userQuery.value.page = e
    getUserList()
}
const onChange = () => {
    userQuery.value.page = 1;
    getUserList()
}
const submitEdit = () => {
    edit(activeUser.value).then(res => {
        editVisible.value = false;
        getUserList();
    })
}
</script>
<style scoped lang="scss">
.h-title {
    padding-bottom: 20px;

    .search {
        width: 240px;
        background: rgba(255, 255, 255, .2);
        border-radius: 10px;
        margin-left: 20px;

        &:focus {
            box-shadow: none;
        }

    }
}

.edit-btn {
    display: flex;
    justify-content: center;

    .el-button {
        flex: 1;
    }
}

.editbox {
    padding: 20px 0 40px;
    margin-top: 10px;
    .item {
        margin-bottom: 10px;
        border-radius: 10px;
        overflow: hidden;
        width: 100%;
        box-sizing: border-box;

        .title {
            margin-bottom: 10px;
        }

        .el-select {
            width: 100% !important;
            background-color: rgba(0, 0, 0, 0);
        }

        .el-input {
            width: 100% !important;
        }
    }
}
</style>