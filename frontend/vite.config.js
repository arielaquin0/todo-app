import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vueJsx from '@vitejs/plugin-vue-jsx'
import { quasar, transformAssetUrls } from '@quasar/vite-plugin'
export default defineConfig({
  server: {
    hmr: { overlay: false },
    port: 8000,
    open: false
  },
  plugins: [
    vue({
      template: { transformAssetUrls }
    }),
    vueJsx(),
    quasar()
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    }
  }
})
