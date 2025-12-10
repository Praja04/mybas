@extends('pos-security.layouts.base')

@section('content')
    <div class="container-fluid">

        @include('pos-security.kartu.components.filter')

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Penggunaan Kartu Aktif</h5>
                            {{-- <span>selama 7 hari terakhir</span> --}}
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="hotReload()">
                            <i class="bi bi-arrow-clockwise"></i> Refresh
                        </button>
                    </div>
                    <div class="card-body">
                        <table id="kartu-paling-sering-table"
                            class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th> <!-- Untuk index column -->
                                    <th>Nomor Kartu</th>
                                    <th>Jumlah Penggunaan</th>
                                    <th>Type</th>
                                    <th>Action</th> <!-- Kolom action -->
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data akan diisi oleh DataTables -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            // Inisialisasi DataTable untuk Kartu Paling Sering
            var kartuPalingSeringTable = $('#kartu-paling-sering-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: API_DATATABLE_KARTU_AKTIF,
                    type: 'GET',
                    data: function(d) {
                        // Ambil data filter dari form
                        var filterData = {};

                        // Filter POS
                        var posValue = $('#filter-form-kartu-sering select[name="pos"]').val();
                        if (posValue) {
                            filterData['pos'] = posValue;
                        }

                        // Filter Type
                        var typeValue = $('#filter-form-kartu-sering select[name="type"]').val();
                        if (typeValue) {
                            filterData['type'] = typeValue;
                        }

                        // Filter No. Kartu (Opsional)
                        var noKartuValue = $('#filter-form-kartu-sering input[name="no_kartu"]').val();
                        if (noKartuValue) {
                            filterData['no_kartu'] = noKartuValue;
                        }

                        // Filter Tanggal (Opsional) - Jika diaktifkan
                        // var tanggalValue = $('#filter-form-kartu-sering input[name="tanggal_penggunaan"]').val();
                        // if (tanggalValue) {
                        //     var dates = tanggalValue.split(' to '); // Format dari flatpickr range
                        //     if (dates.length === 2) {
                        //         filterData['start_date'] = dates[0];
                        //         filterData['end_date'] = dates[1];
                        //     }
                        // }

                        // Kirim data filter ke server
                        d.filter = filterData;
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    },
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    }, // Index Column
                    {
                        data: 'nomor_kartu',
                        name: 'nomor_kartu'
                    },
                    {
                        data: 'jumlah_penggunaan',
                        name: 'jumlah_penggunaan'
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    } // Action Column
                ],
                order: [
                    [2, 'desc']
                ], // Default order berdasarkan jumlah_penggunaan DESC
                pageLength: 10,
                responsive: true,
            });

            // --- Event Listener untuk Filter ---

            // Submit form filter
            $('#filter-form-kartu-sering').on('submit', function(e) {
                e.preventDefault(); // Mencegah submit form default
                // Reload DataTable dengan filter baru
                kartuPalingSeringTable.ajax.reload();
            });

            // Reset form filter
            $('#filter-form-kartu-sering').on('reset', function(e) {
                // Beri jeda kecil agar reset terjadi sebelum reload
                setTimeout(function() {
                    // Reload DataTable tanpa filter
                    kartuPalingSeringTable.ajax.reload();
                }, 100);
            });


            // --- Event handler untuk tombol Detail (opsional) ---
            // Diperbarui untuk menggunakan event delegation yang lebih baik
            $('#kartu-paling-sering-table').on('click', '.detail-kartu-btn', function() {
                var noKartu = $(this).data('nokartu');
                if (noKartu) {
                    // Lakukan sesuatu, misal redirect atau tampilkan modal
                    // alert('Detail untuk kartu: ' + noKartu); // <-- Baris yang diubah
                    Swal.fire({
                        title: 'Detail Kartu',
                        text: 'Detail untuk kartu: ' + noKartu,
                        icon: 'info',
                        confirmButtonText: 'OK'
                    });
                    // Contoh redirect:
                    // window.location.href = '/detail-kartu/' + encodeURIComponent(noKartu);
                    // Contoh tampilkan modal (pastikan modal ada di HTML):
                    // $('#detailKartuModal').modal('show');
                    // Dan isi modal dengan data AJAX berdasarkan noKartu
                } else {
                    console.warn('No Kartu tidak ditemukan pada tombol detail.');
                    Swal.fire({
                        title: 'Error!',
                        text: 'Nomor kartu tidak ditemukan.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
    </script>
@endpush
