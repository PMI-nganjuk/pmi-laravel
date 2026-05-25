/**
 * COA Form State Component
 * Manages form state including cascading selects, code generation, and form submission
 */

export function createCoaFormComponent(config) {
    return {
        storeUrl: config.storeUrl,
        updateBaseUrl: config.updateBaseUrl,
        categoryTwoUrl: config.categoryTwoUrl,
        generateCodeUrl: config.generateCodeUrl,

        // Form state
        editingId: config.initialData?.editingId || '',
        categoryOne: config.initialData?.categoryOne || '',
        categoryTwo: config.initialData?.categoryTwo || '',
        categoryTwoOptions: [],
        accountId: config.initialData?.accountId || '',
        accountName: config.initialData?.accountName || '',
        entryType: config.initialData?.entryType || '',
        reportTypeId: config.initialData?.reportTypeId || '',

        // UI state
        loadingCategoryTwo: false,
        loadingCode: false,
        loadingSubmit: false,

        // Computed properties
        get formAction() {
            return this.editingId
                ? `${this.updateBaseUrl}/${encodeURIComponent(this.editingId)}`
                : this.storeUrl;
        },

        get categoryTwoPlaceholder() {
            if (!this.categoryOne) {
                return 'Pilih kategori utama dulu';
            }
            return this.loadingCategoryTwo ? 'Memuat kategori 2...' : 'Pilih kategori 2';
        },

        // Initialization
        async init() {
            if (this.categoryOne) {
                await this.loadCategoryTwoOptions();

                if (this.categoryTwo && !this.accountId) {
                    await this.updateCode();
                }
            }
        },

        // Event handlers
        async handleCategoryOneChange() {
            this.categoryTwo = '';
            this.accountId = '';
            await this.loadCategoryTwoOptions();
        },

        async handleCategoryTwoChange() {
            await this.updateCode();
        },

        // Data fetching
        async loadCategoryTwoOptions() {
            this.categoryTwoOptions = [];

            if (!this.categoryOne) {
                return;
            }

            this.loadingCategoryTwo = true;

            try {
                const params = new URLSearchParams({ category_one: this.categoryOne });
                const response = await fetch(`${this.categoryTwoUrl}?${params.toString()}`, {
                    headers: { Accept: 'application/json' },
                });

                if (!response.ok) {
                    throw new Error('Gagal memuat kategori 2.');
                }

                const payload = await response.json();
                this.categoryTwoOptions = Object.entries(payload.data || {}).map(([code, name]) => ({ code, name }));
            } catch (error) {
                console.error(error);
                this.categoryTwoOptions = [];
            } finally {
                this.loadingCategoryTwo = false;
            }
        },

        async updateCode() {
            this.accountId = '';

            if (!this.categoryOne || !this.categoryTwo) {
                return;
            }

            this.loadingCode = true;

            try {
                const params = new URLSearchParams({
                    category_one: this.categoryOne,
                    category_two: this.categoryTwo,
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
            this.categoryOne = '';
            this.categoryTwo = '';
            this.categoryTwoOptions = [];
            this.accountId = '';
            this.accountName = '';
            this.entryType = '';
            this.reportTypeId = '';
            this.loadingSubmit = false;

            if (!keepOpen) {
                this.$dispatch('form:cancelled');
            }
        },

        // Edit mode setup
        async loadForEdit(record) {
            this.editingId = String(record.id || '');
            this.categoryOne = String(record.category_one || '');
            this.categoryTwo = '';
            this.accountId = String(record.id || '');
            this.accountName = String(record.account_name || '');
            this.entryType = String(record.entry_type || '');
            this.reportTypeId = String(record.report_type_id || '');

            await this.loadCategoryTwoOptions();
            this.categoryTwo = String(record.category_two || '');
        },
    };
}
