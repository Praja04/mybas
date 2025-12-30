@extends('pos-security.layouts.base')

@section('title', 'Riwayat Vendor/Tamu')

@section('content')
    <div class="container-fluid">

        {{-- @include('pos-security.history-tamu.components.filter-vendor') --}}

        {{-- Visitor History Table --}}
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">History Daftar Vendor / Tamu / Transporter</h5>
                            <span>selama 7 hari terakhir</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="hotReload()">
                            <i class="mdi mdi-refresh"></i> Refresh
                        </button>
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
                                        <th>Nama PIC</th> <!-- host -->
                                        <th>Keperluan</th> <!-- host -->
                                        <th>Departemen</th> <!-- hostdeptid -->
                                        <th>No Kartu</th> <!-- purpose -->
                                        <th>No Identitas</th> <!-- purpose -->
                                        <th>Jenis Kartu</th>
                                        <th>Pakai Kacamata</th>
                                        <th>Kondisi Kacamata</th>
                                        <th>Foto Diri</th> <!-- photo_visitor -->
                                        <th>Foto Identitas</th> <!-- img_visitor -->
                                        <th>Waktu Masuk</th>
                                        <th>Waktu Keluar</th>
                                        {{-- <th>Aksi</th> <!-- action --> --}}
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

        {{-- Preview Image Modal --}}
        <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="imageModalLabel">Preview Image</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img id="modalImage" src="" alt="Full Image"
                            style="max-width: 100%; max-height: 80vh; border-radius: 8px;" />
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="module" src="{{ asset('assets/js/pos-security/history/pages/history-vendor.js') }}"></script>
    <script>
        function showImageModal(imageUrl) {
            document.getElementById('modalImage').src = imageUrl;
            var myModal = new bootstrap.Modal(document.getElementById('imageModal'), {});
            myModal.show();
        }
    </script>

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

        function hotReload() {
            const url = window.location.origin + window.location.pathname + '?_=' + Date.now();
            window.location.replace(url);
        }
    </script>
@endpush
