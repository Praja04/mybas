@extends('layouts.base-display')

@section('title', 'REPORT HARIAN PACKING ')


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
                    <div class="card-body">


                        <div class="row">
                            <div class="col-sm-12">
                                <div id="grafik">

                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="exampleFormControlSelect1">Cari Berdasarkan</label>
                                        <select class="form-control pencarian" name="pencarian">
                                            <option disabled selected>Silahkan Di Pilih</option>
                                            <option value="varian"> Varian</option>
                                            <option value="nama"> Nama</option>
                                            <option value="shift"> Shift</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3" id="varian">
                                    <form action="{{url('/logging_machine/adm_prod/pencarian/varian')}}" method="post">
                                        @csrf
                                        <div class="form-group">
                                            <label>Varian</label>
                                            <select class="form-control" name="varian">
                                                <option disabled selected>Silahkan Di Pilih</option>
                                                @foreach ($pencarian_rasa as $varian)
                                                <option value="{{$varian->rasa}}"> {{$varian->rasa}}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="btn btn-sm btn-primary btn-block mt-2"><i class="fas fa-search"></i> Cari</button>
                                        </form>
                                    </div>
                                </div>
                                <div class="col-sm-3" id="nama">
                                    <form action="{{url('/logging_machine/adm_prod/pencarian/nama')}}" method="post">
                                        @csrf
                                        <div class="form-group">
                                            <label>Nama</label>
                                            <select class="form-control" name="nama">
                                                <option disabled selected>Silahkan Di Pilih</option>
                                                @foreach ($pencarian_nama as $nama)
                                                <option value="{{$nama->nama}}"> {{$nama->nama}}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="btn btn-sm btn-primary btn-block mt-2"><i class="fas fa-search"></i> Cari</button>
                                        </form>
                                    </div>
                                </div>
                                <div class="col-sm-3" id="shift">
                                    <form action="{{url('/logging_machine/adm_prod/pencarian/shift')}}" method="post">
                                        @csrf
                                        <div class="form-group">
                                            <label>Shift</label>
                                            <select class="form-control" name="shift_group">
                                                <option disabled selected>Silahkan Di Pilih</option>
                                                @foreach ($pencarian_shift as $shift_group)
                                                <option value="{{$shift_group->shift_group}}"> {{$shift_group->shift_group}}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="btn btn-sm btn-primary btn-block mt-2"><i class="fas fa-search"></i> Cari</button>
                                        </form>
                                    </div>
                                </div>
                            <table class="table table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center">No.</th>
                                        <th class="text-center">Nama</th>
                                        <th class="text-center">NIK</th>
                                        <th class="text-center">Tanggal</th>
                                        <th class="text-center">Rasa</th>
                                        <th class="text-center">No.Mesin</th>
                                        <th class="text-center">Shift/Group</th>
                                        <th class="text-center">Status Varian</th>
                                        <th class="text-center">Hasil Box</th>
                                        <th class="text-center">Hasil Pcs</th>
                                        <th class="text-center">Opsi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $item)
                                        <tr>
                                            <td class="text-center">
                                                {{ $loop->iteration }}
                                            </td>
                                            <td class="text-center">
                                                {{ $item->nama }}
                                            </td>
                                            <td class="text-center">
                                                {{ $item->nik }}
                                            </td>
                                            <td class="text-center">
                                                {{ \Carbon\Carbon::parse($item->tgl_pengisian)->format('d-M-Y') }}
                                            </td>
                                            <td class="text-center">
                                                {{ $item->rasa }}
                                            </td>
                                            <td class="text-center">
                                                {{ $item->no_mesin }}
                                            </td>
                                            <td class="text-center">
                                                {{ $item->shift_group }}
                                            </td>
                                            <td class="text-center">
                                                @if($item->pindah_varian == 2)
                                                <span class="badge badge-pill badge-info">Pindah Varian</span>
                                                @else 
                                                -
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($item->total_produksi_box == NULL)
                                                <span class="badge badge-pill badge-primary">Belum Input</span>
                                                @else 
                                                {{ $item->total_produksi_box }}
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($item->hasil_produksi_pcs == NULL)
                                                <span class="badge badge-pill badge-primary">Belum Input</span>
                                                @else 
                                                {{ $item->hasil_produksi_pcs }}
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a href="/logging_machine/adm_prod/report_packing_day_detail/{{$item->rasa}}/{{ $item->id }}"
                                                    class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
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



@endsection

@push('scripts')

    <script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>

    <script type="text/javascript">
        $('.table').DataTable();

        $('#varian').hide();
        $('#nama').hide();
        $('#shift').hide();
          
          $('.pencarian').on('change', function() {
            if (this.value == 'varian') {
                $("#varian").show();
            } else {
                $("#varian").hide();
            }
        });
         
          $('.pencarian').on('change', function() {
            if (this.value == 'nama') {
                $("#nama").show();
            } else {
                $("#nama").hide();
            }
        });
        
          $('.pencarian').on('change', function() {
            if (this.value == 'shift') {
                $("#shift").show();
            } else {
                $("#shift").hide();
            }
        });

    </script>

@endpush
