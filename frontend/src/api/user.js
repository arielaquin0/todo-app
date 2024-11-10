import request from '@/utils/axiosReq'

export function loginReq (data) {
  return request({
    url: '/login',
    method: 'post',
    data
  })
}

export function logoutReq (data) {
  return request({
    url: '/logout',
    method: 'post'
  })
}
