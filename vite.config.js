import { defineConfig } from "vite";
import symfonyPlugin from "vite-plugin-symfony";
import path from 'path'; // <-- Import manquant !
export default defineConfig({
  plugins: [
    symfonyPlugin({
      refresh: true // Active le rechargement automatique
    }),
  ],
  resolve: {
    alias: {
      // Alias pour Bootstrap et autres libs
      '~bootstrap': path.resolve(__dirname, 'node_modules/bootstrap'),
      '~material-dashboard': path.resolve(__dirname, 'node_modules/material-dashboard'),
      '~perfect-scrollbar': path.resolve(__dirname, 'node_modules/perfect-scrollbar'),
      '~chart.js': path.resolve(__dirname, 'node_modules/chart.js'),
      '~countup.js': path.resolve(__dirname, 'node_modules/countup.js'),
      '~material-icons': path.resolve(__dirname, 'node_modules/material-icons'),
      '~jquery': path.resolve(__dirname, 'node_modules/jquery'),
      '~popperjs': path.resolve(__dirname, 'node_modules/@popperjs/core'),
    },
},
  build: {
    // Add minification and optimization options
    minify: 'terser',
    terserOptions: {
      compress: {
        drop_console: true,
        drop_debugger: true
      }
      
    },
    // Add CSS optimization
    cssMinify: true,
    // Improve chunk size
    chunkSizeWarningLimit: 1000,
    // Split vendor chunks for better caching
    rollupOptions: {
      input: {
        app: "./assets/app.js",
      },
      output: {
        manualChunks: {
          vendor: [
            // Add your main dependencies here for better caching
            // Example: 'react', 'vue', etc.
          ]
        },
        assetFileNames: (assetInfo) => {
          if (/\.(jpg|jpeg|png|gif|svg|webp)$/.test(assetInfo.name)) {
            return 'assets/images/[name]-[hash][extname]';
          }
          return 'assets/[name]-[hash][extname]';
        },
        chunkFileNames: 'assets/js/[name]-[hash].js',
        entryFileNames: 'assets/js/[name]-[hash].js',
      }
    },
    manifest: true,
    emptyOutDir: true,
    copyPublicDir: false,
    // Enable source maps for production only when needed
    sourcemap: false
  },
  // Improve dev server performance
  server: {
    hmr: true,
    host: true,
    port: 3000,
    overlay: false // Désactive l'overlay d'erreur HMR si nécessaire
  },
  // Better static asset handling - fix potential issue with limited publicDir
  publicDir: 'public',
  // Add proper public base path
  base: '/build/',
  // Add optimized asset handling
  assetsInclude: ['**/*.jpg', '**/*.png', '**/*.svg', '**/*.gif', '**/*.webp', '**/*.woff', '**/*.woff2']
});