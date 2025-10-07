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

            <ul class="sticky-toolbar nav flex-column pl-2 pr-2 pt-3 pb-3 mt-4" style="zoom: 160%;">
                <li class="nav-item mb-2" id="kt_demo_panel_toggle" title="Kembali"
                    data-placement="top">
                    <a class="btn btn-sm btn-icon btn-bg-light btn-icon-dark btn-hover-dark"
                        href="javascript:history.back()">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </li>
                <li class="nav-item mb-2" id="kt_demo_panel_toggle">
                    <a class="btn btn-sm btn-icon btn-bg-light btn-icon-info btn-hover-info doPrint"
                        href="#" onclick="return false;" title="Print Out">
                        <i class="fas fa-print"></i>
                    </a>
                </li>
                <li class="nav-item mb-2" id="kt_demo_panel_toggle">
                    <a class="btn btn-sm btn-icon btn-bg-light btn-icon-danger btn-hover-danger"
                        href="/internal_memo/menu/export_pdf/{{Crypt::encrypt($detail->id)}}" title="Save as PDF">
                        <i class="far fa-file-pdf"></i>
                    </a>
                </li>
            </ul>

            <div class="row gutters-sm">
                <div class="col-md-12 mb-3">
                    <div class="card">
                            <div id="printDiv">
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
                                            <p class="mt-2 ml-4"><u><b>{{$detail->nama_pengisi}}</b></u></p>
                                            <p class="mt-1 ml-4">{{$detail->name}}</p>
                                        </div>
                                        @foreach ($ttd as $val)
                                        @if ($val->status == 1)
                                        <div class="co-sm-3">
                                            <p class="mt-4 ml-4">{{$val->kategori}}</p>
                                            <br>
                                            <br>
                                            <br>
                                            <br>
                                            <br>
                                            <br>
                                            <p class="mt-3 ml-4"><u><b>{{$val->name}}</b></u></p>
                                        </div> 
                                        @else
                                         <div class="co-sm-3">
                                            <p class="mt-4 ml-4">{{$val->kategori}}</p>
                                            <br>
                                             <p>
                                                <img class="mr-4"  src="{{ url('/') }}/master_ttd/ttd.png" alt="" style="width: 100px; padding-right: 25px">
                                            </p>
                                            <p class="mt-2 ml-4"><u><b>{{$val->name}}</b></u></p>
                                            <p class="mt-2 ml-4">
                                                @foreach($dept_ttd as $list)
                                                
                                                @endforeach {{$list->name}}</p>
                                        </div>
                                        @endif
                                        @endforeach 
                                    {{-- <div class="col-sm-4">
                                            <p class="mt-4 ml-4">Disetujui Oleh,</p>
                                            <br>
                                            <p><img class="mr-4"  src="{{ url('/') }}/master_ttd/pembuat.png" alt="" style="width: 100px; padding-right: 25px"></p>
                                            <p class="mt-2 ml-4"><u><b>{{$detail->nama_pembuat}}</b></u></p>
                                            <p class="mt-1 ml-4">{{$detail->dept_pembuat}}</p>
                                        </div> --}}

                                    </div>
                                    <hr style="background-color: black">
                                    <p class="ml-2"><b>FRM-GAP-005-009</b></p>
                                    <p class="ml-2"><b>Rev.00-1 September 2007</b></p>
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

    $(".doPrint").on("click", function() {
     var printContents = document.getElementById('printDiv').innerHTML;
     var originalContents = document.body.innerHTML;
     document.body.innerHTML = printContents;
     window.print();
     document.body.innerHTML = originalContents;
     location.reload();
});

$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})

    </script>

@endpush
