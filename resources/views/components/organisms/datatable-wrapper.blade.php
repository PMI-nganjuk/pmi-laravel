@props([
    'endpoint' => url()->current(),
    'defaultSort' => 'id',
    'defaultDir' => 'desc',
    'filters' => [] // Array default filters
])

<div class="bg-surface-base border border-surface-border rounded-2xl shadow-sm overflow-hidden flex flex-col"
     x-data="{
        endpoint: '{{ $endpoint }}',
        sortBy: '{{ request('sort_by', $defaultSort) }}',
        sortDir: '{{ request('sort_dir', $defaultDir) }}',
        loading: false,
        resultCount: 0,
        // Konversi filters dari Blade ke JS secara dinamis
        filters: {
            search: '{{ request('search', '') }}',
            @foreach($filters as $key => $default)
                {{ $key }}: '{{ request($key, $default) }}',
            @endforeach
        },

        get hasActiveFilters() {
            return Object.values(this.filters).some(val => val !== '');
        },

        async fetchData(pageUrl = null) {
            this.loading = true;

            // FIX BUG RELOAD: Gunakan URL dari pagination (jika ada), lalu timpa dengan state filter/sort saat ini
            let url = new URL(pageUrl || this.endpoint, window.location.origin);

            // Masukkan semua filter yang aktif ke parameter URL
            Object.entries(this.filters).forEach(([key, value]) => {
                if (value) {
                    url.searchParams.set(key, value);
                } else {
                    url.searchParams.delete(key);
                }
            });

            url.searchParams.set('sort_by', this.sortBy);
            url.searchParams.set('sort_dir', this.sortDir);

            // Jika ini pencarian baru (bukan dari klik pagination), reset ke halaman 1
            if (!pageUrl) {
                url.searchParams.delete('page');
            }

            window.history.pushState({}, '', url.toString());

            try {
                const response = await fetch(url.toString(), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (response.ok) {
                    const html = await response.text();
                    const doc = new DOMParser().parseFromString(html, 'text/html');

                    // Inject HANYA isi container, bukan container itu sendiri
                    const newData = doc.querySelector('#data-container').innerHTML;
                    const dataContainer = document.querySelector('#data-container');

                    dataContainer.innerHTML = newData;

                    // Count visible rows for a11y announcement
                    this.resultCount = dataContainer.querySelectorAll('tbody tr').length;

                    // Announce results to screen readers
                    const announcement = this.resultCount === 0
                        ? 'No results found'
                        : this.resultCount + ' result' + (this.resultCount !== 1 ? 's' : '') + ' found';

                    this.$refs.liveRegion.textContent = announcement;

                    if (window.Alpine) {
                        window.Alpine.initTree(dataContainer);
                    }
                }
            } catch (error) {
                console.error('Error fetching datatable:', error);
            } finally {
                this.loading = false;
            }
        },

        resetFilters() {
            Object.keys(this.filters).forEach(key => this.filters[key] = '');
            this.fetchData();
        },

        toggleSort(column) {
            if (this.sortBy === column) {
                this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortBy = column;
                this.sortDir = 'asc';
            }
            this.fetchData(); // Fetch langsung tanpa mereset page (opsional, tapi disarankan)
        }
     }"
     {{-- Event delegation untuk memastikan pagination selalu via AJAX --}}
     x-on:click="if($event.target.closest('#data-container nav a')) {
         $event.preventDefault();
         fetchData($event.target.closest('a').href);
     }">

    <!-- Screen reader live region for AJAX updates -->
    <div
        x-ref="liveRegion"
        aria-live="polite"
        aria-atomic="true"
        class="sr-only"
    ></div>

    {{ $slot }}
</div>
