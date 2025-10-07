@extends('layouts.base-display')
@section('title', 'LIST ALL PERMINTAAN')

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

            <a href="javascript:history.back()" class="btn btn-danger btn-sm mb-3" style="border-radius: 10px"> <i
                    class="fas fa-arrow-left"></i> Kembali</a>

            <div class="row gutters-sm">
                <div class="col-md-12 mb-3">
                    <div class="card">
                        <div class="card-body">

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th width="5%">No.</th>
                                            <th>Aksi</th>
                                            <th>Nama Pengaju</th>
                                            <th>NIK Pengaju</th>
                                            <th>Tanggal Permintaan</th>
                                            <th>Jam Permintaan</th>
                                            <th>Kerusakan</th>
                                            <th>Perbaikan</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($engineering as $list)
                                            <tr>
                                                <td>
                                                    {{ $loop->iteration }}
                                                </td>
                                                  <td>
                                                    @if ($list->status == 2)
                                                        <form
                                                            action="/logging_machine/respon_maintenance/{{ Crypt::encrypt($list->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-primary btn-sm text-center"
                                                                style="border-radius: 7px"><i class="fas fa-check"></i>
                                                                Proses
                                                            </button>
                                                        @elseif($list->status == 1)
                                                            <a href="/logging_machine/get_close_request/{{ Crypt::encrypt($list->id) }}/{{$nik}}"
                                                                class="btn btn-info btn-sm" style="border-radius: 10px"><i
                                                                    class="fas fa-window-close"></i> Close </a>
                                                        @else
                                                            <span class="badge badge-success">Closed Request</span>
                                                    @endif
                                                    </form>
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
                                                    {{ Carbon\Carbon::parse($list->jam_pengisian)->format('H:i') }}
                                                    WIB
                                                </td>
                                                <td>
                                                    {{ $list->kerusakan }}
                                                </td>
                                                <td>
                                                    {{ $list->approval_maintenance_remarks }}
                                                </td>
                                                <td>
                                                    @if ($list->status == 2)
                                                        <span class="badge badge-warning">Belum Di Respon</span>
                                                    @elseif($list->status == 1)
                                                        <span class="badge badge-info">Proses Perbaikan</span>
                                                    @else
                                                        <span class="badge badge-success">Close Request</span>
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
        $('.table').DataTable();

    </script>

@endpush
