export class ContentDatatableOut {
    constructor() {
        this.url = API_DATATABLE_KENDARAAN_OUT;
        this.currentPage = 1;
        this.perPage = 10;
        this.search = '';
    }

    initialize() {
        // Expose instance globally for reloads or other triggers
        window.cekKendaraanOutTable = this;

        // Bind DOM events
        $('#perPageSelectOut').on('change', (e) => {
            this.perPage = parseInt($(e.target).val());
            this.currentPage = 1;
            this.load();
        });

        // Add a small debounce for typing search
        let searchTimeout;
        $('#searchInputOut').on('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.search = $(e.target).val();
                this.currentPage = 1;
                this.load();
            }, 300);
        });

        // Initial load
        return this.load();
    }

    load() {
        return new Promise((resolve, reject) => {
            const tableBody = $('#tableOutCustom tbody');
            tableBody.html('<tr><td colspan="4" class="text-center text-muted py-3">Memuat data...</td></tr>');

            $.ajax({
                url: this.url,
                method: 'GET',
                data: {
                    page: this.currentPage,
                    per_page: this.perPage,
                    search: this.search
                },
                success: (response) => {
                    this.render(response);
                    resolve(response);
                },
                error: (xhr, status, error) => {
                    tableBody.html('<tr><td colspan="4" class="text-center text-danger py-3">Gagal memuat data. Silakan coba lagi.</td></tr>');
                    reject(error);
                }
            });
        });
    }

    // Alias reload to load
    reload(callback, resetPaging = true) {
        if (resetPaging) {
            this.currentPage = 1;
        }
        return this.load().then(() => {
            if (typeof callback === 'function') callback();
        });
    }

    render(response) {
        const tableBody = $('#tableOutCustom tbody');
        tableBody.empty();

        const data = response.data || [];
        if (data.length === 0) {
            tableBody.html('<tr><td colspan="4" class="text-center text-muted py-3">Semua kendaraan sudah dicek keluar</td></tr>');
            $('#paginationInfoOut').text('Menampilkan 0 sampai 0 dari 0 entri');
            $('#paginationListOut').empty();
            return;
        }

        // Render rows
        data.forEach((row) => {
            const tr = $('<tr></tr>');
            tr.append(`<td>${row.DT_RowIndex}</td>`);
            tr.append(`<td>${row.nomor_polisi_html}</td>`);
            tr.append(`<td>${row.status_html}</td>`);
            tr.append(`<td>${row.action_html}</td>`);
            tableBody.append(tr);
        });

        // Render pagination info
        const from = response.from || 0;
        const to = response.to || 0;
        const total = response.total || 0;
        $('#paginationInfoOut').text(`Menampilkan ${from} sampai ${to} dari ${total} entri`);

        // Render pagination links
        const paginationList = $('#paginationListOut');
        paginationList.empty();

        const lastPage = response.last_page || 1;
        
        // Prev button
        const prevClass = this.currentPage === 1 ? 'disabled' : '';
        const prevBtn = $(`<li class="page-item ${prevClass}"><a class="page-link" href="#" data-page="${this.currentPage - 1}">←</a></li>`);
        paginationList.append(prevBtn);

        // Page numbers
        const maxVisible = 5;
        let startPage = Math.max(1, this.currentPage - Math.floor(maxVisible / 2));
        let endPage = Math.min(lastPage, startPage + maxVisible - 1);
        if (endPage - startPage + 1 < maxVisible) {
            startPage = Math.max(1, endPage - maxVisible + 1);
        }

        for (let p = startPage; p <= endPage; p++) {
            const activeClass = p === this.currentPage ? 'active' : '';
            const pageItem = $(`<li class="page-item ${activeClass}"><a class="page-link" href="#" data-page="${p}">${p}</a></li>`);
            paginationList.append(pageItem);
        }

        // Next button
        const nextClass = this.currentPage === lastPage ? 'disabled' : '';
        const nextBtn = $(`<li class="page-item ${nextClass}"><a class="page-link" href="#" data-page="${this.currentPage + 1}">→</a></li>`);
        paginationList.append(nextBtn);

        // Bind pagination clicks
        paginationList.find('a').on('click', (e) => {
            e.preventDefault();
            const targetPage = parseInt($(e.target).attr('data-page'));
            if (targetPage >= 1 && targetPage <= lastPage && targetPage !== this.currentPage) {
                this.currentPage = targetPage;
                this.load();
            }
        });
    }
}

window.ContentDatatableOut = ContentDatatableOut;
