import request from '@/utils/request'
// 项目管理
const url = '/admin/project.Project/'
/**
 * 项目列表
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
 * 项目信息
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
 * 项目添加
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
 * 项目修改
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
 * 项目删除
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
 * 项目禁用
 * @param {array} data 请求数据
 */
export function disable(data) {
  return request({
    url: url + 'disable',
    method: 'post',
    data
  })
}
export function copy(data) {
  return request({
    url: url + 'copy',
    method: 'post',
    data
  })
}
export function restore(data) {
  return request({
    url: url + 'restore',
    method: 'post',
    data
  })
}
export function clearReclycle(data) {
  return request({
    url: url + 'clearReclycle',
    method: 'post',
    data
  })
}
export function disall(data) {
  return request({
    url: url + 'disall',
    method: 'post',
    data
  })
}
export function delall(data) {
  return request({
    url: url + 'delall',
    method: 'post',
    data
  })
}
