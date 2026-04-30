import http from "@/api";
import { tr } from "element-plus/es/locale/index.mjs";
/**
 * @name 用户管理模块
 */
// 网站信息
export const site = (params) => {
    return http.post(`/api/index/site`, params,{ loading: false });
}
export const getLastRewards = (params) => {
    return http.post(`/api/index/get_last_rewards`, params,{ loading: false });
}
// 登录用户
export const login = (params) => {
    return http.post(`/api/member.Login/login`, params);
};
// 退出登录
export const logout = (params) => {
    return http.post(`/api/member.Logout/logout`, params);
};
export const avatar = (params) => {
    return http.post(`/api/member.Member/avatar`, params);
}
export const edit = (params) => {
    return http.post(`/api/member.Member/edit`, params);
}
export const changeNickname = (params) => {
    return http.post(`/api/member.Member/changeNickname`, params);
}
export const add = (params) => {
    return http.post(`/api/member.Member/add`, params);
}
export const list = (params) => {
    return http.post(`/api/member.Member/list`, params,{ loading: false });
}
export const getUserInfo = (params) => {
    return http.get(`/api/member.Member/info`, params,{ loading: false });
}
export const getBaseDetail = (params) => {
    return http.get(`/api/member.Member/baseinfo`, params,{ loading: false });
}
export const getNotice = (params) => {
    return http.get(`/api/member.Member/notice`, params,{ loading: false });
}
export const markNotice = (params) => {
    return http.get(`/api/member.Member/marknotice`, params,{ loading: true });
}
export const noticeDetail = (params) => {
    return http.get(`/api/member.Member/noticedetail`, params,{ loading: true });
}
export const unread = (params) => {
    return http.get(`/api/member.Member/unread`, params,{ loading: false });
}