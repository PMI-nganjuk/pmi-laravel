/**
 * Cash Receipt Page State Component
 * Manages complete page-level state including form, datatable, description suggestions,
 * edit mode triggers, and form submission via AJAX
 */

export function createCashReceiptPageComponent(config) {
    return {
        // Sidebar state
        sidebarOpen: false,

        storeUrl:                  config.storeUrl,
        updateBaseUrl:             config.updateBaseUrl,
        redirectUrl:               config.redirectUrl,
        suggestDescriptionUrl:     config.suggestDescriptionUrl,
        transactionAccountOptions: config.transactionAccountOptions || [],

        // Form/Page state
        editingId:              config.initialEditingId || '',
        transactionDate:        config.initialData?.transactionDate || '',
        cashAccountCode:        config.initialData?.cashAccountCode || '',
        transactionAccountCode: config.initialData?.transactionAccountCode || '',
        amount:                 config.initialData?.amount || '',
        reference:              config.initialData?.reference || '',
        description:            config.initialData?.description || '',
        programId:              config.initialData?.programId || '',
        userId:                 config.initialData?.userId || '',
        nextDocumentNumber:     config.nextDocumentNumber || '',

        // UI state
        loadingSuggestion: false,
        loadingSubmit:     false,

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
            const editButton = event.target.closest('[data-receipt-edit]');

            if (!editButton) {
                return;
            }

            event.preventDefault();

            this.editReceipt({
                id:                          editButton.dataset.receiptId,
                transaction_date:            editButton.dataset.receiptDate,
                cash_account_code:           editButton.dataset.receiptCashAccount,
                transaction_account_code:    editButton.dataset.receiptTransactionAccount,
                amount:                      editButton.dataset.receiptAmount,
                reference:                   editButton.dataset.receiptReference,
                description:                 editButton.dataset.receiptDescription,
                program_id:                  editButton.dataset.receiptProgramId,
                user_id:                     editButton.dataset.receiptUserId,
                document_number:             editButton.dataset.receiptDocumentNumber,
            });
        },

        // Trigger edit mode
        editReceipt(record) {
            this.editingId              = String(record.id || '');
            this.transactionDate        = String(record.transaction_date || '');
            this.cashAccountCode        = String(record.cash_account_code || '');
            this.transactionAccountCode = String(record.transaction_account_code || '');
            this.amount                 = String(record.amount || '');
            this.reference              = String(record.reference || '');
            this.description            = String(record.description || '');
            this.programId              = String(record.program_id || 'null' === String(record.program_id) ? '' : record.program_id || '');
            this.userId                 = String(record.user_id || '');
            this.nextDocumentNumber     = String(record.document_number || '');
            
            // Scroll to form panel
            this.$nextTick(() => {
                const formPanel = document.getElementById('receipt-form-title');
                if (formPanel) {
                    formPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        },

        cancelEdit() {
            this.editingId              = '';
            this.transactionDate        = config.initialData?.transactionDate || new Date().toISOString().split('T')[0];
            this.cashAccountCode        = '';
            this.transactionAccountCode = '';
            this.amount                 = '';
            this.reference              = '';
            this.description            = '';
            this.programId              = '';
            this.userId                 = config.initialData?.userId || '';
            this.nextDocumentNumber     = config.nextDocumentNumber || '';
            this.loadingSubmit          = false;
        },

        async handleTransactionAccountChange() {
            if (!this.transactionAccountCode) {
                return;
            }

            this.loadingSuggestion = true;

            try {
                const url = new URL(this.suggestDescriptionUrl);
                url.searchParams.set('transaction_account_code', this.transactionAccountCode);

                const response = await fetch(url.toString(), {
                    headers: { 'Accept': 'application/json' },
                });

                if (!response.ok) throw new Error('Request failed');

                const json = await response.json();

                if (json.success && json.data?.description) {
                    this.description = json.data.description;
                }
            } catch (err) {
                console.error('Failed to load description suggestion:', err);
                // Fail silently — description stays editable
            } finally {
                this.loadingSuggestion = false;
            }
        },

        submitForm() {
            this.loadingSubmit = true;
            this.$refs.receiptForm.submit();
        },
    };
}
