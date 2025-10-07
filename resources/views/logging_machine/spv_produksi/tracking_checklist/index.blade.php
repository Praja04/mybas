@extends('layouts.base-display')
@section('title', 'TRACKING CHECKLIST SHIFT')

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

            <ul class="sticky-toolbar nav flex-column pl-2 pr-2 pt-3 pb-3 mt-4">
                <!--begin::Item-->
                <li class="nav-item mb-2" id="kt_demo_panel_toggle" data-toggle="tooltip" title="Check out more demos"
                    data-placement="right">
                    <a class="btn btn-sm btn-icon btn-bg-light btn-icon-success btn-hover-success"
                        href="/logging_machine/spv_prod/">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </li>
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
                                            <th>Tanggal</th>
                                            <th>Shift/Group</th>
                                            <th>Varian</th>
                                            <th>No.Mesin</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $val)
                                            <tr class="text-center">
                                                <td>
                                                    {{ $loop->iteration }}
                                                </td>
                                                <td>
                                                    <a href="/logging_machine/spv_prod/detail_shift/{{ $val->id }}">
                                                        {{ $val->nama }}
                                                    </a>
                                                </td>
                                                <td>
                                                    {{ $val->nik }}
                                                </td>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($val->tgl_pengisian)->format('d-M-Y') }}
                                                </td>
                                                <td>
                                                    {{ $val->shift_group }}
                                                </td>
                                                <td>
                                                    {{ $val->rasa }}
                                                </td>
                                                <td>
                                                    {{ $val->no_mesin }}
                                                </td>
                                                <td>
                                                    @if ($val->approval_spv == NULL)
                                                        <span class="badge badge-pill badge-primary"> Menunggu
                                                            Approve</span>
                                                    @else
                                                        <span class="badge badge-pill badge-info"> Full Approve</span>
                                                    @endif
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
        $(document).ready(function() {
            $('.table').DataTable();

            $(".Cbox").click(function() {
                $("#btn_approve").attr("disabled", !this.checked);
            });
        });

    </script>

@endpush
