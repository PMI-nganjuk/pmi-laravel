/**
 * Cash Disbursement Form Component
 * Manages form state including description suggestions for expense accounts.
 */

export function createCashDisbursementFormComponent(config) {
    return {
        storeUrl:                  config.storeUrl,
        redirectUrl:               config.redirectUrl,
        suggestDescriptionUrl:     config.suggestDescriptionUrl,
        transactionAccountOptions: config.transactionAccountOptions || [],

        // Form state
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

        init() {
            // Component is initialized
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

        async submitForm() {
            this.loadingSubmit = true;

            try {
                const form  = this.$refs.disbursementForm;
                const data  = new FormData(form);
                const token = document.querySelector('meta[name="csrf-token"]')?.content;

                const response = await fetch(this.storeUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                    },
                    body: data,
                });

                // Laravel redirects come back as non-JSON — let the browser follow
                if (response.redirected) {
                    window.location.href = response.url;
                    return;
                }

                const json = await response.json();

                if (json.success) {
                    window.location.href = this.redirectUrl || '/disbursements';
                } else {
                    // Fall back to native submit to let Blade/FormRequest handle errors
                    this.$refs.disbursementForm.submit();
                }
            } catch (err) {
                console.error('AJAX submit failed, falling back to native form submission:', err);
                this.$refs.disbursementForm.submit();
            } finally {
                this.loadingSubmit = false;
            }
        },
    };
}
