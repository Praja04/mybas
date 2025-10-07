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
                                    <th>Tanggal Pengisian</th>
                                    <th>No.Mesin</th>
                                    <th>Rasa</th>
                                    <th>Inner Reject(Kg)</th>
                                    <th>Sampah Inner</th>
                                    <th>Total WIP</th>
                                    <th>Sortir</th>
                                    <th>Sobek</th>
                                    <th style="width: 10%">Opsi</th>
                                </tr>
                                </thead>

                                <tbody>
                                  @foreach ($data as $list)                                      
                                    <tr>
                                        <td>
                                          {{$loop->iteration}}
                                        </td>
                                        <td>
                                          {{Carbon\Carbon::parse($list->tgl_pengisian)->format('d-M-Y')}}
                                        </td>
                                        <td>
                                          {{$list->no_mesin}}
                                        </td>
                                        <td>
                                          {{$list->rasa}}
                                        </td>
                                        <td>
                                          {{$list->inner_reject}}
                                        </td>
                                        <td>
                                          {{$list->sampah_inner}}
                                        </td>
                                        <td>
                                          {{$list->total_wip}}
                                        </td>
                                        <td>
                                          {{$list->sortir}}
                                        </td>
                                        <td>
                                          {{$list->sobek}}
                                        </td>
                                        <td>
                                          @if ($list->tgl_pengisian == date('Y-m-d'))
                                          <a href="{{ url('/logging_machine/get_edit_wip/'.$list->id ) }}" class="btn btn-sm btn-info"><i class="fas fa-edit"></i> Edit</a>
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
