const fs = require('fs');
const path = require('path');

function walkDir(dir, callback) {
  fs.readdirSync(dir).forEach(f => {
    let dirPath = path.join(dir, f);
    let isDirectory = fs.statSync(dirPath).isDirectory();
    if (isDirectory) {
      walkDir(dirPath, callback);
    } else if (dirPath.endsWith('.vue')) {
      callback(path.join(dirPath));
    }
  });
}

const safeFiles = [
  path.join(__dirname, 'app', 'layouts', 'default.vue'),
  path.join(__dirname, 'app', 'pages', 'index.vue'),
  path.join(__dirname, 'app', 'pages', 'rooms', 'index.vue'),
  path.join(__dirname, 'app', 'pages', 'rooms', '[id].vue'),
];

function processFile(filePath) {
  if (safeFiles.includes(filePath)) {
    return; // Skip the files we already fixed manually
  }

  let content = fs.readFileSync(filePath, 'utf8');
  let original = content;

  // Backgrounds
  content = content.replace(/bg-dark-950/g, 'bg-slate-50');
  content = content.replace(/bg-dark-900/g, 'bg-white');
  content = content.replace(/bg-dark-800/g, 'bg-gray-100');
  content = content.replace(/bg-dark-[0-9]+/g, 'bg-gray-200');

  // Translucent backgrounds
  content = content.replace(/bg-white\/5/g, 'bg-blue-50');
  content = content.replace(/bg-white\/10/g, 'bg-blue-100');
  content = content.replace(/bg-white\/\[0\.02\]/g, 'bg-white');

  // Borders
  content = content.replace(/border-white\/5/g, 'border-blue-100');
  content = content.replace(/border-white\/10/g, 'border-blue-200');

  // Texts
  content = content.replace(/text-gray-400/g, 'text-gray-600');
  content = content.replace(/text-gray-500/g, 'text-gray-700');
  content = content.replace(/text-white/g, 'text-blue-900');

  if (content !== original) {
    fs.writeFileSync(filePath, content, 'utf8');
    console.log(`Updated ${filePath}`);
  }
}

walkDir(path.join(__dirname, 'app'), processFile);
console.log('Recolor script completed.');
