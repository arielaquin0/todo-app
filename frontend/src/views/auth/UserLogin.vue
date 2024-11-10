<template>
  <div class="fullscreen bg-blue text-white text-center q-pa-md flex flex-center">
    <div class="centered">
      <div class="q-pa-md" style="max-width: 400px">

        <q-form @submit="onSubmit" class="q-gutter-md">
          <q-input
              filled
              v-model="username"
              label="Username"
              lazy-rules
              :rules="[ val => val !== null && val !== '' || 'Please input your username']"
          />

          <q-input
              filled
              type="password"
              v-model="password"
              label="Password"
              lazy-rules
              :rules="[ val => val !== null && val !== '' || 'Please input your password' ]"
          />

          <div>
            <q-btn label="Login" type="submit" color="primary" style="width: 100%" />
          </div>
        </q-form>

      </div>
    </div>
  </div>
</template>

<script>
import { useQuasar } from 'quasar'
import { ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useUserStore } from '@/stores/user'

export default {
  setup () {
    const $q = useQuasar()
    const router = useRouter()
    const route = useRoute()

    const username = ref('admin')
    const password = ref('P@ssw0rd')

    return {
      username,
      password,
      route,

      onSubmit () {
        useUserStore()
          .login({
            username: username.value,
            password: password.value
          })
          .then(() => {
            router.push({ path: '/' })
            location.reload()
          })
          .catch(error => {
            $q.notify({
              type: 'negative',
              position: 'top-right',
              message: error?.response?.data?.message || 'Something went wrong! Please try again later'
            })
          })
      },

      onReset () {
        username.value = null
        password.value = null
      }
    }
  }
}
</script>

<style scoped>
.centered {
  background-color: white;
  padding: 40px;
  border-radius: 8px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  width: 400px;
}
</style>
