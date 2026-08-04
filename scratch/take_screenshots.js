import puppeteer from 'puppeteer';
import path from 'path';
import fs from 'fs';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const outputDir = path.join(__dirname, '../public/manual-book/images');
if (!fs.existsSync(outputDir)) {
    fs.mkdirSync(outputDir, { recursive: true });
}

const edgePath = 'C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe';

async function capture() {
    console.log('Launching browser with 16:9 aspect ratio (1600x900)...');
    const browser = await puppeteer.launch({
        executablePath: edgePath,
        headless: 'new',
        defaultViewport: { width: 1600, height: 900, deviceScaleFactor: 2 }
    });
    const page = await browser.newPage();

    // 1. Login Page
    console.log('Capturing Login Page...');
    await page.goto('http://127.0.0.1:8000/login', { waitUntil: 'networkidle2' });
    await page.screenshot({ path: path.join(outputDir, 'ss_login.png'), fullPage: false });

    // Submit Login Form
    console.log('Logging in as Admin...');
    await page.type('input[name="email"]', 'admin@pmi-nganjuk.or.id');
    await page.type('input[name="password"]', 'password');
    await Promise.all([
        page.click('button[type="submit"]'),
        page.waitForNavigation({ waitUntil: 'networkidle2' })
    ]);

    const pagesToCapture = [
        { url: 'http://127.0.0.1:8000/dashboard', filename: 'ss_dashboard.png', title: 'Dashboard' },
        { url: 'http://127.0.0.1:8000/receipts', filename: 'ss_receipts.png', title: 'Penerimaan Kas' },
        { url: 'http://127.0.0.1:8000/disbursements', filename: 'ss_disbursements.png', title: 'Pengeluaran Kas' },
        { url: 'http://127.0.0.1:8000/adjusting-entries', filename: 'ss_adjusting_entries.png', title: 'Jurnal Penyesuaian' },
        { url: 'http://127.0.0.1:8000/coa', filename: 'ss_coa.png', title: 'Chart of Accounts' },
        { url: 'http://127.0.0.1:8000/general-ledger', filename: 'ss_general_ledger.png', title: 'Buku Besar' },
        { url: 'http://127.0.0.1:8000/profit-loss', filename: 'ss_profit_loss.png', title: 'Laporan Laba Rugi' },
        { url: 'http://127.0.0.1:8000/balance-sheet', filename: 'ss_balance_sheet.png', title: 'Laporan Posisi Keuangan' },
        { url: 'http://127.0.0.1:8000/cash-flow', filename: 'ss_cash_flow.png', title: 'Laporan Arus Kas' },
        { url: 'http://127.0.0.1:8000/analysis-notes', filename: 'ss_analysis_notes.png', title: 'Laporan Perubahan Aset Netto' },
        { url: 'http://127.0.0.1:8000/dashboard/users', filename: 'ss_users.png', title: 'Manajemen Akun' },
        { url: 'http://127.0.0.1:8000/dashboard/settings', filename: 'ss_settings.png', title: 'Profil Organisasi' }
    ];

    for (const target of pagesToCapture) {
        console.log(`Capturing ${target.title}...`);
        await page.goto(target.url, { waitUntil: 'networkidle2' });
        await new Promise(r => setTimeout(r, 800));
        await page.screenshot({ path: path.join(outputDir, target.filename), fullPage: false });
    }

    // Capture Sidebar as well
    const sidebarEl = await page.$('aside');
    if (sidebarEl) {
        await sidebarEl.screenshot({ path: path.join(outputDir, 'ss_sidebar.png') });
    }

    await browser.close();
    console.log('All 16:9 screenshots captured successfully!');
}

capture().catch(err => {
    console.error('Error capturing screenshots:', err);
    process.exit(1);
});
