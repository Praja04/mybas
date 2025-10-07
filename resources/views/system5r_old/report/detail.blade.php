@extends('system5r.layouts.base')

@section('title', 'Penilaian')

@push('styles')
    <style>
        table p {
            margin-bottom: 0 !important
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
            <!-- Primary Alert -->
            <div class="alert alert-primary alert-dismissible alert-label-icon label-arrow shadow fade show" role="alert">
                <i class="ri-user-smile-line label-icon"></i><strong>{{ auth()->user()->name }}</strong> - Kamu termasuk sebagai juri di group <strong>{{ $groupJuri->nama_group }} ( {{ implode(', ', $groupJuri->anggota->where('is_active', 'Y')->pluck('nama_juri')->toArray()) }} )</strong>
            </div>
            <div class="card shadow-none border mt-2">
                <div class="card-body">
                    <form action="">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group mb-3">
                                    <label for="filter_department">DEPARTMENT</label>
                                    <select name="filter_department" required id="filter_department" class="form-control">
                                        <option value="">PILIH</option>
                                        @foreach ($department as $dept)
                                        <option @if(isset($_GET['filter_department']) && $_GET['filter_department'] == $dept->department->id_department) selected @endif value="{{ $dept->department->id_department }}">{{ $dept->department->nama_department }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group mb-3">
                                    <label for="filter_jadwal">JADWAL</label>
                                    <select required name="filter_jadwal" id="filter_jadwal" class="form-control">
                                        <option value="">PILIH</option>
                                        @foreach ($jadwal as $j)
                                        <option @if(isset($_GET['filter_jadwal']) && $_GET['filter_jadwal'] == $j->id_jadwal) selected @endif value="{{ $j->id_jadwal }}">{{ $j->tahun }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group mb-3">
                                    <label for="filter_periode">PERIODE</label>
                                    <select required name="filter_periode" id="filter_periode" class="form-control">
                                    @if($periode != null)
                                        @foreach ($periode as $p)
                                        <option @if(isset($_GET['filter_periode']) && $_GET['filter_periode'] == $p->id_periode) selected @endif value="{{ $p->id_periode }}">{{ $p->nama_periode }}</option>
                                        @endforeach
                                    @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary waves-effect shadow-none">
                                <i class="mdi mdi-paper-plane"></i>
                                GO
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @if(isset($_GET['filter_department']))
            <div class="row">
                <div class="col-md-2">
                    <div class="nav nav-pills flex-column nav-pills-tab custom-verti-nav-pills text-center" role="tablist" aria-orientation="vertical">
                        @foreach ($pertanyaan as $group)
                        <a class="nav-link @if($loop->iteration == 1) active show @endif" id="custom-v-pills-{{ $group->id_group }}-tab" data-bs-toggle="pill" href="#custom-v-pills-{{ $group->id_group }}" role="tab" aria-controls="custom-v-pills-{{ $group->id_group }}" aria-selected="true">
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
                                                <th class="text-white">GROUP</th>
                                                <th class="text-white">ITEM PERIKSA</th>
                                                <th class="text-white">KETERANGAN</th>
                                                <th class="text-white">NILAI</th>
                                                <th class="text-white">FOTO</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($group->pertanyaan->groupBy('jenis') as $__pertanyaan)
                                                @foreach ($__pertanyaan as $_pertanyaan)
                                                <tr>
                                                    @if($loop->first)
                                                    <td style="vertical-align: middle; font-weight: bold; text-align: center; background-color: {{ $colors[$loop->parent->iteration-1] }}; color: {{ $textColors[$loop->parent->iteration-1] }}" rowspan="{{ count($__pertanyaan) }}">{{ $_pertanyaan->jenis }}</td>
                                                    @endif
                                                    <td>
                                                        <div style="width: 200px">
                                                            {!! $_pertanyaan->item_periksa !!}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div style="width: 300px">
                                                            {!! $_pertanyaan->keterangan !!}
                                                        </div>
                                                    </td>
                                                    <td style="width: 5%">
                                                        @if($jawaban != null)
                                                        @php
                                                            $nilai = $jawaban->where('id_pertanyaan', $_pertanyaan->id_pertanyaan)->first()->nilai;
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
                                                    </td>
                                                    <td>
                                                        <div style="width: 200px">
                                                            @if($jawaban != null)
                                                                @php
                                                                    $foto = $jawaban->where('id_pertanyaan', $_pertanyaan->id_pertanyaan)->first()->foto;
                                                                @endphp
                                                                @if($foto != null)
                                                                <div class="image-container">
                                                                    <img src="{{ asset('images/5r/'. $foto) }}" style="width: 100%; height: 100%; object-fit: contain" alt="">
                                                                </div>
                                                                @else
                                                                <i>No Image</i>
                                                                @endif
                                                            @else
                                                            <div class="image-container" id="{{ $_pertanyaan->id_group }}-{{ $_pertanyaan->id_pertanyaan }}-image-container">
                                                            </div>
                                                            <input onchange="addImage('{{ $_pertanyaan->id_group }}-{{ $_pertanyaan->id_pertanyaan }}')" type="file" accept="image/jpeg" class="form-control" name="{{ $_pertanyaan->id_group }}_{{ $_pertanyaan->id_pertanyaan }}_foto" id="{{ $_pertanyaan->id_group }}-{{ $_pertanyaan->id_pertanyaan }}-input-file">
                                                            <textarea style="display: none" name="image[{{ $_pertanyaan->id_pertanyaan }}]" id="{{ $_pertanyaan->id_group }}-{{ $_pertanyaan->id_pertanyaan }}_image"></textarea>
                                                            @endif
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
                                    <button type="submit" form="form-{{ $group->id_group }}" class="btn btn-full btn-success waves-effect">
                                        <i class="mdi mdi-content-save"></i>
                                        SIMPAN
                                    </button>
                                </div>
                                @endif
                            </form>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    @endif
</div>


@endsection

@push('scripts')

<script>
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
    var database = window.indexedDB.open("system_5r", 3);

    // Create object store
    database.onupgradeneeded = function(event) {
        let db = event.target.result;
        let objectStore = db.createObjectStore("penilaian", { keyPath: "id" });
    };

    function changeNilai(idPertanyaan)
    {
        let nilai = document.getElementById(idPertanyaan).value;

        var database = window.indexedDB.open("system_5r", 3);
        
        // Save data to indexeddb
        database.onsuccess = function(event) {
            let db = event.target.result;
            let transaction = db.transaction("penilaian", "readwrite");
            let objectStore = transaction.objectStore("penilaian");
            let request = objectStore.put({ id: idPertanyaan, nilai: nilai });

            request.onsuccess = function(event) {
                console.log("Data berhasil disimpan");
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

        // We'll store the files in this data transfer object
        const dataTransfer = new DataTransfer();

        let file = document.getElementById(idPertanyaan + '-input-file').files[0];
        if (!file.type.startsWith('image')) {
            // Ignore this file, but do add it to our result
            dataTransfer.items.add(file);
            return;
        }

        // We compress the file by 50%
        const compressedFile = await compressImage(file, {
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
            document.getElementById(idPertanyaan + '-image-container').innerHTML = '';
            document.getElementById(idPertanyaan + '-image-container').appendChild(image);

            // Store to textarea
            document.getElementById(idPertanyaan + '_image').value = base64;

            // Clear input file
            document.getElementById(idPertanyaan + '-input-file').value = '';

            // Save image to indexeddb
            var database = window.indexedDB.open("system_5r", 3);

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
                        data.image = base64;
                        objectStore.put(data);
                    } else {
                        console.log(idPertanyaan, "Data not found");
                    }
                };

                request.onerror = function(event) {
                    console.log("Error retrieving data:", event.target.error);
                };
            };
        };
    }

    @foreach ($pertanyaan as $_pertanyaan)
        @foreach($_pertanyaan->pertanyaan as $p)
        
        var database = window.indexedDB.open("system_5r", 3);
        
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
                    if (data.image) {
                        let image = document.createElement('img');
                        image.src = data.image;
                        image.style.width = '100%';
                        image.style.height = '100%';
                        image.style.objectFit = 'contain';
                        document.getElementById(idPertanyaan + '-image-container').innerHTML = '';
                        document.getElementById(idPertanyaan + '-image-container').appendChild(image);

                        // Store to textarea
                        document.getElementById(idPertanyaan + '_image').value = data.image;
                    }
                } else {
                console.log(idPertanyaan, "Data not found");
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

        // Swal loading with loading animation and no button
        Swal.fire({
            title: 'Loading',
            html: 'Mohon menunggu, data sedang disimpan',
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
                    timer: 1000
                })
            }
        })

    })
</script>
@endif
@endpush
