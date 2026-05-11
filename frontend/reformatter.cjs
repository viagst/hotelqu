const fs = require('fs');
const path = require('path');

function walkDir(dir, callback) {
  fs.readdirSync(dir).forEach(f => {
    let dirPath = path.join(dir, f);
    let isDirectory = fs.statSync(dirPath).isDirectory();
    if (isDirectory) {
      walkDir(dirPath, callback);
    } else if (dirPath.endsWith('.vue')) {
      callback(dirPath);
    }
  });
}

function reformatVue(filePath) {
  let content = fs.readFileSync(filePath, 'utf8');
  
  // Skip if file already has newlines (multi-line)
  if (content.split('\n').length > 5) return;
  
  // Add newlines before major tags
  content = content.replace(/<template>/g, '\n<template>\n');
  content = content.replace(/<\/template>/g, '\n</template>\n');
  content = content.replace(/<script/g, '\n<script');
  content = content.replace(/<\/script>/g, '\n</script>\n');
  
  // Add newlines after closing divs at the template level
  content = content.replace(/> </g, '>\n<');
  
  // Clean up excessive blank lines
  content = content.replace(/\n{3,}/g, '\n\n');
  content = content.trim() + '\n';
  
  fs.writeFileSync(filePath, content, 'utf8');
  console.log(`Reformatted: ${filePath}`);
}

walkDir(path.join(__dirname, 'app'), reformatVue);
console.log('Reformat complete.');
