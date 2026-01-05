@extends('system5r.layouts.base')

@section('title', 'Penilaian 5R')

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif !important;
            background-color: #f8f9fa;
        }

        .card {
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border: none;
            overflow: hidden;
        }

        .card-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
        }

        .card-title {
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 0;
        }

        .nav-pills .nav-link {
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 10px;
            color: #495057;
            font-weight: 500;
            transition: all 0.3s ease;
            border: 1px solid #e9ecef;
        }

        .nav-pills .nav-link.active {
            background-color: #6c5ce7;
            color: white;
            box-shadow: 0 4px 12px rgba(108, 92, 231, 0.3);
            border-color: #6c5ce7;
        }

        .nav-pills .nav-link:hover:not(.active) {
            background-color: #f8f9fa;
        }

        .badge-completed {
            background-color: #00b894;
            color: white;
            font-weight: 500;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
        }

        .badge-pending {
            background-color: #fdcb6e;
            color: #2d3436;
            font-weight: 500;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
        }

        .btn-primary {
            background-color: #6c5ce7;
            border-color: #6c5ce7;
            font-weight: 500;
            padding: 8px 20px;
            border-radius: 8px;
        }

        .btn-primary:hover {
            background-color: #5649c0;
            border-color: #5649c0;
        }

        .table th {
            font-weight: 600;
            color: #495057;
            background-color: #f8f9fa;
        }

        .criteria-badge {
            display: inline-block;
            padding: 8px 12px;
            border-radius: 6px;
            color: white;
            font-weight: 600;
            font-size: 0.8rem;
            margin-bottom: 10px;
        }

        .question-card {
            border-left: 4px solid #6c5ce7;
            border-radius: 8px;
            margin-bottom: 20px;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        }

        .question-header {
            padding: 15px;
            border-bottom: 1px solid #f1f1f1;
        }

        .question-body {
            padding: 15px;
        }

        .score-select {
            width: 80px;
            border-radius: 6px;
            border: 1px solid #e9ecef;
            padding: 8px 12px;
        }

        .modal-content {
            border-radius: 12px;
        }

        .progress-container {
            position: relative;
            height: 6px;
            background-color: #e9ecef;
            border-radius: 3px;
            margin-bottom: 20px;
        }

        .progress-bar {
            height: 100%;
            border-radius: 3px;
            background-color: #6c5ce7;
            transition: width 0.3s ease;
        }

        .image-preview-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 12px;
            margin-top: 8px;
        }

        .image-preview-wrapper {
            position: relative;
            aspect-ratio: 1;
            border-radius: 8px;
            overflow: hidden;
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            transition: all 0.2s ease;
        }

        .image-preview-wrapper:hover {
            border-color: #0d6efd;
            box-shadow: 0 2px 8px rgba(13, 110, 253, 0.15);
        }

        .image-preview {
            width: 100px;
            height: 100px;
            object-fit: cover;
            display: block;
        }

        .image-preview-index {
            position: absolute;
            top: 6px;
            left: 6px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            font-size: 11px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 4px;
            line-height: 1.4;
        }

        .image-preview-remove {
            position: absolute;
            top: 6px;
            right: 6px;
            background: rgba(220, 53, 69, 0.9);
            color: white;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 14px;
            line-height: 1;
            transition: background 0.2s ease;
            padding: 0;
        }

        .image-preview-remove:hover {
            background: rgba(220, 53, 69, 1);
        }

        .image-preview-container.readonly .image-preview-wrapper {
            cursor: zoom-in;
        }

        .image-preview-container.readonly .image-preview-remove {
            display: none;
        }

        .image-preview-empty {
            grid-column: 1 / -1;
            padding: 24px;
            text-align: center;
            color: #6c757d;
            background: #f8f9fa;
            border-radius: 8px;
            border: 2px dashed #dee2e6;
        }

        .table-responsive {
            max-height: 500px;
            overflow-y: auto;
        }

        .table thead th {
            position: sticky;
            top: 0;
            background-color: #f8f9fa;
            z-index: 10;
        }

        .img-thumbnail {
            transition: transform 0.2s;
        }

        .img-thumbnail:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .badge-temuan-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #dc3545;
            color: white;
            border-radius: 50%;
            padding: 4px 8px;
            font-size: 10px;
            font-weight: 600;
        }

        .camera-container {
            position: relative;
            width: 100%;
            max-width: 640px;
            margin: 0 auto;
            background: #000;
            border-radius: 8px;
            overflow: hidden;
            min-height: 480px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #cameraVideo {
            width: 100% !important;
            height: auto !important;
            max-height: 480px;
            display: block !important;
            border-radius: 8px;
            background: #000;
            object-fit: contain;
        }

        #cameraModal .modal-dialog {
            max-width: 700px;
        }

        #cameraModal .modal-body {
            background: #f8f9fa;
            padding: 20px;
        }

        #cameraModal .modal-content {
            background: #fff;
        }

        #cameraCanvas {
            display: none !important;
        }

        .btn-open-camera:hover {
            background-color: #28a745;
            color: white;
            border-color: #28a745;
        }

        @media (max-width: 768px) {
            #cameraModal .modal-dialog {
                max-width: 95%;
                margin: 1rem auto;
            }

            .camera-container {
                max-width: 100%;
            }
        }

        @media (max-width: 576px) {
            .image-preview-container {
                grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
                gap: 8px;
            }
        }
    </style>
@endpush

@php
    $colors = ['#264653', '#2a9d8f', '#e9c46a', '#f4a261', '#e76f51', '#6c5ce7'];
    $textColors = ['#fff', '#fff', '#000', '#000', '#000', '#fff'];
@endphp

@section('content')

    <div class="container-fluid py-4">
        @if (!$isJuri)
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body text-center p-5">
                            <div class="mb-4">
                                <i class="mdi mdi-alert-circle-outline text-danger" style="font-size: 60px;"></i>
                            </div>
                            <h3 class="mb-3">Akses Ditolak</h3>
                            <p class="text-muted mb-4">Anda tidak memiliki izin untuk mengakses halaman penilaian ini. Hanya
                                juri yang ditunjuk yang dapat melakukan penilaian.</p>
                            <a href="{{ url('/') }}" class="btn btn-primary">Kembali ke Beranda</a>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title">Penilaian 5R</h4>
                                <p class="text-muted mb-0">Sistem Penilaian 5R Department</p>
                            </div>
                            <div>
                                @if (isset($_GET['filter_department']))
                                    <a href="{{ url('5r-system/penilaian') }}" class="btn btn-outline-secondary">
                                        <i class="mdi mdi-arrow-left me-1"></i> Kembali
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
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
                                    @foreach ($department as $item)
                                        @if (isset($_GET['filter_periode']))
                                            @if ($_GET['filter_periode'] == $item->id_periode && $_GET['filter_department'] == $item->id_department)
                                                <tr>
                                                    <td>{{ $item->periode->jadwal->tahun }}</td>
                                                    <td>
                                                        {{ $item->periode->nama_periode }} <br />
                                                        {{ $item->periode->keterangan }}
                                                    </td>
                                                    <td>{{ $item->group->nama_group }}</td>
                                                    <td>{{ $item->department->nama_department }}</td>
                                                    <td>
                                                        <div class="d-flex">
                                                            <form action="">
                                                                <div class="row" style="display: none">
                                                                    <div class="col-md-2">
                                                                        <div class="form-group mb-3">
                                                                            <label for="filter_jadwal">JADWAL</label>
                                                                            <select required name="filter_jadwal"
                                                                                id="filter_jadwal" class="form-control">
                                                                                <option
                                                                                    value="{{ $item->periode->id_jadwal }}">
                                                                                    {{ $item->periode->id_jadwal }}</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <div class="form-group mb-3">
                                                                            <label for="filter_periode">PERIODE</label>
                                                                            <select required name="filter_periode"
                                                                                id="filter_periode" class="form-control">
                                                                                <option value="{{ $item->id_periode }}">
                                                                                    {{ $item->id_periode }}</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <div class="form-group mb-3">
                                                                            <label
                                                                                for="filter_department">DEPARTMENT</label>
                                                                            <select name="filter_department" required
                                                                                id="filter_department" class="form-control">
                                                                                <option value="{{ $item->id_department }}">
                                                                                    {{ $item->id_department }}</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div>
                                                                    <button type="submit"
                                                                        class="btn btn-sm btn-primary waves-effect shadow-none">
                                                                        <i class="mdi mdi-paper-plane"></i>
                                                                        GO
                                                                    </button>
                                                                </div>
                                                            </form>
                                                            <a href="{{ url('5r-system/penilaian') }}"
                                                                class="btn btn-light btn-sm ms-2">
                                                                <i class="mdi mdi-arrow-left"></i> Kembali
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
                                        @else
                                            <tr>
                                                <td>{{ $item->periode->jadwal->tahun }}</td>
                                                <td>
                                                    {{ $item->periode->nama_periode }} <br />
                                                    {{ $item->periode->keterangan }}
                                                </td>
                                                <td>{{ $item->group->nama_group }}</td>
                                                <td>{{ $item->department->nama_department }}</td>
                                                <td>
                                                    @if ($item->periode->selesai == 'N')
                                                        <form action="">
                                                            <div class="row" style="display: none">
                                                                <div class="col-md-2">
                                                                    <div class="form-group mb-3">
                                                                        <label for="filter_jadwal">JADWAL</label>
                                                                        <select required name="filter_jadwal"
                                                                            id="filter_jadwal" class="form-control">
                                                                            <option
                                                                                value="{{ $item->periode->id_jadwal }}">
                                                                                {{ $item->periode->id_jadwal }}</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="form-group mb-3">
                                                                        <label for="filter_periode">PERIODE</label>
                                                                        <select required name="filter_periode"
                                                                            id="filter_periode" class="form-control">
                                                                            <option value="{{ $item->id_periode }}">
                                                                                {{ $item->id_periode }}</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="form-group mb-3">
                                                                        <label for="filter_department">DEPARTMENT</label>
                                                                        <select name="filter_department" required
                                                                            id="filter_department" class="form-control">
                                                                            <option value="{{ $item->id_department }}">
                                                                                {{ $item->id_department }}</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <button type="submit"
                                                                    class="btn btn-sm btn-primary waves-effect shadow-none">
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

                            @if (isset($_GET['filter_department']))
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-body p-2">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <h6 class="mb-0">Group Penilaian</h6>
                                                    <span class="badge bg-primary">{{ $groups->count() }}</span>
                                                </div>
                                                <div class="nav nav-pills flex-column nav-pills-tab">
                                                    @foreach ($groups as $group)
                                                        <a class="nav-link @if ($current_id_group == $group->id_group) active show @endif mb-2"
                                                            href="{{ route('5r-system.penilaian', ['id_group' => $group->id_group]) }}?filter_jadwal={{ $_GET['filter_jadwal'] }}&filter_periode={{ $_GET['filter_periode'] }}&filter_department={{ $_GET['filter_department'] }}">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <span>
                                                                    <i class="mdi mdi-checkbox-blank-circle-outline me-1"
                                                                        style="font-size: 8px;"></i>
                                                                    {{ $group->nama_group }}
                                                                </span>
                                                                @if ($jawabanGroup->where('id_group', $group->id_group)->where('id_periode', $_GET['filter_periode'])->first() != null)
                                                                    <i class="mdi mdi-check-circle text-success"></i>
                                                                @endif
                                                            </div>
                                                        </a>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-body">
                                                @foreach ($pertanyaan as $group)
                                                    @php
                                                        $jawaban = $jawabanGroup
                                                            ->where('id_group', $group->id_group)
                                                            ->where('id_periode', $_GET['filter_periode'])
                                                            ->first();
                                                        if ($jawaban != null) {
                                                            $jawaban = $jawaban->jawaban;
                                                        }
                                                    @endphp
                                                    <div class="tab-pane fade @if ($loop->iteration == 1) active show @endif"
                                                        id="custom-v-pills-{{ $group->id_group }}" role="tabpanel"
                                                        aria-labelledby="custom-v-pills-{{ $group->id_group }}-tab">
                                                        <form class="form-pertanyaan" id="form-{{ $group->id_group }}">
                                                            <input type="hidden" name="id_group"
                                                                value="{{ $group->id_group }}">
                                                            <input type="hidden" name="id_periode"
                                                                value="{{ $_GET['filter_periode'] }}">
                                                            <div id="draft-status-{{ $group->id_group }}"
                                                                class="mb-3 text-end">
                                                                <small class="text-muted">
                                                                    <i class="mdi mdi-information-outline"></i>
                                                                    Perubahan akan tersimpan otomatis
                                                                </small>
                                                            </div>
                                                            <div
                                                                class="d-flex justify-content-between align-items-center mb-4">
                                                                <h5 class="mb-0">{{ $group->nama_group }}</h5>
                                                                @if ($jawabanGroup->where('id_group', $group->id_group)->where('id_periode', $_GET['filter_periode'])->first() != null)
                                                                    <span class="badge-completed">
                                                                        <i class="mdi mdi-check-circle me-1"></i> Selesai
                                                                        Dinilai
                                                                    </span>
                                                                @else
                                                                    <span class="badge-pending">
                                                                        <i class="mdi mdi-clock-outline me-1"></i> Belum
                                                                        Dinilai
                                                                    </span>
                                                                @endif
                                                            </div>

                                                            @foreach (['RINGKAS', 'RAPI', 'RESIK', 'RAWAT', 'RAJIN', 'DIGITALISASI'] as $jenis)
                                                                @php
                                                                    $__pertanyaan = $group->pertanyaan
                                                                        ->where('jenis', $jenis)
                                                                        ->where('archive_status', 'N');
                                                                @endphp

                                                                @if ($__pertanyaan->count() > 0)
                                                                    <div class="mb-4">
                                                                        <span class="criteria-badge"
                                                                            style="background-color: {{ $colors[$loop->iteration - 1] }};">
                                                                            {{ $jenis }}
                                                                            <button type="button"
                                                                                onClick="showKisiKisi('{{ $jenis }}')"
                                                                                class="btn btn-sm btn-light ms-2 p-1"
                                                                                style="font-size: 10px;">
                                                                                <i class="mdi mdi-information-outline"></i>
                                                                                Panduan
                                                                            </button>
                                                                        </span>

                                                                        {{-- @if ($jenis === 'RAJIN' && $incrementTerakhir)
                                                                            <div class="alert alert-info">
                                                                                <strong>Nilai R1-R4 Periode
                                                                                    Sebelumnya:</strong>
                                                                                {{ $incrementTerakhir->nilai }}
                                                                            </div>
                                                                        @endif --}}

                                                                        @foreach ($__pertanyaan as $_pertanyaan)
                                                                            <div class="question-card mt-3">
                                                                                <div class="question-header">
                                                                                    <h6 class="mb-0">Item Periksa</h6>
                                                                                    <p class="mb-0">
                                                                                        {!! str_replace('||--||', '&', $_pertanyaan->item_periksa) !!}
                                                                                    </p>
                                                                                    
                                                                                    @if ($jenis === 'RAJIN' && $incrementTerakhir)
                                                                                        <div class="alert alert-info">
                                                                                            <strong>Nilai Periode
                                                                                                Sebelumnya:</strong>
                                                                                            {{ $incrementTerakhir->nilai }}
                                                                                        </div>
                                                                                    @endif

                                                                                </div>
                                                                                <div class="question-body">
                                                                                    <div class="row">
                                                                                        <div class="col-md-6">
                                                                                            <div class="mb-3">
                                                                                                <label
                                                                                                    class="form-label fw-bold">Keterangan</label>
                                                                                                <div
                                                                                                    class="bg-light p-3 rounded">
                                                                                                    {!! str_replace('||--||', '&', $_pertanyaan->keterangan) !!}
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-md-6">
                                                                                            <div class="mb-3">
                                                                                                <label
                                                                                                    class="form-label fw-bold">
                                                                                                    Nilai <span
                                                                                                        class="text-danger">*</span>
                                                                                                </label>

                                                                                                @if ($jawaban != null)
                                                                                                    @php
                                                                                                        $nilai =
                                                                                                            $jawaban
                                                                                                                ->where(
                                                                                                                    'id_pertanyaan',
                                                                                                                    $_pertanyaan->id_pertanyaan,
                                                                                                                )
                                                                                                                ->first()
                                                                                                                ->nilai ??
                                                                                                            '';
                                                                                                    @endphp

                                                                                                    <div class="btn-group w-100"
                                                                                                        role="group">
                                                                                                        @for ($i = 1; $i <= 4; $i++)
                                                                                                            <button
                                                                                                                type="button"
                                                                                                                class="btn flex-fill {{ $nilai == $i ? 'btn-primary' : 'btn-outline-secondary' }}"
                                                                                                                style="{{ $nilai == $i ? 'font-weight: bold;' : 'opacity: 0.5;' }}"
                                                                                                                disabled>
                                                                                                                {{ $i }}
                                                                                                                @if ($nilai == $i)
                                                                                                                    <i
                                                                                                                        class="mdi mdi-check-circle ms-1"></i>
                                                                                                                @endif
                                                                                                            </button>
                                                                                                        @endfor
                                                                                                    </div>
                                                                                                @else
                                                                                                    <div class="btn-group w-100"
                                                                                                        role="group">
                                                                                                        @for ($i = 1; $i <= 4; $i++)
                                                                                                            <input
                                                                                                                type="radio"
                                                                                                                class="btn-check"
                                                                                                                name="nilai[{{ $_pertanyaan->id_pertanyaan }}]"
                                                                                                                id="nilai-{{ $_pertanyaan->id_pertanyaan }}-{{ $i }}"
                                                                                                                value="{{ $i }}"
                                                                                                                onchange="changeNilai('{{ $_pertanyaan->id_group }}-{{ $_pertanyaan->id_pertanyaan }}')"
                                                                                                                required>
                                                                                                            <label
                                                                                                                class="btn btn-outline-primary flex-fill text-center"
                                                                                                                for="nilai-{{ $_pertanyaan->id_pertanyaan }}-{{ $i }}">
                                                                                                                {{ $i }}
                                                                                                            </label>
                                                                                                        @endfor
                                                                                                    </div>
                                                                                                @endif
                                                                                            </div>


                                                                                            <div class="mb-3">
                                                                                                <label
                                                                                                    class="form-label fw-bold d-block">Temuan</label>
                                                                                                <div class="d-grid gap-2">
                                                                                                    @if ($jawaban != null)
                                                                                                        {{-- Jika sudah ada jawaban/penilaian tersimpan, disable tombol tambah --}}
                                                                                                        <button
                                                                                                            type="button"
                                                                                                            class="btn btn-primary"
                                                                                                            disabled
                                                                                                            title="Penilaian sudah dikirim, tidak dapat menambah temuan">
                                                                                                            <i
                                                                                                                class="bi bi-lock me-2"></i>
                                                                                                            Tambah Temuan
                                                                                                            (Terkunci)
                                                                                                        </button>
                                                                                                        <button
                                                                                                            type="button"
                                                                                                            class="btn btn-outline-primary btn-list-temuan"
                                                                                                            data-id-pertanyaan="{{ $_pertanyaan->id_pertanyaan }}"
                                                                                                            data-read-only="true"
                                                                                                            data-bs-toggle="modal"
                                                                                                            data-bs-target="#modalListTemuan{{ $_pertanyaan->id_pertanyaan }}">
                                                                                                            <i
                                                                                                                class="bi bi-eye me-2"></i>
                                                                                                            Lihat Daftar
                                                                                                            Temuan (Read
                                                                                                            Only)
                                                                                                        </button>
                                                                                                    @else
                                                                                                        {{-- Jika belum ada jawaban, tampilkan tombol normal --}}
                                                                                                        <button
                                                                                                            type="button"
                                                                                                            class="btn btn-primary"
                                                                                                            data-bs-toggle="modal"
                                                                                                            data-bs-target="#modalTemuan{{ $_pertanyaan->id_pertanyaan }}">
                                                                                                            <i
                                                                                                                class="bi bi-plus-circle me-2"></i>
                                                                                                            Tambah Temuan
                                                                                                        </button>
                                                                                                        <button
                                                                                                            type="button"
                                                                                                            class="btn btn-outline-primary btn-list-temuan"
                                                                                                            data-id-pertanyaan="{{ $_pertanyaan->id_pertanyaan }}"
                                                                                                            data-read-only="false"
                                                                                                            data-bs-toggle="modal"
                                                                                                            data-bs-target="#modalListTemuan{{ $_pertanyaan->id_pertanyaan }}">
                                                                                                            <i
                                                                                                                class="bi bi-list-ul me-2"></i>
                                                                                                            Lihat Daftar
                                                                                                            Temuan
                                                                                                        </button>
                                                                                                    @endif
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="row">
                                                                                        <div class="col-12">
                                                                                            <div class="mb-0">
                                                                                                <label
                                                                                                    class="form-label fw-bold">Keterangan
                                                                                                    Tambahan <span
                                                                                                        class="text-danger">*</span></label>
                                                                                                <textarea onChange="changeKeterangan('{{ $_pertanyaan->id_group }}-{{ $_pertanyaan->id_pertanyaan }}')"
                                                                                                    onKeyup="changeKeterangan('{{ $_pertanyaan->id_group }}-{{ $_pertanyaan->id_pertanyaan }}')" required
                                                                                                    name="keterangan[{{ $_pertanyaan->id_pertanyaan }}]"
                                                                                                    id="keterangan_{{ $_pertanyaan->id_group }}-{{ $_pertanyaan->id_pertanyaan }}" class="form-control"
                                                                                                    rows="4" {{ $jawaban != null ? 'disabled' : '' }}
                                                                                                    placeholder="Tambahkan catatan atau keterangan tambahan di sini...">{{ $jawaban != null ? $jawaban->where('id_pertanyaan', $_pertanyaan->id_pertanyaan)->first()->keterangan ?? '' : '' }}</textarea>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <!-- Modal Temuan -->
                                                                            <div class="modal fade"
                                                                                id="modalTemuan{{ $_pertanyaan->id_pertanyaan }}"
                                                                                tabindex="-1"
                                                                                aria-labelledby="modalTemuanLabel{{ $_pertanyaan->id_pertanyaan }}"
                                                                                aria-hidden="true">
                                                                                <div class="modal-dialog modal-lg">
                                                                                    <div class="modal-content">
                                                                                        <div class="modal-header">
                                                                                            <h5 class="modal-title"
                                                                                                id="modalTemuanLabel{{ $_pertanyaan->id_pertanyaan }}">
                                                                                                Tambah Temuan
                                                                                            </h5>
                                                                                            <button type="button"
                                                                                                class="btn-close"
                                                                                                data-bs-dismiss="modal"
                                                                                                aria-label="Close"></button>
                                                                                        </div>
                                                                                        <div class="modal-body">
                                                                                            <div class="mb-3">
                                                                                                <label
                                                                                                    class="form-label">Area
                                                                                                    <span
                                                                                                        class="text-danger">*</span></label>
                                                                                                <select class="form-select"
                                                                                                    name="area[{{ $_pertanyaan->id_pertanyaan }}]"
                                                                                                    required>
                                                                                                    <option value="">
                                                                                                        Pilih area</option>
                                                                                                    @foreach ($area as $item)
                                                                                                        <option
                                                                                                            value="{{ $item->id_area }}">
                                                                                                            {{ $item->nama_area }}
                                                                                                        </option>
                                                                                                    @endforeach
                                                                                                </select>
                                                                                            </div>

                                                                                            <div class="mb-3">
                                                                                                <label
                                                                                                    class="form-label">Foto
                                                                                                    Pendukung (Bisa lebih
                                                                                                    dari 1 foto) <span
                                                                                                        class="text-danger">*</span></label>

                                                                                                @if ($jawaban != null)
                                                                                                    @php
                                                                                                        $foto =
                                                                                                            $jawaban
                                                                                                                ->where(
                                                                                                                    'id_pertanyaan',
                                                                                                                    $_pertanyaan->id_pertanyaan,
                                                                                                                )
                                                                                                                ->first()
                                                                                                                ->foto ??
                                                                                                            null;
                                                                                                    @endphp
                                                                                                    @if ($foto != null)
                                                                                                        <div
                                                                                                            class="image-preview-container readonly">
                                                                                                            @foreach (explode(',', $foto) as $index => $_foto)
                                                                                                                <div
                                                                                                                    class="image-preview-wrapper">
                                                                                                                    <img src="{{ asset('images/5r/' . $_foto) }}"
                                                                                                                        class="image-preview"
                                                                                                                        alt="Foto {{ $index + 1 }}">
                                                                                                                    <div
                                                                                                                        class="image-preview-index">
                                                                                                                        {{ $index + 1 }}
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            @endforeach
                                                                                                        </div>
                                                                                                    @else
                                                                                                        <div
                                                                                                            class="text-muted">
                                                                                                            Tidak ada foto
                                                                                                        </div>
                                                                                                    @endif
                                                                                                @else
                                                                                                    <!-- Button Group untuk Upload dan Camera -->
                                                                                                    <div class="btn-group w-100 mb-3"
                                                                                                        role="group">
                                                                                                        <button
                                                                                                            type="button"
                                                                                                            class="btn btn-outline-primary flex-fill"
                                                                                                            onclick="document.getElementById('{{ $_pertanyaan->id_group }}-{{ $_pertanyaan->id_pertanyaan }}-input-file').click()">
                                                                                                            <i
                                                                                                                class="mdi mdi-folder-image"></i>
                                                                                                            Upload Foto
                                                                                                        </button>
                                                                                                        <button
                                                                                                            type="button"
                                                                                                            class="btn btn-outline-success flex-fill btn-open-camera"
                                                                                                            data-id-pertanyaan="{{ $_pertanyaan->id_group }}-{{ $_pertanyaan->id_pertanyaan }}">
                                                                                                            <i
                                                                                                                class="mdi mdi-camera"></i>
                                                                                                            Gunakan Kamera
                                                                                                        </button>
                                                                                                    </div>

                                                                                                    <!-- Preview Container -->
                                                                                                    <div class="image-preview-container mb-2"
                                                                                                        id="{{ $_pertanyaan->id_group }}-{{ $_pertanyaan->id_pertanyaan }}-image-container">
                                                                                                    </div>

                                                                                                    <!-- Hidden File Input -->
                                                                                                    <input
                                                                                                        onchange="addMultipleImages('{{ $_pertanyaan->id_group }}-{{ $_pertanyaan->id_pertanyaan }}')"
                                                                                                        type="file"
                                                                                                        accept="image/*"
                                                                                                        multiple
                                                                                                        class="form-control d-none"
                                                                                                        name="{{ $_pertanyaan->id_group }}_{{ $_pertanyaan->id_pertanyaan }}_foto"
                                                                                                        id="{{ $_pertanyaan->id_group }}-{{ $_pertanyaan->id_pertanyaan }}-input-file">

                                                                                                    <small
                                                                                                        class="text-muted d-block mt-1">
                                                                                                        <i
                                                                                                            class="mdi mdi-information-outline"></i>
                                                                                                        Anda dapat upload
                                                                                                        beberapa foto
                                                                                                        sekaligus atau
                                                                                                        gunakan kamera untuk
                                                                                                        mengambil foto
                                                                                                    </small>
                                                                                                @endif
                                                                                            </div>

                                                                                            <div class="mb-3">
                                                                                                <label
                                                                                                    class="form-label">Deskripsi
                                                                                                    Temuan</label>
                                                                                                <textarea class="form-control" rows="4" name="deskripsi_temuan[{{ $_pertanyaan->id_pertanyaan }}]"
                                                                                                    placeholder="Deskripsikan temuan yang ditemukan"></textarea>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="modal-footer">
                                                                                            <button type="button"
                                                                                                class="btn btn-light"
                                                                                                data-bs-dismiss="modal">Batal</button>
                                                                                            <button type="button"
                                                                                                class="btn btn-primary btn-save-temuan">
                                                                                                <i
                                                                                                    class="bi bi-save me-1"></i>
                                                                                                Simpan Temuan
                                                                                            </button>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <!-- Modal List Temuan -->
                                                                            <div class="modal fade"
                                                                                id="modalListTemuan{{ $_pertanyaan->id_pertanyaan }}"
                                                                                tabindex="-1"
                                                                                aria-labelledby="modalListTemuanLabel{{ $_pertanyaan->id_pertanyaan }}"
                                                                                aria-hidden="true">
                                                                                <div class="modal-dialog modal-lg">
                                                                                    <div class="modal-content">
                                                                                        <div class="modal-header">
                                                                                            <h5 class="modal-title"
                                                                                                id="modalListTemuanLabel{{ $_pertanyaan->id_pertanyaan }}">
                                                                                                <i
                                                                                                    class="bi bi-list-check me-2"></i>
                                                                                                Daftar Temuan
                                                                                            </h5>
                                                                                            <button type="button"
                                                                                                class="btn-close"
                                                                                                data-bs-dismiss="modal"
                                                                                                aria-label="Close"></button>
                                                                                        </div>
                                                                                        <div class="modal-body">
                                                                                            <!-- Loading Indicator -->
                                                                                            <div class="text-center py-4"
                                                                                                id="loadingTemuan{{ $_pertanyaan->id_pertanyaan }}">
                                                                                                <div class="spinner-border text-primary"
                                                                                                    role="status">
                                                                                                    <span
                                                                                                        class="visually-hidden">Loading...</span>
                                                                                                </div>
                                                                                                <p class="mt-2 text-muted">
                                                                                                    Memuat data temuan...
                                                                                                </p>
                                                                                            </div>

                                                                                            <!-- Table Container -->
                                                                                            <div class="table-responsive"
                                                                                                id="tableTemuanContainer{{ $_pertanyaan->id_pertanyaan }}"
                                                                                                style="display: none;">
                                                                                                <table
                                                                                                    class="table table-hover table-bordered align-middle">
                                                                                                    <thead
                                                                                                        class="table-light">
                                                                                                        <tr>
                                                                                                            <th width="5%"
                                                                                                                class="text-center">
                                                                                                                No</th>
                                                                                                            <th
                                                                                                                width="12%">
                                                                                                                Area</th>
                                                                                                            <th
                                                                                                                width="30%">
                                                                                                                Deskripsi
                                                                                                            </th>
                                                                                                            <th
                                                                                                                width="12%">
                                                                                                                Dibuat Oleh
                                                                                                            </th>
                                                                                                            <th
                                                                                                                width="10%">
                                                                                                                Tanggal</th>
                                                                                                            <th
                                                                                                                width="14%">
                                                                                                                Foto</th>
                                                                                                            <th width="5%"
                                                                                                                class="text-center">
                                                                                                                Aksi</th>
                                                                                                        </tr>
                                                                                                    </thead>
                                                                                                    <tbody
                                                                                                        id="tableTemuanBody{{ $_pertanyaan->id_pertanyaan }}">
                                                                                                        <!-- Data will be loaded here -->
                                                                                                    </tbody>
                                                                                                </table>
                                                                                            </div>

                                                                                            <!-- Empty State -->
                                                                                            <div class="text-center py-5"
                                                                                                id="emptyStateTemuan{{ $_pertanyaan->id_pertanyaan }}"
                                                                                                style="display: none;">
                                                                                                <i class="bi bi-inbox"
                                                                                                    style="font-size: 48px; color: #ccc;"></i>
                                                                                                <p class="mt-3 text-muted">
                                                                                                    Belum ada temuan untuk
                                                                                                    pertanyaan ini</p>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="modal-footer">
                                                                                            <button type="button"
                                                                                                class="btn btn-light"
                                                                                                data-bs-dismiss="modal">
                                                                                                Tutup
                                                                                            </button>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                @endif
                                                            @endforeach

                                                            @if ($jawabanGroup->where('id_group', $group->id_group)->where('id_periode', $_GET['filter_periode'])->first() == null)
                                                                <div class="text-end mt-4">
                                                                    <button type="button"
                                                                        form="form-{{ $group->id_group }}"
                                                                        class="btn btn-primary" id="btnSimpan"
                                                                        data-id-group="{{ $group->id_group }}">
                                                                        <i class="mdi mdi-send-check me-1"></i> Kirim
                                                                        Penilaian
                                                                    </button>
                                                                </div>
                                                            @endif
                                                        </form>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @if (!empty($dataNilaiSebelumnya))
                <h4 class="mt-4">Nilai Periode Sebelumnya (ID: {{ $previousPeriodeId }})</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Grup</th>
                            <th>Total Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($dataNilaiSebelumnya as $item)
                            <tr>
                                <td>{{ optional($item->group)->nama_group }}</td>
                                <td>
                                    @php
                                        $total = 0;
                                        foreach ($item->jawaban as $jawaban) {
                                            $total += $jawaban->nilai;
                                        }
                                        echo round($total, 2);
                                    @endphp
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="mt-4">Tidak ada data nilai pada periode sebelumnya.</p>
            @endif

            <!-- Camera Modal  -->
            <div class="modal fade" id="cameraModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="mdi mdi-camera"></i>
                                Ambil Foto
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center p-4">
                            <!-- Video Preview -->
                            <div class="camera-container mb-3"
                                style="max-width: 100%; background: #000; border-radius: 8px; overflow: hidden;">
                                <video id="cameraVideo" autoplay playsinline style="width: 100%; display: block;"></video>
                            </div>

                            <!-- Canvas untuk capture (hidden) -->
                            <canvas id="cameraCanvas" style="display: none;"></canvas>

                            <!-- Info -->
                            <div id="cameraInfo" class="alert alert-info">
                                <i class="mdi mdi-information"></i>
                                Posisikan
                                objek di
                                depan
                                kamera, lalu
                                klik tombol
                                Ambil Foto
                            </div>

                            <!-- Error message -->
                            <div id="cameraError" class="alert alert-danger" style="display: none;">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                <i class="mdi mdi-close"></i>
                                Batal
                            </button>
                            <button type="button" class="btn btn-primary" id="btnCapturePhoto">
                                <i class="mdi mdi-camera"></i>
                                Ambil Foto
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kisi-kisi Modal -->
            <div class="modal fade" id="kisi-kisi-modal" tabindex="-1" role="dialog"
                aria-labelledby="kisi-kisi-modal-label" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="kisi-kisi-modal-label">Panduan Penilaian</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center p-4">
                            <img src="" style="max-width: 100%; height: auto; border-radius: 8px;"
                                id="kisi-kisi-image" alt="Panduan Penilaian">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Confirmation Modal -->
            <div id="keteranganModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel"
                aria-hidden="true" style="display: none;">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalLabel1">Konfirmasi Penilaian</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="text-center mb-4">
                                <i class="mdi mdi-alert-circle-outline text-warning" style="font-size: 60px;"></i>
                            </div>
                            <div class="text-left mb-4">
                                <h5 class="text-center mb-3">Verifikasi Penilaian</h5>
                                <div class="alert alert-light bg-light text-dark border-0">
                                    <div class="d-flex align-items-center">
                                        <i class="mdi mdi-account-circle me-2" style="font-size: 24px;"></i>
                                        <div>
                                            <p class="mb-1"><strong>Juri Penilai</strong></p>
                                            <p class="mb-0">NIK: {{ Auth::user()->username }}</p>
                                            <p class="mb-0">Nama: {{ Auth::user()->name }}</p>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-center">Dengan ini menyatakan bahwa penilaian 5R untuk group ini telah
                                    selesai dan akan disimpan.</p>
                            </div>

                            <button type="button" class="btn btn-warning w-100 mb-3" id="btnSetujui">
                                <i class="mdi mdi-send-check me-1"></i> Kirim Penilaian
                            </button>

                            <div id="form-committee" style="display: none;">
                                <hr>
                                <h5 class="text-center mb-3">Verifikasi Committee</h5>
                                <input type="hidden" id="id_group" name="id_group"
                                    value="{{ $group->id_group ?? '' }}">

                                <div class="form-group mb-3">
                                    <label for="username" class="form-label">Username Committee</label>
                                    <input type="text" id="username" name="username" class="form-control"
                                        placeholder="Masukkan username">
                                    <div class="valid-feedback" id="usernameSuccess">
                                        <i class="mdi mdi-check-circle me-1"></i> Username valid
                                    </div>
                                    <div class="invalid-feedback" id="usernameError">
                                        <i class="mdi mdi-alert-circle me-1"></i> Username tidak valid
                                    </div>
                                </div>

                                <div class="form-group mb-4">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" id="password" name="password" class="form-control"
                                        placeholder="Masukkan password">
                                    <div class="valid-feedback" id="passwordSuccess">
                                        <i class="mdi mdi-check-circle me-1"></i> Password valid
                                    </div>
                                    <div class="invalid-feedback" id="passwordError">
                                        <i class="mdi mdi-alert-circle me-1"></i> Password tidak valid
                                    </div>
                                </div>

                                <button type="button" class="btn btn-primary w-100" id="btnCommittee">
                                    <i class="mdi mdi-shield-check me-1"></i> Verifikasi
                                </button>

                                <div class="mt-3" id="btnSimpanContainer" style="display: none;">
                                    <hr>
                                    <button type="submit" form="form-{{ $group->id_group ?? '' }}"
                                        class="btn btn-success w-100 waves-effect btn-konfirmasi-penilaian"
                                        id="btnSimpanFinal">
                                        <i class="mdi mdi-content-save-check me-1"></i> Simpan Penilaian
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        let autoSaveTimer = null;
        const AUTO_SAVE_DELAY = 2000;

        // Function untuk mengumpulkan data form
        function collectFormData(idGroup) {
            const form = $('#form-' + idGroup);
            const nilai = {};
            const keterangan = {};

            // Ambil semua nilai yang sudah dipilih
            form.find('input[type="radio"]:checked').each(function() {
                const name = $(this).attr('name');
                const idPertanyaan = name.match(/nilai\[(.+)\]/)[1];
                nilai[idPertanyaan] = $(this).val();
            });

            // Ambil semua keterangan yang sudah diisi
            form.find('textarea[name^="keterangan"]').each(function() {
                const name = $(this).attr('name');
                const idPertanyaan = name.match(/keterangan\[(.+)\]/)[1];
                const value = $(this).val();
                if (value) {
                    keterangan[idPertanyaan] = value;
                }
            });

            return {
                nilai,
                keterangan
            };
        }

        // Function untuk save draft
        function saveDraftToDatabase(idGroup, idPeriode) {
            const formData = collectFormData(idGroup);

            // Jangan save jika tidak ada data
            if (Object.keys(formData.nilai).length === 0 &&
                Object.keys(formData.keterangan).length === 0) {
                return;
            }

            $.ajax({
                url: "{{ route('5r-system.save-draft') }}",
                type: "POST",
                data: {
                    id_group: idGroup,
                    id_periode: idPeriode,
                    nilai: formData.nilai,
                    keterangan: formData.keterangan,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.status === 'success') {
                        // Update timestamp di UI jika ada
                        const timestamp = response.last_saved || new Date().toLocaleTimeString('id-ID');
                        $('#draft-status-' + idGroup).html(
                            '<small class="text-muted">' +
                            '<i class="mdi mdi-check-circle text-success"></i> ' +
                            'Draft tersimpan ' + timestamp +
                            '</small>'
                        );
                    }
                },
                error: function(xhr) {
                    console.error('Error saving draft:', xhr);
                }
            });
        }

        // Function untuk load draft
        function loadDraftFromDatabase(idGroup, idPeriode) {
            $.ajax({
                url: "{{ route('5r-system.load-draft') }}",
                type: "GET",
                data: {
                    id_group: idGroup,
                    id_periode: idPeriode
                },
                success: function(response) {
                    if (response.status === 'success' && response.data) {
                        const draftData = response.data;

                        // Load nilai
                        if (draftData.nilai) {
                            Object.keys(draftData.nilai).forEach(function(idPertanyaan) {
                                const nilai = draftData.nilai[idPertanyaan];
                                $('input[name="nilai[' + idPertanyaan + ']"][value="' + nilai + '"]')
                                    .prop('checked', true);
                            });
                        }

                        // Load keterangan
                        if (draftData.keterangan) {
                            Object.keys(draftData.keterangan).forEach(function(idPertanyaan) {
                                const keterangan = draftData.keterangan[idPertanyaan];
                                $('textarea[name="keterangan[' + idPertanyaan + ']"]')
                                    .val(keterangan);
                            });
                        }

                        // Show notification
                        if (response.last_saved) {
                            Swal.fire({
                                icon: 'info',
                                title: 'Draft Ditemukan',
                                text: 'Draft terakhir dimuat (' + response.last_saved + ')',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    }
                },
                error: function(xhr) {
                    console.error('Error loading draft:', xhr);
                }
            });
        }

        // Trigger auto-save saat nilai berubah
        function setupAutoSave(idGroup, idPeriode) {
            const form = $('#form-' + idGroup);

            // Auto-save saat radio button berubah
            form.find('input[type="radio"]').on('change', function() {
                clearTimeout(autoSaveTimer);
                autoSaveTimer = setTimeout(function() {
                    saveDraftToDatabase(idGroup, idPeriode);
                }, AUTO_SAVE_DELAY);
            });

            // Auto-save saat textarea berubah
            form.find('textarea[name^="keterangan"]').on('input', function() {
                clearTimeout(autoSaveTimer);
                autoSaveTimer = setTimeout(function() {
                    saveDraftToDatabase(idGroup, idPeriode);
                }, AUTO_SAVE_DELAY);
            });
        }

        // Initialize saat halaman load
        $(document).ready(function() {
            @if (isset($_GET['filter_department']))
                @foreach ($groups as $group)
                    @if ($current_id_group == $group->id_group)
                        const idGroup = '{{ $group->id_group }}';
                        const idPeriode = '{{ $_GET['filter_periode'] }}';

                        // Load draft jika ada
                        loadDraftFromDatabase(idGroup, idPeriode);

                        // Setup auto-save
                        setupAutoSave(idGroup, idPeriode);
                    @endif
                @endforeach
            @endif
        });

        var isBtnSimpanClicked = false;
        var isBtnSetujuiClicked = false;

        function showModal(idGroup) {
            var formId = "#form-" + idGroup;
            var form = $(formId);

            // Validasi nilai (radio button)
            var pertanyaanCards = form.find('.question-card');
            var firstEmptyNilai = null;

            for (var i = 0; i < pertanyaanCards.length; i++) {
                var card = $(pertanyaanCards[i]);
                var radioButtons = card.find('input[type="radio"][name^="nilai"]:checked');

                if (radioButtons.length === 0) {
                    firstEmptyNilai = card;
                    break;
                }
            }

            if (firstEmptyNilai) {
                // Scroll ke card yang belum diisi
                $('html, body').animate({
                    scrollTop: firstEmptyNilai.offset().top - 100
                }, 500);

                // Highlight card
                firstEmptyNilai.css('border-left', '4px solid #dc3545');
                setTimeout(function() {
                    firstEmptyNilai.css('border-left', '4px solid #6c5ce7');
                }, 2000);

                // Focus ke radio button pertama
                firstEmptyNilai.find('input[type="radio"]').first().focus();

                Swal.fire({
                    icon: 'warning',
                    title: 'Nilai Belum Dipilih',
                    text: 'Mohon pilih nilai untuk semua pertanyaan',
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }

            // Validasi keterangan tambahan
            var keteranganTextareas = form.find('textarea[name^="keterangan"]').filter(function() {
                return !$(this).closest('.modal').length && $(this).is(':visible');
            });

            var firstEmptyKeterangan = null;

            keteranganTextareas.each(function() {
                if (!$(this).val().trim()) {
                    firstEmptyKeterangan = $(this);
                    return false; // Break loop
                }
            });

            if (firstEmptyKeterangan) {
                // Scroll ke textarea yang belum diisi
                $('html, body').animate({
                    scrollTop: firstEmptyKeterangan.offset().top - 100
                }, 500);

                // Focus dan highlight
                firstEmptyKeterangan.focus();
                firstEmptyKeterangan.css('border', '2px solid #dc3545');
                setTimeout(function() {
                    firstEmptyKeterangan.css('border', '');
                }, 2000);

                Swal.fire({
                    icon: 'warning',
                    title: 'Keterangan Kosong',
                    text: 'Mohon isi keterangan tambahan',
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }

            // Jika semua validasi lolos
            $('#keteranganModal').modal('show');
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
        //                     // Swal.fire({
        //                     //     icon: 'success',
        //                     //     title: '',
        //                     //     text: `username dan password sesuai dengan group departemen ini`,
        //                     //     showConfirmButton: false,
        //                     //     timer: 2000
        //                     // });
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
        //                     // alert('Terjadi kesalahan dalam permintaan AJAX');
        //                 }
        //             }
        //         });
        //     });
        // });

        $(document).ready(function() {
            $('#btnCommittee').on('click', function() {
                var username = $('#username').val();
                var password = $('#password').val();
                var idGroup = $('#id_group').val();
                var btnCommittee = this;

                // Reset validasi
                $('#username, #password').removeClass('is-invalid is-valid');
                $('#usernameError, #passwordError, #usernameSuccess, #passwordSuccess').hide();

                $.ajax({
                    type: 'GET',
                    url: '/5r-system/validate-credentials-comittee/' + idGroup,
                    data: {
                        id_group: idGroup,
                        username: username,
                        password: password,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response, textStatus, xhr) {
                        if (xhr.status === 200 && response.success) {
                            btnCommittee.classList.remove('btn-warning');
                            btnCommittee.classList.add('btn-ghost-success');
                            $(btnCommittee).html(`<i class="mdi mdi-check-bold"></i> Diterima`);
                            document.getElementById('form-committee').style.display = 'block';
                            $('#btnSimpanContainer').slideDown();
                        }
                        $('#username').addClass('is-valid');
                        $('#usernameSuccess').show();
                        $('#password').addClass('is-valid');
                        $('#passwordSuccess').show();
                    },
                    error: function(xhr, textStatus, errorThrown) {
                        if (xhr.status === 401) {
                            $('#username').addClass('is-invalid');
                            $('#usernameError').show();
                            alert('Unauthorized: Username atau password salah');
                        } else if (xhr.status === 403) {
                            $('#password').addClass('is-invalid');
                            $('#passwordError').show();
                            alert('Forbidden: Username bukan merupakan anggota komite');
                        } else {
                            $('#username, #password').addClass('is-invalid');
                            $('#usernameError').show();
                            $('#passwordError').show();
                        }
                    }
                });
            });
        });

        // Tambahkan event listener untuk tombol simpan final
        $('#btnSimpanFinal').on('click', function() {
            var form = $('.form-pertanyaan');
            var formData = form.serialize();

            // Tambahkan CSRF token ke data
            formData += '&_token={{ csrf_token() }}';

            // Kirim data via AJAX
            $.ajax({
                url: "{{ url('5r-system/do-submit') }}",
                type: "POST",
                dataType: "JSON",
                data: formData,
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            timer: 1000
                        }).then(() => location.reload());
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message,
                            timer: 1000
                        });
                    }
                },
                error: function(error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: error.responseJSON.message,
                    });
                }
            });
        });


        function showKisiKisi(jenis) {
            $('#kisi-kisi-image').attr('src', "{{ asset('assets/foto/system_5r/kisi-kisi') }}-" + jenis + ".png")
            $('#kisi-kisi-modal').modal('show')
        }

        $('#filter_jadwal').on('change', function() {
            var id_jadwal = $(this).val();

            // Get periode
            $.ajax({
                url: "{{ url('5r-system/get-periode-by-id-jadwal') }}/" + id_jadwal,
                type: "GET",
                dataType: "JSON",
                success: function(response) {
                    if (response.status == 'success') {
                        $('#filter_periode').html('');
                        $('#filter_periode').append('<option value="">PILIH</option>');
                        $.each(response.data, function(index, value) {
                            $('#filter_periode').append('<option value="' + value.id_periode +
                                '">' + value.nama_periode + '</option>');
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
                error: function(error) {
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

    @if ($isJuri)
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

                let objectStore = db.createObjectStore("penilaian", {
                    keyPath: "id"
                });
            };

            function changeNilai(idPertanyaan) {
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
                            objectStore.put({
                                id: idPertanyaan,
                                nilai: nilai
                            });
                            console.log("Data berhasil dibuat");
                        }

                    };

                    request.onerror = function(event) {
                        console.log("Data gagal disimpan");
                    };
                };
            }

            function changeKeterangan(idPertanyaan) {
                let keterangan = document.getElementById('keterangan_' + idPertanyaan).value;

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
                            objectStore.put({
                                id: idPertanyaan,
                                keterangan: keterangan
                            });
                            console.log("Data berhasil dibuat");
                        }

                    };

                    request.onerror = function(event) {
                        console.log("Data gagal disimpan");
                    };
                };
            }

            const compressImage = async (file, {
                quality = 1,
                type = file.type
            }) => {
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

            const addMultipleImages = async (idPertanyaan) => {
                var files = document.getElementById(idPertanyaan + '-input-file').files;

                if (!files.length) return;

                // Validasi semua file adalah gambar
                for (let file of files) {
                    if (!file.type.startsWith('image')) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Semua file harus berupa gambar',
                            timer: 1500
                        });
                        return;
                    }
                }

                // PROSES semua foto DULU jadi base64
                let allImages = [];

                for (let file of files) {
                    try {
                        let compressedFile = await compressImage(file, {
                            quality: 0.4,
                            type: 'image/jpeg',
                        });

                        // Convert to base64
                        let base64 = await new Promise((resolve, reject) => {
                            let reader = new FileReader();
                            reader.onload = () => resolve(reader.result);
                            reader.onerror = reject;
                            reader.readAsDataURL(compressedFile);
                        });

                        allImages.push(base64);
                    } catch (error) {
                        console.error('Error processing file:', error);
                    }
                }

                if (allImages.length === 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Tidak ada foto yang berhasil diproses',
                        timer: 1500
                    });
                    return;
                }

                // BARU simpan ke IndexedDB SETELAH semua foto ready
                var database = window.indexedDB.open("system_5r", 5);

                database.onsuccess = function(event) {
                    let db = event.target.result;
                    let transaction = db.transaction("penilaian", "readwrite");
                    let objectStore = transaction.objectStore("penilaian");

                    let request = objectStore.get(idPertanyaan);

                    request.onsuccess = function(event) {
                        let data = event.target.result;
                        let existingImages = data && data.images ? data.images : [];

                        // Gabungkan dengan foto yang sudah ada
                        let mergedImages = existingImages.concat(allImages);

                        // Save ke IndexedDB
                        if (data) {
                            data.images = mergedImages;
                            objectStore.put(data);
                        } else {
                            objectStore.put({
                                id: idPertanyaan,
                                images: mergedImages
                            });
                        }

                        // Render preview
                        renderImages(idPertanyaan, mergedImages);

                        // Clear file input
                        document.getElementById(idPertanyaan + '-input-file').value = '';

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: `${allImages.length} foto berhasil ditambahkan`,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    };

                    request.onerror = function(event) {
                        console.error('Error saving to IndexedDB:', event.target.error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Gagal menyimpan foto ke browser',
                            timer: 1500
                        });
                    };
                };

                database.onerror = function(event) {
                    console.error('Error opening IndexedDB:', event.target.error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Gagal membuka database browser',
                        timer: 1500
                    });
                };
            };

            // Function untuk render images
            // Function untuk render images - TAMBAHKAN INI
            function renderImages(idPertanyaan, images) {
                const container = document.getElementById(idPertanyaan + '-image-container');

                if (!container) {
                    console.error('Container tidak ditemukan:', idPertanyaan + '-image-container');
                    return;
                }

                container.innerHTML = '';

                if (!images || images.length === 0) {
                    return;
                }

                images.forEach((base64, index) => {
                    let imageWrapper = document.createElement('div');
                    imageWrapper.className = 'image-preview-wrapper';

                    let image = document.createElement('img');
                    image.src = base64;
                    image.className = 'image-preview';
                    image.style.width = '100px';
                    image.style.height = '100px';
                    image.style.objectFit = 'cover';

                    let indexBadge = document.createElement('div');
                    indexBadge.className = 'image-preview-index';
                    indexBadge.textContent = index + 1;

                    let deleteButton = document.createElement('button');
                    deleteButton.className = 'image-preview-remove';
                    deleteButton.type = 'button';
                    deleteButton.innerHTML = '×';
                    deleteButton.onclick = function() {
                        deleteImage(idPertanyaan, index);
                    };

                    imageWrapper.appendChild(image);
                    imageWrapper.appendChild(indexBadge);
                    imageWrapper.appendChild(deleteButton);
                    container.appendChild(imageWrapper);
                });
            }

            // Function untuk delete single image
            function deleteImage(idPertanyaan, imageIndex) {
                Swal.fire({
                    title: 'Hapus Foto?',
                    text: 'Apakah anda yakin ingin menghapus foto ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    var database = window.indexedDB.open("system_5r", 5);

                    database.onsuccess = function(event) {
                        let db = event.target.result;
                        let transaction = db.transaction("penilaian", "readwrite");
                        let objectStore = transaction.objectStore("penilaian");

                        let request = objectStore.get(idPertanyaan);

                        request.onsuccess = function(event) {
                            let data = event.target.result;
                            if (data && data.images) {
                                data.images.splice(imageIndex, 1);
                                objectStore.put(data);

                                renderImages(idPertanyaan, data.images);

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Dihapus',
                                    text: 'Foto berhasil dihapus',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            }
                        };
                    };
                });
            }

            // Load images dari IndexedDB saat halaman load
            function loadImagesFromIndexedDB(idPertanyaan) {
                var database = window.indexedDB.open("system_5r", 5);

                database.onsuccess = function(event) {
                    var db = event.target.result;
                    var transaction = db.transaction("penilaian", "readonly");
                    var objectStore = transaction.objectStore("penilaian");
                    var request = objectStore.get(idPertanyaan);

                    request.onsuccess = function(event) {
                        var data = event.target.result;
                        if (data && data.images && data.images.length > 0) {
                            renderImages(idPertanyaan, data.images);
                        }
                    };
                };
            }


            @foreach ($pertanyaan as $_pertanyaan)
                @foreach ($_pertanyaan->pertanyaan as $p)

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
                                document.getElementById('keterangan_' + idPertanyaan).value = data.keterangan ?? '';
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
                                        deleteButton.onclick = function() {

                                            var confirm = window.confirm(
                                                'Apakah anda yakin ingin menghapus foto ini?');
                                            if (!confirm) return;

                                            // Delete image from indexeddb
                                            var database = window.indexedDB.open("system_5r", 5);

                                            // Save data to indexeddb
                                            database.onsuccess = function(event) {
                                                let db = event.target.result;
                                                let transaction = db.transaction("penilaian",
                                                    "readwrite");
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
                                                    console.log("Error retrieving data:", event
                                                        .target.error);
                                                };
                                            };

                                            // Delete image from DOM
                                            this.parentElement.remove();
                                        }
                                        imageInner.appendChild(deleteButton);

                                        document.getElementById(idPertanyaan + '-image-container').appendChild(
                                            imageInner);

                                        // Store to textarea
                                        // document.getElementById(idPertanyaan + '_image').value = data.image;
                                        var textareaImage = document.createElement('textarea');
                                        textareaImage.name = 'image[' + idPertanyaan.split('-')[1] + '][]';
                                        textareaImage.value = _image;

                                        document.getElementById(idPertanyaan + '_image_container').appendChild(
                                            textareaImage);
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

            // Validasi form sebelum submit
            $('.form-pertanyaan').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var formId = form.attr('id');

                // Validasi textarea image container
                var textareaImageContainer = form.find('.textarea_image_container');
                for (let i = 0; i < textareaImageContainer.length; i++) {
                    if (textareaImageContainer[i].innerHTML.trim() === '') {
                        var textareaImageContainerId = textareaImageContainer[i].id;
                        document.getElementById(textareaImageContainerId.replace('_image_container', '-input-file'))
                            .focus();
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Foto tidak boleh kosong',
                            timer: 1000
                        });
                        return;
                    }
                }

                // Validasi semua input biasa
                form.find('input, select, textarea').each(function() {
                    if ($(this).prop('required') && !$(this).val()) {
                        $(this).focus();
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Mohon lengkapi semua data sebelum lanjut ke konfirmasi penilaian',
                            timer: 1500
                        });
                        return false;
                    }
                });

                // Jika semua validasi lolos, lanjut ke konfirmasi penilaian
                $('#keteranganModal').modal('show');
            });

            // Tambahkan event listener untuk Enter di input password
            $(document).on('keypress', '#password', function(e) {
                if (e.which === 13) { // 13 adalah kode tombol Enter
                    e.preventDefault(); // Cegah reload halaman
                    $('#btnCommittee').click(); // Picu klik tombol Verifikasi
                }
            });

            // Tambahkan event listener untuk submit form (jika menggunakan tag <form>)
            $('#committeeForm').on('submit', function(e) {
                e.preventDefault(); // Cegah submit default
                $('#btnCommittee').click(); // Picu klik tombol Verifikasi
            });

            // Handle save temuan via AJAX - FIXED VERSION
            $(document).on('click', '.btn-save-temuan', function() {
                var button = $(this);
                var modal = button.closest('.modal');
                var modalId = modal.attr('id');

                // Extract id_pertanyaan dari modal ID
                var idPertanyaan = modalId.replace('modalTemuan', '');

                console.log('=== DEBUG START ===');
                console.log('Modal ID:', modalId);
                console.log('ID Pertanyaan:', idPertanyaan);

                // Get form values
                var area = modal.find('select[name="area[' + idPertanyaan + ']"]').val();
                var deskripsi = modal.find('textarea[name="deskripsi_temuan[' + idPertanyaan + ']"]').val();

                console.log('Area:', area);
                console.log('Deskripsi:', deskripsi);

                // Validasi area
                if (!area) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Area Wajib Diisi',
                        text: 'Silakan pilih area terlebih dahulu',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#6c5ce7'
                    });
                    return;
                }

                // Cari id_periode dari form yang aktif
                var activeForm = $('.form-pertanyaan').filter(function() {
                    return $(this).closest('.tab-pane').hasClass('active');
                });
                var idPeriode = activeForm.find('input[name="id_periode"]').val();

                console.log('ID Periode:', idPeriode);

                if (!idPeriode) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'ID Periode tidak ditemukan. Silakan refresh halaman.',
                    });
                    return;
                }

                // Cari id_group dari tab-pane yang aktif
                var activeTabPane = $('.tab-pane.active.show');
                var tabPaneId = activeTabPane.attr('id');
                var idGroup = tabPaneId ? tabPaneId.replace('custom-v-pills-', '') : null;

                console.log('Tab Pane ID:', tabPaneId);
                console.log('ID Group:', idGroup);

                if (!idGroup) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'ID Group tidak ditemukan. Silakan refresh halaman.',
                    });
                    return;
                }

                // Construct ID key untuk IndexedDB
                var idKey = idGroup + '-' + idPertanyaan;
                console.log('ID Key untuk IndexedDB:', idKey);

                // Show loading immediately
                button.prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-1"></i> Menyimpan...'
                );

                // Get images from IndexedDB
                var database = window.indexedDB.open("system_5r", 5);

                database.onsuccess = function(event) {
                    var db = event.target.result;

                    // Check if objectStore exists
                    if (!db.objectStoreNames.contains("penilaian")) {
                        console.error('ObjectStore "penilaian" tidak ditemukan');
                        Swal.fire({
                            icon: 'error',
                            title: 'Error Database',
                            text: 'Database belum siap. Silakan refresh halaman.',
                        });
                        button.prop('disabled', false).html(
                            '<i class="bi bi-save me-1"></i> Simpan Temuan'
                        );
                        return;
                    }

                    var transaction = db.transaction("penilaian", "readonly");
                    var objectStore = transaction.objectStore("penilaian");
                    var request = objectStore.get(idKey);

                    request.onsuccess = function(event) {
                        var data = event.target.result;

                        console.log('Data dari IndexedDB:', data);

                        var images = [];

                        if (data && data.images && Array.isArray(data.images)) {
                            images = data.images;
                        }

                        console.log('Jumlah foto:', images.length);
                        console.log('Sample foto (first 100 chars):', images[0] ? images[0].substring(0, 100) :
                            'none');

                        // VALIDASI FOTO
                        if (images.length === 0) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Foto Wajib Diisi',
                                text: 'Silakan upload foto terlebih dahulu sebelum menyimpan temuan',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#6c5ce7'
                            });
                            button.prop('disabled', false).html(
                                '<i class="bi bi-save me-1"></i> Simpan Temuan'
                            );
                            return;
                        }

                        // Validasi format base64
                        var validImage = false;
                        for (var i = 0; i < images.length; i++) {
                            if (images[i] && typeof images[i] === 'string' && images[i].length > 100) {
                                validImage = true;
                                break;
                            }
                        }

                        if (!validImage) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Foto Tidak Valid',
                                text: 'Format foto tidak valid. Silakan upload ulang',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#6c5ce7'
                            });
                            button.prop('disabled', false).html(
                                '<i class="bi bi-save me-1"></i> Simpan Temuan'
                            );
                            return;
                        }

                        // Prepare data untuk dikirim
                        var formData = {
                            id_pertanyaan: idPertanyaan,
                            id_periode: idPeriode,
                            area: area,
                            deskripsi_temuan: deskripsi,
                            foto: images,
                            _token: '{{ csrf_token() }}'
                        };

                        console.log('Data yang akan dikirim:', {
                            id_pertanyaan: idPertanyaan,
                            id_periode: idPeriode,
                            area: area,
                            deskripsi_length: deskripsi ? deskripsi.length : 0,
                            foto_count: images.length
                        });

                        // Send AJAX request
                        $.ajax({
                            url: "{{ route('5r-system.save-temuan') }}",
                            type: "POST",
                            dataType: "JSON",
                            data: formData,
                            success: function(response) {
                                console.log('Response:', response);

                                if (response.status === 'success') {
                                    // Close modal
                                    modal.modal('hide');

                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil',
                                        text: response.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        // Clear form fields
                                        modal.find('select[name="area[' + idPertanyaan +
                                            ']"]').val('');
                                        modal.find('textarea[name="deskripsi_temuan[' +
                                            idPertanyaan + ']"]').val('');

                                        // Clear image preview
                                        var imageContainer = document.getElementById(idKey +
                                            '-image-container');
                                        if (imageContainer) {
                                            imageContainer.innerHTML = '';
                                        }

                                        // Clear file input
                                        var fileInput = document.getElementById(idKey +
                                            '-input-file');
                                        if (fileInput) {
                                            fileInput.value = '';
                                        }

                                        // Clear IndexedDB
                                        var clearDB = window.indexedDB.open("system_5r", 5);
                                        clearDB.onsuccess = function(event) {
                                            var db = event.target.result;
                                            var transaction = db.transaction(
                                                "penilaian", "readwrite");
                                            var objectStore = transaction.objectStore(
                                                "penilaian");
                                            var requestClear = objectStore.get(idKey);

                                            requestClear.onsuccess = function(event) {
                                                var data = event.target.result;
                                                if (data) {
                                                    data.images = [];
                                                    objectStore.put(data);
                                                    console.log(
                                                        '✓ IndexedDB cleared');
                                                }
                                            };
                                        };
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: response.message || 'Terjadi kesalahan',
                                    });
                                }

                                // Reset button
                                button.prop('disabled', false).html(
                                    '<i class="bi bi-save me-1"></i> Simpan Temuan'
                                );
                            },
                            error: function(xhr, status, error) {
                                console.error('AJAX Error:', {
                                    status: xhr.status,
                                    statusText: xhr.statusText,
                                    responseText: xhr.responseText,
                                    error: error
                                });

                                var errorMessage = 'Terjadi kesalahan saat menyimpan';

                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                } else if (xhr.responseText) {
                                    errorMessage = 'Server error: ' + xhr.statusText;
                                }

                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal Menyimpan',
                                    text: errorMessage,
                                });

                                // Reset button
                                button.prop('disabled', false).html(
                                    '<i class="bi bi-save me-1"></i> Simpan Temuan'
                                );
                            }
                        });
                    };

                    request.onerror = function(event) {
                        console.error('IndexedDB request error:', event.target.error);

                        Swal.fire({
                            icon: 'error',
                            title: 'Error Database',
                            text: 'Gagal membaca data foto dari browser',
                        });

                        button.prop('disabled', false).html(
                            '<i class="bi bi-save me-1"></i> Simpan Temuan'
                        );
                    };
                };

                database.onerror = function(event) {
                    console.error('IndexedDB open error:', event.target.error);

                    Swal.fire({
                        icon: 'error',
                        title: 'Error Database',
                        text: 'Gagal membuka database browser',
                    });

                    button.prop('disabled', false).html(
                        '<i class="bi bi-save me-1"></i> Simpan Temuan'
                    );
                };

                console.log('=== DEBUG END ===');
            });

            $(document).on('show.bs.modal', '[id^="modalListTemuan"]', function(e) {
                var modal = $(this);
                var idPertanyaan = modal.attr('id').replace('modalListTemuan', '');
                var idPeriode = modal.closest('.tab-pane').find('input[name="id_periode"]').val();
                var listButton = $('[data-bs-target="#' + modal.attr('id') + '"]');
                var isReadOnly = listButton.data('read-only') === true || listButton.data('read-only') === 'true';

                $('#loadingTemuan' + idPertanyaan).show();
                $('#tableTemuanContainer' + idPertanyaan).hide();
                $('#emptyStateTemuan' + idPertanyaan).hide();

                $.ajax({
                    url: "{{ route('5r-system.get-list-temuan') }}",
                    type: "GET",
                    data: {
                        id_pertanyaan: idPertanyaan,
                        id_periode: idPeriode
                    },
                    success: function(response) {
                        $('#loadingTemuan' + idPertanyaan).hide();

                        if (response.status === 'success' && response.data.length > 0) {
                            var tbody = $('#tableTemuanBody' + idPertanyaan);
                            tbody.empty();

                            $.each(response.data, function(index, temuan) {
                                var fotoHtml = '';

                                if (temuan.foto) {
                                    var fotoArray = temuan.foto.split(',');

                                    if (fotoArray.length > 1) {
                                        // Multiple photos dengan tombol hapus per foto
                                        fotoHtml =
                                            '<div style="display: flex; flex-wrap: wrap; gap: 8px;">';
                                        fotoArray.forEach(function(foto, idx) {
                                            foto = foto.trim();
                                            if (foto) {
                                                fotoHtml += `
                                        <div style="position: relative; border: 2px solid #e9ecef; border-radius: 8px; overflow: hidden;">
                                            <img src="{{ asset('images/5r/temuan/') }}/${foto}" 
                                                class="img-thumbnail" 
                                                style="width: 80px; height: 80px; object-fit: cover; cursor: pointer; display: block;"
                                                onclick="showImagePreview('{{ asset('images/5r/temuan/') }}/${foto}')"
                                                alt="Foto ${idx + 1}">
                                            <span style="position: absolute; top: 4px; left: 4px; background: rgba(0,0,0,0.7); color: white; font-size: 10px; padding: 2px 6px; border-radius: 3px; font-weight: 600;">
                                                ${idx + 1}
                                            </span>`;

                                                // Tombol hapus hanya muncul jika NOT read-only
                                                if (!isReadOnly) {
                                                    fotoHtml += `
                                            <button type="button" 
                                                class="btn btn-danger btn-sm btn-delete-single-photo"
                                                data-id-temuan="${temuan.id_temuan}"
                                                data-foto-name="${foto}"
                                                data-foto-index="${idx}"
                                                style="position: absolute; top: 4px; right: 4px; padding: 2px 6px; font-size: 10px; line-height: 1; border-radius: 3px;"
                                                title="Hapus foto ini">
                                                <i class="mdi mdi-close" style="font-size: 12px;"></i>
                                            </button>`;
                                                }

                                                fotoHtml += `</div>`;
                                            }
                                        });
                                        fotoHtml += '</div>';;
                                    } else {
                                        // Single photo
                                        var foto = fotoArray[0].trim();
                                        fotoHtml = `
                                <div style="position: relative; display: inline-block; border: 2px solid #e9ecef; border-radius: 8px; overflow: hidden;">
                                    <img src="{{ asset('images/5r/temuan/') }}/${foto}" 
                                        class="img-thumbnail" 
                                        style="max-width: 120px; max-height: 120px; cursor: pointer; display: block;"
                                        onclick="showImagePreview('{{ asset('images/5r/temuan/') }}/${foto}')"
                                        alt="Foto Temuan">`;

                                        if (!isReadOnly) {
                                            fotoHtml += `
                                    <button type="button" 
                                        class="btn btn-danger btn-sm btn-delete-single-photo"
                                        data-id-temuan="${temuan.id_temuan}"
                                        data-foto-name="${foto}"
                                        data-foto-index="0"
                                        style="position: absolute; top: 4px; right: 4px; padding: 4px 8px; font-size: 11px;"
                                        title="Hapus foto ini">
                                        <i class="mdi mdi-delete"></i>
                                    </button>`;
                                        }

                                        fotoHtml += `</div>`;
                                    }
                                } else {
                                    fotoHtml = '<span class="text-muted">Tidak ada foto</span>';
                                }

                                var deskripsi = temuan.deskripsi ||
                                    '<span class="text-muted">-</span>';
                                var tanggal = temuan.created_at ? new Date(temuan.created_at)
                                    .toLocaleDateString('id-ID', {
                                        day: '2-digit',
                                        month: 'short',
                                        year: 'numeric',
                                        hour: '2-digit',
                                        minute: '2-digit'
                                    }) : '-';

                                // Tombol hapus temuan
                                var deleteButton = '';
                                if (!isReadOnly) {
                                    deleteButton = `
                            <button type="button" 
                                class="btn btn-sm btn-danger btn-delete-temuan" 
                                data-id-temuan="${temuan.id_temuan}"
                                data-id-pertanyaan="${idPertanyaan}"
                                title="Hapus Temuan">
                                <i class="mdi mdi-delete"></i>
                            </button>`;
                                } else {
                                    deleteButton =
                                        '<span class="text-muted"><i class="mdi mdi-lock"></i></span>';
                                }

                                var row = `
                        <tr>
                            <td class="text-center">${index + 1}</td>
                            <td>${temuan.area?.nama_area || '-'}</td>
                            <td>${deskripsi}</td>
                            <td>${temuan.created_by || '-'}</td>
                            <td class="text-nowrap">${tanggal}</td>
                            <td>${fotoHtml}</td>
                            <td class="text-center">${deleteButton}</td>
                        </tr>
                    `;

                                tbody.append(row);
                            });

                            $('#tableTemuanContainer' + idPertanyaan).show();
                        } else {
                            $('#emptyStateTemuan' + idPertanyaan).show();
                        }
                    },
                    error: function(xhr) {
                        $('#loadingTemuan' + idPertanyaan).hide();
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Gagal memuat data temuan',
                        });
                    }
                });
            });

            $(document).on('click', '.btn-delete-single-photo', function() {
                var button = $(this);
                var idTemuan = button.data('id-temuan');
                var fotoName = button.data('foto-name');
                var fotoIndex = button.data('foto-index');

                Swal.fire({
                    title: 'Hapus Foto Ini?',
                    text: 'Foto akan dihapus dari temuan ini. Tindakan ini tidak dapat dibatalkan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus Foto!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        button.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i>');

                        // Send delete request
                        $.ajax({
                            url: "{{ route('5r-system.delete-single-photo') }}",
                            type: "POST",
                            dataType: "JSON",
                            data: {
                                id_temuan: idTemuan,
                                foto_name: fotoName,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.status === 'success') {
                                    // Animate removal
                                    button.closest('div[style*="position: relative"]').fadeOut(300,
                                        function() {
                                            $(this).remove();

                                            // Check if this was the last photo
                                            var photoContainer = button.closest('td').find(
                                                'div[style*="flex"]');
                                            var remainingPhotos = photoContainer.find('img')
                                                .length;

                                            if (remainingPhotos === 0) {
                                                // Update count or show message
                                                button.closest('td').html(
                                                    '<span class="text-muted">Semua foto telah dihapus</span>'
                                                );
                                            } else {
                                                // Update photo count
                                                var countElement = button.closest('td').find(
                                                    'small.text-muted');

                                                // Re-number remaining photos
                                                photoContainer.find(
                                                    'span[style*="position: absolute"][style*="left"]'
                                                ).each(function(idx) {
                                                    $(this).text(idx + 1);
                                                });
                                            }
                                        });

                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil',
                                        text: response.message,
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: response.message
                                    });
                                    button.prop('disabled', false).html(
                                        '<i class="mdi mdi-close"></i>');
                                }
                            },
                            error: function(xhr) {
                                console.error('Delete Photo Error:', xhr);

                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: xhr.responseJSON?.message ||
                                        'Terjadi kesalahan saat menghapus foto'
                                });

                                button.prop('disabled', false).html(
                                    '<i class="mdi mdi-close"></i>');
                            }
                        });
                    }
                });
            });

            $(document).on('click', '.btn-delete-temuan', function() {
                var button = $(this);
                var modal = button.closest('.modal');
                var listButton = $('[data-bs-target="#' + modal.attr('id') + '"]');
                var isReadOnly = listButton.data('read-only') === true || listButton.data('read-only') === 'true';

                // Double check - jika read-only, jangan izinkan delete
                if (isReadOnly) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Tidak Dapat Menghapus',
                        text: 'Penilaian sudah dikirim. Temuan tidak dapat dihapus.',
                    });
                    return;
                }

                var idTemuan = button.data('id-temuan');
                var idPertanyaan = button.data('id-pertanyaan');
                var row = button.closest('tr');

                Swal.fire({
                    title: 'Hapus Temuan?',
                    text: 'Apakah Anda yakin ingin menghapus temuan ini? Data yang dihapus tidak dapat dikembalikan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading state
                        button.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i>');

                        // Send delete request
                        $.ajax({
                            url: "{{ route('5r-system.delete-temuan') }}",
                            type: "DELETE",
                            dataType: "JSON",
                            data: {
                                id_temuan: idTemuan,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.status === 'success') {
                                    // Animate row removal
                                    row.fadeOut(300, function() {
                                        row.remove();

                                        // Re-number rows
                                        var tbody = $('#tableTemuanBody' + idPertanyaan);
                                        tbody.find('tr').each(function(index) {
                                            $(this).find('td:first').text(index +
                                                1);
                                        });

                                        // Check if table is empty
                                        if (tbody.find('tr').length === 0) {
                                            $('#tableTemuanContainer' + idPertanyaan)
                                                .hide();
                                            $('#emptyStateTemuan' + idPertanyaan).show();
                                        }
                                    });

                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil',
                                        text: response.message,
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: response.message
                                    });
                                    button.prop('disabled', false).html(
                                        '<i class="mdi mdi-delete"></i>');
                                }
                            },
                            error: function(xhr) {
                                console.error('Delete Error:', xhr);

                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: xhr.responseJSON?.message ||
                                        'Terjadi kesalahan saat menghapus temuan'
                                });

                                button.prop('disabled', false).html(
                                    '<i class="mdi mdi-delete"></i>');
                            }
                        });
                    }
                });
            });

            // Function to show image preview in larger modal
            function showImagePreview(imageUrl) {
                Swal.fire({
                    imageUrl: imageUrl,
                    imageAlt: 'Preview Foto Temuan',
                    showConfirmButton: false,
                    showCloseButton: true,
                    width: 'auto',
                    customClass: {
                        image: 'img-fluid'
                    }
                });
            }

            // Camera functionality
            let currentStream = null;
            let currentIdPertanyaan = null;
            let cameraVideo = null;
            let cameraCanvas = null;
            let cameraModalInstance = null;

            // Initialize camera elements when DOM is ready
            function initializeCameraElements() {
                cameraVideo = document.getElementById('cameraVideo');
                cameraCanvas = document.getElementById('cameraCanvas');

                const cameraModalElement = document.getElementById('cameraModal');
                if (cameraModalElement && !cameraModalInstance) {
                    cameraModalInstance = new bootstrap.Modal(cameraModalElement, {
                        backdrop: 'static',
                        keyboard: false
                    });
                }
            }

            // Open camera when button clicked
            $(document).on('click', '.btn-open-camera', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Stop existing stream first
                if (currentStream) {
                    stopCamera();
                }

                currentIdPertanyaan = $(this).data('id-pertanyaan');

                console.log('Opening camera for:', currentIdPertanyaan);

                // Initialize elements if not done yet
                if (!cameraVideo || !cameraCanvas || !cameraModalInstance) {
                    initializeCameraElements();
                }

                // Delay camera opening to ensure modal is ready
                setTimeout(() => {
                    openCamera();
                }, 100);
            });

            // Function to open camera
            async function openCamera() {
                try {
                    // Validate elements
                    if (!cameraVideo || !cameraCanvas) {
                        console.error('Camera elements not found');
                        initializeCameraElements();

                        if (!cameraVideo || !cameraCanvas) {
                            throw new Error('Camera elements tidak ditemukan di halaman');
                        }
                    }

                    // Reset UI
                    $('#cameraError').hide();
                    $('#cameraInfo').show();

                    console.log('Requesting camera access...');

                    // Request camera access
                    const constraints = {
                        video: {
                            facingMode: 'environment',
                            width: {
                                ideal: 1280
                            },
                            height: {
                                ideal: 720
                            }
                        }
                    };

                    currentStream = await navigator.mediaDevices.getUserMedia(constraints);

                    console.log('Camera access granted');

                    // Set video source
                    cameraVideo.srcObject = currentStream;

                    // Wait for video to be ready
                    await new Promise((resolve) => {
                        cameraVideo.onloadedmetadata = () => {
                            cameraVideo.play();
                            resolve();
                        };
                    });

                    console.log('Video stream ready');

                    // Show modal
                    if (cameraModalInstance) {
                        cameraModalInstance.show();
                    } else {
                        $('#cameraModal').modal('show');
                    }

                    console.log('Modal opened');

                } catch (error) {
                    console.error('Error accessing camera:', error);

                    // Stop any partial stream
                    stopCamera();

                    let errorMessage = 'Tidak dapat mengakses kamera. ';

                    if (error.name === 'NotAllowedError') {
                        errorMessage += 'Izin kamera ditolak. Silakan izinkan akses kamera di pengaturan browser.';
                    } else if (error.name === 'NotFoundError') {
                        errorMessage += 'Kamera tidak ditemukan pada perangkat ini.';
                    } else if (error.name === 'NotReadableError') {
                        errorMessage += 'Kamera sedang digunakan aplikasi lain.';
                    } else {
                        errorMessage += error.message;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Membuka Kamera',
                        text: errorMessage,
                        confirmButtonText: 'OK'
                    });
                }
            }

            // Function to stop camera
            function stopCamera() {
                console.log('Stopping camera...');

                if (currentStream) {
                    currentStream.getTracks().forEach(track => {
                        track.stop();
                        console.log('Track stopped:', track.kind);
                    });
                    currentStream = null;
                }

                if (cameraVideo) {
                    cameraVideo.srcObject = null;
                }

                console.log('Camera stopped');
            }

            // Capture photo when button clicked
            $(document).on('click', '#btnCapturePhoto', async function() {
                if (!currentStream || !currentIdPertanyaan) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Kamera tidak aktif atau ID pertanyaan tidak ditemukan',
                        timer: 1500
                    });
                    return;
                }

                try {
                    console.log('Capturing photo...');

                    // Validate elements
                    if (!cameraVideo || !cameraCanvas) {
                        throw new Error('Camera elements tidak ditemukan');
                    }

                    // Set canvas size sama dengan video
                    cameraCanvas.width = cameraVideo.videoWidth;
                    cameraCanvas.height = cameraVideo.videoHeight;

                    // Draw video frame to canvas
                    const context = cameraCanvas.getContext('2d');
                    context.drawImage(cameraVideo, 0, 0);

                    // Convert to blob
                    const blob = await new Promise(resolve => cameraCanvas.toBlob(resolve, 'image/jpeg', 0.8));

                    // Convert blob to base64
                    const reader = new FileReader();
                    reader.onloadend = function() {
                        const base64 = reader.result;

                        console.log('Photo captured, adding to IndexedDB...');

                        // Add to IndexedDB
                        addCapturedImageToIndexedDB(currentIdPertanyaan, base64);

                        // Close camera modal
                        if (cameraModalInstance) {
                            cameraModalInstance.hide();
                        } else {
                            $('#cameraModal').modal('hide');
                        }

                        stopCamera();

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Foto berhasil diambil',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    };
                    reader.readAsDataURL(blob);

                } catch (error) {
                    console.error('Error capturing photo:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Gagal mengambil foto: ' + error.message
                    });
                }
            });

            // Function to add captured image to IndexedDB
            function addCapturedImageToIndexedDB(idPertanyaan, base64Image) {
                const database = window.indexedDB.open("system_5r", 5);

                database.onsuccess = function(event) {
                    const db = event.target.result;
                    const transaction = db.transaction("penilaian", "readwrite");
                    const objectStore = transaction.objectStore("penilaian");

                    const request = objectStore.get(idPertanyaan);

                    request.onsuccess = function(event) {
                        const data = event.target.result;
                        let existingImages = data && data.images ? data.images : [];

                        // Add new image
                        existingImages.push(base64Image);

                        // Save to IndexedDB
                        if (data) {
                            data.images = existingImages;
                            objectStore.put(data);
                        } else {
                            objectStore.put({
                                id: idPertanyaan,
                                images: existingImages
                            });
                        }

                        console.log('Image added to IndexedDB, rendering preview...');

                        // Render preview
                        renderImages(idPertanyaan, existingImages);
                    };

                    request.onerror = function(event) {
                        console.error('Error saving to IndexedDB:', event.target.error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Gagal menyimpan foto ke browser',
                            timer: 1500
                        });
                    };
                };

                database.onerror = function(event) {
                    console.error('Error opening IndexedDB:', event.target.error);
                };
            }

            // Stop camera when modal is closed
            $('#cameraModal').on('hidden.bs.modal', function() {
                console.log('Modal closed, cleaning up...');
                stopCamera();
                currentIdPertanyaan = null;
            });

            // Initialize on document ready
            $(document).ready(function() {
                console.log('Initializing camera functionality...');
                initializeCameraElements();
            });
        </script>
    @endif
@endpush
