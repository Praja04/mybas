{{-- Modal View User Permission --}}
<div class="modal fade" id="modal-view-permission" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--bas-radius-lg);">
            <div class="modal-header border-0 pt-8 px-8 bg-white">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-45 symbol-light-info mr-4">
                        <span class="symbol-label">
                            <i class="fas fa-eye text-info"></i>
                        </span>
                    </div>
                    <div>
                        <h5 class="modal-title font-weight-bolder text-dark" style="font-size: 1.3rem;"
                            id="title-group-name">Detail Hak Akses
                            Group</h5>
                        <p class="text-muted mb-0">Daftar permission yang dimiliki oleh group ini (Read-Only)</p>
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>

            <div class="modal-body px-8 py-6">
                <ul class="auth-permissions">
                    {{-- AJAX CONTENT --}}
                </ul>
            </div>

            <div class="modal-footer border-0 py-4 px-8 bg-white shadow-sm">
                <button type="button" class="btn btn-light-primary font-weight-bold px-8"
                    data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>