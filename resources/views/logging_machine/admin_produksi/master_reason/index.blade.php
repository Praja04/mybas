@extends('layouts.base-display')

@section('title', 'ADMIN PRODUKSI SEASSONING 2')

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

            
            <ul class="sticky-toolbar nav flex-column pl-2 pr-2 pt-3 pb-3 mt-4" style="zoom: 100%;">
                <!--begin::Item-->
                <li class="nav-item mb-2" id="kt_demo_panel_toggle" data-toggle="tooltip" title="Dashboard"
                    data-placement="right">
                    <a class="btn btn-sm btn-icon btn-bg-light btn-icon-danger btn-hover-danger"
                        href="logging_machine/adm_prod/">
                        <i class="fas fa-home"></i>
                    </a>
                </li>
            </ul>

            <div class="row">
                <div class="col-sm-6">
                    <div class="card">
                        <div class="card-body">
                                <form action="{{ url('/logging_machine/adm_prod/post_master_reason') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="exampleFormControlSelect1">Reason Downtime</label>
                                                   <input type="text" name="reason" class="form-control"
                                                    placeholder="Masukan Reason Downtime" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="exampleFormControlSelect1">Kode SAP</label>
                                                <input type="text" name="kode_reason" class="form-control"
                                                placeholder="Masukan Kode SAP Reason" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="exampleFormControlSelect1">Kategori</label>
                                                <select name="kategori" class="form-control" id="" required>
                                                    <option disabled selected> SILAHKAN PILIH</option>
                                                    <option value="Operator">Operator</option>
                                                    <option value="Engineering">Engineering</option>
                                                </select>
                                            </div>
                                        </div>
                                    <div class="float-right">
                                        <button type="submit" class="btn btn-primary mb-2 btn-block ml-4"
                                            style="border-radius: 8px"><i class="fas fa-save"></i> Simpan</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                 <div class="col-sm-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">No.</th>
                                                    <th class="text-center">Kode SAP</th>
                                                    <th class="text-center">Reason</th>
                                                    <th class="text-center">Kategori Reason</th>
                                                    <th class="text-center" style="width: 45%">Opsi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($data as $val)
                                                <tr>
                                                    <td class="text-center">
                                                        {{$loop->iteration}}
                                                    </td>
                                                    <td class="text-center">
                                                        {{$val->kode_reason}}
                                                    </td>
                                                    <td class="text-center">
                                                        {{$val->reason}}
                                                    </td>
                                                    <td class="text-center">
                                                        {{$val->kategori}}
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="/logging_machine/adm_prod/delete_master_reason/{{$val->id}}" class="btn btn-sm btn-primary"><i class="fas fa-trash-alt fa-sm"></i></a>
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

    </script>

@endpush
