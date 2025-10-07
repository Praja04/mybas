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

    <div class="container">
        <div class="main-body">

            
            <ul class="sticky-toolbar nav flex-column pl-2 pr-2 pt-3 pb-3 mt-4" style="zoom: 120%;">
                <!--begin::Item-->
                <li class="nav-item mb-2" id="kt_demo_panel_toggle" data-toggle="tooltip" title="Dashboard"
                    data-placement="right">
                    <a class="btn btn-sm btn-icon btn-bg-light btn-icon-danger btn-hover-danger"
                        href="/internal_memo/menu/index">
                        <i class="fas fa-home"></i>
                    </a>
                </li>
            </ul>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="timeline timeline-4 mb-4">
                                <a href="javascript:history.back()" class="btn btn-dark btn-sm" style="border-radius: 10px;"><i class="fas fa-arrow-left"></i> Kembali</a>
                                <a href="#show" data-toggle="modal" class="btn btn-primary btn-sm" style="border-radius: 10px;"><i class="fas fa-eye"></i> Lihat Surat</a>
                                <div class="timeline-bar"></div>
                                    <div class="timeline-items">
                                        @foreach ($data as $list)
                                        @if ($list->status == 0)
                                        <br>
                                        <div class="timeline-item timeline-item-left" style="border-radius: 10px">
                                            <div class="timeline-badge">
                                                <i class="fas fa-check fa-lg text-success" style="width: 200%;"></i>
                                            </div>
                                            
                                            <div class="timeline-label">
                                                <span class="text-dark">Tanggal:</span>
                                                <span class="text-dark font-weight-bold">{{\Carbon\carbon::parse($list->tgl_ttd)->format('d-M-Y')}}</span>
                                            </div>
                                            <div class="timeline-label">
                                                <span class="text-dark">Jam:</span>
                                                <span class="text-dark font-weight-bold">{{$list->jam_ttd}} WIB</span>
                                            </div>
                                            <div class="timeline-label">
                                                <span class="text-dark">Status:</span>
                                                <span class="text-dark font-weight-bold"><span class="badge badge-pill badge-success"> Selesai Approve</span></span>
                                            </div>
                                            
                                            <div class="timeline-content bg-success text-white mt-2">
                                                <h5>NAMA : {{$list->name}}</h5>
                                                <h5>NIK : {{$list->nik_tujuan}}</h5>
                                            </div>
                                        </div>
                                        <br>
                                        @elseif ($list->status == 1)
                                        <br>
                                        <div class="timeline-item timeline-item-right" style="border-radius: 10px">
                                            <div class="timeline-badge">
                                                <i class="fas fa-user-clock fa-lg text-warning" style="width: 200%;"></i>
                                            </div>
                                            <div class="timeline-label mt-4">
                                                <span class="text-dark">Status:</span>
                                                <span class="text-dark font-weight-bold"><span class="badge badge-pill badge-warning"> Pending Approve</span></span>
                                            </div>
                                            <div class="timeline-content bg-warning text-dark mt-2">
                                                <h5>NAMA : {{$list->name}}</h5>
                                                <h5>NIK : {{$list->nik_tujuan}}</h5>
                                            </div>
                                        </div>
                                        <br>
                                        @else
                                        <br>
                                        <br>
                                        <div class="timeline-item timeline-item-left" style="border-radius: 10px">
                                            <div class="timeline-badge">
                                                <i class="fas fa-window-close fa-lg text-danger" style="width: 200%;"></i>
                                            </div>
                                            <div class="timeline-label">
                                                <span class="text-dark">Alasan:</span>
                                                <span class="text-dark font-weight-bold">{{$list->alasan_tolak}}</span>
                                            </div>
                                            <div class="timeline-label">
                                                <span class="text-dark">Status:</span>
                                                <span class="text-dark font-weight-bold"><span class="badge badge-pill badge-danger"> Permintaan Revisi</span></span>
                                            </div>
                                            <div class="timeline-content bg-danger text-dark mt-2">
                                                <h5>NAMA : {{$list->name}}</h5>
                                                <h5>NIK : {{$list->nik_tujuan}}</h5>
                                            </div>
                                        </div>

                                        @endif
                                        @endforeach
                                    </div>
                               </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="show" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                {!! str_replace('src="', 'src="dokumen_im/', $detail->konten) !!}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>


@endsection


@push('scripts')


<script type="text/javascript">
<script src="{{ url('/assets/js/engage_code.js') }}"></script>


    </script>

@endpush
