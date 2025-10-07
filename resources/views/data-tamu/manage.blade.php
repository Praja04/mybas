@extends('layouts.base')

@push('styles')
    <link rel="stylesheet" href="{{ url('/assets/plugins/custom/datatables/datatables.bundle.css') }}">
    <style type="text/css">
        #table-data-tamu > thead > tr > th, #table-data-tamu > tbody > tr > td {
            white-space: nowrap;
            padding: 5px;
            padding-right: 20px;
        }
    </style>
@endpush

@section('content')

    <div class="container-fluid">

        <!--begin::Row-->
        <div class="row">

            <div class="col-lg-12">
                <div class="card card-custom">
                    <div class="card-header flex-wrap border-0 pt-6 pb-0">
                        <div class="card-title">
                            <h3 class="card-label">Data Tamu
                                <span class="d-block text-muted pt-2 font-size-sm">Data tamu yang berencana datang ke PT. PAS</span></h3>
                        </div>
                        <div class="card-toolbar">
                            <a href="javascript:" class="btn btn-primary font-weight-bolder" onClick="showModalCreateNew()"><i class="fa fa-plus-circle"></i> Upload New</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive pb-5">
                            <table class="table table-hover table-bordered" id="table-data-tamu">
                                <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th>Nama Lengkap</th>
                                    <th>Nama Instansi</th>
                                    <th>Jenis Kunjungan</th>
                                    <th>No Identitas</th>
                                    <th style="width: 70px">Tanggal</th>
                                    <th>Bertemu Dengan</th>
                                    <th>Hasil</th>
                                    <th>4</th>
                                    <th>5</th>
                                    <th>6</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($data_tamu as $key => $data)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>{{ $data->nama }}</td>
                                        <td>{{ $data->nama_instansi }}</td>
                                        <td>{{ $data->jenis_kunjungan }}</td>
                                        <td>{{ $data->no_identitas }}</td>
                                        <td data-sort="{{ strtotime($data->tanggal) }}">{{ @formatTanggalIndonesia2($data->tanggal) }}</td>
                                        <td>{{ $data->bertemu_dengan }}</td>
                                        <td>
                                            @if($data->jawaban_pertanyaan_4 == 'Ya' || $data->jawaban_pertanyaan_5 == 'Ya' || $data->jawaban_pertanyaan_6 == 'Ya')
                                                <span class="label label-inline label-danger" style="white-space: nowrap">Tidak Boleh</span>
                                            @else
                                                <span class="label label-inline label-success" style="white-space: nowrap">Boleh</span>
                                            @endif
                                        </td>
                                        <td>{{ $data->jawaban_pertanyaan_4 }}</td>
                                        <td>{{ $data->jawaban_pertanyaan_5 }}</td>
                                        <td>{{ $data->jawaban_pertanyaan_6 }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Row-->
        <!--end::Dashboard-->
    </div>

    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel"><span id="modal-title"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ url('/hr/data-tamu/upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label>File Data Tamu</label>
                            <div></div>
                            <div class="custom-file">
                                <input accept=".csv" required name="file" type="file" class="custom-file-input" id="customFile">
                                <label class="custom-file-label" for="customFile">Choose file</label>
                            </div>
                        </div>
                        <button id="submitButton" type="submit" class="btn btn-success"><i class="fa fa-paper-plane"></i>Submit</button>
                        <br><br>
                        <a href="{{ url('/templates/contoh-data.csv') }}" class="right-0 float-right hover-opacity-80"><i class="fa fa-download"></i> Contoh Data</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script type="text/javascript">

        $('.custom-file-input').on('change', function() {
            var fileName = $(this).val();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });

        $('.table').DataTable();

        function showModalCreateNew()
        {
            $('#modal-title').text('Upload New Data');
            $('#modal').modal('show');
        }

        $('#submitButton').on('click', function () {
            $(this).html('<i class="fa fa-spinner fa-spin"></i> Submiting...');
            setTimeout(function () {
                $('#submitButton').html('<i class="fa fa-paper-plane"></i> Submit');
            }, 1000)
            // $(this).attr('disabled', true);
        })
    </script>

@endpush
