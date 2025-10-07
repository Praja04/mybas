@extends('layouts.base-display')


@section('title', 'LIST PERMINTAAN BARU DOWNTIME')

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
            <a href="/logging_machine/maintenance/{{ $maintenance->username }}" class="btn btn-danger btn-sm mb-3"
                style="border-radius: 10px"> <i class="fas fa-arrow-left"></i> Kembali</a>

            <div class="row gutters-sm">
                <div class="col-md-12 mb-3">
                    <div class="card">
                        <div class="card-body">


                            <div class="table-responsive">
                                <table class="table table-hover" style="zoom: 90%">
                                    <thead>
                                        <tr>
                                            <th width="5%">No.</th>
                                            <th>Aksi</th>
                                            <th>Nama</th>
                                            <th>No.Mesin</th>
                                            <th>Tanggal Permintaan</th>
                                            <th>Jam Permintaan</th>
                                            <th>Kerusakan</th>
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
                                                            <input type="hidden" name="nama_maintenance"
                                                                value="{{ $maintenance->name }}">
                                                            <input type="hidden" name="nik_maintenance"
                                                                value="{{ $maintenance->username }}">
                                                            <button type="submit" class="btn btn-primary btn-sm text-center"
                                                                style="border-radius: 7px"><i class="fas fa-check"></i>
                                                                Proses
                                                            </button>
                                                        </form>
                                                    @elseif($list->status == 1)
                                                        <a href="/logging_machine/get_close_request/{{ Crypt::encrypt($list->id) }}"
                                                            class="btn btn-info btn-sm" style="border-radius: 10px"><i
                                                                class="fas fa-window-close"></i> Selesai Perbaikan</a>
                                                    @else
                                                        <span class="badge badge-success">Closed Request</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $list->nama }}
                                                </td>
                                                <td>
                                                    {{ $list->no_mesin }}
                                                </td>
                                                <td>
                                                    {{ Carbon\Carbon::parse($list->tgl_pengisian)->format('d-M-Y') }}
                                                </td>
                                                <td>
                                                    {{ Carbon\Carbon::parse($list->jam_pengisian)->format('H:i') }} WIB
                                                </td>
                                                <td>
                                                    {{ $list->kerusakan }}
                                                </td>
                                                <td>
                                                    @if ($list->status == 2)
                                                        <span class="badge badge-warning">Belum Di Respon</span>
                                                    @elseif($list->status == 1)
                                                        <span class="badge badge-info">Proses Perbaikan</span>
                                                    @else
                                                        <span class="badge badge-success">Selesai Perbaikan</span>
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
