const fs = require('fs');
const JSZip = require('jszip');
const zip = new JSZip();
const { normalize } = require('path');

const tpl = fs.readFileSync('copyright');
const root = '../src';
const remove = [
    './sync',
    './data/config.ini'
];

(function parse (parent = '.') {
    try {
        fs.readdirSync(root+'/'+parent).forEach(entity => {
            if (!!~remove.indexOf(parent+'/'+entity)) return;
            const ePath = normalize(root+'/'+parent+'/'+entity);
            if (fs.lstatSync(ePath).isDirectory()) {
                parse(parent+'/'+entity);
            } else {
                let fileCont = fs.readFileSync(ePath);
                let fileTxt = fileCont.toString();
                if (!!~fileTxt.indexOf('@copy')) fileCont = fileTxt.split('@copy').join(tpl);
                zip.file(normalize(parent+'/'+entity).split('\\').join('/'), fileCont);
            }
        });
    } catch (err) {
        console.log(err.message);
    }
})();

zip.file('db.sql', fs.readFileSync('db.sql'));
zip.generateNodeStream({ streamFiles: true })
   .pipe(fs.createWriteStream(process.argv[2]+'.zip'));
