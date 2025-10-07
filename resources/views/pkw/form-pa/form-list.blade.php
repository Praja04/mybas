@extends('layouts.base')

@push('styles')
    <style type="text/css">
        #datatable .datatable-cell {
            padding-top: 5px !important;
            padding-bottom: 5px !important;
            padding-right: 5px !important;
            padding-left: 5px !important;
        }
    </style>
@endpush

@section('content')

    <div class="container-fluid">

        <!--begin::Row-->
        <div class="row">

            <div class="col-lg-12">
                <div class="card card-custom">
                    <div class="card-header flex-wrap border-0 pt-6 pb-0">
                        <div class="card-title">
                            <h3 class="card-label">Data PKW
                                <span class="d-block text-muted pt-2 font-size-sm">Data Calon Karyawan</span></h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <!--begin: Search Form-->
                        <!--begin::Search Form-->
                        <div class="mb-7">
                            <div class="row align-items-center">
                                <div class="col-lg-9 col-xl-8">
                                    <div class="row align-items-center">
                                        <div class="col-md-4 my-2 my-md-0">
                                            <div class="input-icon">
                                                <input type="text" class="form-control" placeholder="Search..." id="datatable_search_query" />
                                                <span>
                                                <i class="flaticon2-search-1 text-muted"></i>
                                            </span>
                                            </div>
                                        </div>
                                        <div class="col-md-4 my-2 my-md-0">
                                            <div class="d-flex align-items-center">
                                                <label class="mr-3 mb-0 d-none d-md-block" style="white-space: nowrap;">Jenis Kelamin:</label>
                                                <select class="form-control" id="datatable_search_jenis_kelamin">
                                                    <option value="">All</option>
                                                    <option value="L">Laki-laki</option>
                                                    <option value="P">Perempuan</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-10 mb-5 collapse" id="datatable_group_action_form">
                            <div class="d-flex align-items-center">
                                <div class="font-weight-bold text-danger mr-3">Selected
                                    (<span id="datatable_selected_records"></span>) records:</div>
                                <button class="btn btn-sm btn-success submitButton" type="button" id="approveButton">Approve</button>
                            </div>
                        </div>
                        <!--begin: Datatable-->
                        <div class="table-responsive">
                            <div class="table-bordered table-hover datatable datatable-bordered datatable-head-custom" id="form-pa-datatable"></div>
                        </div>
                        <!--end: Datatable-->
                    </div>
                </div>
            </div>
        </div>
        <!--end::Row-->
        <!--end::Dashboard-->
    </div>

    <!-- Form PA Modal-->
    <div class="modal fade" id="formPAModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="FormPAModal" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document" style="min-height: 300px">
            <div class="modal-content">
                <form id="formPAForm">
                    <input type="hidden" id="form-pa-id" name="id_form_pa">
                    <div class="modal-header">
                        <h5 class="modal-title" id="formPAModalLabel">FORM PA PEKERJA   MASA PERCOBAAN - MASA ACTING – KWT</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="ki ki-close"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!--begin::Top-->
                        <div class="d-flex">
                            <!--begin::Pic-->
                            <div class="flex-shrink-0 mr-7">
                                <div class="symbol symbol-50 symbol-lg-120">
                                    <img alt="Pic" src="{{ url('/assets/media/users/default.jpg') }}" />
                                </div>
                            </div>
                            <!--end::Pic-->
                            <!--begin: Info-->
                            <div class="flex-grow-1">
                                <!--begin::Title-->
                                <div class="d-flex align-items-center justify-content-between flex-wrap mt-2">
                                    <!--begin::User-->
                                    <div class="mr-3">
                                        <!--begin::Name-->
                                        <a href="javascript:" class="d-flex align-items-center text-dark text-hover-primary font-size-h5 font-weight-bold mr-3"><span id="nama"></span> &nbsp; <span class="label label-light-info label-inline" id="nik"></span>
                                            <i class="flaticon2-correct text-success icon-md ml-2"></i></a>
                                        <!--end::Name-->
                                        <!--begin::Contacts-->
                                        <div class="d-flex flex-wrap my-2">
                                            <a href="javascript:" class="text-muted text-hover-primary font-weight-bold mr-lg-8 mr-5 mb-lg-0 mb-2">
                                                <span id="jenis-kelamin"><i class="fa fa-female"></i></span>
                                            </a>
                                            <a href="javascript:" class="text-muted text-hover-primary font-weight-bold mr-lg-8 mr-5 mb-lg-0 mb-2">
                                                <i class="fa fa-atom"></i> <span id="agama"></span>
                                            </a>
                                            <a href="javascript:" class="text-muted text-hover-primary font-weight-bold mr-5">
                                                <span class="svg-icon svg-icon-md svg-icon-gray-500 mr-1">
                                                    <!--begin::Svg Icon | path:assets/media/svg/icons/Map/Marker2.svg-->
                                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                            <rect x="0" y="0" width="24" height="24" />
                                                            <path d="M9.82829464,16.6565893 C7.02541569,15.7427556 5,13.1079084 5,10 C5,6.13400675 8.13400675,3 12,3 C15.8659932,3 19,6.13400675 19,10 C19,13.1079084 16.9745843,15.7427556 14.1717054,16.6565893 L12,21 L9.82829464,16.6565893 Z M12,12 C13.1045695,12 14,11.1045695 14,10 C14,8.8954305 13.1045695,8 12,8 C10.8954305,8 10,8.8954305 10,10 C10,11.1045695 10.8954305,12 12,12 Z" fill="#000000" />
                                                        </g>
                                                    </svg>
                                                    <!--end::Svg Icon-->
                                                </span>
                                                <span id="tempat-lahir"></span>
                                            </a>
                                            <a href="javascript:" class="text-muted text-hover-primary font-weight-bold mr-lg-8 mr-5 mb-lg-0 mb-2">
                                                <i class="fa fa-file-alt"></i> <span id="status-perdata"></span>
                                            </a>
                                        </div>
                                        <!--end::Contacts-->
                                    </div>
                                </div>
                                <!--end::Title-->
                                <!--begin::Content-->
                                <div class="d-flex align-items-center flex-wrap justify-content-between">
                                    <!--begin::Description-->
                                    <div class="flex-grow-1 font-weight-bold text-dark-50 py-2 py-lg-2 mr-5">
                                        <p id="alamat-rumah"></p>
                                        <p id="alamat-rumah-luar-kota"></p>
                                    </div>
                                    <!--end::Description-->
                                    <!--begin::Progress-->
                                    <!--end::Progress-->
                                </div>
                                <!--end::Content-->
                            </div>
                            <!--end::Info-->
                        </div>
                        <!--end::Top-->
                        <!--begin::Separator-->
                        <div class="separator separator-solid my-7"></div>
                        <!--end::Separator-->
                        <!--begin::Bottom-->
                        <div class="d-flex align-items-center flex-wrap">
                            <!--begin: Item-->
                            <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
                                <span class="mr-4">
                                    <i class="flaticon-calendar icon-2x text-muted font-weight-bold"></i>
                                </span>
                                <div class="d-flex flex-column text-dark-75">
                                    <span class="font-weight-bolder font-size-sm">Tanggal Masuk</span>
                                    <span class="font-weight-bolder font-size-h5">
                                        <span class="text-dark-50 font-weight-bold" id="tanggal-masuk"></span>
                                    </span>
                                </div>
                            </div>
                            <!--end: Item-->
                            <!--begin: Item-->
                            <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
                                <span class="mr-4">
                                    <i class="flaticon-network icon-2x text-muted font-weight-bold"></i>
                                </span>
                                <div class="d-flex flex-column text-dark-75">
                                    <span class="font-weight-bolder font-size-sm">Divisi</span>
                                    <span class="font-weight-bolder font-size-h5">
                                        <span class="text-dark-50 font-weight-bold" id="divisi"></span>
                                    </span>
                                </div>
                            </div>
                            <!--end: Item-->
                            <!--begin: Item-->
                            <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
                                <span class="mr-4">
                                    <i class="flaticon-network icon-2x text-muted font-weight-bold"></i>
                                </span>
                                <div class="d-flex flex-column text-dark-75">
                                    <span class="font-weight-bolder font-size-sm">Bagian</span>
                                    <span class="font-weight-bolder font-size-h5">
                                        <span class="text-dark-50 font-weight-bold" id="bagian"></span>
                                    </span>
                                </div>
                            </div>
                            <!--end: Item-->
                            <!--begin: Item-->
                            <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
                                <span class="mr-4">
                                    <i class="flaticon-network icon-2x text-muted font-weight-bold"></i>
                                </span>
                                <div class="d-flex flex-column text-dark-75">
                                    <span class="font-weight-bolder font-size-sm">Jabatan</span>
                                    <span class="font-weight-bolder font-size-h5">
                                        <span class="text-dark-50 font-weight-bold" id="jabatan"></span>
                                    </span>
                                </div>
                            </div>
                            <!--end: Item-->
                            <!--begin: Item-->
                            <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
                                <span class="mr-4">
                                    <i class="flaticon-network icon-2x text-muted font-weight-bold"></i>
                                </span>
                                <div class="d-flex flex-column text-dark-75">
                                    <span class="font-weight-bolder font-size-sm">Group</span>
                                    <span class="font-weight-bolder font-size-h5">
                                        <span class="text-dark-50 font-weight-bold" id="group"></span>
                                    </span>
                                </div>
                            </div>
                            <!--end: Item-->
                            <!--begin: Item-->
                            <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
                                <span class="mr-4">
                                    <i class="flaticon2-paper icon-2x text-muted font-weight-bold"></i>
                                </span>
                                <div class="d-flex flex-column text-dark-75">
                                    <span class="font-weight-bolder font-size-sm">Jenis Kontrak</span>
                                    <span class="font-weight-bolder font-size-h5">
                                        <span class="font-weight-bold text-uppercase label label-light-info label-inline" id="jenis-kontrak"></span>
                                    </span>
                                </div>
                            </div>
                            <!--end: Item-->
                            <!--begin: Item-->
                            <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
                                <span class="mr-4">
                                    <i class="flaticon-calendar-1 icon-2x text-muted font-weight-bold"></i>
                                </span>
                                <div class="d-flex flex-column text-dark-75">
                                    <span class="font-weight-bolder font-size-sm">Mulai</span>
                                    <span class="font-weight-bolder font-size-h5">
                                        <span class="font-weight-bold label label-light-info label-inline" id="mulai-kontrak"></span>
                                    </span>
                                </div>
                            </div>
                            <!--end: Item-->
                            <!--begin: Item-->
                            <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
                                <span class="mr-4">
                                    <i class="flaticon-calendar-1 icon-2x text-muted font-weight-bold"></i>
                                </span>
                                <div class="d-flex flex-column text-dark-75">
                                    <span class="font-weight-bolder font-size-sm">Selesai</span>
                                    <span class="font-weight-bolder font-size-h5">
                                        <span class="font-weight-bold label label-light-info label-inline" id="selesai-kontrak"></span>
                                    </span>
                                </div>
                            </div>
                            <!--end: Item-->

                        </div>
                        <!--end::Bottom-->
                        <!--begin::Separator-->
                        <div class="separator separator-solid my-7"></div>
                        <!--end::Separator-->
                            <div class="form-group row">
                                <label class="col-2 col-form-label"><strong>Fungsi Penilaian</strong></label>
                                <div class="col-10 col-form-label">
                                    <div class="radio-inline">
                                        <label class="radio radio-danger">
                                            <input required id="masa-percobaan" value="masa percobaan" type="radio" name="fungsi_penilaian">
                                            <span></span>Masa Percobaan</label>
                                        <label class="radio radio-danger">
                                            <input id="masa-acting" value="masa acting" type="radio" name="fungsi_penilaian">
                                            <span></span>Masa Acting</label>
                                        <label class="radio radio-danger">
                                            <input id="pkwt" value="pkwt" type="radio" name="fungsi_penilaian">
                                            <span></span>PKWT</label>
                                    </div>
                                </div>
                            </div>
                            <div class="alert alert-custom alert-secondary" role="alert">
                                <div class="alert-icon">
                                    <i class="flaticon-warning"></i>
                                </div>
                                <div class="alert-text">
                                    <h6>Petunjuk Penilaian</h6>
                                    <table>
                                        <tr>
                                            <td>1. </td>
                                            <td>Aspek yang dinilai pada masa Percobaan adalah</td>
                                            <td> : </td>
                                            <td>a s/d i</td>
                                        </tr>
                                        <tr>
                                            <td>2. </td>
                                            <td>Aspek yang dinilai pada masa acting adalah</td>
                                            <td> : </td>
                                            <td>a, b, c, d, e, f, g*, h,</td>
                                        </tr>
                                        <tr>
                                            <td>3. </td>
                                            <td>Aspek yang dinilai pada KWT (Kerja Waktu Tertentu) adalah</td>
                                            <td> : </td>
                                            <td>a, b, c, d, e, f, h, i</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="text-center">
                                        <th colspan="2" rowspan="2">ASPEK YANG DINILAI</th>
                                        <th colspan="3">SKALA</th>
                                    </tr>
                                    <tr>
                                        <th>Kurang Memuaskan</th>
                                        <th>Memuaskan</th>
                                        <th>Sangat Memuaskan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @php $aspek_penilaian_id = []; @endphp
                                    @foreach($aspek_penilaian as $aspek)
                                        <tr id="aspek-{{ $aspek->huruf }}">
                                            <td rowspan="2">{{ $aspek->huruf }}.</td>
                                            <td rowspan="2">
                                                <h6>{{ $aspek->judul }}</h6>
                                                <span>{{ $aspek->keterangan }}</span>
                                            </td>
                                            <td>
                                                <div style="width: 150px">
                                                    <label class="option cursor-pointer" style="padding: 5px">
                                                        <span class="option-control">
                                                            <span class="radio">
                                                                <input class="skala_1" required type="radio" name="skala_{{ $aspek->id }}" value="1">
                                                                <span></span>
                                                            </span>
                                                        </span>
                                                        <span class="option-label">
                                                            <span class="option-head">
                                                                <span class="option-title"><strong>1</strong></span>
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div style="width: 150px">
                                                    <label class="option cursor-pointer" style="padding: 5px">
                                                        <span class="option-control">
                                                            <span class="radio">
                                                                <input class="skala_2" required type="radio" name="skala_{{ $aspek->id }}" value="2">
                                                                <span></span>
                                                            </span>
                                                        </span>
                                                        <span class="option-label">
                                                            <span class="option-head">
                                                                <span class="option-title"><strong>2</strong></span>
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div style="width: 150px">
                                                    <label class="option cursor-pointer" style="padding: 5px">
                                                        <span class="option-control">
                                                            <span class="radio">
                                                                <input class="skala_3" required type="radio" name="skala_{{ $aspek->id }}" value="3">
                                                                <span></span>
                                                            </span>
                                                        </span>
                                                        <span class="option-label">
                                                            <span class="option-head">
                                                                <span class="option-title"><strong>3</strong></span>
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3"><textarea name="catatan_{{ $aspek->id }}" class="form-control border-0 p-0 pr-10 resize-none" rows="1" placeholder="Catatan :"></textarea></td>
                                        </tr>
                                        @php $aspek_penilaian_id[] = $aspek->id @endphp
                                    @endforeach
                                <input type="hidden" name="aspek_penilaian" value="{{ json_encode($aspek_penilaian_id) }}">
                                        <tr>
                                            <td colspan="2">
                                                <h6>Evaluasi Keseluruhan : </h6>
                                                <span>Hal-hal yang perlu diperbaik/ ditingkatkan, harap disebutkan langkah-langkah yang harus dan akan dilakukan oleh karyawan</span>
                                            </td>
                                            <td colspan="3">
                                                <textarea name="evaluasi" class="form-control border-0 p-0 pr-10 resize-none" rows="4" placeholder="Evaluasi :"></textarea>
                                            </td>
                                        </tr>
                                </tbody>
                            </table>
                            <div class="form-group row">
                                <label class="col-2 col-form-label"><strong>Kesimpulan</strong></label>
                                <div class="col-10 col-form-label">
                                    <div class="radio-inline">
                                        <label class="radio radio-danger">
                                            <input required id="lulus" value="lulus" type="radio" name="kesimpulan" checked="checked">
                                            <span></span>Lulus</label>
                                        <label class="radio radio-danger">
                                            <input id="lulus-dengan-catatan" value="lulus dengan catatan" type="radio" name="kesimpulan">
                                            <span></span>Lulus dengan catatan</label>
                                        <label class="radio radio-danger">
                                            <input id="tidak-lulus" value="tidak lulus" type="radio" name="kesimpulan">
                                            <span></span>Tidak Lulus</label>
                                    </div>
                                </div>
                            </div>
                            <i><strong>Diisi oleh :</strong> {{ Auth::user()->name }}</i>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="submit" class="btn btn-primary font-weight-bold float-left submitButton"><i class="la la-save"></i>Simpan Form</button>
                        <button type="button" class="btn btn-light-secondary font-weight-bold text-black-50" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')

    <script type="text/javascript">

        function showFormPA(form_id)
        {
            $('#formPAModal').modal('show');
            $('#form-pa-id').val(form_id);
            $.ajax({
                url: "{{ url('/pkw/form-pa/get') }}/" + form_id,
                type: "GET",
                dataType: "JSON",
                success: function ( response ) {
                    var data = response.data;
                    $('#nama').text(data.nama);
                    $('#nik').text(data.nik);
                    var icon_gender = data.jenis_kelamin == 'Laki-laki' ? 'male' : 'female';
                    $('#jenis-kelamin').html('\
                    <i class="fa fa-'+ icon_gender +'"></i> ' + data.jenis_kelamin + '\
                    ')
                    $('#agama').text(data.agama);
                    $('#tempat-lahir').text(data.tempat_lahir);
                    $('#alamat-rumah').text(data.alamat_rumah);
                    $('#alamat-rumah-luar-kota').text(data.alamat_rumah_luar_kota);
                    $('#tanggal-masuk').text(data.tanggal_masuk);
                    $('#status-perdata').text(data.status_perdata);
                    $('#divisi').text(data.divisi);
                    $('#bagian').text(data.bagian);
                    $('#jabatan').text(data.jabatan);
                    $('#group').text(data.group);
                    $('#jenis-kontrak').text(data.jenis_kontrak);
                    $('#mulai-kontrak').text(data.mulai_kontrak);
                    $('#selesai-kontrak').text(data.selesai_kontrak);

                    $(".radio input").removeAttr('checked');

                    if(data.status == 'filled') {
                        $('input[name=fungsi_penilaian]').removeAttr('checked');
                        $('#' + data.fungsi_penilaian.replace(/ /g, '-')).attr('checked', true);

                        $('input[name=kesimpulan]').removeAttr('checked');
                        $('#' + data.kesimpulan.replace(/ /g, '-')).attr('checked', true);

                        $('#supervisor').text(data.nama_supervisor);

                        $.each(data.aspek_penilaian, function (key, value) {
                            console.log(value.pivot.skala);
                            $("#aspek-" + value.huruf + " .skala_" + value.pivot.skala).attr("checked", true);
                        });
                    }
                },
                error: function ( e ) {
                    console.log( e );
                }
            })
        }

        $('#formPAForm').on('submit', function(e) {
            e.preventDefault();
            $('.submitButton').attr('disabled', true);
            $('.submitButton').text('Processing..');
            $.ajax({
                url: "{{ url('/') }}/pkw/form-pa/store",
                data: $(this).serialize(),
                type: "POST",
                dataType: "JSON",
                success: function ( response ) {
                    Swal.fire("Oke!", "Form pa berhasil disimpan!", "success")
                        .then((value) => {
                            $('.submitButton').removeAttr('disabled');
                            $('.submitButton').text('Submit');
                            location.reload();
                        });
                },
                error: function ( e ) {
                    // console.log( e );
                    Swal.fire("Gagal!", "Mohon coba lagi!", "error")
                    $('.submitButton').removeAttr('disabled');
                    $('.submitButton').text('Submit');
                }
            });
        });

        $('#pilih-divisi').on('change', function () {
            var divisiId = $(this).val();
            $('#pilih-bagian').attr('disabled', true);
            $.ajax({
                url: "{{ url('/') }}/bagian/get-by-divisi/"+divisiId,
                type: "GET",
                dataType: "JSON",
                success: function ( response ) {
                    $('#pilih-bagian').html('<option value="">Pilih Bagian</option>');
                    $('#pilih-bagian').removeAttr('disabled');
                    $.each(response.bagians, function (key,val) {
                        $('#pilih-bagian').append('<option value="' + val.id + '">' + val.nama_bagian + '</option>');
                    });
                },
                error: function ( error ) {
                    console.log( error );
                }
            });
        });

        $('#pilih-bagian').on('change', function () {
            var bagianId = $(this).val();
            $('#pilih-jabatan').attr('disabled', true);
            $.ajax({
                url: "{{ url('/') }}/jabatan/get-by-bagian/"+bagianId,
                type: "GET",
                dataType: "JSON",
                success: function ( response ) {
                    $('#pilih-jabatan').html('<option value="">Pilih Jabatan</option>');
                    $('#pilih-jabatan').removeAttr('disabled');
                    $.each(response.jabatans, function (key,val) {
                        $('#pilih-jabatan').append('<option value="' + val.id + '">' + val.nama_jabatan + '</option>');
                    });
                },
                error: function ( error ) {
                    console.log( error );
                }
            });
        });

        "use strict";
        // Class definition

        var KTDatatableRecordSelectionDemo = function() {
            // Private functions

            var options = {
                // datasource definition
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: HOST_URL + '/pkw/form-pa/get-all',
                            map: function(raw) {
                                // sample data mapping
                                raw = raw.data;
                                // console.log(raw);
                                var dataSet = raw;
                                if (typeof raw.data !== 'undefined') {
                                    dataSet = raw.data;
                                }
                                return dataSet;
                            },
                        },
                    },
                    pageSize: 10,
                    serverPaging: true,
                    serverFiltering: true,
                    serverSorting: true,
                },

                // layout definition
                layout: {
                    scroll: false, // enable/disable datatable scroll both horizontal and
                    footer: false // display/hide footer
                },

                // column sorting
                sortable: true,

                pagination: true,

                // columns definition
                columns: [
                    {
                        field: 'id',
                        title: 'ID',
                        sortable: false,
                        width: 30,
                        selector: {
                            class: ''
                        },
                        textAlign: 'center',
                    }, {
                        field: 'nik',
                        title: 'NIK',
                        width: 100,
                    }, {
                        field: 'nama',
                        title: 'Nama',
                        template: function(row) {
                            var output = '<div class="ml-4">\
									<div class="text-dark-75 font-weight-bolder font-size-lg mb-0">' + row.nama + '</div>\
									<a class="text-muted font-weight-bold text-hover-primary">'+ row.kontrak_ke +'</a>\
								</div>\
							</div>';
                            return output;
                        },
                        width: 220,
                    }, {
                        field: 'alamat_rumah',
                        title: 'Alamat Rumah',
                    }, {
                        field: 'jenis_kelamin',
                        title: 'Jenis Kelamin',
                    },{
                        field: 'tempat_lahir',
                        title: 'Tempat Lahir',
                    }, {
                        field: 'tanggal_lahir',
                        title: 'Tanggal Lahir',
                        type: 'date',
                        format: 'DD/MM/YYYY',
                    },{
                        field: 'jenis_pkw',
                        title: 'Jenis',
                        width: 60,
                        template: function(row) {
                            return '<span class="label label-lg font-weight-bold label-light-info label-inline">' + row.jenis_pkw + '</span>';
                        }
                    },
                    {
                        field: 'FormPA',
                        title: 'Form PA',
                        sortable: false,
                        width: 130,
                        overflow: 'visible',
                        autoHide: false,
                        template: function(row) {
                            // console.log(row);
                            var text = row.status == 'filled' ? 'Sudah diisi' : '';
                            var color_class = row.status == 'filled' ? 'btn-danger' : 'btn-default';
                            return '\
	                        <a href="javascript:;" onClick="showFormPA(\''+ row.id +'\')" class="btn btn-sm ' + color_class + ' btn-hover-primary mr-2" title="Isi Form PA">\
                                <i class="flaticon2-paper"></i>\
                                '+ text +'\
	                        </a>\
	                    ';
                        },
                    },{
                        field: 'divisi',
                        title: 'Divisi'
                    },
                    {
                        field: 'agama',
                        title: 'Agama',
                        sortable: false,
                    },
                    {
                        field: 'alamat_rumah_luar_kota',
                        title: 'Alamat Rumah Luar Kota',
                        sortable: false,
                    },{
                        field: 'tanggal_masuk',
                        title: 'Tanggal Masuk',
                        sortable: false,
                    },{
                        field: 'status_perdata',
                        title: 'Status Perdata',
                        sortable: false,
                    },{
                        field: 'nama_pasangan',
                        title: 'Nama Pasangan',
                        sortable: false,
                    },{
                        field: 'tempat_pernikahan',
                        title: 'Tempat Pernikahan',
                        sortable: false,
                    },{
                        field: 'tanggal_pernikahan',
                        title: 'Tanggal Pernikahan',
                        sortable: false,
                    },{
                        field: 'tempat_lahir_pasangan',
                        title: 'Tempat Lahir Pasangan',
                        sortable: false,
                    },{
                        field: 'tanggal_lahir_pasangan',
                        title: 'Tanggal Lahir Pasangan',
                        sortable: false,
                    },{
                        field: 'pekerjaan_pasangan',
                        title: 'Pekerjaan Pasangan',
                        sortable: false,
                    },{
                        field: 'tempat_pasangan_bekerja',
                        title: 'Tempat Pasangan Bekerja',
                        sortable: false,
                    },{
                        field: 'nama_ayah',
                        title: 'Nama Ayah',
                        sortable: false,
                    },{
                        field: 'tempat_lahir_ayah',
                        title: 'Tempat Lahir Ayah',
                        sortable: false,
                    },{
                        field: 'tanggal_lahir_ayah',
                        title: 'Tanggal Lahir Ayah',
                        sortable: false,
                    },{
                        field: 'nama_ibu',
                        title: 'Nama Ibu',
                        sortable: false,
                    },{
                        field: 'tempat_lahir_ibu',
                        title: 'Tempat Lahir Ibu',
                        sortable: false,
                    },{
                        field: 'tanggal_lahir_ibu',
                        title: 'Tanggal Lahir Ibu',
                        sortable: false,
                    },{
                        field: 'nama_ayah_mertua',
                        title: 'Nama Ayah Mertua',
                        sortable: false,
                    },
                    {
                        field: 'tempat_lahir_ayah_mertua',
                        title: 'Tempat Lahir Ayah Mertua',
                        sortable: false,
                    },
                    {
                        field: 'tanggal_lahir_ayah_mertua',
                        title: 'Tanggal Lahir Ayah Mertua',
                        sortable: false,
                    },{
                        field: 'nama_ibu_mertua',
                        title: 'Nama Ibu Mertua',
                        sortable: false,
                    },{
                        field: 'tempat_lahir_ibu_mertua',
                        title: 'Tempat Lahir Ibu Mertua',
                        sortable: false,
                    },
                    {
                        field: 'tangal_lahir_ibu_mertua',
                        title: 'Tanggal Lahir Ibu Mertua',
                        sortable: false,
                    },
                    {
                        field: 'nama_kontak_darurat',
                        title: 'Nama Kontak Darurat',
                        sortable: false,
                    },
                    {
                        field: 'hubungan_kontak_darurat',
                        title: 'Hubungan Kontak Darurat',
                        sortable: false,
                    },
                    {
                        field: 'no_telepon_kontak_darurat',
                        title: 'No Telepon Kontak Darurat',
                        sortable: false,
                    },
                    {
                        field: 'nomor_rekening_bank',
                        title: 'Nomor Rekening Bank',
                        sortable: false,
                    },
                    {
                        field: 'nomor_kartu_bpjs_ketenagakerjaan',
                        title: 'Nomor Kartu BPJS Ketenagakerjaan',
                        sortable: false,
                    },{
                        field: 'keterangan_kartu_bpjs_ketenagakerjaan',
                        title: 'Keterangan',
                        sortable: false,
                    },{
                        field: 'nomor_kartu_bpjs_kesehatan',
                        title: 'Nomor Kartu BPJS Kesehatan',
                        sortable: false,
                    },{
                        field: 'keterangan_kartu_bpjs_kesehatan',
                        title: 'Keterangan',
                        sortable: false,
                    },
                ],
            };
            var serverSelectorDemo = function() {
                // enable extension
                options.extensions = {
                    // boolean or object (extension options)
                    checkbox: true,
                };
                options.search = {
                    input: $('#datatable_search_query'),
                    key: 'generalSearch'
                };

                var datatable = $('#form-pa-datatable').KTDatatable(options);

                $('#datatable_search_jenis_kelamin').on('change', function() {
                    datatable.search($(this).val().toLowerCase(), 'jenis_kelamin');
                });

                $('#datatable_search_divisi').on('change', function() {
                    datatable.search($(this).val().toLowerCase(), 'divisi');
                });

                $('#datatable_search_jenis_kelamin, #datatable_search_divisi').selectpicker();

                datatable.on(
                    'datatable-on-click-checkbox',
                    function(e) {
                        // datatable.checkbox() access to extension methods
                        var ids = datatable.checkbox().getSelectedId();
                        var count = ids.length;

                        $('#datatable_selected_records').html(count);

                        if (count > 0) {
                            $('#datatable_group_action_form').collapse('show');
                        } else {
                            $('#datatable_group_action_form').collapse('hide');
                        }
                    });

                $('#approveButton').on('click', function() {

                    $('.submitButton').attr('disabled', true);
                    $('.submitButton').text('Processing..');
                    var data = {
                        id : datatable.checkbox().getSelectedId(),
                        approve_value : 'approve1'
                    }
                    $.ajax({
                        url: "{{ url('/') }}/pkw/form-pa/approve",
                        data: data,
                        type: "POST",
                        dataType: "JSON",
                        success: function ( response ) {
                            if(response.success) {
                                Swal.fire("Oke!", "Approve form berhasil!", "success")
                                    .then((value) => {
                                        $('.submitButton').removeAttr('disabled');
                                        $('.submitButton').text('Approve');
                                        location.reload();
                                    });
                            }else{
                                Swal.fire("Eit!", response.message, "error");
                                $('.submitButton').removeAttr('disabled');
                                $('.submitButton').text('Approve');
                            }
                        },
                        error: function ( e ) {
                            // console.log( e );
                            Swal.fire("Gagal!", "Mohon coba lagi!", "error");
                            $('.submitButton').removeAttr('disabled');
                            $('.submitButton').text('Approve');
                        }
                    });
                });
            };

            return {
                // public functions
                init: function() {
                    serverSelectorDemo();
                },
            };
        }();

        jQuery(document).ready(function() {
            KTDatatableRecordSelectionDemo.init();
        });

    </script>

@endpush
