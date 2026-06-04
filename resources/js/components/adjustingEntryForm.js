/**
 * Adjusting Entry Page State Component
 * Manages complete page-level state: form, edit mode, and form submission
 * for the Jurnal Penyesuaian page.
 */

export function createAdjustingEntryPageComponent(config) {
    return {
        // Sidebar state
        sidebarOpen: false,

        storeUrl:      config.storeUrl,
        updateBaseUrl: config.updateBaseUrl,
        redirectUrl:   config.redirectUrl,

        // Form/Page state
        editingId:          config.initialEditingId || '',
        transactionDate:    config.initialData?.transactionDate || '',
        debitAccountId:     config.initialData?.debitAccountId || '',
        creditAccountId:    config.initialData?.creditAccountId || '',
        amount:             config.initialData?.amount || '',
        reference:          config.initialData?.reference || '',
        description:        config.initialData?.description || '',
        programId:          config.initialData?.programId || '',
        userId:             config.initialData?.userId || '',
        journalEntryType:   config.initialData?.journalEntryType || '',
        nextDocumentNumber: config.nextDocumentNumber || '',

        // UI state
        loadingSubmit: false,

        // Computed properties
        get formAction() {
            return this.editingId
                ? `${this.updateBaseUrl}/${encodeURIComponent(this.editingId)}`
                : this.storeUrl;
        },

        init() {
            // Component is initialized
        },

        // Page-level event handlers
        handlePageClick(event) {
            const editButton = event.target.closest('[data-adjusting-edit]');

            if (!editButton) {
                return;
            }

            event.preventDefault();

            this.editEntry({
                id:                 editButton.dataset.adjustingId,
                transaction_date:   editButton.dataset.adjustingDate,
                debit_account_id:   editButton.dataset.adjustingDebitAccount,
                credit_account_id:  editButton.dataset.adjustingCreditAccount,
                amount:             editButton.dataset.adjustingAmount,
                reference:          editButton.dataset.adjustingReference,
                description:        editButton.dataset.adjustingDescription,
                program_id:         editButton.dataset.adjustingProgramId,
                user_id:            editButton.dataset.adjustingUserId,
                document_number:    editButton.dataset.adjustingDocumentNumber,
                journal_entry_type: editButton.dataset.adjustingJournalEntryType,
            });
        },

        // Trigger edit mode
        editEntry(record) {
            this.editingId          = String(record.id || '');
            this.transactionDate    = String(record.transaction_date || '');
            this.debitAccountId     = String(record.debit_account_id || '');
            this.creditAccountId    = String(record.credit_account_id || '');
            this.amount             = String(record.amount || '');
            this.reference          = String(record.reference || 'null' === String(record.reference) ? '' : record.reference || '');
            this.description        = String(record.description || 'null' === String(record.description) ? '' : record.description || '');
            this.programId          = String(record.program_id || 'null' === String(record.program_id) ? '' : record.program_id || '');
            this.userId             = String(record.user_id || '');
            this.journalEntryType   = String(record.journal_entry_type || '');
            this.nextDocumentNumber = String(record.document_number || '');

            // Scroll to form panel
            this.$nextTick(() => {
                const formPanel = document.getElementById('adjusting-entry-form-title');
                if (formPanel) {
                    formPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        },

        cancelEdit() {
            this.editingId          = '';
            this.transactionDate    = config.initialData?.transactionDate || new Date().toISOString().split('T')[0];
            this.debitAccountId     = '';
            this.creditAccountId    = '';
            this.amount             = '';
            this.reference          = '';
            this.description        = '';
            this.programId          = '';
            this.userId             = config.initialData?.userId || '';
            this.journalEntryType   = '';
            this.nextDocumentNumber = config.nextDocumentNumber || '';
            this.loadingSubmit      = false;
        },

        submitForm() {
            this.loadingSubmit = true;
            this.$refs.adjustingEntryForm.submit();
        },
    };
}
