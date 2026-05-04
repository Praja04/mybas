<div class="modal fade" id="modalPermission" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" id="modalTitle">Tambah Permission</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <form id="formPermission">
                @csrf
                <input type="hidden" id="permission_id" name="id">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Nama Permission <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="p_name"
                            placeholder="Masukkan nama permission..." required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Codename (Slug) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="codename" id="p_codename"
                            placeholder="Otomatis terisi dari nama..." required>
                        <small class="form-text text-muted">Gunakan huruf kecil dan underscore</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold"
                        data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary font-weight-bold">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
