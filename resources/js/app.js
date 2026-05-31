import { createCoaFormComponent } from './components/coaForm.js';
import { createCoaPageComponent } from './components/coaPage.js';
import { createCashReceiptFormComponent } from './components/cashReceiptForm.js';
import { createCashReceiptPageComponent } from './components/cashReceiptPage.js';
import { createCashDisbursementFormComponent } from './components/cashDisbursementForm.js';
import { createCashDisbursementPageComponent } from './components/cashDisbursementPage.js';

// Export untuk penggunaan di Blade views dengan @vite
window.createCoaFormComponent = createCoaFormComponent;
window.createCoaPageComponent = createCoaPageComponent;
window.createCashReceiptFormComponent = createCashReceiptFormComponent;
window.createCashReceiptPageComponent = createCashReceiptPageComponent;
window.createCashDisbursementFormComponent = createCashDisbursementFormComponent;
window.createCashDisbursementPageComponent = createCashDisbursementPageComponent;
