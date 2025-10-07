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
                            <h3 class="card-label">PME
                                <span class="d-block text-muted pt-2 font-size-sm">Monthly Bill</span>
                            </h3>
                        </div>
                        <div class="card-toolbar">
                            <a href="javascript:" class="btn btn-primary font-weight-bolder" onClick="showModalInputBill()"><i class="fa fa-plus-circle"></i> Input Bill</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2">
                                <select class="form-control" name="filter_plant" id="filter-plant">
                                    <option value="">All Plant</option>
                                    @foreach ($plants as $plant)
                                        <option value="{{ $plant->plant }}">{{ $plant->plant }} - {{ $plant->company_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input placeholder="Select Month" readonly required type="text" class="form-control" name="filter_month" id="filter-month">
                            </div>
                        </div>
                        <hr />
                        <table id="monthly-bill" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 10px">No</th>
                                    <th>Plant</th>
                                    <th>Year</th>
                                    <th>Month</th>
                                    <th>KWH</th>
                                    <th style="width: 60px">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bills as $key => $bill)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>{{ $bill->plant }}</td>
                                        <td>{{ $bill->year }}</td>
                                        <td>{{ $bill->month }}</td>
                                        <td>{{ $bill->kwh }}</td>
                                        <td>
                                            <button onClick="handleDelete('{{ $bill->id }}')" class="btn btn-sm btn-icon btn-danger">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                            <button onClick="handleEdit('{{ $bill->id }}')" class="btn btn-sm btn-icon btn-info">
                                                <i class="fa fa-edit"></i>
                                            </button>
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

    <div class="modal fade" id="create-new-modal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel"><span id="modal-title">Input Monthly Bill</span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" id="create-form">
                        @csrf
                        <div class="form-group">
                            <label for="plant">Plant</label>
                            <div></div>
                            <select required class="form-control" name="plant" id="plant">
                                <option value=""></option>
                                @foreach ($plants as $plant)
                                    <option value="{{ $plant->plant }}">{{ $plant->plant }} - {{ $plant->company_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="month">Month</label>
                                    <div></div>
                                    <input readonly required type="text" class="form-control" name="month" id="month">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="kwh">KWH</label>
                                    <div></div>
                                    <input required type="number" class="form-control" name="kwh" id="kwh">
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success submitButton"><i class="fa fa-paper-plane"></i>Submit</button>
                        <br><br>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="edit-modal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel"><span id="modal-title">Update Monthly Bill</span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" id="edit-form">
                        @csrf
                        <input type="hidden" name="id" id="id">
                        <div class="form-group">
                            <label for="plant">Plant</label>
                            <div></div>
                            <select required class="form-control" name="plant" id="edit-plant">
                                <option value=""></option>
                                @foreach ($plants as $plant)
                                    <option value="{{ $plant->plant }}">{{ $plant->plant }} - {{ $plant->company_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="month">Month</label>
                                    <div></div>
                                    <input readonly required type="text" class="form-control" name="month" id="edit-month">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="kwh">KWH</label>
                                    <div></div>
                                    <input required type="number" class="form-control" name="kwh" id="edit-kwh">
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success submitButton"><i class="fa fa-paper-plane"></i>Submit</button>
                        <br><br>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script type="text/javascript">
        $('#monthly-bill').DataTable();

        function handleEdit(id)
        {
            $('#id').val(id);
            $.ajax({
                url: "{{ url('/pme/monthly-bill/get') }}/"+id,
                type: "GET",
                dataType: "JSON",
                success: function ( response ) {
                    var bill = response.data;
                    $('#edit-plant').val(bill.plant);
                    $('#edit-month').datepicker().datepicker('update', bill.month+'/'+bill.year);
                    // $('#edit-month').val(bill.month+'/'+bill.year).trigger('change');
                    $('#edit-kwh').val(bill.kwh);
                    $('#edit-modal').modal('show');
                },
                error: function ( error ) {
                    Swal.fire('Failed!', 'Get data failed, try again please', 'error');
                }
            })
        }

        function handleDelete(id)
        {
            if(!confirm('This data will deleted permamanently')) {
                return false;
            }
            $.ajax({
                url: "{{ url('/pme/monthly-bill/delete') }}/"+id,
                type: "DELETE",
                dataType: "JSON",
                success: function ( response ) {
                    if(response.success == '1') {
                        Swal.fire('Success!', 'Delete monthly bill succeed', 'success')
                        .then(function () {
                            location.reload();
                        });
                    }else{
                        Swal.fire('Gagal!', 'Failed to delete monthly bill, try again please', 'error');
                    }
                },
                error: function ( error ) {
                    Swal.fire('Gagal!', 'Failed to delete monthly bill, try again please', 'error');
                }
            });
        }

        @isset($_GET['filter_plant'])
        $('#filter-plant').val("{{ $_GET['filter_plant'] }}")
        @endisset

        @isset($_GET['filter_month'])
        $('#filter-month').val("{{ $_GET['filter_month'] }}")
        @endisset

        $('#filter-plant,#filter-month').on('change', function () {
            var current_url = '{!! url()->current() !!}';
            var next_url = current_url+'/?filter_plant='+$('#filter-plant').val()+'&filter_month='+$('#filter-month').val();
            window.location.href = next_url;
        });

        $('#month,#filter-month,#edit-month').datepicker({
            format: 'mm/yyyy',
            viewMode: 'months',
            minViewMode: 'months',
            autoclose: true
        });
        function submiting(state) {
            if(state == true) {
                $('.submitButton i').removeClass('fa-paper-plane');
                $('.submitButton i').addClass('fa-spinner');
                $('.submitButton i').addClass('fa-spin');
                $('.submitButton').attr('disabled', true);
            }else{
                $('.submitButton i').addClass('fa-paper-plane');
                $('.submitButton i').removeClass('fa-spinner');
                $('.submitButton i').removeClass('fa-spin');
                $('.submitButton').removeAttr('disabled');
            }
        }

        $('#edit-form').on('submit', function (e) {
            e.preventDefault();
            submiting(true);
            $.ajax({
                url: "{{ url('pme/monthly-bill/update') }}",
                type: 'PATCH',
                dataType: 'JSON',
                data: $(this).serialize(),
                success: function ( response ) {
                    if(response.success == '1') {
                        Swal.fire('Success!', 'Update monthly bill succeed', 'success')
                        .then(function () {
                            submiting(false);
                            location.reload();
                        });
                    }else{
                        Swal.fire('Failed!', response.message, 'error')
                        .then(function () {
                            submiting(false);
                        });
                    }
                },
                error: function ( e ) {
                    Swal.fire('Failed!', 'Failed to Update monthly bill, try again please', 'error')
                    .then(function () {
                        submiting(false);
                    });
                }
            })
        });

        $('#create-form').on('submit', function (e) {
            e.preventDefault();
            submiting(true);
            $.ajax({
                url: "{{ url('pme/monthly-bill/store') }}",
                type: 'POST',
                dataType: 'JSON',
                data: $(this).serialize(),
                success: function ( response ) {
                    if(response.success == '1') {
                        Swal.fire('Berhasil!', 'Input monthly bill succeed', 'success')
                        .then(function () {
                            submiting(false);
                            location.reload();
                        });
                    }else{
                        Swal.fire('Gagal!', response.message, 'error')
                        .then(function () {
                            submiting(false);
                        });
                    }
                },
                error: function ( e ) {
                    Swal.fire('Gagal!', 'Failed to input monthly bill, try again please', 'error')
                    .then(function () {
                        submiting(false);
                    });
                }
            })
        });

        function showModalInputBill()
        {
            $('#create-new-modal').modal('show');
        }

    </script>

@endpush
