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

function processFile(filePath) {
  let content = fs.readFileSync(filePath, 'utf8');
  let original = content;

  // Replace primary gradients with flat solid colors
  content = content.replace(/bg-gradient-to-[a-z]+\s+from-primary-[0-9]+\s+to-primary-[0-9]+/g, 'bg-primary-500');
  content = content.replace(/bg-gradient-to-[a-z]+\s+from-dark-[0-9]+\s+to-dark-[0-9]+/g, 'bg-dark-900 border border-white/5');
  
  // Replace hover gradients
  content = content.replace(/hover:from-primary-[0-9]+\s+hover:to-primary-[0-9]+/g, 'hover:bg-primary-600 border border-primary-500');
  
  content = content.replace(/shadow-lg\s+shadow-primary-500\/[0-9]+/g, '');
  content = content.replace(/shadow-primary-500\/[0-9]+/g, '');
  
  // Replace bubbly border styles
  content = content.replace(/rounded-2xl/g, 'rounded-none'); // Brutalist/Minimalist sharp corners
  content = content.replace(/rounded-xl/g, 'rounded-sm');
  content = content.replace(/rounded-lg/g, 'rounded-sm');
  content = content.replace(/rounded-full/g, 'rounded-none'); // For tags/status, sharp fits minimalist
  
  // Clean up excessive classes
  content = content.replace(/\s+/g, ' ');

  if (content !== original) {
    fs.writeFileSync(filePath, content, 'utf8');
    console.log(`Updated ${filePath}`);
  }
}

walkDir(path.join(__dirname, 'app'), processFile);
console.log('Restyling complete.');
