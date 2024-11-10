import request from '@/utils/axiosReq'

export function fetch (params) {
  return request({
    url: '/tasks',
    method: 'get',
    params
  })
}

export function store (data) {
  return request({
    url: '/tasks',
    method: 'post',
    data
  })
}

export function update (id, data) {
  return request({
    url: `/tasks/${id}`,
    method: 'patch',
    data
  })
}

export function destroy (id) {
  return request({
    url: `/tasks/${id}`,
    method: 'delete'
  })
}
