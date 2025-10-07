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
                                <h3 class="card-label">List Outstanding {{Auth::user()->name}}</h3>
                            </div>
                                <ul class="nav nav-tabs nav-bold nav-tabs-line">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-toggle="tab" href="#pending">
                                            <span class="nav-icon"><i class="fas fa-user-clock"></i></span>
                                            <span class="nav-text">Pending</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#selesai">
                                            <span class="nav-icon"><i class="fas fa-user-check"></i></span>
                                            <span class="nav-text">Selesai</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="table-responsive">
                                                 <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th width="5%">No.</th>
                                                            <th>No.Dokumen</th>
                                                            <th>Tanggal Pembuatan</th>
                                                            <th>Jam Pembuatan</th>
                                                            <th>Perihal</th>
                                                            <th>Opsi</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        @foreach ($list_pending as $list)
                                                        <tr>
                                                            <td>
                                                                {{ $loop->iteration }}
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
                                                            @if ($status_user->kategori == 'Penerima')
                                                            <form action=" {{ url('/internal_memo/menu/penerima') }}" method="post">
                                                                @method('PATCH')
                                                                @csrf

                                                                <input type="hidden" name="no_dokumen" value="{{$list->no_dokumen}}">
                                                                <input type="hidden" name="id_im" value="{{$list->id_im}}">
                                                                <input type="hidden" name="notif_from_nik" value="{{$list->nik_tujuan}}">
                                                                <input type="hidden" name="notif_to_nik" value="{{$list->nik_pembuat}}">
                                                                
                                                                <button type="submit"
                                                                    class="btn btn-primary btn-sm" style="border-radius: 7px"><i
                                                                    class="fas fa-check"></i> Konfrimasi</a>
                                                                </form>

                                                            @else
                                                                <a href="/internal_memo/menu/form_ttd/{{ $list->id }}/{{ $list->sub_kategori }}/{{ Crypt::encrypt($list->nik_tujuan) }}/{{ $list->status }}"
                                                                class="btn btn-primary btn-sm" style="border-radius: 7px"><i
                                                                    class="fas fa-check"></i> Konfrimasi</a>
                                                                </td>
                                                                @endif
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="selesai" role="tabpanel" aria-labelledby="selesai">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="table-responsive">
                                                 <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th width="5%">No.</th>
                                                            <th>No.Dokumen</th>
                                                            <th>Tanggal Pembuatan</th>
                                                            <th>Jam Pembuatan</th>
                                                            <th>Perihal</th>
                                                            <th>Opsi</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        @foreach ($list_selesai as $val)
                                                        <tr>
                                                            <td>
                                                                {{ $loop->iteration }}
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
                                                            <td>
                                                                <a href="/internal_memo/menu/form_ttd/{{ $val->id }}/{{ $val->sub_kategori }}/{{ Crypt::encrypt($val->nik_tujuan) }}/{{ $val->status }}"
                                                                    class="btn btn-primary btn-sm" style="border-radius: 7px"><i
                                                                        class="fas fa-check"></i> Detail</a>
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
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>


    <script type="text/javascript">
        $('.table').DataTable();

    </script>

@endpush
