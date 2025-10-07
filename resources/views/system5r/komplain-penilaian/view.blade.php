@extends('system5r.layouts.base')

@section('title', 'FU Komplain Penilaian')

@push('styles')
    <style>
    .pas-background-color {
        background-color: #a80000 !important;
    }

    .pas-color {
        color: #a80000 !important;
    }
    table p {
        margin-bottom: 0 !important
    }
    .modal-backdrop
    {
        opacity:0.99 !important;
    }
    </style>
@endpush

@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="mb-2 d-flex justify-content-between">
                <h3>FU KOMPLAIN PENILAIAN DARI JURI</h3>
                <div>
                    <a href="{{ route('5r-system.komplain-penilaian') }}" class="btn btn-soft-light waves-effect waves-light btn-sm text-dark">
                        <i class="mdi mdi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="row">
                <div class="col-9">
                    <table class="table table-hover table-bordered">
                        <tbody>
                            <tr>
                                <th style="width: 20%">TAHUN</th>
                                <td style="width: 2%">:</td>
                                <td>2023</td>
                            </tr>
                            <tr>
                                <th>PERIODE</th>
                                <td>:</td>
                                <td>TAHAP 1</td>
                            </tr>
                            <tr>
                                <th>GROUP</th>
                                <td>:</td>
                                <td>ENG GUD SPAREPART</td>
                            </tr>
                            <tr>
                                <th>BATAS KOMPLAIN</th>
                                <td>:</td>
                                <td>31 Juli 2023</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-3">
                    @if($data->status == 'complaining')
                    <button form="form-solve-komplain" id="complaint-button" class="btn btn-success waves-effect waves-light mb-1">
                        <i class="mdi mdi-check-underline"></i>
                        Kirim Nilai Terbaru
                    </button>
                    @elseif($data->status == 'solved')
                        <div class="alert alert-success">
                            <strong>Komplain sudah diselesaikan, dan menunggu peninjauan <i>committee department</i>.</strong>
                        </div>
                    @endif
                </div>
            </div>
            <div>
                @php
                    $colors = ['#264653', '#2a9d8f', 'e9c46a', '#f4a261', '#e76f51'];
                    $textColors = ['#fff', '#fff', '#000', '#000', '#000'];
                @endphp
                @foreach ($pertanyaan as $group)
                {{-- {{ dd($pertanyaan) }} --}}
                @php
                    $jawaban = $jawabanGroup;

                    if ($jawaban != null) {
                        $jawaban = $jawaban->jawaban;
                    }
                @endphp
                <form class="form-pertanyaan" enctype="multipart/form-data" method="POST" action="{{ route('5r-system.komplain-penilaian.submit') }}" id="form-solve-komplain">
                    @csrf
                    <input type="hidden" name="id_group" value="{{ $group->id_group }}">
                    <input type="hidden" name="id_jawaban_group" value="{{ decrypt($id_jawaban_group) }}">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            {{-- <thead> --}}
                                {{-- </thead> --}}
                                <tbody>
                                <tr class="pas-background-color">
                                    <th class="text-white px-1" style="border-width: 1px solid #fff; width: 5% !important">GROUP</th>
                                    <th class="text-white" style="width: 20%">PERTANYAAN</th>
                                    <th class="text-white" style="width: 15%">NILAI</th>
                                    <th class="text-white" style="width: 20%">APPROVAL</th>
                                    <th class="text-white" style="width: 20%">SOLVE KOMPLAIN</th>
                                </tr>
                                @foreach (['RINGKAS', 'RAPI', 'RESIK', 'RAWAT', 'RAJIN'] as $jenis)
                                    @php
                                        $__pertanyaan = $group->pertanyaan->where('jenis', $jenis);
                                    @endphp
                                    @foreach ($__pertanyaan as $_pertanyaan)
                                    <tr>
                                        @if($loop->first)
                                        <td class="p-0" style="vertical-align: middle; font-size: 10px; font-weight: bold; text-align: center; background-color: {{ $colors[$loop->parent->iteration-1] }}; color: {{ $textColors[$loop->parent->iteration-1] }}" rowspan="{{ count($__pertanyaan) }}">
                                            {{ $_pertanyaan->jenis }}<br />
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
                                                <h6>PILIH NILAI <span title="Wajib diisi" class="text-danger">*</span></h6>
                                                @if($jawaban != null)
                                                @php
                                                    $__jawaban = $jawaban->where('id_pertanyaan', $_pertanyaan->id_pertanyaan)->first();
                                                    $nilai = $__jawaban != null ? $__jawaban->nilai_before : '';
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
                                                <h6>FOTO</h6>
                                                @if($jawaban != null)
                                                    @php
                                                        $__jawaban = $jawaban->where('id_pertanyaan', $_pertanyaan->id_pertanyaan)->first();
                                                        $foto = $__jawaban != null ? $__jawaban->foto : null;
                                                    @endphp
                                                    @if($foto != null)
                                                    <div class="image-container">
                                                        <div class="row">
                                                            @foreach (explode(',', $foto) as $_foto)
                                                            <div class="col-12">
                                                                <img src="{{ asset('images/5r/'. $_foto) }}" style="width: 100%; height: 100%; object-fit: contain; margin-bottom: 5px" alt="">
                                                            </div>
                                                            @endforeach
                                                        </div>                                                     
                                                    </div>
                                                    @else
                                                    <i>No Image</i>
                                                    @endif
                                                @else
                                                <div class="image-container mb-1" id="{{ $_pertanyaan->id_group }}-{{ $_pertanyaan->id_pertanyaan }}-image-container">
                                                </div>
                                                <input onchange="addImage('{{ $_pertanyaan->id_group }}-{{ $_pertanyaan->id_pertanyaan }}')" type="file" accept="image/jpeg" class="form-control" name="{{ $_pertanyaan->id_group }}_{{ $_pertanyaan->id_pertanyaan }}_foto" id="{{ $_pertanyaan->id_group }}-{{ $_pertanyaan->id_pertanyaan }}-input-file">
                                                <div class="textarea_image_container" style="display: none" id="{{ $_pertanyaan->id_group }}-{{ $_pertanyaan->id_pertanyaan }}_image_container">
                                                    {{-- <textarea name="image[{{ $_pertanyaan->id_pertanyaan }}]" id="{{ $_pertanyaan->id_group }}-{{ $_pertanyaan->id_pertanyaan }}_image"></textarea> --}}
                                                </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <select @if($__jawaban->approval != 'WAITING') disabled @endif onChange="changeApprove('{{ $__jawaban->id }}')" id="approve-{{ $__jawaban->id }}" name="approve[{{ $__jawaban->id }}]" class="form-control @if($__jawaban->approval == 'TERIMA') bg-success bg-opacity-25 border-success @elseif($__jawaban->approval == 'KOMPLAIN') bg-warning bg-opacity-25 border-warning @endif">
                                                <option @if($__jawaban->approval == 'TERIMA') selected @endif value="TERIMA">TERIMA</option>
                                                <option @if($__jawaban->approval == 'KOMPLAIN') selected @endif value="KOMPLAIN">KOMPLAIN</option>
                                            </select>
                                            <div id="required-comment-{{ $__jawaban->id }}">
                                            </div>
                                            @if($__jawaban->approval != 'WAITING' && $__jawaban->approval != 'TERIMA')
                                            <div>
                                                <label class="mt-3">KOMENTAR <span class="text-danger">*</span></label>
                                                    <textarea disabled name="komentar[{{ $__jawaban->id }}]" class="form-control" rows="3">{{ $__jawaban->alasan_komplain }}</textarea>
                                                    <label class="mt-3">ATTACHMENT: </label>
                                                    @if($__jawaban->attachment_komplain != null)
                                                    <a target="_blank" href="{{ url('images/5r/attachment_complain/' . $__jawaban->attachment_komplain) }}"><i class="mdi mdi-open-in-new"></i> Lihat Attachment</a>
                                                    @else
                                                    <i>No Attachment</i>
                                                    @endif
                                            </div>
                                            @endif
                                        </td>
                                        <td>
                                            @if($__jawaban->approval != 'WAITING' && $__jawaban->approval != 'TERIMA')
                                            <div>
                                                <h6>PILIH NILAI <span title="Wajib diisi" class="text-danger">*</span></h6>
                                                @if($data->nik_solver != null)
                                                @php
                                                    $__jawaban = $jawaban->where('id_pertanyaan', $_pertanyaan->id_pertanyaan)->first();
                                                    $nilai = $__jawaban != null ? $__jawaban->nilai : '';
                                                @endphp
                                                <select disabled class="form-control" style="width: 100px" name="old_nilai[{{ $_pertanyaan->id_pertanyaan }}]">
                                                    <option value="">PILIH</option>
                                                    <option @if($nilai == '1') selected @endif value="1">1</option>
                                                    <option @if($nilai == '2') selected @endif value="2">2</option>
                                                    <option @if($nilai == '3') selected @endif value="3">3</option>
                                                    <option @if($nilai == '4') selected @endif value="4">4</option>
                                                </select>
                                                <label class="mt-3">KETERANGAN <span class="text-danger">*</span></label>
                                                <textarea class="form-control" name="keterangan_solve[{{ $__jawaban->id }}]" disabled>{{ $__jawaban->solve_komplain_ket }}</textarea>
                                                @else
                                                <select required onchange="changeNilai('{{ $_pertanyaan->id_group }}-{{ $_pertanyaan->id_pertanyaan }}')" class="form-control" style="width: 100px" name="new_nilai[{{ $__jawaban->id }}]" id="{{ $_pertanyaan->id_group }}-{{ $_pertanyaan->id_pertanyaan }}">
                                                    <option value="">PILIH</option>
                                                    <option value="1">1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                    <option value="4">4</option>
                                                </select>
                                                <label class="mt-3">KETERANGAN <span class="text-danger">*</span></label>
                                                <textarea class="form-control" name="keterangan_solve[{{ $__jawaban->id }}]" required></textarea>
                                                @endif
                                            </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($jawabanGroup == null)
                    <div class="mt-3">
                        <button type="submit" form="form-{{ $group->id_group }}" class="btn btn-full btn-success waves-effect">
                            <i class="mdi mdi-content-save"></i>
                            SIMPAN
                        </button>
                    </div>
                    @endif
                </form>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script>
        var initAnswerCount = {{ $jawaban->count() }};
        var answerCount = {{ $jawaban->count() }};
        function changeApprove(id_jawaban)
        {
            var approve = $('#approve-'+id_jawaban).val()
            $('#required-comment-'+id_jawaban).html('')

            if(approve == 'KOMPLAIN') {
                $('#required-comment-'+id_jawaban).html(`
                    <label class="mt-3">KOMENTAR <span class="text-danger">*</span></label>
                        <textarea required name="komentar[${id_jawaban}]" class="form-control" rows="3"></textarea>
                        <label class="mt-3">ATTACHMENT: </label>
                    <input type="file" name="attachment[${id_jawaban}]">
                `)

                answerCount--
            }else{
                answerCount++
            }
            
            if(answerCount == initAnswerCount) {
                $('#approve-button').show()
                $('#complaint-button').hide()
            }else{
                $('#approve-button').hide()
                $('#complaint-button').show()
            }

            console.log(answerCount)
        }
        // $('.table').DataTable()
    </script>
@endpush
