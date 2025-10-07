@extends('layouts.base')

@section('content')
    <style>
        .form-control+.form-control {
            margin-top: 1em;
        }

        .form-control:focus-within {
            color: var(--form-control-color);
        }

        input[type="radio"] {
            -webkit-appearance: none;
            appearance: none;
            background-color: var(--form-background);
            cursor: pointer;
            font: inherit;
            color: currentColor;
            width: 1.15em;
            height: 1.15em;
            border: 0.15em solid currentColor;
            border-radius: 50%;
            transform: translateY(-0.075em);

            display: grid;
            place-content: center;
        }

        input[type="radio"]::before {
            content: "";
            width: 0.65em;
            height: 0.65em;
            border-radius: 50%;
            transform: scale(0);
            transition: 120ms transform ease-in-out;
            box-shadow: inset 1em 1em var(--form-control-color);
            background-color: CanvasText;
        }

        input[type="radio"]:checked::before {
            transform: scale(1);
        }

        input[type="radio"]:focus {
            outline: max(2px, 0.15em) solid currentColor;
            outline-offset: max(2px, 0.15em);
        }

        @media only screen and (max-width: 600px) {

            .kategori-c-1 {
                padding-bottom: 70px;
            }

            .kategori-c-2 {
                padding-bottom: 70px;
            }

            .kategori-c-3 {
                padding-bottom: 70px;
            }

            .kategori-c-4 {}
        }
    </style>
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <h3 style="margin-bottom: 13px">Data Catering</h3>
                        <div class="card border">
                            <div class="card-body">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <th>ID Transaksi</th>
                                            <td>{{ $data->id_transaksi }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tanggal</th>
                                            <td>{{ $data->tanggal }}</td>
                                        </tr>
                                        <tr>
                                            <th>Catering</th>
                                            <td>{{ $data->catering }}</td>
                                        </tr>
                                        <tr>
                                            <th>PIC Pengecekan Kendaraan</th>
                                            <td>{{ $data->nama_petugas_security }}</td>
                                        </tr>
                                        <tr>
                                            <th>Shift</th>
                                            <td>{{ $data->shift }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status Cek Kendaraan</th>
                                            <td>
                                                @if ($data->status_cek_kendaraan === 'sudah')
                                                    <span class="badge text-bg-success"
                                                        style="background-color: #00a816; color: white;">Sudah</span>
                                                @elseif ($data->status_cek_kendaraan === 'menunggu approval')
                                                    <span class="badge text-bg-warning"
                                                        style="background-color: #d1d101; color: rgb(255, 255, 255);">Menunggu
                                                        Approval</span>
                                                @else
                                                    <span class="badge text-bg-danger"
                                                        style="background-color: #a80000; color: white;">Belum</span>
                                                @endif
                                            </td>
                                        </tr>
                                        {{-- <tr>
                                            <th>Status Cek Kendaraan</th>
                                            <td>
                                                @if ($data->status_cek_kedatangan === 'sudah')
                                                    <span class="badge text-bg-success"
                                                        style="background-color: #00a816; color: white;">Sudah</span>
                                                @else
                                                    <span class="badge text-bg-danger"
                                                        style="background-color: #a80000; color: white;">Belum</span>
                                                @endif
                                            </td>
                                        </tr> --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        {{-- <button onClick="showModalCreateCatering()" type="button"
                            class="btn btn-primary waves-effect btn-block w-100" data-bs-toggle="modal"
                            data-bs-target="#modalCreateGroup">
                            <i class="mdi mdi-plus"></i>
                            Tambah Catering
                        </button> --}}
                    </div>
                    <div class="col-md-9">
                        <form method="POST" action="">
                            @csrf
                            <input type="hidden" name="id_transaksi" value="{{ $data->id_transaksi }}">
                            <table class="table table-hover table-striped" id="table-group" style="width:100%">
                                <thead>
                                    <tr style="background-color: #008ca8; color: #fff">
                                        <th>Group Pertanyaan</th>
                                        <th>Kondisi</th>
                                        <th class="col-md-3">Penilaian</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <h4>A. Pengecekan Fisik dalam Mobil (Baik/Rusak)</h4>
                                        </td>
                                        <td>
                                            <ol style="margin: 0; padding: 0;">
                                                <li>Dinding sebelah kanan</li>
                                                <li>Dinding sebelah kiri</li>
                                                <li>Kondisi atap</li>
                                                <li>Kondisi lantai</li>
                                            </ol>
                                        </td>
                                        <td>
                                            <ol style="list-style-type: none; margin: 0; padding: 0;">
                                                <li>
                                                    <fieldset id="dindingkanan" style="display: flex; gap: 20px;">
                                                        <div style="margin-right: 20px;">
                                                            <label for="Baik">Baik</label>
                                                        </div>
                                                        <div>
                                                            <input required class="form-check-input" type="radio"
                                                                @if ($jumlahpesanan) disabled @endif
                                                                value="1" name="fisik_dinding_sebelah_kanan"
                                                                {{ $jumlahpesanan && $jumlahpesanan->fisik_dinding_sebelah_kanan == 1 ? 'checked' : '' }}>
                                                        </div>
                                                        <div style="margin-right: 20px;">
                                                            <label for="Tidak Baik">Tidak Baik</label>
                                                        </div>
                                                        <div>
                                                            <input required class="form-check-input" type="radio"
                                                                @if ($jumlahpesanan) disabled @endif
                                                                value="0" name="fisik_dinding_sebelah_kanan"
                                                                {{ $jumlahpesanan && $jumlahpesanan->fisik_dinding_sebelah_kanan == 0 ? 'checked' : '' }}>
                                                        </div>
                                                    </fieldset>
                                                </li>
                                                <li>
                                                    <fieldset id="dindingkiri" style="display: flex; gap: 20px;">
                                                        <div style="margin-right: 20px;">
                                                            <label for="Baik">Baik</label>
                                                        </div>
                                                        <div>
                                                            <input required class="form-check-input" type="radio"
                                                                @if ($jumlahpesanan) disabled @endif
                                                                value="1" name="fisik_dinding_sebelah_kiri"
                                                                {{ $jumlahpesanan && $jumlahpesanan->fisik_dinding_sebelah_kiri == 1 ? 'checked' : '' }}>
                                                        </div>
                                                        <div style="margin-right: 20px;">
                                                            <label for="Tidak Baik">Tidak Baik</label>
                                                        </div>
                                                        <div>
                                                            <input required class="form-check-input" type="radio"
                                                                @if ($jumlahpesanan) disabled @endif
                                                                value="0" name="fisik_dinding_sebelah_kiri"
                                                                {{ $jumlahpesanan && $jumlahpesanan->fisik_dinding_sebelah_kiri == 0 ? 'checked' : '' }}>
                                                        </div>
                                                    </fieldset>
                                                </li>
                                                <li>
                                                    <fieldset id="kondisi-atap" style="display: flex; gap: 20px;">
                                                        <div style="margin-right: 20px;">
                                                            <label for="Baik">Baik</label>
                                                        </div>
                                                        <div>
                                                            <input required class="form-check-input" type="radio"
                                                                @if ($jumlahpesanan) disabled @endif
                                                                value="1" name="fisik_kondisi_atap"
                                                                {{ $jumlahpesanan && $jumlahpesanan->fisik_kondisi_atap == 1 ? 'checked' : '' }}>
                                                        </div>
                                                        <div style="margin-right: 20px;">
                                                            <label for="Tidak Baik">Tidak Baik</label>
                                                        </div>
                                                        <div>
                                                            <input required class="form-check-input" type="radio"
                                                                @if ($jumlahpesanan) disabled @endif
                                                                value="0" name="fisik_kondisi_atap"
                                                                {{ $jumlahpesanan && $jumlahpesanan->fisik_kondisi_atap == 0 ? 'checked' : '' }}>
                                                        </div>
                                                    </fieldset>
                                                </li>
                                                <li>
                                                    <fieldset id="kondisi-lantai" style="display: flex; gap: 20px;">
                                                        <div style="margin-right: 20px;">
                                                            <label for="Baik">Baik</label>
                                                        </div>
                                                        <div>
                                                            <input required class="form-check-input" type="radio"
                                                                @if ($jumlahpesanan) disabled @endif
                                                                value="1" name="fisik_kondisi_lantai"
                                                                {{ $jumlahpesanan && $jumlahpesanan->fisik_kondisi_lantai == 1 ? 'checked' : '' }}>
                                                        </div>
                                                        <div style="margin-right: 20px;">
                                                            <label for="Tidak Baik">Tidak Baik</label>
                                                        </div>
                                                        <div>
                                                            <input required class="form-check-input" type="radio"
                                                                @if ($jumlahpesanan) disabled @endif
                                                                value="0" name="fisik_kondisi_lantai"
                                                                {{ $jumlahpesanan && $jumlahpesanan->fisik_kondisi_lantai == 0 ? 'checked' : '' }}>
                                                        </div>
                                                    </fieldset>
                                                </li>
                                            </ol>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <h4>B. Pengecekan Kebersihan dalam Mobil</h4>
                                        </td>
                                        <td>
                                            <ol style="margin: 0; padding: 0;">
                                                <li>Dinding sebelah kanan</li>
                                                <li>Dinding sebelah kiri</li>
                                                <li>Kondisi atap</li>
                                                <li>Kondisi lantai</li>
                                            </ol>
                                        </td>
                                        <td>
                                            <ol style="list-style-type: none; margin: 0; padding: 0;">
                                                <li>
                                                    <fieldset id="dindingkanan" style="display: flex; gap: 20px;">
                                                        <div style="margin-right: 20px;">
                                                            <label for="Baik">Baik</label>
                                                        </div>
                                                        <div>
                                                            <input required class="form-check-input" type="radio"
                                                                @if ($jumlahpesanan) disabled @endif
                                                                value="1" name="kebersihan_dinding_sebelah_kanan"
                                                                {{ $jumlahpesanan && $jumlahpesanan->kebersihan_dinding_sebelah_kanan == 1 ? 'checked' : '' }}>
                                                        </div>
                                                        <div style="margin-right: 20px;">
                                                            <label for="0">Tidak Baik</label>
                                                        </div>
                                                        <div>
                                                            <input required class="form-check-input" type="radio"
                                                                @if ($jumlahpesanan) disabled @endif
                                                                value="0" name="kebersihan_dinding_sebelah_kanan"
                                                                {{ $jumlahpesanan && $jumlahpesanan->kebersihan_dinding_sebelah_kanan == 0 ? 'checked' : '' }}>
                                                        </div>
                                                    </fieldset>
                                                </li>
                                                <li>
                                                    <fieldset id="dinding-kiri" style="display: flex; gap: 20px;">
                                                        <div style="margin-right: 20px;">
                                                            <label for="Baik">Baik</label>
                                                        </div>
                                                        <div>
                                                            <input required class="form-check-input" type="radio"
                                                                @if ($jumlahpesanan) disabled @endif
                                                                value="1" name="kebersihan_dinding_sebelah_kiri"
                                                                {{ $jumlahpesanan && $jumlahpesanan->kebersihan_dinding_sebelah_kiri == 1 ? 'checked' : '' }}>
                                                        </div>
                                                        <div style="margin-right: 20px;">
                                                            <label for="Tidak Baik">Tidak Baik</label>
                                                        </div>
                                                        <div>
                                                            <input required class="form-check-input" type="radio"
                                                                @if ($jumlahpesanan) disabled @endif
                                                                value="0" name="kebersihan_dinding_sebelah_kiri"
                                                                {{ $jumlahpesanan && $jumlahpesanan->kebersihan_dinding_sebelah_kiri == 0 ? 'checked' : '' }}>
                                                        </div>
                                                    </fieldset>
                                                </li>
                                                <li>
                                                    <fieldset id="kondisi-atap" style="display: flex; gap: 20px;">
                                                        <div style="margin-right: 20px;">
                                                            <label for="Baik">Baik</label>
                                                        </div>
                                                        <div>
                                                            <input required class="form-check-input" type="radio"
                                                                @if ($jumlahpesanan) disabled @endif
                                                                value="1" name="kebersihan_kondisi_atap"
                                                                {{ $jumlahpesanan && $jumlahpesanan->kebersihan_kondisi_atap == 1 ? 'checked' : '' }}>
                                                        </div>
                                                        <div style="margin-right: 20px;">
                                                            <label for="Tidak Baik">Tidak Baik</label>
                                                        </div>
                                                        <div>
                                                            <input required class="form-check-input" type="radio"
                                                                @if ($jumlahpesanan) disabled @endif
                                                                value="0" name="kebersihan_kondisi_atap"
                                                                {{ $jumlahpesanan && $jumlahpesanan->kebersihan_kondisi_atap == 0 ? 'checked' : '' }}>
                                                        </div>
                                                    </fieldset>
                                                </li>
                                                <li>
                                                    <fieldset id="kondisi-lantai" style="display: flex; gap: 20px;">
                                                        <div style="margin-right: 20px;">
                                                            <label for="Baik">Baik</label>
                                                        </div>
                                                        <div>
                                                            <input required class="form-check-input" type="radio"
                                                                @if ($jumlahpesanan) disabled @endif
                                                                value="1" name="kebersihan_kondisi_lantai"
                                                                {{ $jumlahpesanan && $jumlahpesanan->kebersihan_kondisi_lantai == 1 ? 'checked' : '' }}>
                                                        </div>
                                                        <div style="margin-right: 20px;">
                                                            <label for="Tidak Baik">Tidak Baik</label>
                                                        </div>
                                                        <div>
                                                            <input required class="form-check-input" type="radio"
                                                                @if ($jumlahpesanan) disabled @endif
                                                                value="0" name="kebersihan_kondisi_lantai"
                                                                {{ $jumlahpesanan && $jumlahpesanan->kebersihan_kondisi_lantai == 0 ? 'checked' : '' }}>
                                                        </div>
                                                    </fieldset>
                                                </li>
                                            </ol>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <h4>C. Pengecekan Fungsi Pengangkutan</h4>
                                        </td>
                                        <td>
                                            <ol style="margin: 0; padding: 0;">
                                                <li>Ditemukan barang lain diluar kebutuhan catering</li>
                                                <li>Saat penerimaan pintu keadaan tertutup</li>
                                                <li>Saat penerimaan pintu keadaan terkunci</li>
                                                <li>Box Makanan dalam keadaan tertutup</li>
                                            </ol>
                                        </td>
                                        <td>
                                            <ol style="list-style-type: none; margin: 0; padding: 0;">
                                                <li class="kategori-c-1">
                                                    <fieldset id="barang-diluar-kebutuhan"
                                                        style="display: flex; gap: 40px;">
                                                        <div>
                                                            <label for="Baik">Ya</label>
                                                        </div>
                                                        <div>
                                                            <input required class="form-check-input" type="radio"
                                                                @if ($jumlahpesanan) disabled @endif
                                                                value="1"
                                                                name="ditemukan_barang_lain_diluar_kebutuhan_catering"
                                                                {{ $jumlahpesanan && $jumlahpesanan->ditemukan_barang_lain_diluar_kebutuhan_catering == 1 ? 'checked' : '' }}>
                                                        </div>
                                                        <div>
                                                            <label for="Tidak Baik">Tidak</label>
                                                        </div>
                                                        <div>
                                                            <input required class="form-check-input" type="radio"
                                                                @if ($jumlahpesanan) disabled @endif
                                                                value="0"
                                                                name="ditemukan_barang_lain_diluar_kebutuhan_catering"
                                                                {{ $jumlahpesanan && $jumlahpesanan->ditemukan_barang_lain_diluar_kebutuhan_catering == 0 ? 'checked' : '' }}>
                                                        </div>
                                                    </fieldset>
                                                </li>
                                                <li class="kategori-c-2">
                                                    <fieldset id="pintu-keadaan-tertutup"
                                                        style="display: flex; gap: 40px;">
                                                        <div>
                                                            <label for="Baik">Ya</label>
                                                        </div>
                                                        <div>
                                                            <input required class="form-check-input" type="radio"
                                                                @if ($jumlahpesanan) disabled @endif
                                                                value="1"
                                                                name="saat_penerimaan_pintu_keadaan_tertutup"
                                                                {{ $jumlahpesanan && $jumlahpesanan->saat_penerimaan_pintu_keadaan_tertutup == 1 ? 'checked' : '' }}>
                                                        </div>
                                                        <div>
                                                            <label for="Tidak Baik">Tidak</label>
                                                        </div>
                                                        <div>
                                                            <input required class="form-check-input" type="radio"
                                                                @if ($jumlahpesanan) disabled @endif
                                                                value="0"
                                                                name="saat_penerimaan_pintu_keadaan_tertutup"
                                                                {{ $jumlahpesanan && $jumlahpesanan->saat_penerimaan_pintu_keadaan_tertutup == 0 ? 'checked' : '' }}>
                                                        </div>
                                                    </fieldset>
                                                </li>
                                                <li class="kategori-c-3">
                                                    <fieldset id="pintu-keadaan-terkunci"
                                                        style="display: flex; gap: 40px;">
                                                        <div>
                                                            <label for="Baik">Ya</label>
                                                        </div>
                                                        <div>
                                                            <input required class="form-check-input" type="radio"
                                                                @if ($jumlahpesanan) disabled @endif
                                                                value="1"
                                                                name="saat_penerimaan_pintu_keadaan_terkunci"
                                                                {{ $jumlahpesanan && $jumlahpesanan->saat_penerimaan_pintu_keadaan_terkunci == 1 ? 'checked' : '' }}>
                                                        </div>
                                                        <div>
                                                            <label for="Tidak Baik">Tidak</label>
                                                        </div>
                                                        <div>
                                                            <input required class="form-check-input" type="radio"
                                                                @if ($jumlahpesanan) disabled @endif
                                                                value="0"
                                                                name="saat_penerimaan_pintu_keadaan_terkunci"
                                                                {{ $jumlahpesanan && $jumlahpesanan->saat_penerimaan_pintu_keadaan_terkunci == 0 ? 'checked' : '' }}>
                                                        </div>
                                                    </fieldset>
                                                </li>
                                                <li class="kategori-c-4">
                                                    <fieldset id="makanan-keadaan-tertutup"
                                                        style="display: flex; gap: 40px;">
                                                        <div>
                                                            <label for="ya">Ya</label>
                                                        </div>
                                                        <div>
                                                            <input required class="form-check-input" type="radio"
                                                                @if ($jumlahpesanan) disabled @endif
                                                                value="1" name="box_makanan_dalam_keadaan_tertutup"
                                                                {{ $jumlahpesanan && $jumlahpesanan->box_makanan_dalam_keadaan_tertutup == 1 ? 'checked' : '' }}>
                                                        </div>
                                                        <div>
                                                            <label for="tidak">Tidak</label>
                                                        </div>
                                                        <div>
                                                            <input required class="form-check-input" type="radio"
                                                                @if ($jumlahpesanan) disabled @endif
                                                                value="0" name="box_makanan_dalam_keadaan_tertutup"
                                                                {{ $jumlahpesanan && $jumlahpesanan->box_makanan_dalam_keadaan_tertutup == 0 ? 'checked' : '' }}>
                                                        </div>
                                                    </fieldset>
                                                </li>
                                            </ol>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="col-9 button-group pt-4" style="display: flex; gap: 20px;">
                                <a href="{{ url('/reporting/approve-pengecekan-kendaraan/' . $data->id_transaksi) }}"
                                    class="btn btn-info btn-lg" id="SubmitPengecekanKendaraan"
                                    onclick="return confirm('Apakah Anda Yakin Akan Approve Data Ini?');">
                                    Approve Pengecekan Kendaraan <i class="fas fa-check ml-4"></i>
                                </a>
                                <a id="dataApproveKendaraan" class="btn btn-success btn-lg" style="display: none;">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span>{{ $data->approval_cek_kendaraan_at }}</span>
                                    <i class="fas fa-user pl-4"></i>
                                    <span>{{ $data->approval_cek_kendaraan_by }}</span>
                                </a>
                                <a href="/cateringbas/reporting-GA/" style="" id="kembalikehome"
                                    class="btn btn-danger">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var statusCekKendaraan = "{{ $data->approval_cek_kendaraan }}";
            console.log(statusCekKendaraan);

            if (statusCekKendaraan === 'Y') {
                document.getElementById("SubmitPengecekanKendaraan").style.display = "none";
                document.getElementById("dataApproveKendaraan").style.display = "inline-block";
            }
        });

        $(document).ready(function() {
            function showConfirmationModal() {
                Swal.fire({
                    icon: 'info',
                    title: 'Hanya Reminder',
                    text: 'Pastikan Pengecekan Kendaran Sudah Sesuai.',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Tutup'
                });
            }
            showConfirmationModal();
        });
    </script>
@endpush
