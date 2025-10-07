@extends('layouts.base-display')

@section('title', 'History Inner')

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

            <ul class="sticky-toolbar nav flex-column pl-2 pr-2 pt-3 pb-3 mt-4" style="zoom: 160%;">
                <!--begin::Item-->
                <li class="nav-item mb-2" id="kt_demo_panel_toggle" data-toggle="tooltip" title="Check out more demos"
                    data-placement="right">
                    <a class="btn btn-sm btn-icon btn-bg-light btn-icon-danger btn-hover-danger"
                        href="javascript:history.back()">
                        <i class="fas fa-home"></i>
                    </a>
                </li>
            </ul>

            <div class="row gutters-sm">
                <div class="col-md-12 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th width="5%">No.</th>
                                            <th>Nama</th>
                                            <th>NIK</th>
                                            <th>Tanggal Sampling</th>
                                            <th>Jam Ke</th>
                                            <th>Nilai</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($data as $list)
                                            <tr>
                                                <td>
                                                    {{ $loop->iteration }}
                                                </td>
                                                <td>
                                                    {{ $list->nama }}
                                                </td>
                                                <td>
                                                    {{ $list->nik }}
                                                </td>
                                                <td>
                                                    {{ Carbon\Carbon::parse($list->tgl_pengisian)->format('d-M-Y') }}
                                                </td>
                                                <td>
                                                    {{ $list->jam_ke }}
                                                </td>
                                                <td>
                                                    {{ $list->sampling_ke }}
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

@endsection

@push('scripts')
    <script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>


    <script type="text/javascript">
        $('.table').DataTable();

    </script>

@endpush
