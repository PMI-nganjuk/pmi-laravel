/**
 * COA Form State Component
 * Manages form state including cascading selects, code generation, and form submission
 */

export function createCoaFormComponent(config) {
    return {
        storeUrl: config.storeUrl,
        updateBaseUrl: config.updateBaseUrl,
        accountSubcategoryUrl: config.accountSubcategoryUrl,
        generateCodeUrl: config.generateCodeUrl,

        // Form state
        editingId: config.initialData?.editingId || '',
        accountCategoryId: config.initialData?.accountCategoryId || '',
        accountSubcategoryId: config.initialData?.accountSubcategoryId || '',
        accountSubcategoryOptions: [],
        accountId: config.initialData?.accountId || '',
        accountName: config.initialData?.accountName || '',
        normalBalance: config.initialData?.normalBalance || '',
        financialReportTypeId: config.initialData?.financialReportTypeId || '',

        // UI state
        loadingAccountSubcategory: false,
        loadingCode: false,
        loadingSubmit: false,

        // Computed properties
        get formAction() {
            return this.editingId
                ? `${this.updateBaseUrl}/${encodeURIComponent(this.editingId)}`
                : this.storeUrl;
        },

        get accountSubcategoryPlaceholder() {
            if (!this.accountCategoryId) {
                return 'Pilih kategori utama dulu';
            }
            return this.loadingAccountSubcategory ? 'Memuat kategori 2...' : 'Pilih kategori 2';
        },

        // Initialization
        async init() {
            if (this.accountCategoryId) {
                await this.loadAccountSubcategoryOptions();

                if (this.accountSubcategoryId && !this.accountId) {
                    await this.updateCode();
                }
            }
        },

        // Event handlers
        async handleAccountCategoryChange() {
            this.accountSubcategoryId = '';
            this.accountId = '';
            await this.loadAccountSubcategoryOptions();
        },

        async handleAccountSubcategoryChange() {
            await this.updateCode();
        },

        // Data fetching
        async loadAccountSubcategoryOptions() {
            this.accountSubcategoryOptions = [];

            if (!this.accountCategoryId) {
                return;
            }

            this.loadingAccountSubcategory = true;

            try {
                const params = new URLSearchParams({ account_category_id: this.accountCategoryId });
                const response = await fetch(`${this.accountSubcategoryUrl}?${params.toString()}`, {
                    headers: { Accept: 'application/json' },
                });

                if (!response.ok) {
                    throw new Error('Gagal memuat kategori 2.');
                }

                const payload = await response.json();
                this.accountSubcategoryOptions = Object.entries(payload.data || {}).map(([id, name]) => ({ id, name }));
            } catch (error) {
                console.error(error);
                this.accountSubcategoryOptions = [];
            } finally {
                this.loadingAccountSubcategory = false;
            }
        },

        async updateCode() {
            this.accountId = '';

            if (!this.accountCategoryId || !this.accountSubcategoryId) {
                return;
            }

            this.loadingCode = true;

            try {
                const params = new URLSearchParams({
                    account_category_id: this.accountCategoryId,
                    account_subcategory_id: this.accountSubcategoryId,
                });
                const response = await fetch(`${this.generateCodeUrl}?${params.toString()}`, {
                    headers: { Accept: 'application/json' },
                });

                if (!response.ok) {
                    throw new Error('Gagal membuat kode akun.');
                }

                const payload = await response.json();
                this.accountId = payload.data?.code || '';
            } catch (error) {
                console.error(error);
                this.accountId = '';
            } finally {
                this.loadingCode = false;
            }
        },

        // Form submission
        submitForm() {
            if (!this.accountId) {
                alert('Silakan pilih kategori 1 dan kategori 2 terlebih dahulu.');
                return;
            }

            this.loadingSubmit = true;
            this.$refs.coaForm.submit();
        },

        // Form visibility
        closeForm() {
            this.cancelEdit(false);
            this.$dispatch('form:closed');
        },

        cancelEdit(keepOpen = true) {
            this.editingId = '';
            this.accountCategoryId = '';
            this.accountSubcategoryId = '';
            this.accountSubcategoryOptions = [];
            this.accountId = '';
            this.accountName = '';
            this.normalBalance = '';
            this.financialReportTypeId = '';
            this.loadingSubmit = false;

            if (!keepOpen) {
                this.$dispatch('form:cancelled');
            }
        },

        // Edit mode setup
        async loadForEdit(record) {
            this.editingId = String(record.id || '');
            this.accountCategoryId = String(record.account_category_id || '');
            this.accountSubcategoryId = '';
            this.accountId = String(record.id || '');
            this.accountName = String(record.account_name || '');
            this.normalBalance = String(record.normal_balance || '');
            this.financialReportTypeId = String(record.financial_report_type_id || '');

            await this.loadAccountSubcategoryOptions();
            this.accountSubcategoryId = String(record.account_subcategory_id || '');
        },
    };
}
