@push('styles')
    <style type="text/css">
        #table-data-terkini > thead > tr > th, #table-data-terkini > tbody > tr > td {
            white-space: nowrap
        }

        .table-hover tbody tr:hover {
            background-color: #FFA800 !important;
        }

        #table-data-terkini > tfoot > tr > th {
            padding: 2px
        }

        #table-data-terkini td {
            padding: 5px
        }

        .input-search {
            height: 30px;
            padding: 2px !important;
        }
        .no-padding {
            padding: 2px !important;
        }
        div.toolbar {
            text-align: right
        }
    </style>
@endpush

{{-- <div class="card card-custom bg-dark card-stretch gutter-b">
    <!--begin::Body-->
    <div class="card-body">
        <div class="font-weight-bold text-inverse-dark font-size-sm">Update data from payroll</div>
    </div>
    <!--end::Body-->
</div> --}}

<div class="row">
    <div class="col-md-6">
        <button onClick="doCompare()" class="btn btn-primary" type="button"><i class="fas fa-balance-scale"></i> Compare To Payroll</button>
    </div>
    <div class="col-md-6 text-right">
        <select class="selectpicker" id="select-column" multiple>
            <option value="0" selected>NIK</option>
            <option value="1" selected>No KTP</option>
            <option value="2" selected>No KK</option>
            <option value="3" selected>No NPWP</option>
            <option value="4" selected>Nama</option>
            <option value="5" selected>JK</option>
            <option value="6">Agama</option>
            <option value="7">Tempat Lahir</option>
            <option value="8">Tgl Lahir</option>
            <option value="9" selected>No. HP</option>
            <option value="10" selected>Email</option>
            <option value="11">Pendidikan</option>
            <option value="12">Nama Sekolah</option>
            <option value="13">Jurusan</option>
            <option value="14">Kursus</option>
            <option value="15">Golongan Darah</option>
            <option value="16">Kode Divisi</option>
            <option value="17">Kode Bagian</option>
            <option value="18">Kode Group</option>
            <option value="19">Kode Jabatan</option>
            <option value="20">Kode Admin</option>
            <option value="21">Kode Periode</option>
            <option value="22">Kode Kontrak</option>
            <option value="23">Status PPH 21</option>
            <option value="24">Jurnal Group</option>
            <option value="25">Tgl Masuk</option>
            <option value="26">Status Perdata</option>
            <option value="27">Nama Pasangan</option>
            <option value="28">Tempat Pernikahan</option>
            <option value="29">Tanggal Pernikahan</option>
            <option value="30">Tempat Lahir Pasangan</option>
            <option value="31">Tgl Lahir Pasangan</option>
            <option value="32">Pekerjaan Pasangan</option>
            <option value="33">Tempat Kerja Pasangan</option>
            <option value="34">Nama Ayah</option>
            <option value="35">Tempat Lahir Ayah</option>
            <option value="36">Tgl Lahir Ayah</option>
            <option value="37">Nama Ibu</option>
            <option value="38">Tempat Lahir Ibu</option>
            <option value="39">Tgl Lahir Ibu</option>
            <option value="40">Nama Ayah Mertua</option>
            <option value="41">Tempat Lahir Ayah Mertua</option>
            <option value="42">Tgl Lahir Ayah Mertua</option>
            <option value="43">Nama Ibu Mertua</option>
            <option value="44">Tempat Lahir Ibu Mertua</option>
            <option value="45">Tgl Lahir Ibu Mertua</option>
            <option value="46">Nama Kontak Darurat</option>
            <option value="47">Hubungan Kontak Darurat</option>
            <option value="48">No Telepon Kontak Darurat</option>
            <option value="49">No Rekening</option>
            <option value="50">No BPJS TK</option>
            <option value="51">No BPJS Kesehatan</option>
            <option value="52">Alamat KTP</option>
            <option value="53">Alamat KTP Desa</option>
            <option value="54">Alamat KTP Kecamatan</option>
            <option value="55">Alamat KTP Provinsi</option>
            <option value="56">Alamat KTP Kota</option>
            <option value="57">Alamat Sekarang</option>
            <option value="58">Alamat Sekarang Desa</option>
            <option value="59">Alamat Sekarang Kecamatan</option>
            <option value="60">Alamat Sekarang Provinsi</option>
            <option value="61">Alamat Sekarang Kota</option>
            <option value="62">Foto</option>
            <option value="63">Foto KTP</option>
            <option value="64">Foto NPWP</option>
            <option value="65">Foto KK</option>
            <option value="66">Foto Level</option>
            <option value="67">Facebook</option>
            <option value="68">Twitter</option>
            <option value="69">Linkedin</option>
            <option value="70">Instagram</option>
        </select>
    </div>
</div>
<div class="table-responsive">
    
    <table id="table-data-terkini" class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>NIK</th>
                <th>No KTP</th>
                <th>No KK</th>
                <th>No NPWP</th>
                <th>Nama</th>
                <th>JK</th>
                <th>Agama</th>
                <th>Tempat Lahir</th>
                <th>Tgl Lahir</th>
                <th>No. HP</th>
                <th>Email</th>
                <th>Pendidikan</th>
                <th>Nama Sekolah</th>
                <th>Jurusan</th>
                <th>Kursus</th>
                <th>Golongan Darah</th>
                <th>Kode Divisi</th>
                <th>Kode Bagian</th>
                <th>Kode Group</th>
                <th>Kode Jabatan</th>
                <th>Kode Admin</th>
                <th>Kode Periode</th>
                <th>Kode Kontrak</th>
                <th>Status PPH 21</th>
                <th>Jurnal Group</th>
                <th>Tgl Masuk</th>
                <th>Status Perdata</th>
                <th>Nama Pasangan</th>
                <th>Tempat Pernikahan</th>
                <th>Tanggal Pernikahan</th>
                <th>Tempat Lahir Pasangan</th>
                <th>Tgl Lahir Pasangan</th>
                <th>Pekerjaan Pasangan</th>
                <th>Tempat Kerja Pasangan</th>
                <th>Nama Ayah</th>
                <th>Tempat Lahir Ayah</th>
                <th>Tgl Lahir Ayah</th>
                <th>Nama Ibu</th>
                <th>Tempat Lahir Ibu</th>
                <th>Tgl Lahir Ibu</th>
                <th>Nama Ayah Mertua</th>
                <th>Tempat Lahir Ayah Mertua</th>
                <th>Tgl Lahir Ayah Mertua</th>
                <th>Nama Ibu Mertua</th>
                <th>Tempat Lahir Ibu Mertua</th>
                <th>Tgl Lahir Ibu Mertua</th>
                <th>Nama Kontak Darurat</th>
                <th>Hubungan Kontak Darurat</th>
                <th>No Telepon Kontak Darurat</th>
                <th>No Rekening</th>
                <th>No BPJS TK</th>
                <th>No BPJS Kesehatan</th>
                <th>Alamat KTP</th>
                <th>Alamat KTP Desa</th>
                <th>Alamat KTP Kecamatan</th>
                <th>Alamat KTP Provinsi</th>
                <th>Alamat KTP Kota</th>
                <th>Alamat Sekarang</th>
                <th>Alamat Sekarang Desa</th>
                <th>Alamat Sekarang Kecamatan</th>
                <th>Alamat Sekarang Provinsi</th>
                <th>Alamat Sekarang Kota</th>
                <th>Foto</th>
                <th>Foto KTP</th>
                <th>Foto NPWP</th>
                <th>Foto KK</th>
                <th>Foto Level</th>
                <th>Facebook</th>
                <th>Twitter</th>
                <th>Linkedin</th>
                <th>Instagram</th>
            </tr>
            <tr>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
                <th><input class="form-control" placeholder="Search" /> </th>
            </tr>
        </thead>
        {{-- <tfoot>
            <tr>
                <th>NIK</th>
                <th>No KTP</th>
                <th>No KK</th>
                <th>No NPWP</th>
                <th>Nama</th>
                <th>JK</th>
                <th>Agama</th>
                <th>Tempat Lahir</th>
                <th>Tgl Lahir</th>
                <th>No. HP</th>
                <th>Email</th>
                <th>Pendidikan</th>
                <th>Nama Sekolah</th>
                <th>Jurusan</th>
                <th>Kursus</th>
                <th>Golongan Darah</th>
                <th>Kode Divisi</th>
                <th>Kode Bagian</th>
                <th>Kode Group</th>
                <th>Kode Jabatan</th>
                <th>Kode Admin</th>
                <th>Kode Periode</th>
                <th>Kode Kontrak</th>
                <th>Status PPH 21</th>
                <th>Jurnal Group</th>
                <th>Tgl Masuk</th>
                <th>Status Perdata</th>
                <th>Nama Pasangan</th>
                <th>Tempat Pernikahan</th>
                <th>Tanggal Pernikahan</th>
                <th>Tempat Lahir Pasangan</th>
                <th>Tgl Lahir Pasangan</th>
                <th>Pekerjaan Pasangan</th>
                <th>Tempat Kerja Pasangan</th>
                <th>Nama Ayah</th>
                <th>Tempat Lahir Ayah</th>
                <th>Tgl Lahir Ayah</th>
                <th>Nama Ibu</th>
                <th>Tempat Lahir Ibu</th>
                <th>Tgl Lahir Ibu</th>
                <th>Nama Ayah Mertua</th>
                <th>Tempat Lahir Ayah Mertua</th>
                <th>Tgl Lahir Ayah Mertua</th>
                <th>Nama Ibu Mertua</th>
                <th>Tempat Lahir Ibu Mertua</th>
                <th>Tgl Lahir Ibu Mertua</th>
                <th>Nama Kontak Darurat</th>
                <th>Hubungan Kontak Darurat</th>
                <th>No Telepon Kontak Darurat</th>
                <th>No Rekening</th>
                <th>No BPJS TK</th>
                <th>No BPJS Kesehatan</th>
                <th>Alamat KTP</th>
                <th>Alamat KTP Desa</th>
                <th>Alamat KTP Kecamatan</th>
                <th>Alamat KTP Provinsi</th>
                <th>Alamat KTP Kota</th>
                <th>Alamat Sekarang</th>
                <th>Alamat Sekarang Desa</th>
                <th>Alamat Sekarang Kecamatan</th>
                <th>Alamat Sekarang Provinsi</th>
                <th>Alamat Sekarang Kota</th>
                <th>Foto</th>
                <th>Foto KTP</th>
                <th>Foto NPWP</th>
                <th>Foto KK</th>
                <th>Foto Level</th>
                <th>Facebook</th>
                <th>Twitter</th>
                <th>Linkedin</th>
                <th>Instagram</th>
            </tr>
        </tfoot> --}}
        <tbody></tbody>
    </table>
</div>

<div class="modal fade" id="modal-payroll-compare" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Compare Data To Payroll</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div id="compare-view" style="display: none">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card card-custom bg-warning gutter-b">
                                <!--begin::Body-->
                                <div class="card-body d-flex">
                                    <span class="svg-icon svg-icon-2x svg-icon-white">
                                        <!--begin::Svg Icon | path:/metronic/theme/html/demo1/dist/assets/media/svg/icons/Communication/Group.svg-->
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                                <path d="M18,14 C16.3431458,14 15,12.6568542 15,11 C15,9.34314575 16.3431458,8 18,8 C19.6568542,8 21,9.34314575 21,11 C21,12.6568542 19.6568542,14 18,14 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                                                <path d="M17.6011961,15.0006174 C21.0077043,15.0378534 23.7891749,16.7601418 23.9984937,20.4 C24.0069246,20.5466056 23.9984937,21 23.4559499,21 L19.6,21 C19.6,18.7490654 18.8562935,16.6718327 17.6011961,15.0006174 Z M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero"></path>
                                            </g>
                                        </svg>
                                        <!--end::Svg Icon-->
                                    </span>
                                    <div class="ml-5">
                                        <span class="card-title font-weight-bolder text-white font-size-h2 mb-0 d-block" id="jumlah-data-payroll"></span>
                                        <span class="font-weight-bold text-white font-size-sm">Jumlah Karyawan Payroll</span>
                                    </div>
                                </div>
                                <!--end::Body-->
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card card-custom bg-primary gutter-b">
                                <!--begin::Body-->
                                <div class="card-body d-flex">
                                    <span class="svg-icon svg-icon-2x svg-icon-white">
                                        <!--begin::Svg Icon | path:/metronic/theme/html/demo1/dist/assets/media/svg/icons/Communication/Group.svg-->
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                                <path d="M18,14 C16.3431458,14 15,12.6568542 15,11 C15,9.34314575 16.3431458,8 18,8 C19.6568542,8 21,9.34314575 21,11 C21,12.6568542 19.6568542,14 18,14 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                                                <path d="M17.6011961,15.0006174 C21.0077043,15.0378534 23.7891749,16.7601418 23.9984937,20.4 C24.0069246,20.5466056 23.9984937,21 23.4559499,21 L19.6,21 C19.6,18.7490654 18.8562935,16.6718327 17.6011961,15.0006174 Z M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero"></path>
                                            </g>
                                        </svg>
                                        <!--end::Svg Icon-->
                                    </span>
                                    <div class="ml-5">
                                        <span class="card-title font-weight-bolder text-white font-size-h2 mb-0 d-block" id="jumlah-data-local"></span>
                                        <span class="font-weight-bold text-white font-size-sm">Jumlah Karyawan Local</span>
                                    </div>
                                </div>
                                <!--end::Body-->
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div id="ada-selisih">
                                <div class="card card-custom bg-danger gutter-b">
                                    <!--begin::Body-->
                                    <div class="card-body">
                                        <span class="card-title font-weight-bolder text-white font-size-h2 mb-2 d-block">Selisih</span>
                                        <table id="table-selisih" class="table table-dark rounded">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>NIK</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>1</td>
                                                    <td>040917-25749</td>
                                                </tr>
                                            </tbody>
                                        </table>

                                        <div id="selisih-lebih-dari-seratus">
                                            <span class="text-white">Selisih lebih dari seratus</span>
                                        </div>
                                    </div>
                                    <!--end::Body-->
                                </div>
                            </div>
                            <div id="tidak-ada-selisih">
                                <div class="card card-custom bg-success gutter-b">
                                    <!--begin::Body-->
                                    <div class="card-body">
                                        <span class="card-title font-weight-bolder text-white font-size-h2 mb-2 d-block">Tidak Ada Selisih</span>
                                    </div>
                                    <!--end::Body-->
                                </div>
                            </div>
                        </div>
                        <div id="tombol-syncronize" class="col-md-12">
                            <button onClick="doSyncronize()" class="btn btn-success" type="button">Syncronize</button>
                        </div>
                    </div>
                </div>
                <div id="compare-loading">
                    <button disabled type="button" class="btn btn-outline-danger spinner spinner-darker-danger spinner-left mr-3">
                        Comparing...
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>

        function doSyncronize()
        {
            toastr.warning("Syncronize data begin. Do not reload the browser");
            $("#tombol-syncronize button").attr('disabled', true)
            $("#tombol-syncronize button").addClass('spinner spinner-white spinner-left')
            $("#tombol-syncronize button").text('Syncronizing..')
            $.ajax({
                type: "POST",
                dataType: "JSON",
                url: "{{ url('/hr/karyawan/syncronize-data') }}",
                success: function ( response ) {
                    toastr.success("Syncronize data succeed");
                    $("#tombol-syncronize button").removeAttr('disabled')
                    $("#tombol-syncronize button").removeClass('spinner spinner-white spinner-left')
                    $("#tombol-syncronize button").text('Syncronize')
                    doCompare()
                    table.ajax.reload()
                },
                error: function ( e ) {
                    $("#tombol-syncronize button").removeAttr('disabled')
                    $("#tombol-syncronize button").removeClass('spinner spinner-white spinner-left')
                    $("#tombol-syncronize button").text('Syncronize')
                    doCompare()
                    table.ajax.reload()
                }
            })
        }

        function doCompare()
        {
            $("#compare-loading").show();
            $("#compare-view").hide();
            $.ajax({
                type: "POST",
                dataType: "JSON",
                url: "{{ url('/hr/karyawan/compare-data') }}",
                success: function ( response ) {
                    $("#jumlah-data-payroll").text( response.data_payroll_count )
                    $("#jumlah-data-local").text( response.data_local_count )

                    if(response.difference_count > 0)
                    {
                        $("#ada-selisih").show();
                        $("#tidak-ada-selisih").hide();
                        $("#tombol-syncronize").show();

                        if(response.difference_count > 100)
                        {
                            $("#table-selisih").hide();
                            $("#selisih-lebih-dari-seratus").show();
                        }else{
                            $("#table-selisih tbody").html("");
                            $.each(response.difference, function (key, item) {
                                var row = "<tr>";
                                    row += "<td>"+(key+1)+"</td><td>"+item+"</td>";
                                    row += "</tr>";
                                $("#table-selisih tbody").append(row);   
                            })

                            $("#table-selisih").show();
                            $("#selisih-lebih-dari-seratus").hide();
                        }
                    }else{
                        $("#ada-selisih").hide();
                        $("#tidak-ada-selisih").show();
                        $("#tombol-syncronize").hide();
                    }

                    $("#compare-loading").hide();
                    $("#compare-view").show();
                },
                error: function ( e ) {
                    toastr.error("Compare data failed");
                    $("#compare-loading").hide();
                    $("#compare-view").hide();
                }
            })

            $("#modal-payroll-compare").modal("show");
        }


        var table = $('#table-data-terkini').DataTable({
            dom: '<t><"row"<"col-sm-12 col-md-2"l><"col-sm-12 col-md-4"i><"col-sm-12 col-md-6"p>>',
            orderCellsTop: true,
            // searching: false,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('/hr/karyawan/all') }}",
                type: "POST"
            },
            columns: [
                { data : 'nik', name : 'nik' },
                { data : 'nik_ktp', name : 'nik_ktp' },
                { data : 'no_kk', name : 'no_kk' },
                { data : 'no_npwp', name : 'no_npwp' },
                { data : 'nama', name : 'nama' },
                { data : 'jenis_kelamin', name : 'jenis_kelamin' },
                { data : 'agama', name : 'agama' },
                { data : 'tempat_lahir', name : 'tempat_lahir' },
                { data : 'tanggal_lahir', name : 'tanggal_lahir' },
                { data : 'nomor_hp', name : 'nomor_hp' },
                { data : 'email', name : 'email' },
                { data : 'pendidikan', name : 'pendidikan' },
                { data : 'nama_sekolah', name : 'nama_sekolah' },
                { data : 'jurusan', name : 'jurusan' },
                { data : 'kursus', name : 'kursus' },
                { data : 'golongan_darah', name : 'golongan_darah' },
                { data : 'kode_divisi', name : 'kode_divisi' },
                { data : 'kode_bagian', name : 'kode_bagian' },
                { data : 'kode_group', name : 'kode_group' },
                { data : 'kode_jabatan', name : 'kode_jabatan' },
                { data : 'kode_admin', name : 'kode_admin' },
                { data : 'kode_periode', name : 'kode_periode' },
                { data : 'kode_kontrak', name : 'kode_kontrak' },
                { data : 'status_pph21', name : 'status_pph21' },
                { data : 'journal_group', name : 'journal_group' },
                { data : 'tanggal_masuk', name : 'tanggal_masuk' },
                { data : 'status_perdata', name : 'status_perdata' },
                { data : 'nama_pasangan', name : 'nama_pasangan' },
                { data : 'tempat_pernikahan', name : 'tempat_pernikahan' },
                { data : 'tanggal_pernikahan', name : 'tanggal_pernikahan' },
                { data : 'tempat_lahir_pasangan', name : 'tempat_lahir_pasangan' },
                { data : 'tanggal_lahir_pasangan', name : 'tanggal_lahir_pasangan' },
                { data : 'pekerjaan_pasangan', name : 'pekerjaan_pasangan' },
                { data : 'tempat_pasangan_bekerja', name : 'tempat_pasangan_bekerja' },
                { data : 'nama_ayah', name : 'nama_ayah' },
                { data : 'tempat_lahir_ayah', name : 'tempat_lahir_ayah' },
                { data : 'tanggal_lahir_ayah', name : 'tanggal_lahir_ayah' },
                { data : 'nama_ibu', name : 'nama_ibu' },
                { data : 'tempat_lahir_ibu', name : 'tempat_lahir_ibu' },
                { data : 'tanggal_lahir_ibu', name : 'tanggal_lahir_ibu' },
                { data : 'nama_ayah_mertua', name : 'nama_ayah_mertua' },
                { data : 'tempat_lahir_ayah_mertua', name : 'tempat_lahir_ayah_mertua' },
                { data : 'tanggal_lahir_ayah_mertua', name : 'tanggal_lahir_ayah_mertua' },
                { data : 'nama_ibu_mertua', name : 'nama_ibu_mertua' },
                { data : 'tempat_lahir_ibu_mertua', name : 'tempat_lahir_ibu_mertua' },
                { data : 'tanggal_lahir_ibu_mertua', name : 'tanggal_lahir_ibu_mertua' },
                { data : 'nama_kontak_darurat', name : 'nama_kontak_darurat' },
                { data : 'hubungan_kontak_darurat', name : 'hubungan_kontak_darurat' },
                { data : 'no_telepon_kontak_darurat', name : 'no_telepon_kontak_darurat' },
                { data : 'nomor_rekening_bank', name : 'nomor_rekening_bank' },
                { data : 'nomor_kartu_bpjs_ketenagakerjaan', name : 'nomor_kartu_bpjs_ketenagakerjaan' },
                { data : 'nomor_kartu_bpjs_kesehatan', name : 'nomor_kartu_bpjs_kesehatan' },
                { data : 'alamat_ktp', name : 'alamat_ktp' },
                { data : 'alamat_ktp_desa', name : 'alamat_ktp_desa' },
                { data : 'alamat_ktp_kecamatan', name : 'alamat_ktp_kecamatan' },
                { data : 'alamat_ktp_provinsi', name : 'alamat_ktp_provinsi' },
                { data : 'alamat_ktp_kota', name : 'alamat_ktp_kota' },
                { data : 'alamat_sekarang', name : 'alamat_sekarang' },
                { data : 'alamat_sekarang_desa', name : 'alamat_sekarang_desa' },
                { data : 'alamat_sekarang_kecamatan', name : 'alamat_sekarang_kecamatan' },
                { data : 'alamat_sekarang_provinsi', name : 'alamat_sekarang_provinsi' },
                { data : 'alamat_sekarang_kota', name : 'alamat_sekarang_kota' },
                { data : 'foto_diri', name : 'foto_diri' },
                { data : 'foto_ktp', name : 'foto_ktp' },
                { data : 'foto_npwp', name : 'foto_npwp' },
                { data : 'foto_kk', name : 'foto_kk' },
                { data : 'level', name : 'level' },
                { data : 'sosmed_facebook', name : 'sosmed_facebook' },
                { data : 'sosmed_twitter', name : 'sosmed_twitter' },
                { data : 'sosmed_linkedin', name : 'sosmed_linkedin' },
                { data : 'sosmed_instagram', name : 'sosmed_instagram' },
            ],
            // initComplete: function () {
            //     this.api().columns().every(function () {
            //         var column = this;
            //         var input = document.createElement("input");
            //         input.classList.add('form-control')
            //         input.classList.add('input-search')
            //         $(input).appendTo($(column.footer()).empty())
            //         .on('change', function () {
            //             column.search($(this).val(), false, false, true).draw();
            //         });
            //     });
            // }
        });
        // $('#table-data-terkini thead tr').clone(false).appendTo( '#table-data-terkini thead' );
        $('#table-data-terkini thead tr:eq(1) th').each( function (i) {
            // var title = $(this).text();
            $(this).addClass('no-padding');
            $(this).removeClass('sorting');
            $(this).html( '<input class="form-control input-search" type="text" placeholder="Search" />' );
    
            $( 'input', this ).on( 'keyup change', function () {
                if ( table.column(i).search() !== this.value ) {
                    table.column(i).search( this.value ).draw();
                }
            } );
        } );
        
        $("#select-column").change(function () {
            changeColumnVisibility();
        })

        changeColumnVisibility();

        function changeColumnVisibility()
        {
            var all_column = ["0","1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31","32","33","34","35","36","37","38","39","40","41","42","43","44","45","46","47","48","49","50","51","52","53","54","55","56","57","58","59","60","61","62","63","64","65","66","67","68","69","70"];
            var visible_column = $("#select-column").val();
            table.columns(all_column).visible(false);
            table.columns(visible_column).visible(true);
        }

        $("#select-column").selectpicker()
    </script>
@endpush