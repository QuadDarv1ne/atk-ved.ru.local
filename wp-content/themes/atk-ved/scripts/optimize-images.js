/**
 * Скрипт для оптимизации изображений
 * 
 * Использование: npm run optimize:images
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
];

// Настройки качества
const QUALITY = {
  jpeg: 85,
  png: 80,
};

// Максимальные размеры
const MAX_SIZE = {
  width: 1920,
  height: 1080,
};

async function optimizeImage(inputPath) {
  try {
    const ext = extname(inputPath).toLowerCase();
    const isJpeg = ext.match(/\.(jpg|jpeg)$/i);
    const isPng = ext === '.png';
    
    if (!isJpeg && !isPng) {
      return;
    }
    
    // Получаем метаданные оригинала
    const metadata = await sharp(inputPath).metadata();
    const originalStats = await fs.stat(inputPath);
    
    // Определяем, нужно ли изменять размер
    const needsResize = metadata.width > MAX_SIZE.width || metadata.height > MAX_SIZE.height;
    
    let pipeline = sharp(inputPath);
    
    // Изменяем размер если нужно
    if (needsResize) {
      pipeline = pipeline.resize(MAX_SIZE.width, MAX_SIZE.height, {
        fit: 'inside',
        withoutEnlargement: true,
      });
    }
    
    // Оптимизируем в зависимости от типа
    if (isJpeg) {
      pipeline = pipeline.jpeg({ 
        quality: QUALITY.jpeg,
        progressive: true,
        mozjpeg: true, // Используем mozjpeg для лучшего сжатия
      });
    } else if (isPng) {
      pipeline = pipeline.png({ 
        quality: QUALITY.png,
        compressionLevel: 9,
        palette: true, // Конвертируем в палитру если возможно
      });
    }
    
    // Сохраняем оптимизированную версию
    const tempPath = inputPath + '.tmp';
    await pipeline.toFile(tempPath);
    
    const optimizedStats = await fs.stat(tempPath);
    const savings = ((originalStats.size - optimizedStats.size) / originalStats.size * 100).toFixed(2);
    
    // Заменяем оригинал оптимизированной версией
    await fs.rename(tempPath, inputPath);
    
    console.log(
      `✅ ${inputPath.split('/').pop()}: ` +
      `${Math.round(originalStats.size / 1024)}KB → ${Math.round(optimizedStats.size / 1024)}KB ` +
      `(${savings}% экономии)` +
      (needsResize ? ` [${metadata.width}x${metadata.height}]` : '')
    );
    
  } catch (error) {
    console.error(`❌ Ошибка при оптимизации ${inputPath}:`, error.message);
  }
}

async function main() {
  console.log('🚀 Оптимизация изображений...\n');
  
  let totalFiles = 0;
  let totalSavings = 0;
  
  // Поиск и оптимизация изображений
  for (const pattern of imagePaths) {
    const files = await glob(pattern, { 
      ignore: ['**/node_modules/**', '**/dist/**', '**/*.webp'] 
    });
    totalFiles += files.length;
    
    for (const file of files) {
      const before = (await fs.stat(file)).size;
      await optimizeImage(file);
      const after = (await fs.stat(file)).size;
      totalSavings += (before - after);
    }
  }
  
  console.log(`\n📊 Всего обработано: ${totalFiles} изображений`);
  console.log(`💾 Общая экономия: ${Math.round(totalSavings / 1024)}KB`);
  console.log('\n✅ Готово!');
}

main().catch(console.error);
