@extends('layouts.base-display')

@section('title', 'HISTORY SAMPLING')

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

    <div class="container-fluid">
        <div class="main-body">

            <div class="row gutters-sm">
                <div class="col-md-12 mb-3">
                    <div class="card">
                        <div class="card-header">
                            <div class="float-right">
                                <a href="javascript:window.history.back()" class="btn btn-danger btn-sm mb-3"
                                    style="border-radius: 10px"> <i class="fas fa-arrow-left"></i> Kembali</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th width="5%">No.</th>
                                            <th>Nama</th>
                                            <th>Tanggal Sampling</th>
                                            <th>Shift/Group</th>
                                            <th>Jam Ke</th>
                                            <th>Opsi</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($list as $val)
                                            <tr>
                                                <td>
                                                    {{ $loop->iteration }}
                                                </td>
                                                <td>
                                                    {{ $val->nama }}
                                                </td>
                                                <td>
                                                    {{ Carbon\Carbon::parse($val->tgl_pengisian)->format('d-M-Y') }}
                                                </td>
                                                <td>
                                                    {{ $val->shift_group }}
                                                </td>
                                                <td>
                                                    {{ $val->jam_ke }}
                                                </td>
                                                <td>
                                                    <a href="/logging_machine/detail_input/{{ Crypt::encrypt($val->id_logging_machine) }}"
                                                        class="btn btn-primary btn-sm" style="border-radius: 7px"><i
                                                            class="fas fa-eye"></i> Detail</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                            </div>
                            </table>
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

    </script>

@endpush
