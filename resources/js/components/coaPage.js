/**
 * COA Page State Component
 * Manages page-level state including form visibility, edit mode, and event handling
 */

export function createCoaPageComponent(config) {
    return {
        sidebarOpen: false,
        showForm: Boolean(config.initialShowForm),

        // Edit state and form data
        editingId: config.initialEditingId || '',
        categoryOne: config.initialCategoryOne || '',
        categoryTwo: config.initialCategoryTwo || '',
        accountId: config.initialAccountId || '',
        accountName: config.initialAccountName || '',
        entryType: config.initialEntryType || '',
        reportTypeId: config.initialReportTypeId || '',

        // Initialize
        init() {
            // Event delegation for edit buttons and forms
            this.$watch('showForm', (show) => {
                if (show) {
                    this.scrollToForm();
                }
            });
        },

        // Form visibility toggles
        toggleCreateForm() {
            if (this.showForm && !this.editingId) {
                this.closeForm();
                return;
            }

            this.cancelEdit(false);
            this.showForm = true;
        },

        closeForm() {
            this.cancelEdit(false);
            this.showForm = false;
        },

        cancelEdit(keepOpen = true) {
            this.editingId = '';
            this.categoryOne = '';
            this.categoryTwo = '';
            this.accountId = '';
            this.accountName = '';
            this.entryType = '';
            this.reportTypeId = '';
            this.showForm = keepOpen ? true : this.showForm;
        },

        // Edit mode
        editCoa(record) {
            this.showForm = true;
            this.editingId = String(record.id || '');
            this.categoryOne = String(record.category_one || '');
            this.categoryTwo = '';
            this.accountId = String(record.id || '');
            this.accountName = String(record.account_name || '');
            this.entryType = String(record.entry_type || '');
            this.reportTypeId = String(record.report_type_id || '');

            // Dispatch event to form component to load category two options
            this.$dispatch('edit:record-loaded', {
                category_one: record.category_one,
                category_two: record.category_two,
            });

            this.scrollToForm();
        },

        // Event handlers
        handlePageClick(event) {
            const editButton = event.target.closest('[data-coa-edit]');

            if (!editButton) {
                return;
            }

            event.preventDefault();

            this.editCoa({
                id: editButton.dataset.coaId,
                category_one: editButton.dataset.coaCategoryOne,
                category_two: editButton.dataset.coaCategoryTwo,
                account_name: editButton.dataset.coaAccountName,
                entry_type: editButton.dataset.coaEntryType,
                report_type_id: editButton.dataset.coaReportTypeId,
            });
        },

        handlePageSubmit(event) {
            const form = event.target.closest('[data-confirm-submit]');

            if (!form) {
                return;
            }

            if (!confirm(form.dataset.confirmSubmit)) {
                event.preventDefault();
            }
        },

        // Scroll to form
        scrollToForm() {
            this.$nextTick(() => {
                this.$refs.coaPanel?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        },
    };
}
