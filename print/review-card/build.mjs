// Renders review-card.html to a print PDF (A6 + 3 mm bleed) and two PNG previews.
// Needs Chrome and playwright-core: CHROME=/path/to/chrome node build.mjs
import { chromium } from 'playwright-core';
import { fileURLToPath } from 'node:url';
import path from 'node:path';

const here = path.dirname(fileURLToPath(import.meta.url));
const url = 'file://' + path.join(here, 'review-card.html');
const browser = await chromium.launch({ executablePath: process.env.CHROME || '/usr/local/bin/google-chrome', args: ['--no-sandbox'] });
const page = await browser.newPage();
await page.goto(url, { waitUntil: 'networkidle' });
await page.emulateMedia({ media: 'print' });
await page.pdf({ path: path.join(here, 'review-card.pdf'), width: '111mm', height: '154mm', printBackground: true, preferCSSPageSize: true });

const shot = await browser.newPage({ viewport: { width: 420, height: 582 }, deviceScaleFactor: 3 });
await shot.goto(url, { waitUntil: 'networkidle' });
await shot.emulateMedia({ media: 'print' });
for (const [i, name] of [[0, 'front'], [1, 'back']]) {
  const box = await shot.locator('.page').nth(i).boundingBox();
  await shot.screenshot({ path: path.join(here, `review-card-${name}.png`), fullPage: true, clip: { x: box.x, y: box.y, width: Math.floor(box.width), height: Math.floor(box.height) } });
}
await browser.close();
