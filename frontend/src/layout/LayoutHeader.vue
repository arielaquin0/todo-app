<script setup>
import { useUserStore } from '@/stores/user'
import { useRouter } from 'vue-router'

const router = useRouter()

const logOut = () => {
  useUserStore().logout().then(() => {
    router.push('/login?redirect=/')
  })
}
</script>

<template>
  <q-header elevated class="bg-primary text-white" height-hint="98">
    <q-toolbar>
      <q-toolbar-title>
        <q-avatar>
          <img src="https://cdn.quasar.dev/logo-v2/svg/logo-mono-white.svg">
        </q-avatar>
        To-Do Application
      </q-toolbar-title>
      <q-btn unelevated round color="primary" icon="lock" @click="logOut">
        <q-tooltip anchor="center left" self="center right" :offset="[10, 10]">
          <strong>Logout</strong>
        </q-tooltip>
      </q-btn>
    </q-toolbar>
  </q-header>
</template>
