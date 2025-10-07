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
                                            FORM PENGIRIMAN BARANG
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="example-search-input" class="col-sm-2 col-form-label">Dept Pengirim
                                    </label>
                                    <div class="col-sm-10">
                                        <input class="form-control" type="text" readonly name="dept_pengirim"
                                            id="" value="{{ $dept }}" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="example-search-input" class="col-sm-2 col-form-label">Nama Penerima
                                    </label>
                                    <div class="col-sm-10">
                                        <input class="form-control" type="text" name="nama_penerima" id=""
                                            value="" placeholder="Masukan Nama Penerima" />
                                        <small class="form-text text-muted">*Opsional</small>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="example-email-input" class="col-sm-2 col-form-label">Nama PT
                                        Penerima</label>
                                    <div class="col-sm-10">
                                        <input class="form-control" type="text" name="nama_pt_penerima" id=""
                                            required placeholder="Masukan Nama PT Pengirim" />
                                        <small class="form-text text-danger">*Wajib</small>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="example-search-input" class="col-sm-2 col-form-label">Tanggal Pengiriman
                                    </label>
                                    <div class="col-sm-10">
                                        <input class="form-control" type="date" name="tanggal_pengiriman" id=""
                                            value="{{ date('Y-m-d') }}" />
                                    </div>
                                </div>
                                <div class=" form-group row">
                                    <select class="form-control" name="jenis" id="" required>
                                        <option value="" selected disabled>JENIS BARANG</option>
                                        <option value="Barang">Barang</option>
                                        <option value="Dokumen">Dokumen</option>
                                    </select>
                                </div>
                                <div class="form-group row">
                                    <label for="">Kerangan</label>
                                    <textarea class="form-control" name="keterangan" id="" rows="5" placeholder="Keterangan"></textarea>
                                    <small class="form-text text-muted">*Opsional</small>
                                </div>
                                <div class=" form-group row">
                                    <select onchange="changekurir()" class="form-control" name="kurir" id="kurir"
                                        required>
                                        <option value="" selected disabled>PILIH KURIR</option>
                                        {{-- <option value="JNE">JNE</option> --}}
                                        <option value="JNT">JNT</option>
                                        <option value="Messanger PT. PAS">Messanger PT. PAS</option>
                                        <option value="Messanger PT. BAS">Messanger PT. BAS</option>
                                        <option value="Ojek Online">Ojek Online</option>
                                        <option value="Dll">Dll</option>
                                    </select>
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
            if (kurir == 'Dll') {
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
