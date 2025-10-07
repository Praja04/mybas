@extends('layouts.base-display')

@section('title', 'DASHBOARD TABLET')

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

            <div class="row gutters-sm">
                <div class="col-sm-4 mb-3">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-tittle text-center">
                                FORM INPUT HASIL PRODUKSI
                            </div>
                        </div>
                        <div class="card-body">
                            @if ($data->total_produksi_box != null)
                            <div class="row">
                                <div class="col-sm-12">
                                     <div class="alert alert-primary" role="alert">
                                <h4 class="alert-heading">Well done!</h4>
                                <hr>
                                <p><b>Kamu Sudah Memasukan Hasil Produksi</b> </p>
                                <p><a href="javascript:history.back()" class="btn btn-info btn-sm"><i class="fas fa-arrow-left"></i> Kembali</p></a>
                                </div>
                                </div>
                            </div>
                            @else
                            <form action="/logging_machine/post_hasil_produksi/{{ Crypt::encrypt($data->id) }}"
                                method="POST">
                                @csrf
                                @method('PATCH')
                                <div class="form-group">
                                    <label for="exampleFormControlSelect1">Hasil(Box)</label>
                                    <input type="number" class="form-control" name="hasil_produksi_box"
                                        placeholder="Masukan Hanya Angka.." value="{{count($counter)}}" required>

                                </div>
                                <div class="form-group">
                                    <label for="exampleFormControlSelect1">Hasil(Pcs)</label>
                                    <input type="number" class="form-control" name="hasil_produksi_pcs"
                                        placeholder="Masukan Hanya Angka.." value="{{$total_pcs}}" required>
                                </div>
                                <div class="form-group row">
                                    <label class="col-3 col-form-label">Kondisi Gear Box</label>
                                    <div class="col-9 col-form-label">
                                        <div class="checkbox-list">
                                            <label class="checkbox">
                                                <input type="checkbox" checked="checked" name="kondisi_gear" value="OK" />
                                                <span></span>OK</label>
                                            <label class="checkbox">
                                                <input type="checkbox" name="kondisi_gear" value="NOT OK" />
                                                <span></span>NOT OK</label>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <a href="javascript:history.back()" class="btn btn-info"><i class="fas fa-arrow-left"></i>
                                    Kembali</a>
                                <a href="#konfirm" data-toggle="modal" class="btn btn-primary"
                                    style="border-radius: 10px"><i class="fas fa-save"></i> Simpan</a>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-tittle text-center">
                                INFORMASI PIECE COUNTER
                            </div>
                        </div>
                        <div class="card-body">

                            <div class="alert alert-primary" role="alert">
                                <h4 class="alert-heading">Well done!</h4>
                                <hr>
                                <p>Total Produksi Box Piece Counter : <b>{{count($counter)}} BOX</b> </p>
                                <p class="mb-0">Total Produksi Pcs Piece Counter : <b>{{$total_pcs}} PCS</b></p>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Box Ke.</th>
                                            <th class="text-center">Tanggal, Jam</th>
                                            <th class="text-center">Total Pcs</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($counter as $item)
                                            <tr class="text-center">
                                                <td>
                                                    {{ $loop->iteration }}
                                                </td>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($item->TglJam)->format('d-M-Y H:i:s') }}
                                                </td>
                                                <td>
                                                    {{ $item->CntDev }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="konfirm" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">KONFIRMASI</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h6>Saya Yang Bertanda Tangan Di Bawah Ini. </h6>
                    <hr>
                    <h6 class="mt-4">Nama : {{ $data->nama }} </h6>
                    <h6 class="mt-2">NIK : {{ $data->nik }} </h6>
                    <hr>
                    <h6 class="mt-4">Dengan Ini Saya Menyatakan Bahwa Saya Telah Selesai Mengisi Administrasi
                        Produksi
                        Dalam Sistem Aplikasi. </h6>
                    <hr>
                    <h6 class="mt-2">Demikian Laporan Ini Saya Buat Dengan Data Yang Sebenar-benarnya Dan Dapat Di
                        Pertanggung Jawabkan </h6>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" onclick="Simpan()" class="btn btn-primary" style="border-radius: 10px"><i class="fas fa-save"></i>
                        Simpan</button>
                </div>
                </form>
            </div>
        </div>
    </div>


@endsection

@push('scripts')

    <script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>

    <script type="text/javascript">
        $('.table').DataTable();
          
        function Simpan() {
          sessionStorage.clear();
        }

          if(sessionStorage.length == 0 )
            {
                    location.href = "{{ url('/display/logging_machine') }}";
            }

    </script>

@endpush
