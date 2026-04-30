import http from "@/api";
/**
 * @name 课程模块
 */

// 获取课程主题视频
export const getCourseTheme = (params) => {
    return http.get(`/api/course/theme_video`, params,{ loading: false });
};
// 获取课程分类
export const getCourseCate = (params) => {
    return http.get(`/api/course/cate`, params,{ loading: false });
};
// 获取课程列表
export const getCourseLists = (params) => {
    return http.post(`/api/course/list`, params,{ loading: false });
};
// 更新播放量
export const updateCoursePlays = (params) => {
    return http.post(`/api/course/update_plays`, params,{ loading: false });
};