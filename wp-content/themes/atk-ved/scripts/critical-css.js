/**
 * Скрипт для генерации критического CSS
 * 
 * Использование: npm run critical
 */

import critical from 'critical';
import { join, dirname } from 'path';
import { fileURLToPath } from 'url';
import fs from 'fs/promises';
import { existsSync } from 'fs';

const __dirname = dirname(fileURLToPath(import.meta.url));
const rootDir = join(__dirname, '..');

// Конфигурация
const CONFIG = {
  // URL для генерации критического CSS
  urls: [
    'http://localhost:8080/',
    'http://localhost:8080/calculator',
    'http://localhost:8080/tracking',
  ],
  
  // Настройки генерации
  width: 1920,
  height: 1080,
  penthouse: {
    timeout: 30000,
    renderWaitTime: 2000,
    blockJSRequests: false,
  },
  
  // Выходная директория
  outputDir: join(rootDir, 'css'),
};

async function generateCriticalCSS() {
  console.log('🚀 Генерация критического CSS...\n');
  
  for (const url of CONFIG.urls) {
    try {
      const pageName = url === 'http://localhost:8080/' 
        ? 'critical' 
        : url.split('/').pop();
      
      const outputPath = join(CONFIG.outputDir, `critical-${pageName}.css`);
      
      console.log(`📄 Обработка: ${url}`);
      
      await critical.generate({
        inline: false,
        base: rootDir,
        src: url,
        dest: outputPath,
        path: CONFIG.outputDir,
        width: CONFIG.width,
        height: CONFIG.height,
        penthouse: CONFIG.penthouse,
        ignore: {
          atrule: ['@keyframes', '@font-face'],
          rule: [/^@media/, /^\.wp-/],
        },
      });
      
      console.log(`✅ Критический CSS сохранён: ${outputPath}\n`);
      
    } catch (error) {
      console.error(`❌ Ошибка при генерации для ${url}:`, error.message);
    }
  }
  
  console.log('✅ Готово!');
}

// Альтернативный метод - извлечение критического CSS из существующих файлов
async function extractCriticalCSS() {
  console.log('🚀 Извлечение критического CSS из существующих файлов...\n');
  
  const cssFiles = [
    'modern-design.css',
    'design-tokens.css',
    'landing-sections.css',
    'hero-counters.css',
  ];
  
  const criticalRules = [];
  
  for (const file of cssFiles) {
    const filePath = join(rootDir, 'css', file);
    
    if (!existsSync(filePath)) {
      continue;
    }
    
    const content = await fs.readFile(filePath, 'utf8');
    
    // Извлекаем критические правила (базовые стили, переменные, reset)
    const rules = content.match(/(:root|[\w-]+)\s*\{[^}]*\}/g) || [];
    
    for (const rule of rules) {
      // Добавляем только критические селекторы
      if (rule.match(/:root|body|html|\.container|\.site-header|\.hero-section/)) {
        criticalRules.push(rule);
      }
    }
  }
  
  // Формируем итоговый критический CSS
  const criticalCSS = criticalRules.join('\n\n');
  const outputPath = join(rootDir, 'css', 'critical.css');
  
  await fs.writeFile(outputPath, criticalCSS);
  
  console.log(`✅ Критический CSS сохранён: ${outputPath}`);
  console.log(`📊 Размер: ${Math.round(Buffer.byteLength(criticalCSS, 'utf8') / 1024)}KB`);
}

async function main() {
  // Пробуем сгенерировать из URL, если не получится - извлекаем из файлов
  try {
    await generateCriticalCSS();
  } catch (error) {
    console.log('⚠️  Не удалось сгенерировать из URL, используем извлечение...\n');
    await extractCriticalCSS();
  }
}

main().catch(console.error);
