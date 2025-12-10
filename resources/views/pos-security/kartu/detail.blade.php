@extends('pos-security.layouts.base')

@section('content')
    <div class="container-fluid">

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
                <div class="card shadow-sm">
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div>
                            <h5 class="card-title mb-0">Riwayat Transaksi Kartu</h5>
                            <small class="text-muted">Menampilkan catatan masuk/keluar 7 hari terakhir</small>
                        </div>
                        <div class="d-flex flex-wrap gap-1">
                            <a href="{{ route('pos-security.kartu-aktif') }}" class="btn btn-sm btn-primary">
                                <i class="mdi mdi-arrow-left"></i> Kembali
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="hotReload()">
                                <i class="mdi mdi-refresh"></i> Refresh
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table
                                class="ga-history-vendor-pas-datatables table table-striped table-bordered nowrap align-middle"
                                style="width:100%">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Perusahaan</th>
                                        <th>Nama Visitor</th> {{-- Note: 'host' data used, maybe 'namavisitor' is better? --}}
                                        <th>Departemen</th>
                                        <th>No. Kartu / RFID</th> {{-- Now maps to 'rfidpass' --}}
                                        <th>No. Identitas</th> {{-- Now maps to 'no_ktp_sim' --}}
                                        <th>Jenis Kartu</th> {{-- Now maps to 'type' --}}
                                        <th>Foto Diri</th>
                                        <th>Foto Identitas</th>
                                        <th>Waktu Masuk</th>
                                        <th>Waktu Keluar</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        function showImageModal(imageUrl) {
            // Prevent multiple definitions, keep one instance
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

        function getNomorKartuFromUrl() {
            const parts = window.location.pathname.split('/');
            return parts[parts.length - 1];
        }

        $(document).ready(function() {
            const table = $('.ga-history-vendor-pas-datatables').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('datatable.pos-security.kartu-aktif.detail.index') }}",
                    data: function(d) {
                        const form = $('#filter-form').serializeArray();
                        form.forEach(field => {
                            d[field.name] = field.value;
                        });
                        d.nomor_kartu = getNomorKartuFromUrl();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex', // Correct
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'namacomp', // Correct
                        name: 'namacomp'
                    },
                    {
                        data: 'host', // This seems to be the visitor's name based on data, but header says 'Nama Visitor'. The host is in 'host'. Data has 'namavisitor' which might be better for 'Nama Visitor'.
                        name: 'host'
                    },
                    {
                        data: 'hostdeptid', // Correct
                        name: 'hostdeptid'
                    },
                    {
                        // --- FIX HERE ---
                        // Original: data: 'rfid' -> Error because 'rfid' doesn't exist
                        // Use the correct field name from JSON, e.g., 'rfidpass'
                        // You might want logic to choose between rfidpass, rfidemp, passcardid etc.
                        data: 'no_kartu', // Or 'passcardid' or a custom render function
                        name: 'no_kartu', // Match the data property or use the original alias if backend maps it
                        defaultContent: '-', // Show '-' if the property is null/undefined
                        render: function(data, type, row) {
                            // Example: Fallback logic if needed
                            // return data || row.passcardid || row.rfidemp || '-';
                            return data || '-'; // Just show rfidpass or '-'
                        }
                    },
                    {
                        // --- FIX HERE ---
                        // Original: data: 'no_identitas' -> Error because 'no_identitas' doesn't exist
                        data: 'no_ktp_sim', // Use the correct field name from JSON
                        name: 'no_ktp_sim', // Match the data property
                        defaultContent: '-'
                    },
                    {
                        // --- FIX HERE ---
                        // Original: data: 'jenis_kartu' -> Error because 'jenis_kartu' doesn't exist
                        data: 'type', // Use the correct field name from JSON
                        name: 'type', // Match the data property
                        defaultContent: '-'
                    },
                    {
                        data: 'photo_visitor', // This seems to be pre-formatted HTML
                        name: 'photo_visitor',
                        // Consider if the server-side data is already HTML or if you need to build it here
                        // If it's already HTML like in your data sample, maybe no render needed or just return data
                        render: function(data, type, row) {
                            // If data is already HTML string, just return it.
                            // The onclick handler should be in the HTML string from the server.
                            // Your current server data seems to include the onclick.
                            if (data && type === 'display') {
                                // If data is HTML string, return as is. DataTables will inject it.
                                // Ensure the HTML from server is safe.
                                return data;
                            }
                            return '-'; // Fallback for non-display types or missing data
                        },
                        orderable: false, // Usually images aren't ordered
                        searchable: false
                    },
                    {
                        data: 'img_visitor', // This also seems to be pre-formatted HTML
                        name: 'img_visitor',
                        render: function(data, type, row) {
                            if (data && type === 'display') {
                                return data;
                            }
                            return '-';
                        },
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'waktu_masuk', // Correct if server sends HTML
                        name: 'waktu_masuk',
                        render: function(data, type, row) {
                            if (data && type === 'display') {
                                return data; // Return pre-formatted HTML
                            }
                            // Optionally format date/time if data is raw
                            // return row.datein ? moment(row.datein).format('DD-MM-YYYY') : '-';
                            return '-'; // Fallback
                        }
                    },
                    {
                        data: 'waktu_keluar', // Correct if server sends HTML
                        name: 'waktu_keluar',
                        render: function(data, type, row) {
                            if (data && type === 'display') {
                                return data; // Return pre-formatted HTML
                            }
                            return '-'; // Fallback (e.g., if null)
                        }
                    }
                ],
                // Optional: Handle the case where the whole row data might be missing expected keys more gracefully
                // This is less common if server strictly follows the column definition keys
                // createdRow: function(row, data, dataIndex) {
                //    // Example: Add class if needed based on data
                // }
            });

            $('#filter-form').on('submit', function(e) {
                e.preventDefault();
                table.ajax.reload();
            });

            $('#filter-form').on('reset', function() {
                // Clear any custom inputs not handled by serializeArray if needed
                setTimeout(() => {
                    table.ajax.reload();
                }, 200);
            });
        });
    </script>
@endpush
