@extends('pos-security.layouts.base')

@section('title', 'Absensi Tapping Security')

@section('content')
    <div class="container-fluid">

        {{-- @include('pos-security.absensi.components.filter') --}}

        <!-- Modal -->
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

        <!-- Modal untuk Lihat Foto -->
        {{-- <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Lihat Foto</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <img id="modalImage" src="" class="img-fluid"
                                style="max-height: 80vh; object-fit: contain;">
                        </div>
                    </div>
                </div>
            </div> --}}

        <!-- Modal Detail (opsional) -->
        {{-- <div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Detail Akses</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <ul id="detailList" class="list-unstyled"></ul>
                        </div>
                    </div>
                </div>
            </div> --}}

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Record Tapping Tamu oleh Security</h5>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="hotReload()">
                            <i class="mdi mdi-refresh"></i> Refresh
                        </button>
                    </div>
                    <div class="card-body">
                        <table
                            class="ga-security-gate-datatables table table-bordered table-hover nowrap align-middle table-responsive"
                            style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Security</th>
                                    <th>NIK Security</th>
                                    <th>Nama Tamu</th>
                                    <th>Perusahaan</th>
                                    <th>Tujuan</th>
                                    <th>No Identitas</th>
                                    <th>Plat Nomor</th>
                                    <th>Waktu Akses</th>
                                    <th>Aktivitas</th>
                                    <th>Foto Diri Tamu</th>
                                    <th>Foto Gate (Security)</th>
                                    {{-- <th>Aksi</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data diisi oleh DataTable -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="module" src="{{ asset('assets/js/pos-security/absensi/pages/absensi-gate-log.js') }}"></script>

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
            // Tambahkan query unik agar browser gak ambil dari cache
            const url = window.location.origin + window.location.pathname + '?_=' + Date.now();
            window.location.replace(url); // replace supaya gak nambah history
        }
    </script>
@endpush
