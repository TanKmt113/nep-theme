import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'

// NẾP — Sage 11 uses laravel-vite-plugin (same as Sage default).
// `npm run dev` for HMR, `npm run build` for production assets in public/build.
export default defineConfig({
  base: '/wp-content/themes/nep-theme/public/build/',
  plugins: [
    laravel({
      input: [
        'resources/css/app.css',
        'resources/js/app.js',
      ],
      refresh: true,
    }),
  ],
})
