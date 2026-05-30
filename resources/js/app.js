import { createCoaFormComponent } from './components/coaForm.js';
import { createCoaPageComponent } from './components/coaPage.js';
import { createCashReceiptFormComponent } from './components/cashReceiptForm.js';
import { createCashReceiptPageComponent } from './components/cashReceiptPage.js';

// Export untuk penggunaan di Blade views dengan @vite
window.createCoaFormComponent = createCoaFormComponent;
window.createCoaPageComponent = createCoaPageComponent;
window.createCashReceiptFormComponent = createCashReceiptFormComponent;
window.createCashReceiptPageComponent = createCashReceiptPageComponent;
