import request from '@/utils/request'
// 团队管理
const url = '/admin/team.Team/'
/**
 * 团队列表
 * @param {array} params 请求参数
 */
export function list(params) {
  return request({
    url: url + 'list',
    method: 'get',
    params: params
  })
}
/**
 * 团队信息
 * @param {array} params 请求参数
 */
export function info(params) {
  return request({
    url: url + 'info',
    method: 'get',
    params: params
  })
}
/**
 * 团队添加
 * @param {array} data 请求数据
 */
export function add(data) {
  return request({
    url: url + 'add',
    method: 'post',
    data
  })
}
/**
 * 团队修改
 * @param {array} data 请求数据
 */
export function edit(data) {
  return request({
    url: url + 'edit',
    method: 'post',
    data
  })
}
/**
 * 团队删除
 * @param {array} data 请求数据
 */
export function dele(data) {
  return request({
    url: url + 'dele',
    method: 'post',
    data
  })
}
/**
 * 团队是否禁用
 * @param {array} data 请求数据
 */
export function disable(data) {
  return request({
    url: url + 'disable',
    method: 'post',
    data
  })
}
export function teamAuth(data) {
  return request({
    url: url + 'teamAuth',
    method: 'post',
    data
  })
}
export function teamUser(data) {
  return request({
    url: url + 'teamUser',
    method: 'post',
    data
  })
}
export function teamlist(data) {
  return request({
    url: url + 'teamlist',
    method: 'post',
    data
  })
}
export function statistic(data) {
  return request({
    url: url + 'statistic',
    method: 'post',
    data
  })
}