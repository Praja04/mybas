@extends('pos-security.layouts.base')

@section('title', 'Absensi Pengunjung')

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
                            @include('pos-security.absensi.components.filter')
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-none d-lg-block">
            @include('pos-security.absensi.components.filter')
        </div>

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

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Record Tapping Tamu </span>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="hotReload()">
                            <i class="ri-refresh-line"></i> Refresh
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="ga-history-vendor-pas-datatables table nowrap align-middle" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Tamu</th>
                                        <th>Perusahaan</th>
                                        <th>Tujuan</th>
                                        <th>No Kartu</th>
                                        <th>No Identitas</th>
                                        <th>Bertemu Dgn</th>
                                        <th>Plat Nomor</th>
                                        <th>Jenis Tamu</th>
                                        <th>Waktu Scan</th>
                                        <th>Aktivitas</th>
                                        <th>Kartu Dikembalikan</th>
                                        <th>Foto Diri</th>
                                        <th>Foto Identitas</th>
                                        {{-- <th>Aksi</th>  --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data akan diisi oleh DataTable JS -->
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
    <script type="module" src="{{ asset('assets/js/pos-security/absensi/pages/absensi-rest-log.js') }}"></script>

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
            // Tambahkan query unik agar browser gak ambil dari cache
            const url = window.location.origin + window.location.pathname + '?_=' + Date.now();
            window.location.replace(url); // replace supaya gak nambah history
        }
    </script>
@endpush
