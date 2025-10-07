@extends('layouts.base')

@push('styles')
    <link rel="stylesheet" href="{{ url('/assets/plugins/custom/datatables/datatables.bundle.css') }}">
@endpush

@section('content')
    <div class="container-fluid">

        <!--begin::Row-->
        <div class="row">
            <!-- Import Excel -->
            <div class="modal fade" id="importExcel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <form method="post" action="/klinik/master/import_excel" enctype="multipart/form-data" id="import">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Tambah Data Obat</h5>
                            </div>
                            <div class="modal-body">

                                {{ csrf_field() }}


                                <div class="modal-body">
                                    <label>Pilih file excel</label>
                                    <div class="form-group">
                                        <label for=""></label>
                                        <input type="file" class="form-control-file" name="excel" id=""
                                            placeholder="Masukan" aria-describedby="fileHelpId">
                                    </div>
                                    <a href="{{ url('/master_import/UPLOAD_DATA_OBAT.xlsx') }}"
                                        class="btn btn-sm text-white mt-4"
                                        style="background-color: green; border-radius: 15px;"><i
                                            class="fas fa-file-excel text-white"></i> Download Master Excel</a>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary btn-sm BtnUpdateFile"
                                    style="border-radius: 13px;"><i class="fas fa-save"></i>Import</button>
                                <button type="button"
                                    class="btn btn-info spinner spinner-darker-info spinner-left mr-3 Proses"
                                    style="display: none;" disabled>
                                    Proses Import..
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card card-custom">
                    <div class="card-header flex-wrap border-0 pt-6 pb-0">
                        <div class="card-title">
                            <h3 class="card-label">Master Obat
                                <span class="d-block text-muted pt-2 font-size-sm">Master Obat</span>
                            </h3>
                        </div>
                        <div>
                            <button type="button" class="btn btn-primary mr-5" data-toggle="modal"
                                data-target="#importExcel">
                                <i class="la la-file-download"></i>Tambah Data Obat
                            </button>
                        </div>


                    </div>

                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger" style="border-radius: 13px;">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                    <button type="button" class="close text-white" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                </ul>
                            </div>
                        @endif
                        <table class="table table-bordered table-hover table-thin">
                            <thead>
                                <tr>
                                    <th>NO</th>
                                    <th>OBAT</th>
                                    <th>HARGA</th>
                                    <th>SATUAN</th>
                                    <th>ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($obat as $key => $o)
                                    <tr>
                                        <td>{{ $loop->iteration }}.</td>
                                        <td>{{ $o->nama_obat }}</td>
                                        <td class="text-right">Rp. {{ number_format($o->harga, 0, ',', '.') }}</td>
                                        <td class="text-right">{{ $o->satuan }}</td>
                                        <td>

                                            <a href="{{ url('/klinik/master/edit/' . $o->id) }}"class="btn btn-sm text-white"
                                                style="background-color: rgb(35, 69, 207); border-radius: 15px;">Edit</a>
                                            |
                                            <a href="{{ url('/klinik/Delete/' . $o->id) }}"class="btn btn-sm text-white"
                                                style="background-color: rgb(197, 12, 12); border-radius: 15px;"
                                                onclick="return confirm('Apakah Anda Yakin Akan Menghapus Data Ini?');">Delete</a>
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Row-->
        <!--end::Dashboard-->


    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
    </script>

    <script>
        $('#import').on('submit', function() {
            $('.BtnUpdateFile').hide()
            $('.spinner').show()
        })
    </script>
    <script>
        $('#update').on('submit', function() {
            $('.BtnUpdateFile').hide()
            $('.spinner').show()
        })
    </script>
@endpush
