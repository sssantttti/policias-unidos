const sizeOf = require('image-size');
const fs = require('fs');
const files = fs.readdirSync('evento-30-abril');
const v = [];
const h = [];
files.forEach(f => {
    if(!f.endsWith('.webp')) return;
    try {
        const dim = sizeOf('evento-30-abril/' + f);
        if(dim.height > dim.width) v.push(f);
        else h.push(f);
    } catch(e) {
        console.error(f, e);
    }
});
console.log('Vertical:', v.length > 0 ? v : 'None');
console.log('Horizontal:', h.length > 0 ? h : 'None');
