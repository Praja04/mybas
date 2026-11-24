@extends('pos-security.layouts.base')

@section('title', 'Riwayat Supplier')

@section('content')
    <div class="container-fluid">
        <div class="d-block d-lg-none mb-3">
            <div class="accordion" id="filterAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseFilter">
                            Filter Pencarian
                        </button>
                    </h2>
                    <div id="collapseFilter" class="accordion-collapse collapse">
                        <div class="accordion-body p-0">
                            @include('pos-security.history-supplier.components.filter-supplier')
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-none d-lg-block">
            @include('pos-security.history-supplier.components.filter-supplier')
        </div>


        @include('pos-security.history-supplier.components.modal-detail')
        @include('pos-security.history-supplier.components.modal-lapor-kartu')
        @include('pos-security.history-supplier.components.modal-blacklist')
        @include('pos-security.history-supplier.components.modal-image')

        <!-- Modal Export Excel Supplier -->
        <div class="modal fade" id="modalExportSupplier" tabindex="-1" aria-labelledby="modalExportSupplierLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('pos-security.history.supplier.export') }}" method="GET" target="_blank">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalExportSupplierLabel">
                                <i class="mdi mdi-file-excel text-success me-1"></i> Export History Supplier ke Excel
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="text-muted small mb-3">Pilih rentang tanggal masuk data riwayat supplier yang ingin diunduh.</p>
                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="form-label fw-semibold">Tanggal Awal <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="start_date" value="{{ date('Y-m-01') }}" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-semibold">Tanggal Akhir <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="end_date" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success">
                                <i class="mdi mdi-download me-1"></i> Download Excel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">History Transporter/Supplier</h5>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalExportSupplier">
                                <i class="mdi mdi-file-excel me-1"></i> Download Excel
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="location.reload()">
                                <i class="mdi mdi-refresh"></i> Refresh
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="ga-history-supplier-pas-datatables table nowrap align-middle" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Supir/Kernet</th>
                                        <th>Keterangan</th>
                                        <th>Perusahaan</th>
                                        <th>No. Polisi</th>
                                        <th>No. Kartu</th>
                                        <th>Waktu Masuk</th>
                                        <th>Waktu Keluar</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="module" src="{{ asset('assets/js/pos-security/history/pages/history-supplier.js') }}"></script>

    <script src="{{ asset('assets/js/pos-security/history/pages/history-supplier-modal.js') }}"></script>
    <script>
        function showImageModal(imageUrl) {
            document.getElementById('modalImage').src = imageUrl;
            var myModal = new bootstrap.Modal(document.getElementById('imageModal'), {});
            myModal.show();
        }

        // Init flatpickr date range
        flatpickr(".flatpickr-range", {
            mode: "range",
            dateFormat: "d-m-Y",
            locale: "id",
            allowInput: true
        });

        // Filter form submit
        $(document).on("submit", ".filter-form-supplier", function (e) {
            e.preventDefault();

            var params = {};
            var tanggalMasuk = $(this).find('input[name="tanggal_masuk"]').val();

            console.log("Raw tanggal_masuk value:", tanggalMasuk);

            if (tanggalMasuk) {
                if (tanggalMasuk.indexOf(" to ") !== -1) {
                    var parts = tanggalMasuk.split(" to ");
                    params.start_date = parts[0].trim();
                    params.end_date = parts[1].trim();
                } else {
                    params.start_date = tanggalMasuk.trim();
                }
            }

            console.log("Filter params being sent:", params);

            if (window._supplierDatatableConfig && window._supplierTable) {
                window._supplierDatatableConfig.dataSend = params;
                window._supplierTable.ajax.reload();
            } else {
                console.error("Datatable not ready yet");
            }
        });

        // Reset form
        $(document).on("reset", ".filter-form-supplier", function () {
            var form = $(this);
            setTimeout(function () {
                // Clear flatpickr
                form.find(".flatpickr-range").each(function () {
                    if (this._flatpickr) {
                        this._flatpickr.clear();
                    }
                });

                if (window._supplierDatatableConfig && window._supplierTable) {
                    window._supplierDatatableConfig.dataSend = {};
                    window._supplierTable.ajax.reload();
                }
            }, 100);
        });
    </script>
@endpush
