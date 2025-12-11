@extends('pos-security.layouts.base')

@section('title', 'Riwayat Pengecekan Kendaraan')

@section('content')
    <div class="container-fluid">
        @include('pos-security.history-cek-kendaraan.components.modal-detail')

        {{-- todo: filter --}}

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">History Data Pengecekan Kendaraan</h5>
                            <span>selama 7 hari terakhir</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="location.reload()">
                            <i class="mdi mdi-refresh"></i> Refresh
                        </button>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="ga-history-cek-kendaraan-datatables table nowrap align-middle" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nomor Polisi</th>
                                        <th>Nama Supir</th>
                                        <th>Nama Perusahaan</th>
                                        <th>Nama Petugas</th>
                                        <th>Waktu Pemeriksaan</th>
                                        <th>Jenis Truk</th>
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
    <script type="module" src="{{ asset('assets/js/pos-security/history/pages/history-cek-kendaraan.js') }}"></script>

    <script src="{{ asset('assets/js/pos-security/history/pages/history-cek-kendaraan-modal.js') }}"></script>
@endpush
