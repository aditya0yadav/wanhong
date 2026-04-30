import axios from "axios";
import { showFullScreenLoading, tryHideFullScreenLoading } from "@/components/Loading/fullScreen";
import { ElMessage } from "element-plus";
import { checkStatus } from "./helper/checkStatus";
import { useUserStore } from "@/stores/modules/user";
import router from "@/router";

const config = {
  // 默认地址请求地址
  // baseURL: 'http',
  baseURL: 'https://Wanhong.com',
  // 设置超时时间
  timeout: 300000,
  headers: {},
  params: {},
  data: {}
};

class RequestHttp {
  service;
  constructor(config) {
    // instantiation
    this.service = axios.create(config);

    /**
     * @description 请求拦截器
     * 客户端发送请求 -> [请求拦截器] -> 服务器
     * token校验(JWT) : 接受服务器返回的 token,存储到 vuex/pinia/本地储存当中
     */
    this.service.interceptors.request.use(
      (config) => {
        const userStore = useUserStore();
        // 当前请求不需要显示 loading，在 api 服务中通过指定的第三个参数: { loading: false } 来控制
        config.loading ?? (config.loading = true);
        config.loading && showFullScreenLoading();
        if (config.headers && typeof config.headers.set === "function") {
          config.headers.set("apiToken", userStore.token);
        }
        return config;
      },
      (error) => {
        return Promise.reject(error);
      }
    );

    /**
     * 
     * @description 响应拦截器
     *  服务器换返回信息 -> [拦截统一处理] -> 客户端JS获取到信息
     */
    this.service.interceptors.response.use(
      (response) => {
        if (response.data.type && response.data.type != 'application/json')  {
          const file_name = response.config.params?.file_name || ''
          let link = document.createElement('a')
          link.href = window.URL.createObjectURL(response.data)
          if (file_name) {
            link.setAttribute('download', file_name)
          }
          document.body.appendChild(link)
          link.click()
          link.remove()
          tryHideFullScreenLoading();
          return {}
        }
        const { data,config } = response;
        tryHideFullScreenLoading();
        // 返回错误信息
        if (data.code !== 200) {
          checkStatus(data.code, data.msg);
          const userStore = useUserStore();
          // 登陆失效
          if (data.code == 401) {
            userStore.setToken("");
            userStore.setUserInfo("");
            userStore.setMenu([]);
            router.push("/login");
          }
          // 未获得授权
          if (data.code == 409) {
            userStore.setToken("");
            userStore.setUserInfo("");
            userStore.setMenu([]);
            router.push("/unauth");
          }
          return Promise.reject(data.msg);
        }
        // 成功请求（在页面上除非特殊情况，否则不用处理失败逻辑）
        return data;
      },
      async (error) => {
        const { response } = error;
        tryHideFullScreenLoading();
        // 请求超时 && 网络错误单独判断，没有 response
        if (error.message.indexOf("timeout") !== -1) ElMessage.error("请求超时！请您稍后重试");
        if (error.message.indexOf("Network Error") !== -1) ElMessage.error("网络错误！请您稍后重试");
        // 根据服务器响应的错误状态码，做不同的处理
        if (response) {
          console.log(response)
          checkStatus(response.status);
          // 登陆失效
          if (response.status == 401) {
            const userStore = useUserStore();
            userStore.setToken("");
            userStore.setUserInfo("");
            userStore.setMenu([]);
            window.location.href="/login";
          }
          if (response.status == 403) {
            window.location.href="/offers";
          }
        }
        // 服务器结果都没有返回(可能服务器错误可能客户端断网)，断网处理:可以跳转到断网页面
        if (!window.navigator.onLine) router.replace("/500");
        return Promise.reject(error);
      }
    );
  }

  /**
   * @description 常用请求方法封装
   */
  get(url, params, _object = {}) {
    return this.service.get(url, { params, ..._object });
  }
  post(url, params, _object = {},header = {}) {
    return this.service.post(url, params, _object);
  }
  formPost(url, formData, _object = {},header = {}) {
    return this.service.post(url, formData, _object);
  }
  put(url, params, _object = {}) {
    return this.service.put(url, params, _object);
  }
  delete(url, params, _object = {}) {
    return this.service.delete(url, { params, ..._object });
  }
  download(url, params, _object = {}) {
    return this.service.get(url, params, { ..._object, responseType: "blob" });
  }
}

export default new RequestHttp(config);
