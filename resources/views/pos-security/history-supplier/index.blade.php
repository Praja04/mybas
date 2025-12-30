@extends('pos-security.layouts.base')

@section('title', 'Riwayat Supplier')

@section('content')
    <div class="container-fluid">
        {{-- @include('pos-security.history-supplier.components.filter-supplier') --}}
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
                                        <th>Pakai Kacamata</th>
                                        <th>Kondisi Kacamata</th>
                                        <th>Foto Tamu</th>
                                        <th>Foto Identitas</th>
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
        });
    </script>
@endpush
