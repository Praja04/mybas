@extends('system5r.layouts.base')

@section('title', 'Approval Penilaian')

@section('activeMenu', 'approval')

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
                <h3>APPROVAL PENILAIAN DARI JURI</h3>
                <div>
                    <a href="{{ route('5r-system.approval-penilaian') }}" class="btn btn-soft-light waves-effect waves-light btn-sm text-dark">
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
                                <td>{{ $data->periode->jadwal->tahun }}</td>
                            </tr>
                            <tr>
                                <th>PERIODE</th>
                                <td>:</td>
                                <td>{{ $data->periode->nama_periode }}</td>
                            </tr>
                            <tr>
                                <th>GROUP</th>
                                <td>:</td>
                                <td>{{ $data->group->department->nama_department }} - {{ $data->group->nama_group }}</td>
                            </tr>
                            <tr>
                                <th>BATAS KOMPLAIN</th>
                                <td>:</td>
                                <td>{{ formatTanggalIndonesia2($data->komplain_deadline) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-3">
                    @if($data->status == 'complaining')
                        <div class="alert alert-warning">
                            <strong>Komplain nilai sedang berjalan.</strong> Dan menunggu review dari juri.
                        </div>
                    @elseif($data->status == 'approved')
                        <div class="alert alert-success">
                            <strong>Penilaian sudah disetujui</strong>
                        </div>
                    @else
                    <button form="form-approval" id="approve-button" class="btn btn-success waves-effect waves-light mb-1">
                        <i class="mdi mdi-check"></i>
                        Approve Tanpa Komplain
                    </button>
                    @if($data->komplain_deadline >= date('Y-m-d'))
                    <button form="form-approval" id="complaint-button" class="btn btn-secondary waves-effect waves-light mb-1" style="display: none">
                        <i class="mdi mdi-check-underline"></i>
                        Ajukan Komplain
                    </button>
                    @else
                    <div class="alert alert-warning mt-3">
                        <strong>Batas komplain sudah lewat.</strong> Anda tidak bisa mengajukan komplain. <br /><br />
                        <label>Deadline Komplain: </label><strong> {{ formatTanggalIndonesia2($data->komplain_deadline) }}</strong>
                    </div>
                    @endif
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
                <form class="form-pertanyaan" enctype="multipart/form-data" method="POST" action="{{ route('5r-system.approval-penilaian.submit') }}" id="form-approval">
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
                                    <th class="text-white">APPROVAL</th>
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
                                                <h6>NILAI SAAT INI <span title="Wajib diisi" class="text-danger">*</span></h6>
                                                @if($jawaban != null)
                                                @php
                                                    $__jawaban = $jawaban->where('id_pertanyaan', $_pertanyaan->id_pertanyaan)->first();
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
                                                <h6>FOTO</h6>
                                                @php
                                                $__jawaban = null;
                                                @endphp
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
                                            <div class="mt-3">
                                                <h6>KETERANGAN</h6>
                                                <textarea onChange="changeKeterangan('{{ $_pertanyaan->id_group }}-{{ $_pertanyaan->id_pertanyaan }}')" onKeyup="changeKeterangan('{{ $_pertanyaan->id_group }}-{{ $_pertanyaan->id_pertanyaan }}')" required name="keterangan[{{ $_pertanyaan->id_pertanyaan }}]" id="keterangan_{{ $_pertanyaan->id_group }}-{{ $_pertanyaan->id_pertanyaan }}" class="form-control" {{ $__jawaban != null ? 'disabled' : '' }} placeholder="Keterangan tambahan">{{ $__jawaban != null ? $__jawaban->keterangan : '' }}</textarea>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="card border">
                                                <div class="card-body">
                                                    <select @if($__jawaban->approval != 'WAITING') disabled @endif onChange="changeApprove('{{ $__jawaban->id }}')" id="approve-{{ $__jawaban->id }}" name="approve[{{ $__jawaban->id }}]" class="form-control @if($__jawaban->approval == 'TERIMA') bg-success bg-opacity-25 border-success @elseif($__jawaban->approval == 'KOMPLAIN') bg-warning bg-opacity-25 border-warning @endif">
                                                        <option @if($__jawaban->approval == 'TERIMA') selected @endif value="TERIMA">TERIMA</option>
                                                        @if($data->komplain_deadline >= date('Y-m-d'))
                                                        <option @if($__jawaban->approval == 'KOMPLAIN') selected @endif value="KOMPLAIN">KOMPLAIN</option>
                                                        @endif
                                                    </select>
                                                    <div id="required-comment-{{ $__jawaban->id }}">
                                                    </div>
                                                    @if($__jawaban->approval != 'WAITING' && $__jawaban->approval != 'TERIMA')
                                                    <div class="mt-3">
                                                        <strong>NILAI YANG DIKOMPLAIN:</strong> <span class="badge bg-secondary bg-opacity-50">{{ $__jawaban->nilai_before }}</span>
                                                    </div>
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
                                                </div>
                                            </div>
                                            @if($__jawaban->solve_komplain_ket != null)
                                            <div class="mt-2 card border">
                                                <div class="card-body">
                                                    <div>
                                                        <strong>NILAI TERBARU:</strong> <span class="badge bg-success bg-opacity-50">{{ $__jawaban->nilai }}</span>
                                                    </div>
                                                    <label class="mt-2">KETERANGAN</label>
                                                    <textarea disabled class="form-control">{{ $__jawaban->solve_komplain_ket }}</textarea>
                                                </div>
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
