/**
 * Скрипт для генерации WebP версий изображений
 * 
 * Использование: npm run generate:webp
 */

import sharp from 'sharp';
import { glob } from 'glob';
import { join, dirname, extname } from 'path';
import { fileURLToPath } from 'url';
import fs from 'fs/promises';
import { existsSync } from 'fs';

const __dirname = dirname(fileURLToPath(import.meta.url));
const rootDir = join(__dirname, '..');

// Пути для поиска изображений
const imagePaths = [
  join(rootDir, 'images/**/*.{jpg,jpeg,png}'),
  join(rootDir, '**/images/**/*.{jpg,jpeg,png}'),
  join(rootDir, 'screenshot.png'),
];

// Настройки качества
const QUALITY = {
  jpeg: 85,
  png: 80,
  webp: 80,
};

async function generateWebP(inputPath) {
  try {
    const ext = extname(inputPath).toLowerCase();
    
    // Пропускаем уже существующие .webp файлы
    if (ext === '.webp') {
      console.log(`⏭️  Пропущено (уже WebP): ${inputPath}`);
      return;
    }
    
    const outputPath = inputPath.replace(/\.(jpg|jpeg|png)$/i, '.webp');
    
    // Проверяем, существует ли уже WebP версия
    if (existsSync(outputPath)) {
      console.log(`⏭️  Пропущено (WebP существует): ${inputPath}`);
      return;
    }
    
    // Создаем оптимизированную WebP версию
    await sharp(inputPath)
      .webp({ 
        quality: QUALITY.webp,
        effort: 6, // Максимальное сжатие (0-6)
      })
      .toFile(outputPath);
    
    // Получаем размеры оригинала и новой версии
    const originalStats = await fs.stat(inputPath);
    const webpStats = await fs.stat(outputPath);
    
    const savings = ((originalStats.size - webpStats.size) / originalStats.size * 100).toFixed(2);
    
    console.log(`✅ ${inputPath.split('/').pop()} → ${savings}% экономии (${Math.round(webpStats.size / 1024)}KB)`);
    
  } catch (error) {
    console.error(`❌ Ошибка при конвертации ${inputPath}:`, error.message);
  }
}

async function optimizeExistingWebP() {
  const webpFiles = await glob(join(rootDir, '**/*.webp'));
  
  for (const file of webpFiles) {
    try {
      const stats = await fs.stat(file);
      const originalSize = stats.size;
      
      // Пережимаем с лучшими настройками
      await sharp(file)
        .webp({ 
          quality: QUALITY.webp,
          effort: 6,
        })
        .toFile(file + '.tmp');
      
      const newStats = await fs.stat(file + '.tmp');
      const newSize = newStats.size;
      
      if (newSize < originalSize) {
        await fs.rename(file + '.tmp', file);
        const savings = ((originalSize - newSize) / originalSize * 100).toFixed(2);
        console.log(`🔄 Оптимизировано ${file.split('/').pop()}: ${savings}%`);
      } else {
        await fs.unlink(file + '.tmp');
      }
    } catch (error) {
      console.error(`❌ Ошибка при оптимизации ${file}:`, error.message);
    }
  }
}

async function main() {
  console.log('🚀 Генерация WebP версий изображений...\n');
  
  let totalFiles = 0;
  
  // Поиск и конвертация изображений
  for (const pattern of imagePaths) {
    const files = await glob(pattern, { ignore: ['**/node_modules/**', '**/dist/**'] });
    totalFiles += files.length;
    
    for (const file of files) {
      await generateWebP(file);
    }
  }
  
  console.log(`\n📊 Всего найдено: ${totalFiles} изображений`);
  
  // Оптимизация существующих WebP
  console.log('\n🔄 Оптимизация существующих WebP...\n');
  await optimizeExistingWebP();
  
  console.log('\n✅ Готово!');
}

main().catch(console.error);
