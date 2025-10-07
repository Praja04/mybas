@extends('layouts.base')

@section('content')
    <div class="container-fluid">

        <!--begin::Row-->
        <div class="row">

            <div class="col-lg-12">
                <!--begin::Advance Table Widget 4-->
                <div class="card card-custom card-stretch gutter-b">
                    <!--begin::Header-->
                    {{-- notifikasi form validasi --}}
                    <!-- Import Excel -->
                    <div class="card-header border-0 py-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">Upload Data Karyawan</span>
                        </h3>

                        {{-- <button type="submit" class="btn btn-info btn-lg">+ Import Excel</button> --}}
                    </div>
                    <div class="card-body">
                        <form action="{{ url('/masukharilibur/import-data-karyawan') }}" method="POST"
                            enctype="multipart/form-data" id="form_upload">
                            @csrf
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="exampleSelectl">Tanggal Masuk Hari Libur</label>
                                        <input type="date" name="tanggal" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleSelectl">Approver</label>
                                        <select class="form-control form-control-lg" id="approver" name="nik_approver">
                                            @foreach ($users as $user)
                                                <option value="{{ $user->nik_approval }}">{{ $user->nik_approval }} -
                                                    {{ $user->nama_approval }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleSelectl">Upload Masuk Hari Libur </label>
                                        <label for=""></label>
                                        <input type="file" class="form-control-file" name="excel" id="excelFile"
                                            placeholder="Masukan" aria-describedby="fileHelpId">
                                        <a href="{{ url('/master_import/contoh_upload_masuk_hari_libur_2023.xlsx') }}"
                                            class="btn btn-sm text-white mt-4"
                                            style="background-color: green; border-radius: 15px;"><i
                                                class="fas fa-file-excel text-white"></i> Download Master Excel</a>
                                    </div>

                                    <div class="float-right">
                                        <button type="submit" class="btn btn-primary btn-sm BtnUploadFile"
                                            style="border-radius: 13px;"><i class="fas fa-save"></i> Simpan</button>
                                        <button type="button"
                                            class="btn btn-info spinner spinner-darker-info spinner-left mr-3 Proses"
                                            style="display: none;" disabled>
                                            Proses Upload..
                                        </button>
                                    </div>
                                </div>
                        </form>
                        <!--end::Body-->
                    </div>
                    <!--end::Advance Table Widget 4-->
                </div>
            </div>
        </div>
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
            $('#form_upload').on('submit', function(e) {
                e.preventDefault();

                var selectedDate = $('input[name="tanggal"]').val();

                if (selectedDate === '') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih Tanggal',
                        text: 'Silakan pilih tanggal sebelum melakukan upload.',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                } else {
                    $('.BtnUploadFile').hide();
                    $('.spinner').show();
                    this.submit();
                }
            });

        // validasi excel
        document.addEventListener("DOMContentLoaded", function () {
        var form = document.querySelector("#form_upload");
        var submitButton = form.querySelector(".BtnUploadFile");
        var fileInput = document.getElementById('excelFile');
        submitButton.disabled = true;

        function validateFileExtension() {
            var filePath = fileInput.value;
            var allowedExtensions = /(\.xls|\.xlsx|\.csv)$/i;

            if (!allowedExtensions.exec(filePath)) {
                submitButton.disabled = true; 
                Swal.fire({
                    title: "Error!",
                    text: "Harap masukkan file tipe nya xls, xlsx, atau csv yang masih berkaitan dengan excel!",
                    icon: "error",
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
            } else {
                submitButton.disabled = false;
            }
        }
        fileInput.addEventListener('change', validateFileExtension);
    });




            // $('#approver').select2({
            //     width: '100%'
            // });
        </script>
    </div>
@endsection
