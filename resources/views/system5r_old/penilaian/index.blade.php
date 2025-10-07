@extends('system5r.layouts.base')

@section('title', 'Penilaian')

@push('styles')
    <style>
        table p {
            margin-bottom: 0 !important
        }
        .modal-backdrop
        {
            opacity:0.99 !important;
        }
    </style>
@endpush

@php
    $colors = ['#264653', '#2a9d8f', 'e9c46a', '#f4a261', '#e76f51'];
    $textColors = ['#fff', '#fff', '#000', '#000', '#000'];
@endphp

@section('content')

<div class="container-fluid">

    {{-- Not allowed message card with material icon --}}
    @if (!$isJuri)
    <!-- Danger Alert -->
    <div class="alert alert-danger alert-dismissible alert-additional shadow fade show" role="alert">
        <div class="alert-body">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            <div class="d-flex">
                <div class="flex-shrink-0 me-3">
                    <i class="ri-error-warning-line fs-16 align-middle"></i>
                </div>
                <div class="flex-grow-1">
                    <h5 class="alert-heading">Something is very wrong!</h5>
                    <p class="mb-0">Kamu tidak termasuk sebagai juri</p>
                </div>
            </div>
        </div>
    </div>
    @else

    <div class="card">
        <div class="card-body">
            <h3>PENILAIAN 5R</h3>
            {{-- filter data penilaian --}}
            <form action="{{ route('5r-system.penilaian') }}" class="pt-2" method="GET">
                {{-- jadwal dropdown --}}
                <div class="form-group pt-2">
                    <label for="filter_jadwal">Jadwal <span style="color: red">*</span></label>
                    <select name="filter_jadwal" id="filter_jadwal" class="form-control" onchange="this.form.submit()">
                        <option value="">Pilih Jadwal</option>
                        @foreach($jadwal as $j)
                            <option value="{{ $j->id_jadwal }}" {{ request('filter_jadwal') == $j->id_jadwal ? 'selected' : '' }}>
                                {{ $j->tahun }}
                            </option>
                        @endforeach
                    </select>
                </div>
            
                <!-- Periode Dropdown -->
                <div class="form-group pt-2">
                    <label for="filter_periode">Periode <span style="color: red">*</span></label>
                    <select name="filter_periode" id="filter_periode" class="form-control" onchange="this.form.submit()">
                        <option value="">Pilih Periode</option>
                        @foreach($periode as $period)
                            <option value="{{ $period->id_periode }}" {{ request('filter_periode') == $period->id_periode ? 'selected' : '' }}>
                                {{ $period->nama_periode }}
                            </option>
                        @endforeach
                    </select>
                </div>
            
                {{-- departments dropdown --}}
                <div class="form-group pt-2">
                    <label for="filter_department">Department <span style="color: red">*</span></label>
                    <select name="filter_department" id="filter_department" class="form-control" onchange="this.form.submit()">
                        <option value="">Pilih Department</option>
                        @foreach($departments as $department)
                            @if($department->department && $department->id_periode == request('filter_periode'))
                                <option value="{{ $department->department->id_department }}" {{ request('filter_department') == $department->department->id_department ? 'selected' : '' }}>
                                    {{ $department->department->nama_department }}
                            @endif
                        @endforeach
                    </select>
                </div>
            
                <div class="mt-4 mb-4">
                    <button type="submit" class="btn btn-secondary btn-animation waves-effect waves-light pt-2 pb-2 pl-2">
                        <i class="bx bx-search-alt-2"></i> Cari
                    </button>
                    <button type="button" class="btn btn-warning btn-animation waves-effect waves-light" onclick="window.location.href='{{ url('5r-system/penilaian') }}'">
                        <i class="bx bx-reset"></i> Reset
                    </button>
                </div>                
            </form>
            
            <!-- Primary Alert -->
            <table class="table table-hover align-middle">
                <thead>
                    <tr class="pas-background-color text-white">
                        <th>TAHUN</th>
                        <th>PERIODE</th>
                        <th>GROUP</th>
                        <th>DEPARTMENT</th>
                        <th>PENILAIAN</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sortedDepartments as $item)
                        @if(isset($_GET['filter_periode']))
                            @if($_GET['filter_periode'] == $item->id_periode)
                                @if(isset($_GET['filter_jadwal']) && $_GET['filter_jadwal'] == $item->periode->jadwal->id_jadwal && isset($_GET['filter_department']) && $_GET['filter_department'] == $item->id_department)
                                    <tr>
                                        <td>{{ $item->periode->jadwal->tahun }}</td>
                                        <td>{{ $item->periode->nama_periode }}</td>
                                        <td>{{ $item->group->nama_group }}</td>
                                        <td>{{ optional($item->department)->nama_department }}</td>
                                        <td>
                                            <div class="d-flex">
                                                <form action="">
                                                    <div class="row" style="display: none">
                                                        <div class="col-md-2">
                                                            <div class="form-group mb-3">
                                                                <label for="filter_jadwal">JADWAL</label>
                                                                <select required name="filter_jadwal" id="filter_jadwal" class="form-control">
                                                                    <option value="{{ $item->periode->id_jadwal }}">{{ $item->periode->id_jadwal }}</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group mb-3">
                                                                <label for="filter_periode">PERIODE</label>
                                                                <select required name="filter_periode" id="filter_periode" class="form-control">
                                                                    <option value="{{ $item->id_periode }}">{{ $item->id_periode }}</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group mb-3">
                                                                <label for="filter_department">DEPARTMENT</label>
                                                                <select name="filter_department" required id="filter_department" class="form-control">
                                                                    <option value="{{ $item->id_department }}">{{ $item->id_department }}</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <button type="submit" class="btn btn-sm btn-primary waves-effect shadow-none">
                                                            <i class="mdi mdi-paper-plane"></i>
                                                            GO
                                                        </button>
                                                    </div>
                                                </form>
                                                <a href="{{ url('5r-system/penilaian') }}" class="btn btn-light btn-sm ms-2">
                                                    <i class="mdi mdi-arrow-left"></i> Kembali
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endif
                        @else
                            <tr>
                                <td>{{ $item->periode->jadwal->tahun }}</td>
                                <td>{{ $item->periode->nama_periode }}</td>
                                <td>{{ $item->group->nama_group }}</td>
                                <td>{{ optional($item->department)->nama_department }}</td>
                                <td>
                                    @if($item->periode->selesai == 'N')
                                        <form action="">
                                            <div class="row" style="display: none">
                                                <div class="col-md-2">
                                                    <div class="form-group mb-3">
                                                        <label for="filter_jadwal">JADWAL</label>
                                                        <select required name="filter_jadwal" id="filter_jadwal" class="form-control">
                                                            <option value="{{ $item->periode->id_jadwal }}">{{ $item->periode->id_jadwal }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group mb-3">
                                                        <label for="filter_periode">PERIODE</label>
                                                        <select required name="filter_periode" id="filter_periode" class="form-control">
                                                            <option value="{{ $item->id_periode }}">{{ $item->id_periode }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group mb-3">
                                                        <label for="filter_department">DEPARTMENT</label>
                                                        <select name="filter_department" required id="filter_department" class="form-control">
                                                            <option value="{{ $item->id_department }}">{{ $item->id_department }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <button type="submit" class="btn btn-sm btn-primary waves-effect shadow-none">
                                                    <i class="mdi mdi-paper-plane"></i>
                                                    GO
                                                </button>
                                            </div>
                                        </form>
                                    @else
                                        <i class="mdi mdi-check text-success"></i>
                                        <span class="text-success ms-2">Selesai</span>
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
                
                
            </table>
            @if(isset($_GET['filter_department']))
            <div class="row">
                <div class="col-md-2">
                    <div class="nav nav-pills flex-column nav-pills-tab custom-verti-nav-pills text-center" role="tablist" aria-orientation="vertical">
                        @foreach ($groups as $group)
                        <a class="nav-link @if($current_id_group == $group->id_group) active show @endif" id="custom-v-pills-{{ $group->id_group }}-tab" href="{{ route('5r-system.penilaian', ['id_group' => $group->id_group]) }}?filter_jadwal={{$_GET['filter_jadwal']}}&filter_periode={{$_GET['filter_periode']}}&filter_department={{$_GET['filter_department']}}" role="tab" aria-controls="custom-v-pills-{{ $group->id_group }}" aria-selected="true">
                            <div class="d-block fs-20 mb-1">
                                <i class="">{{ $loop->iteration }}</i>
                                @if($jawabanGroup->where('id_group', $group->id_group)->where('id_periode', $_GET['filter_periode'])->first() != null)
                                <i class="mdi mdi-check-bold text-success"></i>
                                @endif
                            </div>
                            {{ $group->nama_group }}
                        </a>
                        @endforeach
                    </div>
                </div>
                <div class="col-md-10">
                    <div class="tab-content text-muted mt-3 mt-lg-0">
                        @foreach ($pertanyaan as $group)
                        @php
                            $jawaban = $jawabanGroup->where('id_group', $group->id_group)->where('id_periode', $_GET['filter_periode'])->first();
                            if ($jawaban != null) {
                                $jawaban = $jawaban->jawaban;
                            }

                            // dd($jawaban);
                        @endphp
                        <div class="tab-pane fade @if($loop->iteration == 1) active show @endif" id="custom-v-pills-{{ $group->id_group }}" role="tabpanel" aria-labelledby="custom-v-pills-{{ $group->id_group }}-tab">
                            <form class="form-pertanyaan" id="form-{{ $group->id_group }}">
                                <input type="hidden" name="id_group" value="{{ $group->id_group }}">
                                <input type="hidden" name="id_periode" value="{{ $_GET['filter_periode'] }}">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr class="pas-background-color">
                                                <th class="text-white px-1" style="border-width: 1px solid #fff">GROUP</th>
                                                <th class="text-white">PERTANYAAN</th>
                                                {{-- <th class="text-white">KETERANGAN</th> --}}
                                                <th class="text-white">NILAI</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach (['RINGKAS', 'RAPI', 'RESIK', 'RAWAT', 'RAJIN'] as $jenis)
                                                @php
                                                    $__pertanyaan = $group->pertanyaan->where('jenis', $jenis)->where('archive_status', 'N');
                                                @endphp
                                                @foreach ($__pertanyaan as $_pertanyaan)
                                                <tr>
                                                    @if($loop->first)
                                                    <td class="p-0" style="vertical-align: middle; font-size: 10px; font-weight: bold; text-align: center; background-color: {{ $colors[$loop->parent->iteration-1] }}; color: {{ $textColors[$loop->parent->iteration-1] }}" rowspan="{{ count($__pertanyaan) }}">
                                                        {{ $_pertanyaan->jenis }}<br />
                                                        <button onClick="showKisiKisi('{{ $_pertanyaan->jenis }}')" type="button" class="btn p-1 pb-0 btn-primary waves-effect" style="font-size: 10px;">Info</button>
                                                    </td>
                                                    @endif
                                                    <td>
                                                        <div style="width: 300px">
                                                            <h6>ITEM PERIKSA</h6>
                                                            {!! str_replace('||--||', '&', $_pertanyaan->item_periksa) !!}
                                                            <h6 class="mt-3">KETERANGAN</h6>
                                                            {!! str_replace('||--||', '&', $_pertanyaan->keterangan) !!}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div>
                                                            @if($jawaban != null)
                                                                @php
                                                                $__jawaban = $jawaban->where('id_pertanyaan', $_pertanyaan->id_pertanyaan)->first();
                                                                @endphp
                                                            @else
                                                                @php
                                                                $__jawaban = null;
                                                                @endphp
                                                            @endif
                                                            <h6>PILIH NILAI <span title="Wajib diisi" class="text-danger">*</span></h6>
                                                            @if($jawaban != null)
                                                            @php
                                                                $nilai = $__jawaban != null ? $__jawaban->nilai : '';
                                                            @endphp
                                                            <select disabled class="form-control" style="width: 100px" name="nilai[{{ $_pertanyaan->id_pertanyaan }}]">
                                                                <option value="">PILIH</option>
                                                                <option @if($nilai == '1') selected @endif value="1">1</option>
                                                                <option @if($nilai == '2') selected @endif value="2">2</option>
                                                                <option @if($nilai == '3') selected @endif value="3">3</option>
                                                                <option @if($nilai == '4') selected @endif value="4">4</option>
                                                            </select>
                                                            @else
                                                            <select required onchange="changeNilai('{{ $_pertanyaan->id_group }}-{{ $_pertanyaan->id_pertanyaan }}')" class="form-control" style="width: 100px" name="nilai[{{ $_pertanyaan->id_pertanyaan }}]" id="{{ $_pertanyaan->id_group }}-{{ $_pertanyaan->id_pertanyaan }}">
                                                                <option value="">PILIH</option>
                                                                <option value="1">1</option>
                                                                <option value="2">2</option>
                                                                <option value="3">3</option>
                                                                <option value="4">4</option>
                                                            </select>
                                                            @endif
                                                        </div>
                                                        <div class="mt-3">
                                                            <h6>TAMBAHKAN FOTO <span class="text-danger">*</span></h6>
                                                            @if($jawaban != null)
                                                                @php
                                                                    $foto = $__jawaban != null ? $__jawaban->foto : null;
                                                                @endphp
                                                                @if($foto != null)
                                                                <div class="image-container">
                                                                    @foreach (explode(',', $foto) as $_foto)
                                                                    <div style="margin-bottom: 2px">
                                                                        <img src="{{ asset('images/5r/'. $_foto) }}" style="width: 100%; height: 100%; object-fit: contain" alt="">
                                                                    </div>                                                                        
                                                                    @endforeach
                                                                </div>
                                                                @else
                                                                <i>No Image</i>
                                                                @endif
                                                            @else
                                                            <div class="image-container mb-1" id="{{ $_pertanyaan->id_group }}-{{ $_pertanyaan->id_pertanyaan }}-image-container">
                                                            </div>
                                                            <input onchange="addImage('{{ $_pertanyaan->id_group }}-{{ $_pertanyaan->id_pertanyaan }}')" type="file" multiple accept="image/jpeg" class="form-control" name="{{ $_pertanyaan->id_group }}_{{ $_pertanyaan->id_pertanyaan }}_foto" id="{{ $_pertanyaan->id_group }}-{{ $_pertanyaan->id_pertanyaan }}-input-file">
                                                            <div class="textarea_image_container" style="display: none" id="{{ $_pertanyaan->id_group }}-{{ $_pertanyaan->id_pertanyaan }}_image_container"></div>
                                                            @endif
                                                        </div>
                                                        <div class="mt-3">
                                                            <h6>KETERANGAN <span class="text-danger">*</span></h6>
                                                            <textarea onChange="changeKeterangan('{{ $_pertanyaan->id_group }}-{{ $_pertanyaan->id_pertanyaan }}')" onKeyup="changeKeterangan('{{ $_pertanyaan->id_group }}-{{ $_pertanyaan->id_pertanyaan }}')" required name="keterangan[{{ $_pertanyaan->id_pertanyaan }}]" id="keterangan_{{ $_pertanyaan->id_group }}-{{ $_pertanyaan->id_pertanyaan }}" class="form-control" {{ $__jawaban != null ? 'disabled' : '' }} placeholder="Keterangan tambahan">{{ $__jawaban != null ? $__jawaban->keterangan : '' }}</textarea>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if($jawabanGroup->where('id_group', $group->id_group)->where('id_periode', $_GET['filter_periode'])->first() == null)
                                <div class="mt-3">
                                    <button type="submit" form="form-{{ $group->id_group }}" class="btn btn-full waves-effect" id="btnSimpan" data-id-group="{{ $group->id_group }}" style="background-color: purple; color:#fff;">
                                        {{-- <i class="mdi mdi-eye"></i>  --}}
                                        Kirim Penilaian
                                    </button>
                                </div>
                                @endif    
                            {{-- @if($jawabanGroup->where('id_group', $group->id_group)->where('id_periode', $_GET['filter_periode'])->first() == null)
                            <div id="keteranganModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modalLabel1">Konfirmasi Penilaian</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                                        </div>
                                        <div class="modal-body p-5">
                                            <div class="text-left mb-4">
                                                <h5>Juri</h5>
                                                <div>Nik: {{ Auth::user()->username }}</div>
                                                <div>Nama: {{ Auth::user()->name }}</div>
                                                <p>Dengan ini menyatakan penilaian 5R di group ini dan department ini telat selesai</p>
                                            </div>
                                            <button type="button" class="btn btn-warning btn-block mb-3" id="btnSetujui">Kirim</button>
                                            <div id="form-committee" style="display: none;">
                                                <hr>
                                                <h5>Committee Department</h5>
                                                <!-- Tambahkan input tersembunyi untuk 'id_group' -->
                                                <input type="hidden" id="id_group" name="id_group" value="{{ $group->id_group }}">
                                                <div class="form-group">
                                                    <label for="username">Username</label>
                                                    <input type="text" id="username" name="username" class="form-control">
                                                    <div class="valid-feedback" id="usernameSuccess">Username sesuai.</div>
                                                    <div class="invalid-feedback" id="usernameError">Username tidak sesuai.</div>
                                                </div>
                                                <div class="form-group mt-2">
                                                    <label for="password">Password</label>
                                                    <input type="password" id="password" name="password" class="form-control">
                                                    <div class="valid-feedback" id="passwordSuccess">Password sesuai.</div>
                                                    <div class="invalid-feedback" id="passwordError">Password tidak sesuai.</div>
                                                </div>                                         
                                                <button type="button" class="btn btn-warning mt-4" id="btnCommittee">Terima</button>
                        
                                                <div class="mt-3" id="btnSimpanContainer" style="display: none;">
                                                    <hr>
                                                    <button type="submit" form="form-{{ $group->id_group }}" class="btn btn-success btn-block waves-effect btn-konfirmasi-penilaian" id="btnSimpan" data-id-group="{{ $group->id_group }}">
                                                        <i class="mdi mdi-content-save"></i>
                                                        SIMPAN
                                                    </button>
                                                </div>                        
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>  
                            @endif     --}}
                            </form>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <div class="modal fade" id="kisi-kisi-modal" tabindex="-1" role="dialog" aria-labelledby="kisi-kisi-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0" style="background-color: transparent">
                <div class="modal-header pb-3 d-flex justify-content-end px-0" style="background-color: transparent">
                    <button type="button" class="btn btn-danger p-2 py-1 m-0" data-bs-dismiss="modal" aria-label="Close">
                        <i class="mdi mdi-close"></i>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <img src="" style="width: 100%" id="kisi-kisi-image" alt="Kisi-kisi">
                </div>
            </div>
        </div>
    </div>

    @endif
</div>


@endsection

@push('scripts')

<script>

    var isBtnSimpanClicked = false;
    var isBtnSetujuiClicked = false;
    
    function showModal(idGroup) {
        var formId = "#form-" + idGroup;
        // if (validateForm(formId)) {
        //     document.getElementById('id_group').value = idGroup;

        var data = $(formId).serialize();

        // Form id
        // var formId = $(formId).attr('id');

        // List all .textarea_image_container inside form
        var textareaImageContainer = $(formId).find('.textarea_image_container');

        // Alert if textarea image container is null
        for (let i = 0; i < textareaImageContainer.length; i++) {
            // alert('test')
            if (textareaImageContainer[i].innerHTML == '') {
                // Get textarea_image_container id
                var textareaImageContainerId = textareaImageContainer[i].id;

                // Go to textarea_image_container
                document.getElementById(textareaImageContainerId.replace('_image_container', '-input-file')).focus();

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Foto tidak boleh kosong',
                    showConfirmButton: false,
                    timer: 1000
                })
                return;
            }
        }

        // Cek semua input dan select yang required pastikan sudah terisi
        var inputs = $(formId).find('input, select, textarea');
        for (let i = 0; i < inputs.length; i++) {
            if (inputs[i].required && !inputs[i].value.trim()) {
                inputs[i].focus();
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Mohon lengkapi semua data sebelum lanjut ke konfirmasi penilaian',
                    showConfirmButton: false,
                    timer: 1000
                })
                return;
            }
        }

        $('#keteranganModal').modal('show');
        // } else {
        //     alert("Mohon lengkapi semua data sebelum lanjut ke konfirmasi penilaian.");
        // }
    }

// function validateForm(formId) {
//     var form = document.getElementById(formId);
//     var inputs = form.querySelectorAll('input, select, textarea');
//     for (var i = 0; i < inputs.length; i++) {
//         if (inputs[i].required && !inputs[i].value.trim()) {
//             return false;
//         }
//     }
//     return true;
// }
    
    document.getElementById('btnSimpan').addEventListener('click', function() {
        isBtnSimpanClicked = true;
        var idGroup = this.getAttribute('data-id-group');
        showModal(idGroup);
    });
    
    document.getElementById('btnSetujui').addEventListener('click', function() {
        isBtnSetujuiClicked = true;
        this.classList.remove('btn-warning');
        this.classList.add('btn-ghost-success');
        // this.textContent = 'Disetujui';
        $(this).html(`
        <i class="mdi mdi-check-bold"></i>
        Berhasil dikirim
        `)
        
    
        document.getElementById('form-committee').style.display = 'block';
    });
    
    // document.getElementById('btnCommittee').addEventListener('click', function() {
    //     var btnCommittee = this;
    //     btnCommittee.classList.remove('btn-warning'); 
    //     btnCommittee.classList.add('btn-success'); 
    //     btnCommittee.textContent = 'Disetujui'; 
    
    //     document.getElementById('form-committee').style.display = 'block'; 
    
    //     var btnSimpanContainer = document.getElementById('btnSimpanContainer');
    //     if (btnSimpanContainer.style.display === 'none') {
    //         $('#btnSimpanContainer').slideDown();
    //     }
    // });

    // bas belum pake
    // $(document).ready(function() {
    //     $('#btnCommittee').on('click', function() {
    //         var username = $('#username').val();
    //         var password = $('#password').val();
    //         var idGroup = $('#id_group').val();
    //         var btnCommittee = this;

    //         // Reset validation states
    //         $('#username, #password').removeClass('is-invalid is-valid');
    //         $('#usernameError, #passwordError, #usernameSuccess, #passwordSuccess').hide();

    //         $.ajax({
    //             type: 'GET',
    //             url: '/5r-system/validate-credentials-comittee/' + idGroup,
    //             data: {
    //                 id_group: idGroup,
    //                 username: username,
    //                 password: password,
    //                 _token: '{{ csrf_token() }}'
    //             },
    //             success: function(response, textStatus, xhr) {
    //                 if (xhr.status === 200 && response.success) {
    //                     Swal.fire({
    //                         icon: 'success',
    //                         title: '',
    //                         text: `username dan password sesuai dengan group departemen ini`,
    //                         showConfirmButton: false,
    //                         timer: 2000
    //                     });
    //                     btnCommittee.classList.remove('btn-warning'); 
    //                     btnCommittee.classList.add('btn-ghost-success'); 
    //                     // btnCommittee.textContent = 'Disetujui'; 

    //                     $(btnCommittee).html(`
    //                     <i class="mdi mdi-check-bold"></i>
    //                     Diterima
    //                     `)

    //                     document.getElementById('form-committee').style.display = 'block'; 

    //                     var btnSimpanContainer = document.getElementById('btnSimpanContainer');
    //                     if (btnSimpanContainer.style.display === 'none') {
    //                         $('#btnSimpanContainer').slideDown();
    //                     }

    //                     var message = 'Nik Data User: ' + username + '\nNik Data Committee: ' + password;
    //                 }

    //                 $('#username').addClass('is-valid');
    //                 $('#usernameSuccess').show();
    //                 $('#password').addClass('is-valid');
    //                 $('#passwordSuccess').show();
    //             },
    //             error: function(xhr, textStatus, errorThrown) {
    //                 if (xhr.status === 401) { 
    //                     $('#username').addClass('is-invalid');
    //                     $('#usernameError').show();
    //                     alert('Unauthorized: Username atau password salah');
    //                 } else if (xhr.status === 403) { 
    //                     $('#password').addClass('is-invalid');
    //                     $('#passwordError').show();
    //                     alert('Forbidden: Username bukan merupakan anggota komite');
    //                 } else {
    //                     $('#username, #password').addClass('is-invalid');
    //                     $('#usernameError').show();
    //                     $('#passwordError').show();
    //                     alert('Terjadi kesalahan dalam permintaan AJAX');
    //                 }
    //             }
    //         });
    //     });
    // });


    function showKisiKisi(jenis)
    {
        $('#kisi-kisi-image').attr('src', "{{ asset('assets/foto/system_5r/kisi-kisi') }}-"+jenis+".png")
        $('#kisi-kisi-modal').modal('show')
    }

    $('#filter_jadwal').on('change', function () {
        var id_jadwal = $(this).val();

        // Get periode
        $.ajax({
            url: "{{ url('5r-system/get-periode-by-id-jadwal') }}/" + id_jadwal,
            type: "GET",
            dataType: "JSON",
            success: function (response) {
                if (response.status == 'success') {
                    $('#filter_periode').html('');
                    $('#filter_periode').append('<option value="">PILIH</option>');
                    $.each(response.data, function (index, value) {
                        $('#filter_periode').append('<option value="' + value.id_periode + '">' + value.nama_periode + '</option>');
                    })
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1000
                    })
                }
            },
            error: function (error) {
                // show error
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: error.responseJSON.message,
                    showConfirmButton: false,
                    timer: 1000
                })
            }
        })
    })
</script>

@if($isJuri)
<script>
    var database = window.indexedDB.open("system_5r", 5);

    // Create object store
    database.onupgradeneeded = function(event) {
        let db = event.target.result;
        // Delete old object store
        if (db.objectStoreNames.contains("penilaian")) {
            db.deleteObjectStore("penilaian");
            console.log('Delete because upgrade needed')
        }

        let objectStore = db.createObjectStore("penilaian", { keyPath: "id" });
    };

    function changeNilai(idPertanyaan)
    {
        let nilai = document.getElementById(idPertanyaan).value;

        var database = window.indexedDB.open("system_5r", 5);
        
        // Save data to indexeddb
        database.onsuccess = function(event) {
            let db = event.target.result;
            let transaction = db.transaction("penilaian", "readwrite");
            let objectStore = transaction.objectStore("penilaian");

            let request = objectStore.get(idPertanyaan);

            request.onsuccess = function(event) {
                let data = event.target.result;
                if (data) {
                    data.nilai = nilai;
                    objectStore.put(data);
                    console.log("Data berhasil diubah");
                } else {
                    objectStore.put({ id: idPertanyaan, nilai: nilai });
                    console.log("Data berhasil dibuat");
                }

            };

            request.onerror = function(event) {
                console.log("Data gagal disimpan");
            };
        };
    }

    function changeKeterangan(idPertanyaan)
    {
        let keterangan = document.getElementById('keterangan_'+idPertanyaan).value;

        var database = window.indexedDB.open("system_5r", 5);
        
        // Save data to indexeddb
        database.onsuccess = function(event) {
            let db = event.target.result;
            let transaction = db.transaction("penilaian", "readwrite");
            let objectStore = transaction.objectStore("penilaian");

            let request = objectStore.get(idPertanyaan);

            request.onsuccess = function(event) {
                let data = event.target.result;
                if (data) {
                    data.keterangan = keterangan;
                    objectStore.put(data);
                    console.log("Data berhasil diubah");
                } else {
                    objectStore.put({ id: idPertanyaan, keterangan: keterangan });
                    console.log("Data berhasil dibuat");
                }

            };

            request.onerror = function(event) {
                console.log("Data gagal disimpan");
            };
        };
    }

    const compressImage = async (file, { quality = 1, type = file.type }) => {
        // Get as image data
        const imageBitmap = await createImageBitmap(file);

        // Resize image to width 100px
        const canvas = document.createElement('canvas');
        canvas.width = 450;
        canvas.height = imageBitmap.height * (450 / imageBitmap.width);
        const ctx = canvas.getContext('2d');

        ctx.drawImage(imageBitmap, 0, 0, canvas.width, canvas.height);

        // Turn into Blob
        const blob = await new Promise((resolve) =>
            canvas.toBlob(resolve, type, quality)
        );

        // Turn Blob into File
        return new File([blob], file.name, {
            type: blob.type,
        });
    };

    const addImage = async (idPertanyaan) =>
    {
        var files = document.getElementById(idPertanyaan + '-input-file').files;

        // No files selected
        if (!files.length) return;

        console.log(files.length)
        
        // Loop the files
        for (let i = 0; i < files.length; i++) {
            console.log(i)
            let file = files[i];
            
            // We'll store the files in this data transfer object
            let dataTransfer = new DataTransfer();

            // Console.log file name and file size
            console.log(file.name, file.size);

            if (!file.type.startsWith('image')) {
                // Ignore this file, but do add it to our result
                dataTransfer.items.add(file);
                return;
            }

            // We compress the file by 50%
            let compressedFile = await compressImage(file, {
                quality: 0.4,
                type: 'image/jpeg',
            });

            // Save back the compressed file instead of the original file
            dataTransfer.items.add(compressedFile);

            file = dataTransfer.files[0];
            let reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = function() {
                let base64 = reader.result;
                let image = document.createElement('img');
                image.src = base64;
                image.style.width = '100%';
                image.style.height = '100%';
                image.style.objectFit = 'contain';

                var imageInner = document.createElement('div');
                imageInner.style.marginBottom = '2px';
                // Position relative
                imageInner.style.position = 'relative';
                imageInner.appendChild(image);

                // Create delete button
                var deleteButton = document.createElement('button');
                deleteButton.style.position = 'absolute';
                deleteButton.style.top = '0';
                deleteButton.style.right = '0';
                deleteButton.style.margin = '2px';
                deleteButton.style.padding = '2px 5px';
                deleteButton.style.fontSize = '10px';
                deleteButton.style.cursor = 'pointer';
                deleteButton.innerHTML = 'Hapus';
                deleteButton.type = 'button';
                // Add css class
                deleteButton.classList.add('btn');
                deleteButton.classList.add('btn-xs');
                deleteButton.classList.add('btn-danger');
                // Add onclick event
                deleteButton.onclick = function () {

                    var confirm = window.confirm('Apakah anda yakin ingin menghapus foto ini?');
                    if (!confirm) return;

                    // Delete image from indexeddb
                    var database = window.indexedDB.open("system_5r", 5);

                    // Save data to indexeddb
                    database.onsuccess = function(event) {
                        let db = event.target.result;
                        let transaction = db.transaction("penilaian", "readwrite");
                        let objectStore = transaction.objectStore("penilaian");
                        // let request = objectStore.put({ id: idPertanyaan, image: base64 });
                        // Change only image
                        let request = objectStore.get(idPertanyaan);
                        request.onsuccess = function(event) {
                            let data = event.target.result;
                            if (data) {
                                var images = data.images;
                                if (images == null) {
                                    images = [];
                                }

                                var index = images.indexOf(_image);
                                if (index > -1) {
                                    images.splice(index, 1);
                                }

                                data.images = images;
                                objectStore.put(data);
                            }
                        };

                        request.onerror = function(event) {
                            console.log("Error retrieving data:", event.target.error);
                        };
                    };

                    // Delete image from DOM
                    this.parentElement.remove();
                }
                imageInner.appendChild(deleteButton);

                document.getElementById(idPertanyaan + '-image-container').appendChild(imageInner);

                // Store to textarea
                // document.getElementById(idPertanyaan + '_image').value = base64;
                var textareaImage = document.createElement('textarea');
                textareaImage.name = 'image[' + idPertanyaan.split('-')[1] + '][]';
                textareaImage.value = base64;

                document.getElementById(idPertanyaan + '_image_container').appendChild(textareaImage);

                // Save image to indexeddb
                var database = window.indexedDB.open("system_5r", 5);

                // Save data to indexeddb
                database.onsuccess = function(event) {
                    let db = event.target.result;
                    let transaction = db.transaction("penilaian", "readwrite");
                    let objectStore = transaction.objectStore("penilaian");
                    // let request = objectStore.put({ id: idPertanyaan, image: base64 });
                    // Change only image
                    let request = objectStore.get(idPertanyaan);
                    request.onsuccess = function(event) {
                        let data = event.target.result;
                        if (data) {
                            var images = data.images;
                            if (images == null) {
                                images = [];
                            }

                            images.push(base64);

                            data.images = images;
                            objectStore.put(data);
                        } else {
                            objectStore.put({ id: idPertanyaan, images: [base64] });
                        }
                    };

                    request.onerror = function(event) {
                        console.log("Error retrieving data:", event.target.error);
                    };
                };

                // Clear input file
                setTimeout(() => {
                    document.getElementById(idPertanyaan + '-input-file').value = '';
                }, 5000);
            };
        }
    }

    @foreach ($pertanyaan as $_pertanyaan)
        @foreach($_pertanyaan->pertanyaan as $p)
        
        var database = window.indexedDB.open("system_5r", 5);
        
        // Retrieve data from indexedDB
        database.onsuccess = function(event) {
            var idPertanyaan = '{{ $p->id_group }}-{{ $p->id_pertanyaan }}';
            var db = event.target.result;
            var transaction = db.transaction("penilaian", "readonly");
            var objectStore = transaction.objectStore("penilaian");

            var request = objectStore.get(idPertanyaan);

            request.onsuccess = function(event) {
                var data = event.target.result;
                if (data) {
                    console.log("Data retrieved:", data);
                    // Use the retrieved data as needed
                    document.getElementById(idPertanyaan).value = data.nilai;
                    document.getElementById('keterangan_'+idPertanyaan).value = data.keterangan ?? '';
                    if (data.images) {
                        document.getElementById(idPertanyaan + '-image-container').innerHTML = '';
                        data.images.forEach(_image => {
                            let image = document.createElement('img');
                            image.src = _image;
                            image.style.width = '100%';
                            image.style.height = '100%';
                            image.style.objectFit = 'contain';

                            var imageInner = document.createElement('div');
                            imageInner.style.marginBottom = '2px';
                            // Position relative
                            imageInner.style.position = 'relative';
                            imageInner.appendChild(image);

                            // Create delete button
                            var deleteButton = document.createElement('button');
                            deleteButton.style.position = 'absolute';
                            deleteButton.style.top = '0';
                            deleteButton.style.right = '0';
                            deleteButton.style.margin = '2px';
                            deleteButton.style.padding = '2px 5px';
                            deleteButton.style.fontSize = '10px';
                            deleteButton.style.cursor = 'pointer';
                            deleteButton.innerHTML = 'Hapus';
                            deleteButton.type = 'button';
                            // Add css class
                            deleteButton.classList.add('btn');
                            deleteButton.classList.add('btn-xs');
                            deleteButton.classList.add('btn-danger');
                            // Add onclick event
                            deleteButton.onclick = function () {

                                var confirm = window.confirm('Apakah anda yakin ingin menghapus foto ini?');
                                if (!confirm) return;

                                // Delete image from indexeddb
                                var database = window.indexedDB.open("system_5r", 5);

                                // Save data to indexeddb
                                database.onsuccess = function(event) {
                                    let db = event.target.result;
                                    let transaction = db.transaction("penilaian", "readwrite");
                                    let objectStore = transaction.objectStore("penilaian");
                                    // let request = objectStore.put({ id: idPertanyaan, image: base64 });
                                    // Change only image
                                    let request = objectStore.get(idPertanyaan);
                                    request.onsuccess = function(event) {
                                        let data = event.target.result;
                                        if (data) {
                                            var images = data.images;
                                            if (images == null) {
                                                images = [];
                                            }

                                            var index = images.indexOf(_image);
                                            if (index > -1) {
                                                images.splice(index, 1);
                                            }

                                            data.images = images;
                                            objectStore.put(data);
                                        }
                                    };

                                    request.onerror = function(event) {
                                        console.log("Error retrieving data:", event.target.error);
                                    };
                                };

                                // Delete image from DOM
                                this.parentElement.remove();
                            }
                            imageInner.appendChild(deleteButton);

                            document.getElementById(idPertanyaan + '-image-container').appendChild(imageInner);

                            // Store to textarea
                            // document.getElementById(idPertanyaan + '_image').value = data.image;
                            var textareaImage = document.createElement('textarea');
                            textareaImage.name = 'image[' + idPertanyaan.split('-')[1] + '][]';
                            textareaImage.value = _image;

                            document.getElementById(idPertanyaan + '_image_container').appendChild(textareaImage);
                        });
                    }
                } else {
                // console.log(idPertanyaan, "Data not found");
                }
            };

            request.onerror = function(event) {
                console.log("Error retrieving data:", event.target.error);
            };
        };
        // Get data from database
        // setNilai(idPertanyaan);
        @endforeach
    @endforeach

    $('.form-pertanyaan').on('submit', function (e) {
        e.preventDefault()

        var data = $(this).serialize();

        // Form id
        var formId = $(this).attr('id');

        // List all .textarea_image_container inside form
        var textareaImageContainer = $(this).find('.textarea_image_container');

        // Alert if textarea image container is null
        for (let i = 0; i < textareaImageContainer.length; i++) {
            if (textareaImageContainer[i].innerHTML == '') {
                // Get textarea_image_container id
                var textareaImageContainerId = textareaImageContainer[i].id;

                // Go to textarea_image_container
                document.getElementById(textareaImageContainerId.replace('_image_container', '-input-file')).focus();

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Foto tidak boleh kosong',
                    showConfirmButton: false,
                    timer: 1000
                })
                return;
            }
        }

        // Tampilkan peringatan bahwa data yang sudah disimpan tidak dapat dirubah kembali
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan Penting!!!',
            text: 'Data yang sudah disimpan tidak dapat dirubah kembali, apakah anda yakin data sudah final dan ingin menyimpannya?',
            showCancelButton: true,
            confirmButtonText: 'Ya, simpan',
            cancelButtonText: 'Nanti dulu',
        }).then((result) => {
            if (result.isConfirmed) {
                // Swal loading with loading animation and no button
                Swal.fire({
                    title: 'Loading',
                    html: 'Mohon menunggu, data sedang disimpan. Jika menunggu terlalu lama, silahkan refresh dulu untuk memastikan sudah terkoneksi kedalam jaringan PT. PAS.',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading()
                    },
                })
                
                $.ajax({
                    url: "{{ url('5r-system/do-submit') }}",
                    type: "POST",
                    dataType: "JSON",
                    data: data,
                    success: function (response) {
                        if (response.status == 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1000
                            }).then(() => {
                                // Clear indexeddb
                                // var database = window.indexedDB.open("system_5r", 5);

                                // // Save data to indexeddb
                                // database.onsuccess = function(event) {
                                //     let db = event.target.result;
                                //     let transaction = db.transaction("penilaian", "readwrite");
                                //     let objectStore = transaction.objectStore("penilaian");
                                //     let request = objectStore.clear();

                                //     request.onsuccess = function(event) {
                                //         console.log("Data berhasil dihapus");
                                //     };
                                    
                                //     request.onerror = function(event) {
                                //         console.log("Data gagal dihapus");
                                //     };
                                // };

                                location.reload()
                            })
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1000
                            })
                        }
                    },
                    error: function (error) {
                        // show error
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: error.responseJSON.message,
                            showConfirmButton: false,
                            // timer: 1000
                        })
                    }
                })
            }
        })

    })
</script>
@endif
@endpush
