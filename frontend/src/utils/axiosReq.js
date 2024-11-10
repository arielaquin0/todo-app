import axios from 'axios'
import { getToken } from '@/utils/auth'

const service = axios.create()

service.interceptors.request.use(
  (request) => {
    const token = getToken()

    if (token) request.headers.token = token
    if ('get'.includes(request.method?.toLowerCase()) && !request.params) request.params = request.data

    return request
  },
  (err) => {
    Promise.reject(err)
  }
)

service.interceptors.response.use(
  (res) => {
    console.log('response: ', res)
    const code = res.status
    const successCode = '0,200,20000'
    if (successCode.includes(code.toString())) {
      return res.data
    } else {
      return Promise.reject(res.data)
    }
  },
  (err) => {
    return Promise.reject(err)
  }
)

export default function axiosReq (config) {
  return service({
    baseURL: import.meta.env.VITE_APP_BASE_URL,
    timeout: 15 * 1000,
    ...config
  })
}
