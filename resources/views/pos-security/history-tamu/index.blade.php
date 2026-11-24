@extends('pos-security.layouts.base')

@section('title', 'Riwayat Vendor/Tamu')

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
                            @include('pos-security.history-tamu.components.filter-vendor')
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-none d-lg-block">
            @include('pos-security.history-tamu.components.filter-vendor')
        </div>
        
        @include('pos-security.history-supplier.components.modal-blacklist')
        @include('pos-security.history-supplier.components.modal-image')

        <!-- Modal Export Excel Tamu / Vendor -->
        <div class="modal fade" id="modalExportVendor" tabindex="-1" aria-labelledby="modalExportVendorLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('pos-security.history.vendor.export') }}" method="GET" target="_blank">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalExportVendorLabel">
                                <i class="mdi mdi-file-excel text-success me-1"></i> Export History Tamu / Vendor ke Excel
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="text-muted small mb-3">Pilih rentang tanggal data riwayat tamu / vendor yang ingin diunduh.</p>
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

        {{-- Visitor History Table --}}
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">History Daftar Vendor / Tamu / Transporter</h5>
                            <span>selama 7 hari terakhir</span>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalExportVendor">
                                <i class="mdi mdi-file-excel me-1"></i> Download Excel
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="hotReload()">
                                <i class="mdi mdi-refresh"></i> Refresh
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="ga-history-vendor-pas-datatables table nowrap align-middle" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>No</th> <!-- DT_RowIndex -->
                                        <th>Perusahaan</th> <!-- namacomp -->
                                        <th>Nama Pengunjung</th> <!-- host -->
                                        <th>Tanggal Lahir</th> <!-- host -->
                                        <th>Bertemu Dgn</th> <!-- host -->
                                        <th>Keperluan</th> <!-- host -->
                                        <th>Departemen</th> <!-- hostdeptid -->
                                        <th>No Kartu</th> <!-- purpose -->
                                        <th>No Identitas</th> <!-- purpose -->
                                        <th>Jenis Kartu</th>
                                        <th>Nomor Polisi</th>
                                        <th>Pakai Kacamata</th>
                                        <th>Kondisi Kacamata (Masuk)</th>
                                        <th>Kondisi Kacamata (Keluar)</th>
                                        <th>Foto Tamu (Masuk)</th> <!-- photo_visitor -->
                                        <th>Foto Tamu (Keluar)</th> <!-- photo_visitor_out -->
                                        <th>Foto Identitas</th> <!-- img_visitor -->
                                        <th>Waktu Masuk</th>
                                        <th>Waktu Keluar</th>
                                        <th>Aksi</th> <!-- action -->
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
    <script type="module" src="{{ asset('assets/js/pos-security/history/pages/history-vendor.js') }}"></script>
    <script src="{{ asset('assets/js/pos-security/history/pages/history-vendor-modal.js') }}"></script>
    <script>
        function showImageModal(imageUrl) {
            document.getElementById('modalImage').src = imageUrl;
            var myModal = new bootstrap.Modal(document.getElementById('imageModal'), {});
            myModal.show();
        }

        flatpickr(".flatpickr-range", {
            mode: "range",
            dateFormat: "d-m-Y",
            locale: "id",
            allowInput: true,
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates.length === 1) {
                    const singleDate = instance.formatDate(selectedDates[0], "d-m-Y");
                    instance.input.value = singleDate;
                    instance.close();
                }
            }
        });

        function hotReload() {
            const url = window.location.origin + window.location.pathname + '?_=' + Date.now();
            window.location.replace(url);
        }
    </script>
@endpush
