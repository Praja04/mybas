@extends('pos-security.layouts.base')

@section('title', 'Manajemen Kartu Aktif')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Daftar Kartu Sedang Digunakan</h5>
                            <p class="text-muted mb-0">Kartu yang belum dikembalikan atau belum checkout secara sistem.</p>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="hotReload()">
                            <i class="ri-refresh-line"></i> Refresh
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="active-cards-table" class="table nowrap align-middle" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>ID Visitor</th>
                                        <th>Nama Pengunjung</th>
                                        <th>Perusahaan</th>
                                        <th>No Kartu</th>
                                        <th>Waktu Masuk</th>
                                        <th>Kategori</th>
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
    <script>
        $(document).ready(function() {
            var table = $('#active-cards-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: API_DATATABLE_ACTIVE_LIST_KARTU,
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'trnvisitorid', name: 'trnvisitorid' },
                    { data: 'namavisitor', name: 'namavisitor' },
                    { data: 'namacomp', name: 'namacomp' },
                    { data: 'no_kartu', name: 'no_kartu' },
                    { data: 'datein', name: 'datein' },
                    { 
                        data: 'type_visitor', 
                        name: 'type_visitor',
                        searchable: false,
                        render: function(data) {
                            let badgeClass = data === 'supplier' ? 'bg-info' : 'bg-primary';
                            return `<span class="badge ${badgeClass}">${data.toUpperCase()}</span>`;
                        }
                    },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                language: {
                    emptyTable: "Tidak ada kartu yang sedang aktif (nyangkut)."
                }
            });

            $(document).on('click', '.btn-reset-kartu', function() {
                const id = $(this).data('id');
                const type = $(this).data('type');
                const nama = $(this).data('nama');
                const kartu = $(this).data('kartu');

                Swal.fire({
                    title: 'Reset Kartu?',
                    text: `Apakah Anda yakin ingin mereset kartu ${kartu} yang digunakan oleh ${nama}? Status kartu akan menjadi 'Kembali' dan transaksi ditutup.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Reset!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: API_RESET_KARTU,
                            method: 'POST',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                trnvisitorid: id,
                                type: type
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Berhasil', response.message, 'success');
                                    table.ajax.reload();
                                } else {
                                    Swal.fire('Gagal', response.message, 'error');
                                }
                            },
                            error: function(xhr) {
                                Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
                            }
                        });
                    }
                });
            });
        });

        function hotReload() {
            window.location.reload();
        }
    </script>
@endpush
