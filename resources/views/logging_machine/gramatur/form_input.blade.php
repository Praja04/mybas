@extends('layouts.base-display')

@section('title', 'FORM INPUT GRAMATUR')

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

        <ul class="sticky-toolbar nav flex-column pl-2 pr-2 pt-3 pb-3 mt-4" style="zoom: 160%;">
            <!--begin::Item-->
            <li class="nav-item mb-2" id="kt_demo_panel_toggle" data-toggle="tooltip" title="Check out more demos"
                data-placement="right">
                <a class="btn btn-sm btn-icon btn-bg-light btn-icon-danger btn-hover-danger"
                    href="/logging_machine/index/{{ $data->nik }}">
                    <i class="fas fa-home"></i>
                </a>
            </li>
        </ul>

        <div class="main-body">
            <div class="row gutters-sm">
                <div class="col-sm-12">
                    <div class="card card-custom">
                        <div class="float-left">
                            <a href="javascript:history.back()"
                                class="btn btn-danger mt-2 ml-2"> <i class=" fas fa-arrow-left"> </i> Kembali</a>
                        </div>
                        <!--begin::Form-->
                        <form action="{{url('/logging_machine/post_gramatur')}}" method="POST">
                            @csrf

                            <input type="hidden" name="id_logging_machine" value="{{$data->id}}">

                            <div class="card-body">
                                    @if($data->pindah_varian == 2)
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="alert alert-primary" role="alert">
                                           <b>Kamu Baru Ganti Varian Baru, Silahkan Pilih Jam Timbangan Secara Manual.</b>
                                            </div>
                                        </div>
                                    </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="exampleSelect1">Jam Timbangan Ke <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control" id="exampleSelect1" name="jam_ke">
                                                    <option value="1">1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="exampleSelect1">Shift <span class="text-danger">*</span></label>
                                            <select class="form-control" id="exampleSelect1" readonly>
                                                @foreach ($shift_ke as $shift)
                                                <option value="{{ $shift }}">{{ $shift }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                        
                                        @else
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="exampleSelect1">Jam Timbangan Ke <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-control" id="exampleSelect1" readonly name="jam_ke">
                                                        @foreach ($jam_ke as $jam)
                                                            <option value="{{ $jam }}">{{ $jam }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="exampleSelect1">Shift <span class="text-danger">*</span></label>
                                                    <select class="form-control" id="exampleSelect1" readonly>
                                                        @foreach ($shift_ke as $shift)
                                                        <option value="{{ $shift }}">{{ $shift }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                <table class="table">
                                    <thead class="thead-dark">
                                    </thead>
                                    <tbody>
                                    @if($jam_ke != NULL)
                                        @if ($cek_jam == $jam_ke[0])
                                    <div class="alert alert-primary" role="alert">
                                        Ooopss, Kamu Sudah Input Sampling Ke {{ $jam_ke[0] }}
                                    </div>
                                        @else
                                        <tr>
                                            @php
                                             $nomer = 1;   
                                            @endphp
                                                @for ($i = 0; $i < 4; $i++)
                                            <td class="text-center"> 
                                                <select class="form-control" id="exampleSelect1" name="sampling_ke[]">
                                                    @for ($k = $minimum; $k < $maksimum; $k = $k + 0.01)
                                                    <option value="{{ number_format($k, 2) }}">{{ number_format($k, 2) }}</option>
                                                    @endfor
                                                </select>
                                            </td>
                                            @endfor
                                        </tr>
                                        @endif
                                    @endif

                                    @if($jam_ke != NULL)
                                          @if ($cek_jam == $jam_ke[0])

                                    <div class="alert alert-primary" role="alert">
                                        <i class="fas fa-smile text-white"></i>
                                    </div>
                                        @else
                                        <tr>
                                            @php
                                                $num = 5;
                                            @endphp
                                            @for ($i = 0; $i < 4; $i++)
                                            <td class="text-center"> 
                                                <select class="form-control" id="exampleSelect1" name="sampling_ke[]">
                                                    @for ($k = $minimum; $k < $maksimum; $k = $k + 0.01)
                                                    <option value="{{ number_format($k, 2) }}">{{ number_format($k, 2) }}</option>
                                                    @endfor
                                                </select>
                                               </td>
                                               @endfor
                                            </tr>
                                            @endif
                                        @endif
                                </table>
                            </div>
                            <div class="card-footer">

                                <input type="text" name="id_logging_machine" value="{{ $data->id }}" hidden>
                                <input type="text" name="nik" value="{{ $data->nik }}" hidden>
                        @if($jam_ke != NULL)
                            @if ($cek_jam != $jam_ke[0])
                                <button type="submit" class="btn btn-primary mr-2" style="border-radius: 7px"><i
                                        class="fas fa-save"></i> Simpan</button>
                            @endif
                        @endif
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
</script>

@endpush
