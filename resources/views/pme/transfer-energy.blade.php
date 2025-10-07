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
                                <span class="d-block text-muted pt-2 font-size-sm">Transfer Energy</span>
                            </h3>
                        </div>
                        <div class="card-toolbar">
                            <a href="javascript:" class="btn btn-primary font-weight-bolder" onClick="showModalCreateTransfer()"><i class="fa fa-plus-circle"></i> Create New Transfer</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-1">
                                <input placeholder="Select Date" readonly required type="text" class="form-control" name="filter_date" id="filter-date">
                            </div>
                            <div class="col-md-2">
                                <select class="form-control" name="filter_source_sloc" id="filter-source-sloc">
                                    <option value="">All Source S LOC</option>
                                    @foreach ($slocs as $sloc)
                                        <option value="{{ $sloc->sloc }}">{{ $sloc->sloc }} - {{ $sloc->power_meter }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="form-control" name="filter_destination_sloc" id="filter-destination-sloc">
                                    <option value="">All Destination S LOC</option>
                                    @foreach ($slocs as $sloc)
                                        <option value="{{ $sloc->sloc }}">{{ $sloc->sloc }} - {{ $sloc->power_meter }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <hr />
                        <table id="transfer-energy" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 10px">No</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                    <th>Source</th>
                                    <th>Destination</th>
                                    <th>KWH</th>
                                    <th>Description</th>
                                    <th style="width: 60px">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transfers as $key => $transfer)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>{{ formatTanggalIndonesia2($transfer->date) }}</td>
                                        <td>{{ $transfer->action }}</td>
                                        <td>{{ $transfer->source_quantity_id }} - {{ $transfer->source_sloc }}</td>
                                        <td>{{ $transfer->destination_quantity_id }} - {{ $transfer->destination_sloc }}</td>
                                        <td>{{ $transfer->kwh }}</td>
                                        <td>{{ $transfer->description }}</td>
                                        <td>
                                            <button onClick="handleDelete('{{ $transfer->id }}')" class="btn btn-sm btn-icon btn-danger">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                            <button class="btn btn-sm btn-icon btn-info">
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
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel"><span id="modal-title">Input Transfer Energy</span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" id="create-form">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="date">Date</label>
                                    <div></div>
                                    <input readonly required type="text" class="form-control" name="date" id="date">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="action">Action</label>
                            <div></div>
                            <input required type="text" class="form-control" name="action" id="action">
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="source_sloc">Source Sloc</label>
                                    <div></div>
                                    <select style="width: 100%" required class="form-control" name="source_sloc" id="source_sloc">
                                        <option value="">Select Sloc / Quantity ID</option>
                                        @foreach ($slocs as $sloc)
                                            <option value="{{ $sloc->sloc }}-{{ $sloc->quantity_id }}">{{ $sloc->quantity_id }} - {{ $sloc->power_meter }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="destination_sloc">Destination Sloc</label>
                                    <div></div>
                                    <select style="width: 100%" required class="form-control" name="destination_sloc" id="destination_sloc">
                                        <option value="">Select Sloc / Quantity ID</option>
                                        @foreach ($slocs as $sloc)
                                            <option value="{{ $sloc->sloc }}-{{ $sloc->quantity_id }}">{{ $sloc->quantity_id }} - {{ $sloc->power_meter }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="jenis">Jenis Energy</label>
                                    <div></div>
                                    <select name="jenis_energy" id="jenis" class="form-control">
                                        <option value=""></option>
                                        <option value="listrik">Listrik</option>
                                        <option value="steam">Steam</option>
                                        <option value="batubara">Batubara</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="kwh">KWH</label>
                                    <div></div>
                                    <input required type="number" class="form-control" name="kwh" id="kwh">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <div></div>
                                    <textarea name="description" id="description" cols="30" rows="2" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                        <button id="submitButton" type="submit" class="btn btn-success"><i class="fa fa-paper-plane"></i>Submit</button>
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
        $('#transfer-energy').DataTable();
        $('#source_sloc').select2({
            placeholder: 'Select Sloc'
        });
        $('#destination_sloc').select2({
            placeholder: 'Select Sloc'
        });

        function handleDelete(id)
        {
            if(!confirm('This data will deleted permamanently')) {
                return false;
            }

            $.ajax({
                url: "{{ url('/pme/transfer-energy/delete') }}/"+id,
                type: "DELETE",
                dataType: "JSON",
                success: function ( response ) {
                    if(response.success == '1') {
                        Swal.fire('Success!', 'Delete transfer energy succeed', 'success')
                        .then(function () {
                            location.reload();
                        });
                    }else{
                        Swal.fire('Gagal!', 'Failed to delete transfer energy, try again please', 'error');
                    }
                },
                error: function ( error ) {
                    Swal.fire('Gagal!', 'Failed to delete transfer energy, try again please', 'error');
                }
            });
        }

        @isset($_GET['filter_date'])
        $('#filter-date').val("{{ $_GET['filter_date'] }}")
        @endisset

        @isset($_GET['filter_source_sloc'])
        $('#filter-source-sloc').val("{{ $_GET['filter_source_sloc'] }}")
        @endisset

        @isset($_GET['filter_destination_sloc'])
        $('#filter-destination-sloc').val("{{ $_GET['filter_destination_sloc'] }}")
        @endisset

        $('#filter-source-sloc,#filter-destination-sloc,#filter-date').on('change', function () {
            var current_url = '{!! url()->current() !!}';
            var next_url = current_url+'/?filter_date='+$('#filter-date').val()+'&filter_source_sloc='+$('#filter-source-sloc').val()+'&filter_destination_sloc='+$('#filter-destination-sloc').val();
            window.location.href = next_url;
        });

        $('#date,#filter-date').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            todayHighlight: true
        });

        function submiting(state) {
            if(state == true) {
                $('#submitButton i').removeClass('fa-paper-plane');
                $('#submitButton i').addClass('fa-spinner');
                $('#submitButton i').addClass('fa-spin');
                $('#submitButton').attr('disabled', true);
            }else{
                $('#submitButton i').addClass('fa-paper-plane');
                $('#submitButton i').removeClass('fa-spinner');
                $('#submitButton i').removeClass('fa-spin');
                $('#submitButton').removeAttr('disabled');
            }
        }

        $('#create-form').on('submit', function (e) {
            e.preventDefault();
            submiting(true);
            $.ajax({
                url: "{{ url('pme/transfer-energy/store') }}",
                type: 'POST',
                dataType: 'JSON',
                data: $(this).serialize(),
                success: function ( response ) {
                    if(response.success == '1') {
                        Swal.fire('Success!', 'Transfer energy succeed', 'success')
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
                    Swal.fire('Failed!', 'Transfer energy transaction failed, try again please', 'error')
                    .then(function () {
                        submiting(false);
                    });
                }
            })
        });

        // $('#source_sloc,#destination_sloc').on('change', function () {
        //     if($('#source_sloc').val() == $('#destination_sloc').val()) {
        //         alert('The source must not be the same as the destination');
        //         $(this).val('').trigger('change');
        //     }
        // })

        function showModalCreateTransfer()
        {
            $('#create-new-modal').modal('show');
        }

    </script>

@endpush
