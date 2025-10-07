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
        <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
    
    
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
                        <div class="card-header">
                            <div class="card-tittle text-center">
                                FORM UPDATE INTERNAL MEMO  
                            </div>
                        </div>
                        <div class="card-body">
                            
                            <form action="{{ url('/internal_memo/menu/update_dokumen/' . $detail->id_im .'/'. Crypt::encrypt($detail->nik_tujuan)) }}" method="post" enctype="multipart/form-data">

                                
                                @method('PATCH')
                                @csrf
                                
                        <input type="hidden" value="{{$detail->nik_tujuan}}" name="nik_tujuan">
                        <input type="hidden" value="{{$detail->name}}" name="nama_tujuan">
                        <input type="hidden" value="{{$detail->no_dokumen}}" name="no_dokumen">
                        <input type="hidden" value="{{$detail->nik_pembuat}}" name="notif_from_nik">
                        <input type="hidden" value="{{$detail->nik_tujuan}}" name="notif_to_nik">
                        <input type="hidden" value="{{$detail->id_im}}" name="id_im">
                        <input type="hidden" value="{{$detail->perihal}}" name="perihal">

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">No Dokumen :
                                        </label>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <input type="text" name="no_dokumen" class="form-control" id="" value="{{$detail->no_dokumen}}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Perihal :
                                        </label>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <input type="text" name="perihal" class="form-control mb-4" id="" placeholder="Masukan Perihal" value="{{$detail->perihal}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <textarea id="summernote" name="konten"></textarea>

                                            <hr style="background-color: black">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                                
                                    <div class="card-footer">
                                            <div class="float-right">
                                                        <button type="submit" class="btn btn-md btn-primary btn-block" data-toggle="modal" style="border-radius: 10px"><i class="fas fa-save"></i> Update</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                </div>
            </div>


@endsection


@push('scripts')

<script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script src="{{url ('/assets/js/summernote.js')}}"></script>

<script type="text/javascript">


  $(document).ready(function() {
            $('#summernote').summernote('code', {!! json_encode($detail->konten) !!} );
        });

    </script>

@endpush
