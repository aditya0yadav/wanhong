import http from "@/api";

// 获取平台列表
export const getPlatformList = (params) => {
    return http.get(`/api/member.Platform/list`, params,{ loading: false });
};
export const getOffers = (params) => {
    return http.get(`/api/member.Platform/offers`, params,{ loading: false });
}
export const getFeatured = (params) => {
    return http.get(`/api/member.Platform/featured`, params,{ loading: false });
}
export const getRewards = (params) => {
    return http.get(`/api/member.Team/rewards`, params,{ loading: false });
}
export const getTeamRewards = (params) => {
    return http.get(`/api/member.Team/team_rewards`, params,{ loading: false });
}
export const getOfferLink = (params) => {
    return http.get(`/api/member.Platform/copy`, params,{});
}
export const getWallLink = (params) => {
    return http.get(`/api/member.Platform/wall_copy`, params,{});
}
export const getQuota = (params) => {
    return http.get(`/api/member.Platform/quota`, params,{loading: false});
}
export const getRanking = (params) => {
    return http.get(`/api/member.Team/ranking`, params,{ loading: false });
}
export const getMyStatistics = (params) => {
    return http.get(`/api/member.Team/statistics`, params,{ loading: false });
}
export const getTeamStatistics = (params) => {
    return http.get(`/api/member.Team/team_statistics`, params,{ loading: false });
}
export const exportTeamRewards = (params,methods='post') => {
    if(methods == 'get') return http.get(`/api/member.Team/export_team_rewards`, params,{responseType: "blob"});
    return http.post(`/api/member.Team/export_team_rewards`, params);
}
export const getMarkDetailList = (params) => {
    return http.get(`/api/member.Platform/markdetaillist`, params,{ loading: false });
}
export const getMarkList = (params) => {
    return http.get(`/api/member.Platform/marklist`, params,{ loading: false });
}
export const markProject = (params) => {
    return http.post(`/api/member.Platform/mark`, params);
}