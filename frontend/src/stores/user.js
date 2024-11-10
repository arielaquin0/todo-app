import { defineStore } from 'pinia'
import { loginReq, logoutReq } from '@/api/user'
import { removeToken, setToken } from '@/utils/auth'

export const useUserStore = defineStore('user', {
  actions: {
    login (data) {
      return new Promise((resolve, reject) => {
        loginReq(data)
          .then(res => {
            setToken(res.data.token)
            resolve(null)
          })
          .catch(error => {
            reject(error)
          })
      })
    },
    logout () {
      return new Promise((resolve, reject) => {
        logoutReq()
          .then(async () => {
            await this.resetState()
            resolve(null)
          })
          .catch(error => {
            reject(error)
          })
      })
    },
    resetState () {
      return new Promise((resolve) => {
        removeToken()
        resolve(null)
      })
    }
  }
})
