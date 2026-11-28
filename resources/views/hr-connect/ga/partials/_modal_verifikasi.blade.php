@push('styles')
    <style>
        #modalVerifikasi .modal-content {
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1) !important;
            border: 1px solid #ebedf3;
        }

        #modalVerifikasi .modal-header {
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
        }

        #modalVerifikasi .modal-footer {
            border-bottom-left-radius: 20px;
            border-bottom-right-radius: 20px;
            background-color: #f8f9fa;
        }

        .input-group-text {
            padding: 1rem;
        }

        .verif-scanner-box {
            border: 2px dashed #3699ff;
            background-color: #f3f6f9;
            border-radius: 16px;
            padding: 1rem;
            transition: all 0.3s ease;
        }

        .verif-scanner-box .input-group {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.04);
        }

        #verif_rfid_scan {
            font-size: 1.4rem !important;
            letter-spacing: 3px;
        }

        #verif_rfid_scan:focus {
            box-shadow: none;
            outline: none;
            background-color: #ffffff !important;
        }

        .verif-preview-card {
            border-radius: 16px;
            border: 1px solid #e4e6ef;
            padding: 1.5rem;
            background-color: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
        }

        @media (max-width: 768px) {
            .verif-scanner-box .input-group {
                max-width: 100% !important;
            }

            .modal-body {
                padding: 1.5rem !important;
            }
        }
    </style>
@endpush

<div class="modal fade" id="modalVerifikasi" tabindex="-1" role="dialog" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0">

            <div class="modal-header bg-white border-0 pt-6 px-8 pb-2">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-50 symbol-light-info mr-4">
                        <span class="symbol-label bg-light-info rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 50px; height: 50px;">
                            <i class="ri-rfid-line text-info" style="font-size: 1.8rem;"></i>
                        </span>
                    </div>
                    <div class="mt-2">
                        <h4 class="font-weight-bolder text-dark mb-1">Verifikasi ID Card & Alokasi Loker</h4>
                        <p class="text-muted font-weight-bold mb-0" style="font-size: 0.9rem;">
                            Tahap Akhir: Silakan pindai (tap) kartu identitas karyawan untuk memvalidasi penempatan.
                        </p>
                    </div>
                </div>
                <button type="button" class="close btn btn-xs btn-icon btn-light btn-hover-danger"
                    data-bs-dismiss="modal" aria-label="Close" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Tutup Jendela">
                    <i class="ri-close-fill text-danger fw-bold" style="font-size: 1.8rem;"></i>
                </button>
            </div>

            <div class="modal-body px-8 py-6">

                <div class="row justify-content-center" id="verif_scan_area">
                    <div class="col-md-11 text-center">
                        <div class="verif-scanner-box mx-auto">
                            <label class="font-weight-bolder text-info mb-3 d-block" style="letter-spacing: 1px;">
                                AREA PEMINDAIAN ID CARD
                            </label>

                            <div class="input-group input-group-lg mx-auto" style="max-width: 85%;">
                                <div class="input-group-prepend m-auto">
                                    <span class="input-group-text bg-white border-info border-right-0">
                                        <i class="ri-barcode-box-line text-info fs-3"></i>
                                    </span>
                                </div>
                                <input type="text" id="verif_rfid_scan"
                                    class="form-control border-info border-left-0 bg-light-info font-weight-bolder text-center text-dark"
                                    placeholder="Silakan tap kartu ke mesin scanner..." autocomplete="off" autofocus>
                            </div>

                            <div class="mt-4">
                                <span id="verif_status_text"
                                    class="badge badge-light-secondary font-weight-bold px-4 py-2"
                                    style="font-size: 0.9rem;">
                                    <span class="spinner-grow spinner-grow-sm text-secondary mr-2" role="status"
                                        aria-hidden="true"></span>
                                    Sistem siap menerima pindaian kartu...
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="verif_detail_container" style="display: none; margin-top: 1.5rem;">
                    <div class="verif-preview-card">
                        <div class="row align-items-center">

                            <div class="col-md-8 border-right pr-md-6">
                                <h6 class="font-weight-bolder text-dark border-bottom pb-3 mb-4">Detail Pemegang Kartu</h6>

                                <div class="row mb-3">
                                    <div class="col-6">
                                        <span class="text-muted font-weight-bold d-block mb-1"
                                            style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px;">NIK
                                        </span>
                                        <input type="text" id="verif_target_nik"
                                            class="form-control-plaintext font-weight-bolder text-dark p-0" readonly
                                            value="-">
                                    </div>
                                    <div class="col-6">
                                        <span class="text-muted font-weight-bold d-block mb-1"
                                            style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px;">Kategori
                                        </span>
                                        <input type="text" id="verif_kategori"
                                            class="form-control-plaintext font-weight-bolder text-dark p-0 text-uppercase"
                                            readonly value="-">
                                    </div>
                                </div>

                                <div class="row mb-5">
                                    <div class="col-12">
                                        <span class="text-muted font-weight-bold d-block mb-1"
                                            style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px;">Nama Lengkap
                                        </span>
                                        <input type="text" id="verif_nama"
                                            class="form-control-plaintext font-weight-bolder text-dark p-0" readonly
                                            value="-">
                                    </div>
                                </div>

                                <div id="wrapper_verif_loker" class="bg-light-info rounded p-3 border border-info border-left-info"
                                    style="border-left-width: 4px !important;">
                                    <span class="text-info font-weight-bold d-block mb-1"
                                        style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px;">
                                        Loker Alokasi Tujuan
                                    </span>
                                    <div class="d-flex align-items-center">
                                        <i class="ri-door-lock-box-line text-info mr-3" style="font-size: 1rem;"></i>
                                        <input type="text" id="verif_loker_tujuan"
                                            class="form-control-plaintext font-weight-bolder text-info p-0 ms-2" readonly
                                            style="font-size: 1rem;" value="-">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 text-center d-flex flex-column justify-content-center align-items-center pt-4 pt-md-0">
                                <span class="text-muted font-weight-bold mb-3"
                                    style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px;">Preview Foto
                                </span>

                                <div class="symbol shadow-sm bg-white p-1"
                                    style="border-radius: 12px; width: 150px; height: 180px; border: 1px solid #e4e6ef;">
                                    <img id="verif_foto_img" src="{{ asset('assets/media/users/default.jpg') }}"
                                        alt="Foto Karyawan"
                                        style="border-radius: 8px; width: 100%; height: 100%; object-fit: cover;">
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

            <input type="hidden" id="verif_hidden_idcard">
            <input type="hidden" id="verif_hidden_lokerid">
            <input type="hidden" id="verif_hidden_koderak">
            <input type="hidden" id="verif_hidden_noloker">
            <input type="hidden" id="verif_hidden_divisi">
            <input type="hidden" id="verif_hidden_jk">

            <div class="modal-footer border-0 py-4 px-8 justify-content-between w-100" id="verif_footer"
                style="display: none;">
                <button type="button" id="btnSimpanVerifikasi"
                    class="btn btn-info font-weight-bolder px-8 shadow-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Simpan data dan selesaikan verifikasi" disabled>
                    <i class="ri-check-double-line mr-1"></i> Konfirmasi & Simpan
                </button>
            </div>

        </div>
    </div>
</div>
