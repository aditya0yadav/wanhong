import request from '@/utils/request'
// 平台管理
const url = '/admin/platform.Platform/'
/**
 * 平台列表
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
 * 平台信息
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
 * 平台添加
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
 * 平台修改
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
 * 平台删除
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
 * 平台是否禁用
 * @param {array} data 请求数据
 */
export function disable(data) {
  return request({
    url: url + 'disable',
    method: 'post',
    data
  })
}
export function platlist(data) {
  return request({
    url: url + 'platlist',
    method: 'post',
    data
  })
}
export function platformAuth(data) {
  return request({
    url: url + 'platformAuth',
    method: 'post',
    data
  })
}
export function platformEditAuth(data) {
  return request({
    url: url + 'platformEditAuth',
    method: 'post',
    data
  })
}
export function platformAuthList(data) {
  return request({
    url: url + 'platformAuthList',
    method: 'post',
    data
  })
}
export function deleAuth(data) {
  return request({
    url: url + 'deleAuth',
    method: 'post',
    data
  })
}
export function currencyAllList(data) {
  return request({
    url: url + 'currencyAllList',
    method: 'post',
    data
  })
}
export function currencyList(data) {
  return request({
    url: url + 'currencyList',
    method: 'post',
    data
  })
}
export function currencyAdd(data) {
  return request({
    url: url + 'currencyAdd',
    method: 'post',
    data
  })
}
export function currencyEdit(data) {
  return request({
    url: url + 'currencyEdit',
    method: 'post',
    data
  })
}
export function currencyDele(data) {
  return request({
    url: url + 'currencyDele',
    method: 'post',
    data
  })
}
export function pull(data) {
  return request({
    url: url + 'pull',
    method: 'post',
    data
  })
}
export function platformStatistic(data) {
  return request({
    url: url + 'platformStatistic',
    method: 'post',
    data
  })
}