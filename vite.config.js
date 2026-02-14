import { resolve } from 'path'
import { defineConfig } from 'vite'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
  plugins: [tailwindcss()],
  build: {
    rollupOptions: {
      input: {
        main: resolve(__dirname, 'index.html'),
        design1: resolve(__dirname, '1/index.html'),
        design2: resolve(__dirname, '2/index.html'),
        design3: resolve(__dirname, '3/index.html'),
        design4: resolve(__dirname, '4/index.html'),
        design5: resolve(__dirname, '5/index.html'),
        design6: resolve(__dirname, '6/index.html'),
        design62: resolve(__dirname, '6-2/index.html'),
        design7: resolve(__dirname, '7/index.html'),
        design8: resolve(__dirname, '8/index.html'),
        design9: resolve(__dirname, '9/index.html'),
      },
    },
  },
})
