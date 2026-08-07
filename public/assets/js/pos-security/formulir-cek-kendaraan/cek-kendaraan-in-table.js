import { getDatatable } from "../../shared/services/datatable.services.js";

export class ContentDatatable {
    constructor() {
        this.url = API_DATATABLE_KENDARAAN_IN;
        this.currentPage = 1;
        this.perPage = 10;
        this.search = '';
    }

    initialize() {
        // Expose instance globally for reloads or other triggers
        window.cekKendaraanInTable = this;

        // Bind DOM events
        $('#perPageSelectIn').on('change', (e) => {
            this.perPage = parseInt($(e.target).val());
            this.currentPage = 1;
            this.load();
        });

        // Add a small debounce for typing search
        let searchTimeout;
        $('#searchInputIn').on('input', (e) => {
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
            const tableBody = $('#tableInCustom tbody');
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
        const tableBody = $('#tableInCustom tbody');
        tableBody.empty();

        const data = response.data || [];
        if (data.length === 0) {
            tableBody.html('<tr><td colspan="4" class="text-center text-muted py-3">Semua kendaraan sudah dicek masuk</td></tr>');
            $('#paginationInfoIn').text('Menampilkan 0 sampai 0 dari 0 entri');
            $('#paginationListIn').empty();
            return;
        }

        // Render rows
        data.forEach((row) => {
            const tr = $('<tr></tr>');
            
            // Format status with draft badge if applicable
            let statusBadge = row.status_html;
            if (window.getStatusWithDraft) {
                statusBadge = window.getStatusWithDraft(row);
            }

            tr.append(`<td>${row.DT_RowIndex}</td>`);
            tr.append(`<td>${row.nomor_polisi_html}</td>`);
            tr.append(`<td>${statusBadge}</td>`);
            tr.append(`<td>${row.action_html}</td>`);
            tableBody.append(tr);
        });

        // Render pagination info
        const from = response.from || 0;
        const to = response.to || 0;
        const total = response.total || 0;
        $('#paginationInfoIn').text(`Menampilkan ${from} sampai ${to} dari ${total} entri`);

        // Render pagination links
        const paginationList = $('#paginationListIn');
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

window.ContentDatatableIn = ContentDatatable;

async function preloadDraftCache() {
    if (!window.IDBDraft) {
        console.warn("IDBDraft belum siap");
        return;
    }

    window.draftCache = window.draftCache || {};

    const drafts = await window.IDBDraft.getAllDrafts();

    drafts.forEach(d => {
        window.draftCache[d.sessionId] = true;
    });

    console.log("Draft cache loaded:", window.draftCache);
}

const contentDatatable = new ContentDatatable();

// Initial datatable load
(async () => {
    await preloadDraftCache();

    contentDatatable
        .initialize()
        .then(() => {
            console.log("All datatables initialized successfully");
        })
        .catch((error) => {
            console.error("Initialization failed:", error);
        });
})();

window.getStatusWithDraft = function (row) {
    const trnId = row.trnvisitorid;

    console.log("DraftCache:", window.draftCache);
    console.log("Row trnvisitorid:", row.trnvisitorid);

    // kalau ada draft
    if (window.draftCache[trnId]) {
        return `
          <span class="badge bg-warning text-dark">
            Sudah Dicek (Belum Disimpan)
          </span>
        `;
    }

    // status normal dari backend
    return `
        ${row.status_html || row.status || '-'}
    `;
};
