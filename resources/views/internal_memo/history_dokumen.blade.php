@extends('internal_memo.master.layout')


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

            <div class="row">
                <div class="col-md-12 mb-3">
                    <div class="card card-custom gutter-b">
                         <div class="card-header card-header-tabs-line">
                                <ul class="nav nav-tabs nav-bold nav-tabs-line">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-toggle="tab" href="#history_im">
                                            <span class="nav-icon"><i class="fas fa-history"></i></span>
                                            <span class="nav-text">History Pembuatan IM</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#approval">
                                            <span class="nav-icon"><i class="fas fa-user-check"></i></span>
                                            <span class="nav-text">History Approval</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="history_im" role="tabpanel" aria-labelledby="history_im">
                                  <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th width="5%">No.</th>
                                                    <th>Opsi</th>
                                                    <th>No.Dokumen</th>
                                                    <th>Tanggal Pembuatan</th>
                                                    <th>Jam Pembuatan</th>
                                                    <th>Perihal</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach ($detail as $list)
                                                    <tr>
                                                        <td>
                                                            {{ $loop->iteration }}
                                                        </td>
                                                           <td>
                                                            <a href="/internal_memo/menu/detail_dokumen/{{ Crypt::encrypt($list->nik_pembuat) }}/{{ $list->id }}"
                                                                class="btn btn-primary btn-sm" style="border-radius: 7px" data-placement="right" title="Detail"><i
                                                                    class="fas fa-eye"></i></a>
                                                                 <a href="/internal_memo/menu/tracking_dokumen/{{ Crypt::encrypt($list->id) }}"
                                                            class="btn btn-primary btn-info btn-sm" style="border-radius: 7px" data-toggle="tooltip" data-placement="top" title="Tracking">
                                                            <i class="fas fa-search"></i>
                                                        </a>
                                                        </td>
                                                        <td>
                                                            {{ $list->no_dokumen }}
                                                        </td>
                                                        <td>
                                                            {{ Carbon\Carbon::parse($list->tgl_pengisian)->format('d-M-Y') }}
                                                        </td>
                                                        <td>
                                                            {{ $list->jam_pengisian }} WIB
                                                        </td>
                                                        <td>
                                                            {{ $list->perihal }}
                                                        </td>
                                                     
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            <div class="tab-pane fade" id="approval" role="tabpanel" aria-labelledby="approval">
                                <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th width="5%">No.</th>
                                                    <th>Opsi</th>
                                                    <th>No.Dokumen</th>
                                                    <th>Tanggal Pembuatan</th>
                                                    <th>Jam Pembuatan</th>
                                                    <th>Perihal</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach ($list_selesai as $val)
                                                    <tr>
                                                        <td>
                                                            {{ $loop->iteration }}
                                                        </td>
                                                        <td>
                                                            <a href="/internal_memo/menu/detail_dokumen/{{ Crypt::encrypt($val->nik_pembuat) }}/{{ $val->id }}"
                                                                class="btn btn-primary btn-sm" style="border-radius: 7px" data-toggle="tooltip" data-placement="right" title="Detail"><i
                                                                    class="fas fa-eye"></i></a>
                                                            <a href="/internal_memo/menu/tracking_dokumen/{{ Crypt::encrypt($val->id_im) }}"
                                                                class="btn btn-primary btn-info btn-sm" style="border-radius: 7px" data-toggle="tooltip" data-placement="top" title="Tracking">
                                                                <i class="fas fa-search"></i>
                                                            </a>
                                                        </td>
                                                        <td>
                                                            {{ $val->no_dokumen }}
                                                        </td>
                                                        <td>
                                                            {{ Carbon\Carbon::parse($val->tgl_pengisian)->format('d-M-Y') }}
                                                        </td>
                                                        <td>
                                                            {{ $val->jam_pengisian }} WIB
                                                        </td>
                                                        <td>
                                                            {{ $val->perihal }}
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

         $(function () {
        $('[data-toggle="tooltip"]').tooltip()
        })

    </script>

@endpush
