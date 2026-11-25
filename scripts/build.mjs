import { build } from 'esbuild';
import { statSync } from 'node:fs';

const files = ['public/assets/css/app.css', 'public/assets/css/admin.css', 'public/assets/js/app.js', 'public/assets/js/admin.js'];

for (const file of files) {
  const out = file.replace(/\.(css|js)$/, '.min.$1');
  await build({
    entryPoints: [file],
    outfile: out,
    minify: true,
    bundle: false,
    target: ['es2020', 'chrome100', 'firefox100', 'safari15'],
    legalComments: 'none',
    logLevel: 'warning',
  });
  const before = statSync(file).size;
  const after = statSync(out).size;
  console.log(`${out.padEnd(40)} ${(before / 1024).toFixed(1)} KB -> ${(after / 1024).toFixed(1)} KB`);
}
