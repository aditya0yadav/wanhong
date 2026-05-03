import request from '@/utils/request'

const url = '/admin/platform.Persona/'

export function list(params) {
  return request({
    url: url + 'list',
    method: 'get',
    params: params
  })
}
export function info(params) {
  return request({
    url: url + 'info',
    method: 'get',
    params: params
  })
}
export function add(data) {
  return request({
    url: url + 'add',
    method: 'post',
    data
  })
}
export function edit(data) {
  return request({
    url: url + 'edit',
    method: 'post',
    data
  })
}
export function dele(data) {
  return request({
    url: url + 'dele',
    method: 'post',
    data
  })
}
export function dataList(params) {
  return request({
    url: url + 'dataList',
    method: 'get',
    params: params
  })
}
export function dataInfo(params) {
  return request({
    url: url + 'dataInfo',
    method: 'get',
    params: params
  })
}
export function dataAdd(data) {
  return request({
    url: url + 'dataAdd',
    method: 'post',
    data
  })
}
export function dataEdit(data) {
  return request({
    url: url + 'dataEdit',
    method: 'post',
    data
  })
}
export function dataDele(data) {
  return request({
    url: url + 'dataDele',
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