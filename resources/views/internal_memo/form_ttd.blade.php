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

            <div class="row gutters-sm">
                <div class="col-md-12 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <table class="table table-bordered" style="zoom: 90%;">
                                    <thead>
                                    <tr>
                                        <th rowspan="4" style="width: 20%" class="mb-4">
                                            <img src="{{ url('/') }}/assets/media/logos/logo-pas-with-text.png" class="mb-4 pb-10" style="width: 80%;"/>
                                        </th>
                                        <th rowspan="2"><br><span style="font-weight:bold"><h1 class="text-center">PT PRAKARSA ALAM SEGAR</h1></span></th>
                                        <th>Doc No</th>
                                        <th>{{$detail->no_dokumen}}</th>
                                    </tr>
                                    <tr>
                                        <td>Rev.</td>
                                        <td>00</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center"><h4>{{ $dept->name }}</h4></td>
                                        <td>Date</td>
                                        <td>{{\Carbon\carbon::parse($detail->tgl_pengisian)->format('d M Y')}}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center"><span style="font-weight:bold"><h4>INTERNAL MEMO</h4></span></td>
                                        <td>Page</td>
                                        <td>1/1</td>
                                    </tr>
                                    </thead>
                                    </table>
                                    <div class="row mt-4">
                                        <div class="col-sm-1">
                                                <p><b>Kepada</b></p>
                                        </div>
                                        <div class="col-sm-8">
                                                <p>: <b class="ml-2">
                                                @foreach ($kepada as $penerima)

                                                {{$penerima->name}}
                                                    
                                                @endforeach
                                            </b></p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-1">
                                                <p><b>CC</b></p>
                                        </div>
                                        <div class="col-sm-8">
                                                <p>: <b class="ml-2">
                                                @foreach ($cc as $val)

                                                {{$val->name}},
                                                    
                                                @endforeach    
                                                </b></p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-1">
                                                <p><b>Dari</b></p>
                                        </div>
                                        <div class="col-sm-8">
                                                <p>: <b class="ml-2">{{$detail->nama_pengisi}}</b></p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-1">
                                                <p><b>Perihal</b></p>
                                        </div>
                                        <div class="col-sm-8">
                                                <p>: <b class="ml-2">{{$detail->perihal}}</b></p>
                                        </div>
                                    </div>
                                    <hr style="background-color: black;">
                                    {!! str_replace('src="', 'src="dokumen_im/', $detail->konten) !!}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-">
                                    
                                </div>
                                <div class="col-sm-3">
                                    <p class="mt-4 ml-4">Dibuat Oleh,</p>
                                    <br>
                                    <p><img class="mr-4"  src="{{ url('/') }}/master_ttd/ttd.png" alt="" style="width: 100px; padding-right: 25px"></p>
                                    <p class="mt-2 ml-4"><u><b>{{$pembuat->nama_pengisi}}</b></u></p>
                                    <p class="mt-1 ml-4">{{$dept_pembuat->name}}</p>
                                </div>
                                   @foreach ($ttd as $val)
                                        @if ($val->status == 0)
                                        <div class="co-sm-3">
                                            <p class="mt-4 ml-4">{{$val->kategori}}</p>
                                            <br>
                                            <p><img class="mr-4"  src="{{ url('/') }}/master_ttd/ttd.png" alt="" style="width: 100px; padding-right: 25px"></p>
                                            <p class="mt-2 ml-4"><u><b>{{$val->name}}</b></u></p>
                                            <p class="mt-1 ml-4">{{$dept_tujuan->name}}</p>
                                        </div> 
                                        @else
                                        <div class="co-sm-3">
                                            <p class="mt-4 ml-4">{{$val->kategori}}</p>
                                            <br>
                                            <br>
                                            <br>
                                            <br>
                                            <p><a href="#konfirmasi" class="btn btn-lg btn-primary mr-2" data-toggle="modal" style="border-radius: 15px">
                                                <i class="fas fa-check"></i> Konfirmasi</a></p>
                                            <p class="mt-2 ml-4"><u><b>{{$val->name}}</b></u></p>
                                            <p class="mt-1 ml-4">{{$dept_tujuan->name}}</p>
                                        </div> 
                                        @endif
                                    @endforeach 
                            </div>
                            <hr style="background-color: black">
                            <p class="ml-2"><b>FRM-GAP-005-009</b></p>
                            <p class="ml-2"><b>Rev.00-1 September 2007</b></p>
                        </div>
                    </div>


<!-- Modal -->
                <div class="modal fade" id="konfirmasi" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                      <div class="modal-body">
                        <form action="{{ url('/internal_memo/menu/konfirmasi/'.$detail->id)}}" method="post">
                        @method('PATCH')
                        @csrf

                        <input type="hidden" value="{{$detail->sub_kategori}}" name="sub_kategori">
                        <input type="hidden" value="{{$detail->nik_tujuan}}" name="nik_tujuan">
                        <input type="hidden" value="{{$detail->name}}" name="nama_tujuan">
                        <input type="hidden" value="{{$detail->no_dokumen}}" name="no_dokumen">
                        <input type="hidden" value="{{$detail->nik_tujuan}}" name="notif_from_nik">
                        <input type="hidden" value="{{$detail->nik_pembuat}}" name="notif_to_nik">
                        <input type="hidden" value="{{$detail->id_im}}" name="id_im">
                        <input type="hidden" value="{{$detail->perihal}}" name="perihal">

                       <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="exampleFormControlSelect1">KONFIRMASI SURAT</label>
                                    <select class="form-control konfirmasi" name="konfirmasi" required>
                                        <option disabled selected>Silahkan Pilih</option>
                                        <option value="0"> Terima</option>
                                        <option value="1"> Revisi</option>
                                    </select>
                                </div>
                                <div id="alasan">
                                    <div class="form-group">
                                        <label for="exampleFormControlTextarea1">Masukan Alasan Revisi</label>
                                        <textarea class="form-control" name="alasan_tolak" id="exampleFormControlTextarea1" rows="3"></textarea>
                                    </div>
                              </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save"></i> Simpan</button>
                    </div>
                </form>
                    </div>
                </div>
                </div>

                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')

    <script type="text/javascript">
    $("#alasan").hide();

     $('.konfirmasi').on('change', function() {
            if (this.value == '1') {
                $("#alasan").show();
            } else {
                $("#alasan").hide();
            }
        });

    </script>

@endpush
