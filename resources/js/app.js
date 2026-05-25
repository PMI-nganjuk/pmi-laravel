import { createCoaFormComponent } from './components/coaForm.js';
import { createCoaPageComponent } from './components/coaPage.js';

// Export untuk penggunaan di Blade views dengan @vite
window.createCoaFormComponent = createCoaFormComponent;
window.createCoaPageComponent = createCoaPageComponent;
