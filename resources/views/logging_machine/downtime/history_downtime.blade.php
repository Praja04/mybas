@extends('layouts.base-display')

@section('title', 'HISTORY DOWNTIME')

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
                                            <th>Tanggal Permintaan</th>
                                            <th>Detail Kerusakan</th>
                                            <th>Status</th>
                                            <th>Opsi</th>
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
                                                    {{ Carbon\Carbon::parse($list->tgl_pengisian)->format('d-M-Y') }}
                                                </td>
                                                <td>
                                                    {{ $list->kerusakan }}
                                                </td>
                                                <td>
                                                    @if ($list->status == 2)
                                                        <span class="badge badge-pill badge-warning">Belum Di Respon</span>
                                                    @elseif($list->status == 1)
                                                        <span class="badge badge-pill badge-info">Proses Perbaikan</span>
                                                    @elseif($list->status == 0)
                                                        <span class="badge badge-pill badge-success">Sudah Di
                                                            Perbaiki</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($list->close_operator == 1 && $list->status == 0)
                                                        <form
                                                            action="/logging_machine/close_operator/{{ Crypt::encrypt($list->id_logging_machine) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="text" name="nik" value={{ $list->nik }} hidden>
                                                            <button type="submit" class="btn btn-danger btn-sm"
                                                                style="border-radius: 7px"><i
                                                                    class="fas fa-window-close"></i> Close</a>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <a href="/logging_machine/detail_downtime/{{ Crypt::encrypt($list->id) }}"
                                                            class="btn btn-primary btn-sm" style="border-radius: 7px"><i
                                                                class="fas fa-eye"></i> Detail</a>
                                                    @endif
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
