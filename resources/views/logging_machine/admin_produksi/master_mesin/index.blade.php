@extends('layouts.base-display')

@section('title', 'FORM INPUT MASTER MESIN')

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
                            <div class="row">
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
                            </div>
                            <div id="import">
                                <div class="row">
                                    <form action="{{url('/adm_prod/import_master_mesin')}}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="exampleFormControlInput1">Pilih File</label>
                                                <input accept=".xlsx" type="file" name="file" class="form-control">
                                            </div>
                                            <div class="col-sm-12">
                                                <a href="/master_import/MASTER_DATA_MESIN_S2.xlsx"
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
                                <form action="{{ url('/logging_machine/adm_prod/post_master_mesin')}} " method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleFormControlSelect1">Line</label>
                                                <select class="form-control" name="line" required>
                                                    <option disabled selected>Silahkan Pilih</option>
                                                    @for  ($i = 1; $i <= 12; $i++ )
                                                        <option value="{{ $i }}">{{ $i }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="exampleFormControlSelect1">Group</label>
                                                <select class="form-control" name="group" required>
                                                    <option disabled selected>Silahkan Pilih</option>
                                                    @foreach ($group as $item)
                                                        <option value="{{ $item->group }}">{{ $item->group }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleFormControlInput1">No. Mesin</label>
                                                <input type="text" name="no_mesin" class="form-control"
                                                    placeholder="Masukan No. Mesin">
                                            </div>
                                            <div class="form-group">
                                                <label for="exampleFormControlInput1">NoSeq</label>
                                                <input type="number" name="NoSeq" class="form-control"
                                                    placeholder="Masukan No. Seq Counter">
                                            </div>
                                           
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleFormControlInput1">Jenis Mesin</label>
                                                <select class="form-control" name="jenis_mesin">
                                                    <option disabled selected>Silahkan Pilih</option>
                                                    <option value="packing">Packing</option>
                                                    <option value="proses">Proses</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleFormControlInput1">WorkCenter</label>
                                                <input type="text" name="workcenter" class="form-control"
                                                    placeholder="Masukan Workcenter">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="float-right">
                                        <button type="submit" class="btn btn-primary mb-2 btn-block"
                                            style="border-radius: 8px"><i class="fas fa-save"></i> Simpan</button>
                                    </div>
                                </form>
                                <hr>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Line</th>
                                                    <th class="text-center">Group</th>
                                                    <th class="text-center">Kode Mesin</th>
                                                    <th class="text-center">WorkCenter</th>
                                                    <th class="text-center">NoSeq</th>
                                                    <th class="text-center">Opsi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($mesin as $list)
                                                    <tr>
                                                        <td class="text-center">{{ $list->line }}</td>
                                                        <td class="text-center">{{ $list->group }}</td>
                                                        <td class="text-center">{{ $list->no_mesin }}</td>
                                                        <td class="text-center">{{ $list->workcenter }}</td>
                                                        <td class="text-center">{{ $list->NoSeq }}</td>
                                                        <td class="text-center">
                                                            <a href="/logging_machine/adm_prod/get_master_mesin/{{ $list->id }}"
                                                                class="btn btn-info btn-sm" style="border-radius: 7px"><i
                                                                    class="fas fa-edit"></i> Edit</a>
                                                            <a href="/logging_machine/adm_prod/delete_master_mesin/{{ $list->id }}"
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
    </div>





@endsection

@push('scripts')
    <script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>

    <script type="text/javascript">
        $('.table').DataTable();

        $('.Varian').select2();
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

    </script>

@endpush
