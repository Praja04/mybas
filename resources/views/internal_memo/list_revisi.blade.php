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
                            <div class="card-title">
                                <h3 class="card-label">List Revisi {{Auth::user()->name}}</h3>
                            </div>
                            </div>
                        <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-12">
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
                                                            <th>Ditolak Oleh</th>
                                                            <th>Alasan Tolak</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        @foreach ($list_revisi as $list)
                                                        <tr>
                                                            <td>
                                                                {{ $loop->iteration }}
                                                            </td>
                                                             <td>
                                                                <a href="/internal_memo/menu/form_revisi/{{ $list->id }}/{{ Crypt::encrypt($list->nik_tujuan) }}"
                                                                    class="btn btn-primary btn-sm" style="border-radius: 15px" data-toggle="tooltip" data-placement="right" title="Revisi"><i
                                                                        class="fas fa-edit" style="border-radius: 15px"></i></a>
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
                                                            <td>
                                                                {{ $nama_penolak->name }}
                                                            </td>
                                                            <td>
                                                                {{ $list->alasan_tolak }}
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
