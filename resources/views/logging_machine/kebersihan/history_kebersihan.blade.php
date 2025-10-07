@extends('layouts.base-display')

@section('title', 'HISTORY WIP')

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
        
          <a href="javascript:history.back()" class="btn btn-danger btn-sm mb-3" style="border-radius: 10px"> <i class="fas fa-arrow-left"></i> Kembali</a>

              <div class="row gutters-sm">
                <div class="col-md-12 mb-3">
                     <div class="card">
                          <div class="card-body">
                            <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th width="5%">No.</th>
                                    <th>Nama</th>
                                    <th>NIK</th>
                                    <th>Tanggal Pengisian</th>
                                    <th>Lantai</th>
                                    <th>Bak</th>
                                    <th>Body Mesin</th>
                                    <th>Sealer</th>
                                    <th>Gayung</th>
                                    <th>Sodokan</th>
                                    <th>Tutup Hopper</th>
                                    <th>Serbet</th>
                                </tr>
                                </thead>

                                <tbody>
                                  @foreach ($data as $list)                                      
                                    <tr>
                                        <td>
                                          {{$loop->iteration}}
                                        </td>
                                        <td>
                                          {{$list->nama}}
                                        </td>
                                        <td>
                                          {{$list->nik}}
                                        </td>
                                        <td>
                                          {{Carbon\Carbon::parse($list->tgl_pengisian)->format('d-M-Y')}}
                                        </td>
                                        <td>
                                          {{$list->lantai}}
                                        </td>
                                        <td>
                                          {{$list->bak}}
                                        </td>
                                        <td>
                                          {{$list->body_mesin}}
                                        </td>
                                        <td>
                                          {{$list->sealer}}
                                        </td>
                                        <td>
                                          {{$list->gayung}}
                                        </td>
                                        <td>
                                          {{$list->sodokan}}
                                        </td>
                                        <td>
                                          {{$list->tutup_hopper}}
                                        </td>
                                        <td>
                                          {{$list->serbet}}
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
