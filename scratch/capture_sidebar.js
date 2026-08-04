import puppeteer from 'puppeteer';
import path from 'path';
import fs from 'fs';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const outputDir = path.join(__dirname, '../public/manual-book/images');
const edgePath = 'C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe';

async function captureSidebar() {
    console.log('Capturing Sidebar screenshot...');
    const browser = await puppeteer.launch({
        executablePath: edgePath,
        headless: 'new',
        defaultViewport: { width: 1366, height: 900, deviceScaleFactor: 2 }
    });
    const page = await browser.newPage();
    await page.goto('http://127.0.0.1:8000/login', { waitUntil: 'networkidle2' });
    await page.type('input[name="email"]', 'admin@pmi-nganjuk.or.id');
    await page.type('input[name="password"]', 'password');
    await Promise.all([
        page.click('button[type="submit"]'),
        page.waitForNavigation({ waitUntil: 'networkidle2' })
    ]);

    await new Promise(r => setTimeout(r, 1000));
    const sidebarEl = await page.$('aside');
    if (sidebarEl) {
        await sidebarEl.screenshot({ path: path.join(outputDir, 'ss_sidebar.png') });
        console.log('Sidebar screenshot saved successfully as ss_sidebar.png');
    } else {
        console.error('Sidebar element not found');
    }

    await browser.close();
}

captureSidebar().catch(err => {
    console.error(err);
    process.exit(1);
});
