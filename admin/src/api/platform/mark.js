import request from '@/utils/request'
// 平台管理
const url = '/admin/platform.Mark/'
export function MarkList(data) {
    return request({
      url: url + 'list',
      method: 'post',
      data
    })
  }
  export function MarkAdd(data) {
    return request({
      url: url + 'add',
      method: 'post',
      data
    })
  }
  export function MarkEdit(data) {
    return request({
      url: url + 'edit',
      method: 'post',
      data
    })
  }
  export function MarkDele(data) {
    return request({
      url: url + 'dele',
      method: 'post',
      data
    })
  }
  export function MarkDetailList(data) {
    return request({
      url: url + 'detailList',
      method: 'post',
      data
    })
  }
  export function MarkDetailDele(data) {
    return request({
      url: url + 'detailDele',
      method: 'post',
      data
    })
  }