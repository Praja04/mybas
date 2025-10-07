@extends('layouts.base-display')

@section('title', 'TRACKING FILE UPLOAD')

    @push('styles')
        <link rel="stylesheet" href="{{ url('/assets/plugins/custom/datatables/datatables.bundle.css') }}">
    @endpush

@section('content')

    <div class="container-fluid">
        <div class="row">

            <ul class="sticky-toolbar nav flex-column pl-2 pr-2 pt-3 pb-3 mt-4">
                <!--begin::Item-->
                <li class="nav-item mb-2" id="kt_demo_panel_toggle" data-toggle="tooltip" title="Check out more demos"
                    data-placement="right">
                    <a class="btn btn-sm btn-icon btn-bg-light btn-icon-success btn-hover-success"
                        href="/logging_machine/adm_prod/report">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </li>
                </li>
            </ul>
            <div class="col-lg-12">
                <div class="card card-custom">
                    <div class="card-header flex-wrap border-0 pt-6 pb-0">
                        <div class="card-tittle">
                            <form action="{{ url('/logging_machine/adm_prod/cari_file') }} " method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col">
                                        <input type="date" class="form-control" placeholder="Tanggal Mulai"
                                            name="tgl_mulai">
                                    </div>
                                    <div class="col">
                                        <input type="date" class="form-control" placeholder="Tanggal Selesai"
                                            name="tgl_selesai">
                                    </div>
                                </div>
                                    <div class="col-sm-12">

                                        <div class="form-group">
                                            <button type="submit" class="btn btn-info btn-sm mt-2"><i class="fas fa-search"></i>
                                                Cari</button>
                                            </div>
                                        </div>
                                </form>
                            </div>
                        </div>
                    <div class="card-body">
                        <div id="grafik">
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered">
                                        <thead>
                                            <tr class="text-center">
                                                <th class="text-center">No.</th>
                                                <th class="text-center">Prod Ord.</th>
                                                <th class="text-center">Varian</th>
                                                <th class="text-center">Shift</th>
                                                <th class="text-center">Tanggal Upload</th>
                                                <th class="text-center">Opsi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($data as $item)
                                            <tr class="text-center">
                                                <td>
                                                    {{$loop->iteration}}
                                                </td>
                                                <td>
                                                    {{$item->prod_order}}
                                                </td>
                                                <td>
                                                    {{$item->varian}}
                                                </td>
                                                <td>
                                                    {{$item->shift}}{{$item->group}}
                                                </td>
                                                <td>
                                                    {{\Carbon\carbon::parse($item->tgl_pengisian)->format('d-M-Y')}}
                                                </td>
                                                @if ($item->tgl_pengisian == date('Y-m-d'))
                                                <td>
                                                    <a href="/logging_machine/adm_prod/edit_file/{{$item->id}}" class="btn btn-info btn-sm" style="border-radius: 10px;"><i class="fas fa-edit"></i> Edit</a>
                                                    <a href="/logging_machine/adm_prod/hapus_file/{{$item->id}}" class="btn btn-primary btn-sm" style="border-radius: 10px;"><i class="fas fa-trash-alt"></i> Hapus</a>
                                                </td>
                                                @else
                                                <td class="text-center"> - </td>
                                                @endif
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



@endsection

@push('scripts')

    <script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>

    <script type="text/javascript">
        $('.table').DataTable();
    </script>

@endpush
