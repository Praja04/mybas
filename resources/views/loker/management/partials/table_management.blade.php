<style>
    /* CSS Tambahan khusus untuk Tabel Management */
    .bas-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .bas-table thead th {
        background: #F9FAFB;
        padding: 16px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: var(--bas-neutral);
        border-bottom: 1.5px solid var(--bas-border);
    }

    .bas-table tbody td {
        padding: 16px;
        vertical-align: middle;
        color: var(--bas-dark);
        border-bottom: 1px solid var(--bas-border);
        font-size: 14px;
    }

    .bas-table tbody tr:hover {
        background-color: #FFB00005;
        /* Fade orange tipis pas hover */
    }

    .bas-badge-outline {
        padding: 4px 12px;
        border-radius: 99px;
        font-size: 11px;
        font-weight: 700;
        border: 1.5px solid;
    }

    .badge-primary-bas {
        background: var(--bas-primary-light);
        color: var(--bas-primary-dark);
        border-color: rgba(245, 158, 11, 0.3);
    }

    .bas-btn-icon-danger {
        width: 35px;
        height: 35px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        color: var(--bas-danger);
        background: var(--bas-danger-light);
        border: none;
        transition: var(--bas-transition);
    }

    .bas-btn-icon-danger:hover {
        background: var(--bas-danger);
        color: white;
        transform: scale(1.1);
    }
</style>

<div id="wrapper-{{ $gender }}">

    <input type="hidden" id="data-total-count" value="{{ number_format($data->total()) }}">

    <div class="table-responsive">
        <table class="bas-table">
            <thead>
                <tr>
                    <th style="border-top-left-radius: 12px;">No. Loker</th>
                    <th>Kode Rak</th>
                    <th>Status</th>
                    <th class="text-right" style="border-top-right-radius: 12px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $l)
                <tr>
                    <td class="font-weight-bolder" style="font-size: 16px; color: var(--bas-dark);">
                        <i class="fas fa-door-closed mr-2 text-muted" style="font-size: 12px;"></i>
                        {{ $l->no_loker }}
                    </td>
                    <td>
                        <span class="bas-badge-outline badge-primary-bas">
                            {{ $l->kode_rak == 'LP' ? 'P' : 'W' }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <span class="bas-dot mr-2" style="background: var(--bas-success);"></span>
                            <span class="font-weight-bold"
                                style="color: var(--bas-success); font-size: 13px;">Tersedia</span>
                        </div>
                    </td>
                    <td class="text-right">
                        <button type="button" class="bas-btn-icon-danger" data-toggle="tooltip" title="Hapus Loker"
                            onclick="hapusLoker('{{ $l->id }}', '{{ $l->no_loker }}')">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-20">
                        <div class="bas-empty-icon mb-4">
                            <i class="fas fa-box-open"></i>
                        </div>
                        <p class="bas-empty-text font-weight-bold">Tidak ada unit loker kosong yang ditemukan.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-between align-items-center flex-wrap mt-7 px-2">
        <div class="text-muted font-weight-bold font-size-sm">
            Menampilkan {{ $data->firstItem() ?? 0 }} sampai {{ $data->lastItem() ?? 0 }} dari {{ $data->total() }} unit
        </div>
        <div class="bas-pagination">
            {{ $data->links() }}
        </div>
    </div>
</div>
