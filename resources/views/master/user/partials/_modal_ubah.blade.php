{{-- Modal Edit Permission via User Table --}}
<div class="modal fade" id="modal-view-permission" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--bas-radius-lg);">
            <form id="change-permission-form" method="POST" class="d-flex flex-column w-100 h-100">
                @csrf
                <input type="hidden" name="group_id" id="hidden_group_id">

                <div class="modal-header border-0 pt-8 px-8 bg-white">
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-45 symbol-light-info mr-4">
                            <span class="symbol-label">
                                <i class="fas fa-user-shield text-info"></i>
                            </span>
                        </div>
                        <div>
                            <h5 class="modal-title font-weight-bolder text-dark" style="font-size: 1.3rem;"
                                id="title-group-name">
                                Mapping Permissions
                            </h5>
                            <p class="text-muted mb-0">Atur hak akses spesifik untuk grup pengguna ini</p>
                        </div>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>

                <div class="modal-body px-8 py-6">
                    {{-- Style ditambahkan biar grid-nya sejajar rapi --}}
                    <ul class="auth-permissions"
                        style="list-style: none; padding: 0; display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 15px;">
                        {{-- AJAX CONTENT --}}
                    </ul>
                </div>

                <div class="modal-footer border-0 py-6 px-8 bg-white shadow-sm">
                    <div class="d-flex justify-content-between w-100 align-items-center">
                        <div class="bg-light-info px-4 py-2 rounded-sm">
                            <span class="text-muted font-size-sm font-weight-bold">
                                <i class="fa fa-info-circle text-info mr-2"></i> Perubahan akan berdampak pada seluruh
                                karyawan di grup ini.
                            </span>
                        </div>
                        <div>
                            <button type="button" class="btn btn-light-danger font-weight-bold mr-3 px-8"
                                data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary font-weight-bold px-12 h-45px shadow-sm">
                                <i class="fas fa-save mr-2"></i> SIMPAN PERUBAHAN
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
