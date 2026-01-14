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

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">History Transporter/Supplier</h5>
                            <span>selama 7 hari terakhir</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="location.reload()">
                            <i class="mdi mdi-refresh"></i> Refresh
                        </button>
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
                                        {{-- <th>Pakai Kacamata</th>
                                        <th>Kondisi Kacamata (Masuk)</th>
                                        <th>Kondisi Kacamata (Keluar)</th> --}}
                                        {{-- <th>Foto Tamu (Masuk)</th> <!-- photo_visitor -->
                                        <th>Foto Tamu (Keluar)</th> <!-- photo_visitor_out -->
                                        <th>Foto Identitas</th> --}}
                                        <th>Waktu Masuk</th>
                                        <th>Waktu Keluar</th>
                                        {{-- <th>Tanggal Keluar </th>
                                            <th>Jam Keluar</th> --}}
                                        {{-- <th>Dibuat Oleh</th> --}}
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
    </script>
@endpush
