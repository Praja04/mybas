@extends('pos-security.layouts.base')

@section('content')
    {{-- TABLE --}}
    <div class="container-fluid">
        {{-- @include('pos-security.history-cek-kendaraan.components.modal-detail') --}}

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Data Security PT BAS</h5>
                        </div>
                        <div>
                            <button class="btn btn-primary btn-sm" onclick="openCreateSecurityModal()">
                                <i class="mdi mdi-plus"></i> Tambah Security
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="ga-data-security-datatables table nowrap align-middle" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>NIK</th>
                                        <th>Nama Security</th>
                                        <th>Nomor Kartu</th>
                                        <th>Foto</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- CREATE MODAL --}}
    <div class="modal fade" id="modalCreateSecurity" tabindex="-1">
        <div class="modal-dialog modal-md">
            <form id="formCreateSecurity">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Data Security</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label>NIK <span class="text-danger">*</span></label>
                            <input type="text" name="nik" class="form-control" required placeholder="Masukkan NIK">
                        </div>

                        <div class="mb-3">
                            <label>Nama Security <span class="text-danger">*</span></label>
                            <input type="text" name="nama_security" class="form-control" required
                                placeholder="Masukkan nama security">
                        </div>

                        <div class="mb-3">
                            <label>Nomor Kartu <span class="text-danger">*</span></label>
                            <input type="text" name="nomor_kartu" class="form-control" required
                                placeholder="Masukkan nomor kartu">
                        </div>

                        <div class="mb-3">
                            <label>Foto (Max. 1MB)</label>
                            <input type="file" name="foto" class="form-control" accept="image/*">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Simpan</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- UPDATE MODAL --}}
    <div class="modal fade" id="modalEditSecurity" tabindex="-1">
        <div class="modal-dialog modal-md">
            <form id="formEditSecurity">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="edit_id">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Data Security</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label>NIK</label>
                            <input type="text" name="nik" id="edit_nik" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Nama Security</label>
                            <input type="text" name="nama_security" id="edit_nama_security" class="form-control"
                                required>
                        </div>

                        <div class="mb-3">
                            <label>Nomor Kartu</label>
                            <input type="text" name="nomor_kartu" id="edit_nomor_kartu" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Foto (Max. 1MB)</label>
                            <input type="file" name="foto" class="form-control" accept="image/*">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Update</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- PREVIEW IMG MODAL --}}
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Preview Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" alt="Full Image"
                        style="max-width: 100%; max-height: 80vh; border-radius: 8px;" />
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="module" src="{{ asset('assets/js/pos-security/master/security/data-security-table.js') }}"></script>
    <script src="{{ asset('assets/js/pos-security/master/security/data-security.js') }}"></script>

    {{-- <script src="{{ asset('assets/js/pos-security/history/pages/history-cek-kendaraan-modal.js') }}"></script> --}}
@endpush
