import request from '@/utils/request'

const url = '/admin/platform.PersonaData/'

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