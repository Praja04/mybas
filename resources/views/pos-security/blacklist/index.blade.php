@extends('pos-security.layouts.base')

@push('styles')
    <style>
        .ga-blacklist-datatables tbody td:not(:first-child):not(:last-child),
        #blacklistDetailModal .modal-body td {
            text-transform: uppercase;
        }
    </style>
@endpush

@push('scripts')
    <script type="module" src="{{ asset('assets/js/pos-security/blacklist/pages/blacklist-datatable.js') }}"></script>

    {{-- <script src="{{ asset('portal\module\ga\sistem-tracking\history\pages\blacklist-modal.js') }}"></script> --}}

    <script>
        function showImageModal(imageUrl) {
            document.getElementById('modalImage').src = imageUrl;
            var myModal = new bootstrap.Modal(document.getElementById('imageModal'), {});
            myModal.show();
        }

        function openBlacklistDetailModal(id) {
            $.ajax({
                url: API_BLACKLIST_SHOW,
                method: 'GET',
                data: {
                    id: id
                },
                beforeSend: () => {
                    $('#blacklistDetailModal .modal-body').html('<p class="text-center">Loading...</p>');
                    $('#blacklistDetailModal').modal('show');
                },
                success: function(response) {
                    if (response.success) {
                        const data = response.data;

                        const statusBadge = data.aktif ?
                            '<span class="badge bg-success">Aktif</span>' :
                            '<span class="badge bg-danger">Nonaktif</span>';

                        const html = `
                            <div class="row mb-4">
                                <div class="col-md-6 text-center">
                                    <div class="fw-semibold mb-2">Foto Diri</div>
                                    ${data.foto_diri_url
                                        ? `<img src="${data.foto_diri_url}" class="img-fluid rounded shadow-sm" style="max-height:200px;" />`
                                        : '<p class="text-muted">Tidak ada foto diri</p>'
                                    }
                                </div>
                                <div class="col-md-6 text-center">
                                    <div class="fw-semibold mb-2">Foto KTP</div>
                                    ${data.foto_ktp_url
                                        ? `<img src="${data.foto_ktp_url}" class="img-fluid rounded shadow-sm" style="max-height:200px;" />`
                                        : '<p class="text-muted">Tidak ada foto KTP</p>'
                                    }
                                </div>
                            </div>

                            <table class="table table-bordered">
                                <tr><th>Nama</th><td>${data.nama}</td></tr>
                                <tr><th>No. Identitas</th><td>${data.no_identitas}</td></tr>
                                <tr><th>Jenis Identitas</th><td>${data.jenis_identitas}</td></tr>
                                <tr><th>Tanggal Lahir</th><td>${data.tanggal_lahir || '-'}</td></tr>
                                <tr><th>Alasan Blacklist</th><td>${data.alasan_blacklist}</td></tr>
                                <tr><th>Tanggal Blacklist</th><td>${data.tanggal_blacklist || '-'}</td></tr>
                                <tr><th>Di-blacklist Oleh</th><td>${data.diblacklist_oleh}</td></tr>
                                <tr><th>Status</th><td>${data.aktif ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Nonaktif</span>'}</td></tr>
                            </table>
                        `;

                        $('#blacklistDetailModal .modal-body').html(html);
                    } else {
                        $('#blacklistDetailModal .modal-body').html(
                            '<p class="text-danger">Gagal memuat data</p>');
                    }
                },
                error: function() {
                    $('#blacklistDetailModal .modal-body').html(
                        '<p class="text-danger">Terjadi kesalahan saat mengambil data</p>');
                }
            });
        }
    </script>
@endpush

@section('content')
    <div class="container-fluid">

        {{-- Detail Modal --}}
        <div class="modal fade" id="blacklistDetailModal" tabindex="-1" aria-labelledby="blacklistDetailModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="blacklistDetailModalLabel">Detail Blacklist</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center text-muted">Memuat data...</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Blacklist Table --}}
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Daftar Blacklist Visitor</h5>
                            <span>Data blacklist selama 7 hari terakhir</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="location.reload()">
                            <i class="bi bi-arrow-clockwise"></i> Refresh
                        </button>
                    </div>
                    <div class="card-body">
                        <table class="ga-blacklist-datatables table nowrap align-middle" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>No. Identitas</th>
                                    <th>Jenis Identitas</th>
                                    <th>Tanggal Lahir</th>
                                    <th>Alasan Blacklist</th>
                                    <th>Tanggal Blacklist</th>
                                    <th>Di-blacklist Oleh</th>
                                    <th>Status</th>
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
@endsection
