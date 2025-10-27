@extends('layouts.base-sidebar')

@push('styles')
    <style type="text/css">
        .hide {
            display: none;
        }

        .message {
            transition-duration: 0.7ms;
        }

        .fixTableHead {
            overflow-y: auto;
            height: 400px;
        }

        .fixTableHead thead th {
            position: sticky;
            top: 0;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            padding: 8px 15px;
        }

        th {
            background: #dbdbdb;
        }
    </style>
@endpush

@section('content')
    <div class="container">
        <div class="main-body">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card card-custom">
                        <form action="{{ url('edoc/post_pengiriman') }}" method="POST" enctype="multipart/form-data"
                            id="PostKedatanganForm">
                            @csrf
                            <div class="card-body">
                                <div class="form-group mb-8">
                                    <div class="alert alert-custom alert-default" role="alert">
                                        <div class="alert-icon"><i class="flaticon-warning text-primary"></i></div>
                                        <div class="alert-text">
                                            <h3> FORM PENGIRIMAN BARANG
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="dept-pengirim" class="col-sm-2 col-form-label">Departemen Pengirim
                                    </label>
                                    <div class="col-sm-10">
                                        <input class="form-control" type="text" readonly name="dept_pengirim"
                                            id="dept-pengirim" value="{{ $dept }}" required />
                                        <small class="form-text text-danger">*Wajib</small>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="nama-penerima" class="col-sm-2 col-form-label">Nama Penerima
                                    </label>
                                    <div class="col-sm-10">
                                        <input class="form-control" type="text" name="nama_penerima" id="nama-penerima"
                                            placeholder="Masukan Nama Penerima" required />
                                        <small class="form-text text-danger">*Wajib</small>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="nama-pt-penerima" class="col-sm-2 col-form-label">Nama PT
                                        Penerima</label>
                                    <div class="col-sm-10">
                                        <input class="form-control" type="text" name="nama_pt_penerima"
                                            id="nama-pt-penerima" required placeholder="Masukan Nama PT Penerima" />
                                        <small class="form-text text-danger">*Wajib</small>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="tanggal-pengiriman" class="col-sm-2 col-form-label">Tanggal Pengiriman
                                    </label>
                                    <div class="col-sm-10">
                                        <input class="form-control" type="date" name="tanggal_pengiriman"
                                            id="tanggal-pengiriman" value="{{ date('Y-m-d') }}" required />
                                        <small class="form-text text-danger">*Wajib</small>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="jenis" class="col-sm-2 col-form-label">Jenis Barang/Dokumen</label>
                                    <div class="col-sm-10">
                                        <select class="form-control" name="jenis" id="jenis" required>
                                            <option value="" selected disabled>Pilih Jenis</option>
                                            <option value="Barang">Barang</option>
                                            <option value="Dokumen">Dokumen</option>
                                        </select>
                                        <small class="form-text text-danger">*Wajib</small>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="keterangan" class="col-sm-2 col-form-label">Keterangan</label>
                                    <div class="col-sm-10">
                                        <textarea class="form-control" name="keterangan" id="keterangan" rows="5" placeholder="Masukan keterangan"></textarea>
                                        <small class="form-text text-muted">*Opsional</small>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="keterangan" class="col-sm-2 col-form-label">Kurir</label>
                                    <div class="col-sm-10">
                                        <select onchange="changekurir()" class="form-control" name="kurir" id="kurir"
                                            required>
                                            <option value="" selected disabled>Pilih Kurir</option>
                                            {{-- <option value="JNE">JNE</option> --}}
                                            <option value="JNT">JNT</option>
                                            <option value="Messanger PT. PAS">Messanger PT. PAS</option>
                                            <option value="Messanger PT. BAS">Messanger PT. BAS</option>
                                            <option value="Ojek Online">Ojek Online</option>
                                            <option value="Lainnya">Lainnya</option>
                                        </select>
                                        <small class="form-text text-danger">*Wajib</small>
                                    </div>
                                </div>
                                <div class="form-group row" id="kurir-lain-container">
                                </div>
                                <div class="float-right mb-4">
                                    <button type="submit" class="btn btn-info btn-lg mr-2 SavePengiriman"
                                        style="border-radius: 10px;">
                                        Simpan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        $('#PostKedatanganForm').on('submit', function() {
            $('.SavePengiriman').attr('disabled', 'true');
            $('.SavePengiriman').addClass('spinner spinner-left pl-15')
        });

        function changekurir() {
            var kurir = $('#kurir').val()
            if (kurir == 'Lainnya') {
                $('#kurir-lain-container').html(`
                    <label for="example-search-input" class="col-sm-2 col-form-label">Kurir Lain</label>
                    <div class="col-sm-10">
                        <input required class="form-control" type="text" name="kurir_lain" placeholder="Nama kurir lain" />
                        <small class="text-danger">*Wajib diisi</small>
                    </div>
                `)
            } else {
                $('#kurir-lain-container').html('')
            }
        }
    </script>
@endpush
