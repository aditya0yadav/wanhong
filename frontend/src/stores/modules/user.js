import { defineStore } from "pinia";
import piniaPersistConfig from "@/stores/persist";
import i18n from "@/i18n";
export const useUserStore = defineStore({
  id: "user",
  state: () => ({
    token: "",
    showLogin: false,
    hideSide: false,
    menus:[],
    sys: {},
    userInfo: {},
    theme: "dark",
    lang: "en",
    hb:'coins',
    noticeNum: 0
  }),
  getters: {},
  actions: {
    // Set Token
    setToken(token) {
      this.token = token;
    },
    SET_LOCALE(locale) {
      this.lang = locale
      localStorage.setItem('lang', locale)
      i18n.global.locale.value = locale
    },
    SET_HB(hb) {
      this.hb = hb
    },
    // Set setUserInfo
    setUserInfo(userInfo) {
      this.userInfo = userInfo;
    },
    // Set sys
    setSys(sys) {
      this.sys = sys;
    },
    setMenu(menu){
      this.menus = menu
    }
  },
  persist: piniaPersistConfig("user")
});