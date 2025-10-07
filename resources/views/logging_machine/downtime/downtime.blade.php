@extends('layouts.base-display')

@section('title', 'Form Input Downtime')

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

            <ul class="sticky-toolbar nav flex-column pl-2 pr-2 pt-3 pb-3 mt-4" style="zoom: 160%;">
                <!--begin::Item-->
                <li class="nav-item mb-2" id="kt_demo_panel_toggle" data-toggle="tooltip" title="Check out more demos"
                    data-placement="right">
                    <a class="btn btn-sm btn-icon btn-bg-light btn-icon-danger btn-hover-danger"
                        href="/logging_machine/index/{{ $user->nik }}">
                        <i class="fas fa-home"></i>
                    </a>
                </li>
            </ul>


            <div class="row gutters-sm">
                <div class="col-md-12 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ url('/logging_machine/post_downtime') }}" method="POST">
                                @csrf

                                <input type="hidden" name="id_logging_machine" value="{{$user->id}}">

                                <a href="/logging_machine/index/{{ $user->nik }}" class="btn btn-danger btn-sm mb-3"
                                    style="border-radius: 10px"> <i class="fas fa-arrow-left"></i> Kembali</a>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="exampleFormControlInput1">Nama Lengkap</label>
                                            <input type="text" class="form-control" value="{{ $user->nama }}" name="nama"
                                                disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="exampleFormControlInput1">NIK</label>
                                            <input type="text" class="form-control" value="{{ $user->nik }}" name="nik"
                                                disabled>
                                            <input type="text" class="form-control" value="{{ $user->nik }}" name="nik"
                                                hidden>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="exampleFormControlSelect1">Varian</label>
                                            <select class="form-control" name="varian" disabled>
                                                <option disabled selected>{{ $user->rasa }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="exampleFormControlSelect1">No. Mesin</label>
                                            <select class="form-control" name="no_mesin" id="no_mesin">
                                                <option value="{{ $user->no_mesin }}">{{ $user->no_mesin }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="exampleFormControlInput1">Shift/Group</label>
                                            <input type="text" class="form-control" placeholder="Contoh: 1/C"
                                                name="shift_group" value="{{ $user->shift_group }}" disabled>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="exampleFormControlSelect1">Kategori</label>
                                            <select class="form-control" name="jenis_maintenance" required>
                                                <option disabled selected>
                                                    Silahkan Pilih
                                                </option>
                                                <option value="Operator">
                                                    Operator
                                                </option>
                                                <option value="Engineering">
                                                    Engineering
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="exampleFormControlSelect1">Reason</label>
                                            <select class="form-control Reason" name="kode_reason" id="reason" required>
                                                    <option value=""></option>
                                            </select>
                                        </div>

                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group mb-1">
                                            <label for="exampleTextarea">Detail Kerusakan <span class="text-danger">*</span></label>
                                            <textarea class="form-control Detail" id="exampleTextarea" name="kerusakan" rows="3" placeholder="Masukan Detail Kerusakan" required></textarea>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                  <div class="row">
                                    <div class="col-sm-5"></div>
                                    <div class="col-sm-4"></div>
                                    <div class="col-sm-3">
                                        <button type="button" data-toggle="modal" data-target="#exampleModalCenter"
                                            class="btn btn-primary mb-2 btn-block"><i class="fas fa-save"></i>
                                            Simpan</button>
                                    </div>
                                </div>
                                <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog"
                                    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="yakin">Apakah Anda Yakin?</h5>
                                                <h5 class="modal-title" id="loading">Data Anda Sedang DI Simpan..</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary BtnClose"
                                                    data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary BtnSave"><i class="fas fa-save"></i>
                                                    Ya, Simpan</button>
                                            </div>
                                        </div>
                                    </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>




        @endsection

        @push('scripts')

            <script type="text/javascript">
                $('.Reason').select2({
                    placeholder: 'Select an option'
                });

            $('#loading').hide();
                 $('.BtnSave').click(function(){
                        $('.BtnSave').hide();
                        $('.BtnClose').hide();
                        $('#yakin').hide();
                        $('#loading').show();
                    });

        // $('.Detail')
        jQuery('select[name="jenis_maintenance"]').on('change', function() {
            var kategori = this.value;
            jQuery.ajax({
                url: '/logging_machine/get_reason/' + kategori,
                type: "GET",
                data: {
                    kategori: kategori
                },
                dataType: "json",
                success: function(response) {
                    if (response.success == 1) {
                        jQuery('select[name="kode_reason"]').empty();
                        jQuery.each(response.data, function(id, value) {
                            $('select[name="kode_reason"]').append('<option value="' + value
                                .kode_reason + '">' + value.reason + '</option>');
                        });
                    }
                }
            });
        });

            </script>


        @endpush
