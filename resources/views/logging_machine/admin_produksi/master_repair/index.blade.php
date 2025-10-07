@extends('layouts.base-display')

@section('title', 'FORM INPUT MASTER REPAIR MESIN')

    @push('styles')
        <style type="text/css">
            .hide {
                display: none;
            }

            .message {
                transition-duration: 0.7ms;
            }

        </style>
    @endpush

@section('content')

    <div class="container">
        <div class="main-body">


            <div class="row gutters-sm">
                <div class="col-md-12 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <a href="/logging_machine/adm_prod" class="btn btn-danger btn-sm mb-3"
                                style="border-radius: 10px"> <i class="fas fa-arrow-left"></i> Kembali</a>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="exampleFormControlInput1">Jenis Import Data</label>
                                    <select class="form-control jenis_import">
                                        <option disabled selected>Silahkan Pilih</option>
                                        <option value="1">Import Excel</option>
                                        <option value="2">Manual</option>
                                    </select>
                                </div>
                            </div>

                            <div id="import">
                                <div class="row">
                                    <form action="{{url('/adm_prod/import_master_repair')}}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="exampleFormControlInput1">Pilih File</label>
                                                <input accept=".xlsx" type="file" name="file" class="form-control">
                                            </div>
                                            <div class="col-sm-12">
                                                <a href="/master_import/MASTER_REPAIR_S2.xlsx"
                                                    class="btn mb-2 btn-block text-white"
                                                    style="border-radius: 8px; background-color: green"><i
                                                        class="fas fa-file-excel text-white"></i> Download Master Excel</a>
                                                <button type="submit" class="btn btn-primary mb-2 btn-block"
                                                    style="border-radius: 8px"><i class="fas fa-save"></i> Simpan</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>


                            <div id="manual">
                                <form action="/logging_machine/adm_prod/post_master_mesin_repair" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="exampleFormControlInput1"> Jenis Mesin </label>
                                                <select class="form-control" name="jenis_mesin">
                                                    <option disabled selected>Silahkan Pilih</option>
                                                    <option value="packing"> Packing</option>
                                                    <option value="proses"> Proses</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="exampleFormControlInput1"> No. Mesin </label>
                                                <select class="form-control nomesin" name="no_mesin">
                                                    <option disabled selected>Silahkan Pilih</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <label for="exampleFormControlInput1">Kategori</label>
                                            <select class="form-control" required name="kategori">
                                                <option disabled selected>Silahkan Pilih</option>
                                                <option value="Operator">Operator</option>
                                                <option value="Engineering">Engineering</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="exampleFormControlInput1"> Reason </label>
                                                <select class="form-control" name="reason">
                                                    <option disabled selected>Silahkan Pilih</option>
                                                    @foreach ($reason as $item)
                                                        <option value="{{ $item->reason }}">{{ $item->reason }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="exampleFormControlInput1"> Repair </label>
                                                <textarea class="form-control" name="repair"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="float-right">
                                        <button type="submit" class="btn btn-primary mb-2 btn-block"
                                            style="border-radius: 8px"><i class="fas fa-save"></i> Simpan</button>
                                    </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">No.</th>
                                                    <th class="text-center">Jenis Mesin</th>
                                                    <th class="text-center">Kode Mesin</th>
                                                    <th class="text-center">Reason</th>
                                                    <th class="text-center">Repair</th>
                                                    <th class="text-center">Kategori</th>
                                                    <th class="text-center">Opsi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($repair as $list)
                                                    <tr>
                                                        <td class="text-center">{{ $loop->iteration }}</td>
                                                        <td class="text-center">{{ $list->jenis_mesin }}</td>
                                                        <td class="text-center">{{ $list->no_mesin }}</td>
                                                        <td class="text-center">{{ $list->reason }}</td>
                                                        <td class="text-center">{{ $list->repair }}</td>
                                                        <td class="text-center">{{ $list->kategori }}</td>
                                                        <td class="text-center">
                                                            <a href="/logging_machine/adm_prod/get_master_mesin_repair/{{ $list->id }}/{{ $list->no_mesin }}"
                                                                class="btn btn-info btn-sm" style="border-radius: 7px"><i
                                                                    class="fas fa-edit"></i> Edit</a>
                                                            <a href="/logging_machine/adm_prod/delete_master_mesin_repair/{{ $list->id }}"
                                                                class="btn btn-primary btn-sm" style="border-radius: 7px"><i
                                                                    class="fas fa-trash"></i> Hapus</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>





    @endsection

    @push('scripts')
        <script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>

        <script type="text/javascript">
            $('.table').DataTable();

            $('.Varian').select2();

            $('.nomesin').select2();

            $('#import').hide();
            $('#manual').hide();

            $('.jenis_import').on('change', function() {
                if (this.value == '1') {
                    $("#import").show();
                } else {
                    $("#import").hide();
                }
            });

            $('.jenis_import').on('change', function() {
                if (this.value == '2') {
                    $("#manual").show();
                } else {
                    $("#manual").hide();
                }
            });

            jQuery(document).ready(function() {
                jQuery('select[name="jenis_mesin"]').on('change', function() {
                    var jenis_mesin = this.value;
                    if (jenis_mesin == 'packing') {
                        jQuery.ajax({
                            url: '/logging_machine/get_kategori/' + jenis_mesin,
                            type: "GET",
                            data: {
                                jenis_mesin: jenis_mesin
                            },
                            dataType: "json",
                            success: function(data) {
                                console.log(data);
                                jQuery('select[name="no_mesin"]').empty();
                                jQuery.each(data, function(id, value) {
                                    $('select[name="no_mesin"]').append(
                                        '<option value="' + value.no_mesin + '">' +
                                        value.no_mesin + '</option>');
                                });
                            }
                        });
                    } else if (jenis_mesin == 'proses') {
                        jQuery.ajax({
                            url: '/logging_machine/get_kategori/' + jenis_mesin,
                            type: "GET",
                            data: {
                                jenis_mesin: jenis_mesin
                            },
                            dataType: "json",
                            success: function(data) {
                                console.log(data);
                                jQuery('select[name="no_mesin"]').empty();
                                jQuery.each(data, function(id, value) {
                                    $('select[name="no_mesin"]').append(
                                        '<option value="' + value.no_mesin + '">' +
                                        value.no_mesin + '</option>');
                                });
                            }
                        });
                    }
                });
            });

        </script>

    @endpush
