/**
 * COA Page State Component
 * Manages complete page-level state including form, datatable, and form field handling
 */

export function createCoaPageComponent(config) {
    return {
        // Sidebar state
        sidebarOpen: false,
        showForm: Boolean(config.initialShowForm),

        // Form state
        storeUrl: config.storeUrl,
        updateBaseUrl: config.updateBaseUrl,
        accountSubcategoryUrl: config.accountSubcategoryUrl,
        generateCodeUrl: config.generateCodeUrl,
        editingId: config.initialEditingId || '',
        accountCategoryId: config.initialAccountCategoryId || '',
        accountSubcategoryId: config.initialAccountSubcategoryId || '',
        accountSubcategoryOptions: [],
        accountId: config.initialAccountId || '',
        accountName: config.initialAccountName || '',
        normalBalance: config.initialNormalBalance || '',
        financialReportTypeId: config.initialFinancialReportTypeId || '',

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

        // Page event handlers
        handlePageClick(event) {
            const editButton = event.target.closest('[data-coa-edit]');

            if (!editButton) {
                return;
            }

            event.preventDefault();

            this.editCoa({
                id: editButton.dataset.coaId,
                account_category_id: editButton.dataset.coaAccountCategoryId,
                account_subcategory_id: editButton.dataset.coaAccountSubcategoryId,
                account_name: editButton.dataset.coaAccountName,
                normal_balance: editButton.dataset.coaNormalBalance,
                financial_report_type_id: editButton.dataset.coaFinancialReportTypeId,
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

        // Form visibility
        toggleCreateForm() {
            if (this.showForm && !this.editingId) {
                this.closeForm();
                return;
            }

            this.cancelEdit(false);
            this.showForm = true;
            this.scrollToForm();
        },

        closeForm() {
            this.cancelEdit(false);
            this.showForm = false;
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
            this.showForm = keepOpen ? true : this.showForm;
        },

        // Edit mode
        async editCoa(record) {
            this.showForm = true;
            this.editingId = String(record.id || '');
            this.accountCategoryId = String(record.account_category_id || '');
            this.accountSubcategoryId = '';
            this.accountId = String(record.id || '');
            this.accountName = String(record.account_name || '');
            this.normalBalance = String(record.normal_balance || '');
            this.financialReportTypeId = String(record.financial_report_type_id || '');

            await this.loadAccountSubcategoryOptions();
            this.accountSubcategoryId = String(record.account_subcategory_id || '');
            this.scrollToForm();
        },

        // Form field event handlers
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

        // Scroll to form
        scrollToForm() {
            this.$nextTick(() => {
                this.$refs.coaPanel?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        },
    };
}
