import Alpine from 'alpinejs';

import { createCoaFormComponent } from './components/coaForm.js';
import { createCoaPageComponent } from './components/coaPage.js';
import { createCashReceiptFormComponent } from './components/cashReceiptForm.js';
import { createCashReceiptPageComponent } from './components/cashReceiptPage.js';
import { createCashDisbursementFormComponent } from './components/cashDisbursementForm.js';
import { createCashDisbursementPageComponent } from './components/cashDisbursementPage.js';
import { createAdjustingEntryPageComponent } from './components/adjustingEntryForm.js';

window.createCoaFormComponent = createCoaFormComponent;
window.createCoaPageComponent = createCoaPageComponent;
window.createCashReceiptFormComponent = createCashReceiptFormComponent;
window.createCashReceiptPageComponent = createCashReceiptPageComponent;
window.createCashDisbursementFormComponent = createCashDisbursementFormComponent;
window.createCashDisbursementPageComponent = createCashDisbursementPageComponent;
window.createAdjustingEntryPageComponent = createAdjustingEntryPageComponent;

window.Alpine = Alpine;
Alpine.start();
