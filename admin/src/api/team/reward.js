import request from '@/utils/request'
// 业绩管理
const url = '/admin/team.Reward/'
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
/**
 * 业绩导入
 * @param {array} data 请求数据
 * @param {string} method get下载导入模板，post上传导入文件
 */
export function imports(data = {}, method = 'post') {
    if (method == 'get') {
      return request({
        url: url + 'import',
        method: 'get',
        params: data,
        responseType: 'blob'
      })
    }
    return import.meta.env.VITE_APP_BASE_URL + url + 'import'
  }
  /**
   * 业绩导出
   * @param {array} data 请求数据
   * @param {string} method get下载导出文件，post提交导出
   */
  export function exports(data, method = 'post') {
    if (method == 'get') {
      return request({
        url: url + 'export',
        method: 'get',
        params: data,
        responseType: 'blob'
      })
    }
    return request({
      url: url + 'export',
      method: 'post',
      data
    })
  }
  export function batchAudit(data) {
    return request({
      url: url + 'batchAudit',
      method: 'post',
      data
    })
  }
  export function uuidAudit(data) {
    return request({
      url: url + 'uuidAudit',
      method: 'post',
      data
    })
  }