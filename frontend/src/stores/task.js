import { defineStore } from 'pinia'
import { fetch, store, update, destroy } from '@/api/task.js'

export const useTaskStore = defineStore('task', {
  actions: {
    fetchTasks (params) {
      return new Promise((resolve, reject) => {
        fetch(params)
          .then(res => {
            resolve(res)
          })
          .catch(error => {
            reject(error)
          })
      })
    },
    storeTasks (data) {
      return new Promise((resolve, reject) => {
        store(data)
          .then(res => {
            resolve(res)
          })
          .catch(error => {
            reject(error)
          })
      })
    },
    updateTask (id, data) {
      return new Promise((resolve, reject) => {
        update(id, data)
          .then(res => {
            resolve(res)
          })
          .catch(error => {
            reject(error)
          })
      })
    },
    deleteTask (id) {
      return new Promise((resolve, reject) => {
        destroy(id)
          .then(res => {
            resolve(res)
          })
          .catch(error => {
            reject(error)
          })
      })
    }
  }
})
