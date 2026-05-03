<template>
  <div class="login-container" >
    <!--<iframe :src="bgurl" frameborder="0" width="100%" :height="height"></iframe>-->
    <div class="tool">
      <lang-select class="cursor-pointer mr-4" />
      <theme-select class="cursor-pointer mr-4" />
      <svg-icon icon-class="full-screen" size="16" @click="toggle" />
    </div>
    <div class="login-logo">
      <el-image class="mr-4" v-if="logo_url" :src="logo_url">
      </el-image>
    </div>
    <div style="width: calc(100% - 530px);height: 100vh;" :style="{
    backgroundImage: 'url(' + login_bg_url + ')',
    backgroundColor: login_bg_color,
    backgroundPosition:'50%',
    backgroundSize:'cover',
  }"></div>
    <div class="login-box">
      <el-form ref="ref" class="login-form" :model="model" :rules="rules">

        <div class="login-title">{{ $t('login.welcome') }}👋🏻</div>
        <div class="login-title-desc">{{ $t('login.welcomeDesc') }}</div>
        <el-form-item prop="username">
          <el-input size="large" v-model="model.username" :placeholder="$t('login.username')">

          </el-input>
        </el-form-item>
        <el-form-item prop="password">
          <el-input size="large" v-model="model.password" type="password" :placeholder="$t('login.password')"
            show-password>

          </el-input>
        </el-form-item>
        <el-form-item v-if="captcha_switch && captcha_src" prop="captcha_code">
          <el-col :span="13">
            <el-input plain v-model="model.captcha_code" :placeholder="$t('login.captchaCode')" autocomplete="off"
              clearable>
              <template #prefix>
                <svg-icon icon-class="picture" />
              </template>
            </el-input>
          </el-col>
          <el-col :span="11">
            <el-image class="login-captcha" :style="{ height: captchaHeight }" :src="captcha_src" fit="fill"
              :alt="$t('login.captchaCode')" :title="$t('login.Click to refresh the captcha code')" @click="captcha" />
          </el-col>
        </el-form-item>
        <el-form-item prop="remember">
          <div class="w-full flex justify-between">
            <el-checkbox v-model="model.remember" :label="$t('login.rememberMe')" size="large" />
            <a class="login-forget">{{ $t('login.forgetPassword') }}?</a>
          </div>
        </el-form-item>
        <aj-captcha v-if="captcha_switch && captcha_mode == 2" ref="ajcaptcha" mode="pop" :captcha-type="captcha_type"
          @success="ajcaptchaSuccess" />
        <el-button size="large" v-if="captcha_switch && captcha_mode == 2" :loading="loading" type="primary"
          class="login-bottom" @click="ajcaptchaShow">
          {{ $t('login.login') }}
        </el-button>
        <el-button size="large" v-else :loading="loading" type="primary" class="login-bottom"
          @click.prevent="handleLogin">
          {{ $t('login.login') }}
        </el-button>
        <div class="mt-4 flex items-center justify-between text-gray-500"><span
            class="border-input w-[35%] border-b border-gray-600"></span><span
            class="text-muted-foreground text-center text-xs uppercase">其他登录方式</span><span
            class="border-input w-[35%] border-b border-gray-600"></span></div>
      </el-form>
      <p style="position: absolute; bottom: 0px;color:#4c4c4c;text-align: center;left:0;right:0;font-size: 14px;">
        Copyright © 2024 Wanhong Survey
      </p>
    </div>
  </div>
</template>

<script>
import defaultSettings from '@/settings'
import LangSelect from '@/components/LangSelect/index.vue'
import ThemeSelect from '@/components/ThemeSelect/index.vue'
import AjCaptcha from '@/components/AjCaptcha/index.vue'
import { captcha, setting } from '@/api/system/login'
import { useAppStoreHook } from '@/store/modules/app'
import { useSettingsStoreHook } from '@/store/modules/settings'
import { useUserStoreHook } from '@/store/modules/user'
import { delNotice } from '@/utils/settings'

export default {
  name: 'SystemLogin',
  components: { LangSelect, ThemeSelect, AjCaptcha },
  data() {
    return {
      name: '登录',
      loading: false,
      redirect: undefined,
      otherQuery: {},
      captcha_switch: 0,
      captcha_mode: 1,
      captcha_type: 'blockPuzzle',
      captcha_src: '',
      login_bg_url: '',
      login_bg_color: '',
      logo_url: '',
      system_name: '',
      model: {
        username: '',
        password: '',
        captcha_id: '',
        captcha_code: '',
        ajcaptcha: {}
      },
      bgurl: import.meta.env.VITE_APP_BASE_URL + '/a1.html'
    }
  },
  computed: {
    captchaHeight() {
      const appStore = useAppStoreHook()
      if (appStore.size == 'large') {
        return '40px'
      } else if (appStore.size == 'small') {
        return '24px'
      }
      return '32px'
    },
    rules() {
      return {
        username: [{ required: true, message: this.$t('login.Please enter username'), trigger: 'blur' }],
        password: [{ required: true, message: this.$t('login.Please enter password'), trigger: 'blur' }],
        captcha_code: [{ required: true, message: this.$t('login.Please enter captcha code'), trigger: 'blur' }]
      }
    }
  },
  watch: {
    $route: {
      handler(route) {
        const query = route.query
        if (query) {
          this.redirect = query.redirect
          this.otherQuery = this.getOtherQuery(query)
        }
      },
      immediate: true
    }
  },
  created() {
    this.setting()
  },
  methods: {
    // 验证码
    captcha() {
      captcha().then((res) => {
        this.captchaData(res.data)
      })
    },
    toggle() {
      if (!document.fullscreenElement) {
        this.enterFullScreen();
      } else {
        this.exitFullScreen();
      }
    },
    enterFullScreen() {
      let element = document.documentElement;
      if (element.requestFullscreen) {
        element.requestFullscreen();
      } else if (element.mozRequestFullScreen) { /* Firefox */
        element.mozRequestFullScreen();
      } else if (element.webkitRequestFullscreen) { /* Chrome, Safari & Opera */
        element.webkitRequestFullscreen();
      } else if (element.msRequestFullscreen) { /* IE/Edge */
        element.msRequestFullscreen();
      }
    },
    exitFullScreen() {
      if (document.exitFullscreen) {
        document.exitFullscreen();
      } else if (document.mozCancelFullScreen) { /* Firefox */
        document.mozCancelFullScreen();
      } else if (document.webkitExitFullscreen) { /* Chrome, Safari and Opera */
        document.webkitExitFullscreen();
      } else if (document.msExitFullscreen) { /* IE/Edge */
        document.msExitFullscreen();
      }
    },
    captchaData(data) {
      this.model.captcha_id = ''
      this.model.captcha_code = ''
      if (data.captcha_switch) {
        if (data.captcha_mode === 1) {
          this.captcha_src = data.captcha_src
          this.model.captcha_id = data.captcha_id
        }
      }
      this.captcha_switch = data.captcha_switch
      this.captcha_mode = data.captcha_mode
      if (data.captcha_type === 1) {
        this.captcha_type = 'blockPuzzle'
      } else {
        this.captcha_type = 'clickWord'
      }
      const storePrefix = defaultSettings.storePrefix
      localStorage.setItem(storePrefix + 'captchaSwitch', data.captcha_switch)
      localStorage.setItem(storePrefix + 'captchaMode', data.captcha_mode)
      localStorage.setItem(storePrefix + 'captchaType', data.captcha_type)
      localStorage.setItem(storePrefix + 'captchaSrc', data.captcha_src)
    },
    ajcaptchaSuccess(params) {
      this.model.ajcaptcha = params
      this.handleLogin()
    },
    ajcaptchaShow() {
      this.$refs['ref'].validate((valid) => {
        if (valid) {
          this.$refs.ajcaptcha.show()
        } else {
          return false
        }
      })
    },
    // 设置
    setting() {
      const storePrefix = defaultSettings.storePrefix
      this.login_bg_url = localStorage.getItem(storePrefix + 'loginBgUrl')
      this.login_bg_color = localStorage.getItem(storePrefix + 'loginBgColor')
      this.logo_url = localStorage.getItem(storePrefix + 'logoUrl')
      this.system_name = localStorage.getItem(storePrefix + 'systemName')
      this.model.captcha_id = ''
      this.model.captcha_code = ''
      this.captcha_switch = localStorage.getItem(storePrefix + 'captchaSwitch')
      this.captcha_mode = localStorage.getItem(storePrefix + 'captchaMode')
      this.captcha_type = localStorage.getItem(storePrefix + 'captchaType')
      this.captcha_src = localStorage.getItem(storePrefix + 'captchaSrc')
      const settingsStore = useSettingsStoreHook()
      settingsStore.changeSetting({ key: 'watermarkContent', value: '' })
      setting()
        .then((res) => {
          delNotice()
          const data = res.data
          this.captchaData(data)
          this.login_bg_url = data.login_bg_url
          this.login_bg_color = data.login_bg_color
          this.logo_url = data.logo_url
          this.system_name = data.system_name
          settingsStore.changeSetting({ key: 'loginBgUrl', value: data.login_bg_url })
          settingsStore.changeSetting({ key: 'loginBgColor', value: data.login_bg_color })
          settingsStore.changeSetting({ key: 'logoUrl', value: data.logo_url })
          settingsStore.changeSetting({ key: 'systemName', value: data.system_name })
          settingsStore.changeSetting({ key: 'pageTitle', value: data.page_title })
          settingsStore.changeSetting({ key: 'pageLimit', value: data.page_limit })
          settingsStore.changeSetting({ key: 'notice', value: 0 })
          settingsStore.changeSetting({ key: 'watermarkEnabled', value: data.is_watermark })
        })
        .catch(() => { })
    },
    // 登录
    handleLogin() {
      this.$refs['ref'].validate((valid) => {
        if (valid) {
          this.loading = true
          const userStore = useUserStoreHook()
          userStore
            .login(this.model)
            .then(() => {
              this.$router
                .push({
                  path: this.redirect || '/',
                  query: this.otherQuery
                })
                .catch(() => {
                  this.loading = false
                })
            })
            .catch(() => {
              this.loading = false
              if (this.captcha_switch && this.captcha_mode === 2) {
                this.$refs.ajcaptcha.refresh()
              } else {
                this.captcha()
              }
            })
        } else {
          return false
        }
      })
    },
    getOtherQuery(query) {
      return Object.keys(query).reduce((acc, cur) => {
        if (cur !== 'redirect') {
          acc[cur] = query[cur]
        }
        return acc
      }, {})
    }
  }
}
</script>

<style lang="scss" scoped>
:deep(.el-input__wrapper) {
  background: none !important;
}

:deep(.el-form-item__error) {
  margin: 5px 0;
}

:deep(.el-form-item--default) {
  margin-bottom: 30px;
}

@keyframes changeBorderColor {
  0% {
    transform: translate(-50%, -50%);
  }

  100% {
    transform: translate(-50%, -48%);
  }
}

.login-bg {
  display: block;
  width: 50%;
  height: auto;
  position: absolute;
  left: 35%;
  top: 50%;
  transform: translate(-50%, -50%);
  padding: 0 50px;
  //animation: changeBorderColor 1s infinite alternate;
}

.tool {
  padding: 8px 20px;
  position: absolute;
  right: 35px;
  top: 20px;
  //background-color: #444;
  border: 1px solid #dbdbdb;
  border-radius: 40px;
  display: flex;
  align-items: center;
  z-index: 3;

  svg {
    cursor: pointer;
  }
}

.login-container {
  width: 100%;
  min-height: 100%;
  overflow: hidden;
  background-color: var(--el-bg-color-overlay);
  background-size: 100% 100%;
  background-position: center center;
  display: flex;

  .login-box {
    position: absolute;
    right: 0;
    height: 100%;
    border-left: 2px solid var(--el-color-primary);
    width: 530px;
    background-color: var(--el-bg-color-overlay);
  }

  .login-forget {
    color: var(--el-color-primary);
  }

  .login-form {
    position: relative;
    padding: 35px;
    margin: 100px auto;
    width: 560px;
    max-width: 100%;
    overflow: hidden;
  }

  .login-title {
    height: 30px;
    line-height: 30px;
    margin: 0px auto;
    font-weight: bold;
    font-size: 2.25rem;
    //color: #eee;
  }

  .login-title-desc {
    //color: #eee;
    margin: 15px 0 50px;
  }

  .login-logo {
    display: flex;
    align-items: center;
    justify-content: center;
    position: absolute;
    top: 30px;
    left: 30px;
    font-size: 40px;
    line-height: 0;
    color:#fff;
    .el-image {
      height: 50px;
    }
  }

  .login-captcha {
    float: right;
    width: 90%;
    border-radius: 4px;
    vertical-align: middle;
  }

  .login-bottom {
    width: 100%;
    margin-bottom: 30px;
    padding: 20px 0;
  }
}
</style>
