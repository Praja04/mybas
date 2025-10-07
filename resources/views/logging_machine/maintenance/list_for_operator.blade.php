@extends('layouts.base-display')
@section('title', 'LIST ALL OUTSTANDING OPERATOR')

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
                                            <th>Nama Pengaju</th>
                                            <th>NIK Pengaju</th>
                                            <th>Tanggal Permintaan</th>
                                            <th>Jam Permintaan</th>
                                            <th>Kerusakan</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $val)
                                        <tr>
                                            <td>
                                                {{$loop->iteration}} 
                                            </td>
                                            <td>
                                                {{ $val->nama }}
                                            </td>
                                            <td>
                                                {{ $val->nik }}
                                            </td>
                                            <td>
                                                {{ Carbon\Carbon::parse($val->tgl_pengisian)->format('d-M-Y') }}
                                            </td>
                                            <td>
                                                {{ Carbon\Carbon::parse($val->jam_pengisian)->format('H:i') }}
                                                WIB
                                            </td>
                                            <td>
                                                {{ $val->kerusakan }}
                                            </td>
                                            <td>
                                                @if ($val->close_operator == 1)
                                                    <span class="badge badge-info">Proses Perbaikan</span>
                                                @else
                                                    <span class="badge badge-success">Selesai</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($val->close_operator == 1)
                                                <form
                                                    action="/logging_machine/close_from_operator/{{ Crypt::encrypt($val->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PATCH')

                                                    <input type="text" value="{{ $val->nik }}" name="nik" hidden>
                                                    <input type="text" value="{{ $val->jam_pengisian }}" name="jam_pengisian" hidden>

                                                    {{-- <input type="text" hidden value="{{ $diff }}" name="waktu_penyelesaian"> --}}
                                                    
                                                    <input type="text" value="{{ $val->nama }}" name="nama" hidden>

                                                    <button type="submit" class="btn btn-info btn-sm"
                                                        style="border-radius: 10px"><i class="fas fa-window-close"></i>
                                                        Close </button>
                                                </form>
                                                @else
                                                -
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
