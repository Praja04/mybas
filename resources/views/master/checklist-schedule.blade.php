@extends('layouts.base')

@push('styles')
    <link rel="stylesheet" href="{{ url('/assets/plugins/custom/datatables/datatables.bundle.css') }}">
@endpush

@section('content')

    <div class="container-fluid">

        <!--begin::Row-->
        <div class="row">

            <div class="col-lg-12">
                <div class="card card-custom">
                    <div class="card-header flex-wrap border-0 pt-6 pb-0">
                        <div class="card-title">
                            <h3 class="card-label">Master Schedule Checklist
                            <span class="d-block text-muted pt-2 font-size-sm">Manage all checklist schedules</span></h3>
                                <a href="javascript:" class="btn btn-primary font-weight-bolder" onClick="showModalCreateNew()"><i class="fa fa-plus-circle"></i> Create New</a>
                        </div>
                        
                    </div>
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Dept</th>
                                    <th>Jenis Asset</th>
                                    <th>Year</th>
                                    <th>Month</th>
                                    <th>Week</th>
                                    <th>Jumlah Item</th>
                                    <th>Persentase Cek</th>
                                    <th><i class="fa fa-tools text-dark-75"></i></th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($schedules as $key => $schedule)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ $schedule->department != null ? $schedule->department->name : '' }}</td>
                                    <td>{{ $schedule->jenis_asset != null ? $schedule->jenis_asset->name : '' }}</td>
                                    <td>{{ $schedule->year }}</td>
                                    <td>{{ getMonth((int)$schedule->month-1) }}</td>
                                    <td>{{ $schedule->week }}</td>
                                    <td>{{ $schedule->asset_count }}</td>
                                    <td></td>
                                    <td>
                                        <a href="javascript:"><i class="fa fa-edit text-primary text-hover-dark"></i></a>
                                        <a href="javascript:"><i class="fa fa-trash text-primary text-hover-dark"></i></a>
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

    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="formCreateSchedule">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel"><span id="modal-title"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label  class="col-3 col-form-label" for="pilih-jenis-asset">Jenis Asset</label>
                        <div class="col-9">
                            <select name="jenis_asset" id="pilih-jenis-asset" class="form-control">
                                <option value="">Pilih Jenis Asset</option>
                                @foreach($jenis_assets as $jenis_asset)
                                    <option value="{{ $jenis_asset->id }}">{{ $jenis_asset->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label  class="col-3 col-form-label" for="pilih-department">Department</label>
                        <div class="col-9">
                            <select required name="department" id="pilih-department" class="form-control">
                                <option value="">Pilih Department</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label  class="col-3 col-form-label" for="pilih-tahun">Tahun</label>
                        <div class="col-9">
                            <input required type="text" class="form-control datepicker-year" autocomplete="off" id="pilih-tahun" name="tahun">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label  class="col-3 col-form-label" for="pilih-bulan">Bulan</label>
                        <div class="col-9">
                            <select required name="bulan" id="pilih-bulan" class="form-control">
                                <option value="">Pilih Bulan</option>
                                <option value="01">Januari</option>
                                <option value="02">Februari</option>
                                <option value="03">Maret</option>
                                <option value="04">April</option>
                                <option value="05">Mei</option>
                                <option value="06">Juni</option>
                                <option value="07">Juli</option>
                                <option value="08">Agustus</option>
                                <option value="09">September</option>
                                <option value="10">Oktober</option>
                                <option value="11">November</option>
                                <option value="12">Desember</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label  class="col-3 col-form-label" for="pilih-minggu">Minggu Ke</label>
                        <div class="col-9">
                            <select name="minggu" id="pilih-minggu" class="form-control">
                                <option value="">Minggu ke</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary font-weight-bold" id="submitButton">Save</button>
                    <button type="button" class="btn btn-default font-weight-bold" data-dismiss="modal">Close</button>
                </div>
            </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script type="text/javascript">
        $('.table').DataTable({
            "paging" : false
        });

        $('#pilih-department').select2();
        $('#pilih-jenis-asset').select2();
        $('#pilih-bulan').select2();
        $('#pilih-minggu').select2();

        function showModalCreateNew()
        {
            $('#modal-title').text('Create New');
            $('#modal').modal('show');
        }

        $('#formCreateSchedule').on('submit', function(e) {
            e.preventDefault();
            $('#submitButton').attr('disabled', true);
            $('#submitButton').text('Processing..');
            var data = $(this).serialize();
            $.ajax({
                url: "{{ url('/') }}/master/checklist_schedule/store",
                data: data,
                type: "POST",
                dataType: "JSON",
                success: function ( response ) {
                    $('#modal').modal('hide');
                    Swal.fire("Oke!", "Data berhasil disimpan!", "success")
                        .then((value) => {
                            $('#submitButton').removeAttr('disabled');
                            $('#submitButton').text('Submit');
                            location.reload();
                        });
                },
                error: function ( e ) {
                    // console.log( e );
                    Swal.fire("Gagal!", "Mohon coba lagi!", "error");
                    $('#submitButton').removeAttr('disabled');
                    $('#submitButton').text('Submit');
                }
            });
        });
    </script>

@endpush
