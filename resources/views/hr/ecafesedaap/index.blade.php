@extends('layouts.base')

@section('content')

<div class="container-fluid">

    <!--begin::Row-->
    <div class="row">

        <div class="col-lg-12">
            <!--begin::Advance Table Widget 4-->
            <div class="card card-custom card-stretch gutter-b">
                <!--begin::Header-->
                <div class="card-header border-0 py-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label font-weight-bolder text-dark">Pesanan Catering</span>
                    </h3>
                </div>
                <div class="card-body" >
                    <form action="{{url('/PostUpdatePesanancatering')}}" method="POST">
                        @csrf
                     <div class="row">
                         <div class="col-sm-12">
                             <div class="table-responsive">
                                 <table class="table table-bordered">
                                     <tr>
                                         <thead>
                                             <th>Nomor</th>
                                             <th>Tanggal</th>
                                             <th>Shift</th>
                                             <th>Jumlah</th>
                                             <th>Alasan Update</th>
                                             <th>Jenis</th>
                                         </thead>
                                     </tr>
                                 </table>
                             </div>
                         </div>
                     </div>
                    </form>
                    </div>
  
                <!--end::Body-->
            </div>
            <!--end::Advance Table Widget 4-->
        </div>
    </div>
    <!--end::Row-->
    <!--end::Dashboard-->
</div>

@endsection
