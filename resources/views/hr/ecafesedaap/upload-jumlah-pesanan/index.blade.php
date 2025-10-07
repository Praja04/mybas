    @extends('layouts.base')

    @section('content')
        <style>
            input[type="radio"] {
                appearance: none;
                background-color: #fff;
                border: 2px solid #000;
                border-radius: 50%;
                width: 15px;
                height: 15px;
                position: relative;
            }

            input[type="radio"]:checked::before {
                content: '';
                position: absolute;
                background-color: #000;
                border-radius: 50%;
                width: 10px;
                height: 10px;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
            }

            .radio-group {
                display: flex;
                align-items: center;
            }

            .radio-group input[type="radio"] {
                margin-right: 10px;
            }

            .radio-group label {
                margin: 0;
                font-size: 14px;
            }
        </style>

        <div class="container-fluid">

            <!--begin::Row-->
            <div class="row">

                <div class="col-lg-12">
                    <!--begin::Advance Table Widget 4-->
                    <div class="card card-custom card-stretch gutter-b">
                        <!--begin::Header-->
                        {{-- notifikasi form validasi --}}
                        @if ($errors->has('file'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('file') }}</strong>
                            </span>
                        @endif


                        <!-- Import Excel -->
                        {{-- <div class="modal fade" id="importExcel" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <form method="post" action="/ecafesedaap/import_excel" enctype="multipart/form-data">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Import Excel</h5>
                                        </div>
                                        <div class="modal-body">

                                            {{ csrf_field() }}

                                            <label>Pilih file excel</label>
                                            <div class="form-group">
                                                <input type="file" name="file" required="required">
                                            </div>

                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Import</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="card-header border-0 py-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label font-weight-bolder text-dark">Upload Jumlah Pesanan</span>
                            </h3>
                            <button type="button" class="btn btn-primary mr-5" data-toggle="modal"
                                data-target="#importExcel">
                                <i class="la la-file-download"></i>Import Excel
                            </button>
                            {{-- <button type="submit" class="btn btn-info btn-lg">+ Import Excel</button> --}}
                        {{-- </div> --}} 
                        <div class="card-body">
                            <form action="{{ url('/PostPesananCatering') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <!-- Input Tanggal Umum -->
                                    <div class="form-group col-7">
                                        <label for="tanggal">Tanggal Upload Pesanan</label>
                                        <input type="date" name="tanggal" class="form-control" id="tanggal">
                                    </div>

                                    <!-- Kolom Kiri untuk Staff -->
                                    <div class="col-md-6">
                                        <h3>Staff</h3>
                                        <input type="hidden" name="kategori[staff]" value="staff">

                                        <!-- Input Shift dan Jumlah untuk Staff -->
                                        @for ($i = 1; $i <= 3; $i++)
                                            <div class="form-group">
                                                <label for="shift{{ $i }}-staff">Shift
                                                    {{ $i }}</label>
                                                <input type="hidden" name="shift[staff][{{ $i }}]"
                                                    value="{{ $i }}">
                                                <input type="number" class="form-control"
                                                    name="shift_qty[staff][{{ $i }}]" placeholder="Jumlah Porsi"
                                                    id="shift{{ $i }}-staff">
                                            </div>
                                        @endfor

                                        <div class="form-group">
                                            <label>Total Keseluruhan Staff</label>
                                            <input type="number" id="sum_shift_qty" class="form-control" disabled>
                                        </div>
                                    </div>

                                    <!-- Kolom Kanan untuk Non-Staff -->
                                    <div class="col-md-6">
                                        <h3>Non-Staff</h3>
                                        <input type="hidden" name="kategori[non-staff]" value="non-staff">

                                        <!-- Input Shift dan Jumlah untuk Non-Staff -->
                                        @for ($i = 1; $i <= 3; $i++)
                                            <div class="form-group">
                                                <label for="shift{{ $i }}-non-staff">Shift
                                                    {{ $i }}</label>
                                                <input type="hidden" name="shift[non-staff][{{ $i }}]"
                                                    value="{{ $i }}">
                                                <input type="number" class="form-control"
                                                    name="shift_qty[non-staff][{{ $i }}]"
                                                    placeholder="Jumlah Porsi" id="shift{{ $i }}-non-staff">
                                            </div>
                                        @endfor

                                        <div class="form-group">
                                            <label>Total Keseluruhan Non Staff</label>
                                            <input type="number" id="sum_non_shift_qty" class="form-control" disabled>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tombol Submit -->
                                <div class="form-group">
                                    <button type="submit" class="btn btn-info btn-lg btn-block">Simpan</button>
                                </div>
                            </form>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Advance Table Widget 4-->
                </div>
            </div>
        @endsection

        @push('scripts')
            <!--end::Row-->
            <!--end::Dashboard-->
            <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
                integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
            </script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
                integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
            </script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
                integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
            </script>
            <script>
                function hitungTotalShift() {
                    var total = 0;

                    $('input[name^="shift_qty[staff]"]').each(function() {
                        total += parseInt($(this).val()) || 0;
                    });

                    $('#sum_shift_qty').val(total);
                }

                $(document).on('input', 'input[name^="shift_qty[staff]"]', hitungTotalShift);

                function hitungTotalNonShift() {
                    var total = 0;

                    $('input[name^="shift_qty[non-staff]"]').each(function() {
                        total += parseInt($(this).val()) || 0;
                    });

                    $('#sum_non_shift_qty').val(total);
                }

                $(document).on('input', 'input[name^="shift_qty[non-staff]"]', hitungTotalNonShift);

                // wajib isi semua field
                document.querySelector('form').addEventListener('submit', function(e) {
                var tanggal = document.getElementById('tanggal').value;
                var inputsRequired = document.querySelectorAll('input[type="number"]'); 
                var isAllFilled = Array.from(inputsRequired).every(function(input) {
                    return input.value !== ''; 
                });

                if (!tanggal || !isAllFilled) {
                    e.preventDefault(); 
                    Swal.fire({
                        title: 'Error!',
                        text: 'Harap Lengkapi Data Jumlah Pesanan.',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                }
            });
            </script>
        @endpush
