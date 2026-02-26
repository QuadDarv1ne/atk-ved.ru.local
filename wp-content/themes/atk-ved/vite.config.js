import { defineConfig } from 'vite';
import { resolve, join } from 'path';
import { readdirSync, statSync } from 'fs';
import autoprefixer from 'autoprefixer';
import cssnano from 'cssnano';

// Получаем список всех CSS и JS файлов
const getFiles = (dir) => {
  const files = [];
  const items = readdirSync(dir, { withFileTypes: true });
  
  for (const item of items) {
    const fullPath = join(dir, item.name);
    if (item.isDirectory() && !item.name.startsWith('.')) {
      files.push(...getFiles(fullPath));
    } else if (item.isFile() && /\.(css|js)$/.test(item.name) && !item.name.includes('.min.')) {
      files.push(fullPath);
    }
  }
  
  return files;
};

const themeDir = resolve(__dirname);
const cssFiles = getFiles(join(themeDir, 'css'));
const jsFiles = getFiles(join(themeDir, 'js'));

// Создаём entry points для всех файлов
const input = {};

// CSS entry points
cssFiles.forEach(file => {
  const name = 'css/' + file.split('/').pop().replace('.css', '');
  input[name] = file;
});

// JS entry points
jsFiles.forEach(file => {
  const name = 'js/' + file.split('/').pop().replace('.js', '');
  input[name] = file;
});

export default defineConfig({
  base: './',
  
  build: {
    outDir: 'dist',
    assetsDir: '',
    manifest: true,
    sourcemap: process.env.NODE_ENV === 'development',
    minify: 'terser',
    terserOptions: {
      compress: {
        drop_console: false, // Оставляем console для отладки
        drop_debugger: true,
        passes: 2,
        pure_funcs: ['console.log'], // Удаляем только console.log
      },
      format: {
        comments: /^!|@preserve|@license|@cc_on/i, // Сохраняем важные комментарии
      },
    },
    cssMinify: 'lightningcss',
    rollupOptions: {
      input,
      output: {
        entryFileNames: '[name].min.js',
        chunkFileNames: 'js/[name]-[hash].min.js',
        assetFileNames: (assetInfo) => {
          if (assetInfo.name.endsWith('.css')) {
            return '[name].min.css';
          }
          return 'assets/[name]-[hash][extname]';
        },
      },
    },
    target: 'es2015',
    cssTarget: 'chrome61',
    
    // Сжатие gzip/brotli
    rollupOptions: {
      plugins: [
        // Плагин для генерации .gz и .br файлов
        {
          name: 'compression',
          writeBundle: async (options, bundle) => {
            const { gzip, brotliCompress } = await import('zlib');
            const { promisify } = await import('util');
            const { readdir, readFile, writeFile } = await import('fs/promises');
            const { join } = await import('path');
            
            const gzipAsync = promisify(gzip);
            const brotliCompressAsync = promisify(brotliCompress);
            
            const files = await readdir(options.dir || 'dist');
            
            for (const file of files) {
              if (file.endsWith('.js') || file.endsWith('.css')) {
                const filePath = join(options.dir || 'dist', file);
                const content = await readFile(filePath);
                
                // Gzip
                const gzipped = await gzipAsync(content, { level: 9 });
                await writeFile(filePath + '.gz', gzipped);
                
                // Brotli
                const brotlied = await brotliCompressAsync(content, {
                  params: { [11]: 11 } // Максимальное сжатие
                });
                await writeFile(filePath + '.br', brotlied);
              }
            }
          },
        },
      ],
    },
  },
  
  plugins: [],
  
  css: {
    postcss: {
      plugins: [
        autoprefixer({
          overrideBrowserslist: ['> 1%', 'last 2 versions', 'not dead', 'not ie 11'],
        }),
        cssnano({
          preset: [
            'advanced',
            {
              autoprefixer: false,
              convertValues: true,
              discardComments: true,
              discardUnused: true,
              mergeIdents: true,
              reduceIdents: true,
              svgo: true,
              zindex: true,
            },
          ],
        }),
      ],
    },
  },
  
  server: {
    port: 3000,
    strictPort: true,
    open: false,
    hmr: {
      host: 'localhost',
      port: 3000,
    },
  },
});
