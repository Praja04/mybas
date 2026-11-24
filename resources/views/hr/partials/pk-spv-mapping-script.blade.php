<script>
$(function () {
    // Cegah Esc menutup Bootstrap modal saat popup SweetAlert sedang terbuka.
    const SPV_SWAL = Swal.mixin({ keydownListenerCapture: true });
    const SPV_SEARCH_URL = "{{ url('/hr/upload-file-template-pk/otorisasi/search-nik') }}";
    const SPV_LIST_URL = "{{ url('/hr/upload-file-template-pk/otorisasi') }}";
    const SPV_UPDATE_URL = "{{ url('/hr/upload-file-template-pk/otorisasi/update') }}";
    const SPV_DELETE_URL = "{{ url('/hr/upload-file-template-pk/otorisasi/delete') }}";
    const SPV_EMPTY_MSG = 'Ketik minimal 2 karakter untuk mencari NIK / Nama...';
    let spvSearchTimeout = {};

    function spvEscape(s) {
        return String(s === null || s === undefined ? '' : s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function spvCsrf() {
        return $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').first().val() || '';
    }

    function spvSearchNik($wrap, q) {
        let $list = $wrap.find('.hd-ms-list');
        if (q.length < 2 && !$wrap.data('role')) {
            $list.html('<div class="hd-ms-empty text-muted" style="padding:6px 10px; font-size:.82rem;">' + SPV_EMPTY_MSG + '</div>');
            return;
        }
        $list.html('<div class="hd-ms-empty text-muted" style="padding:6px 10px; font-size:.82rem;">Memuat...</div>');
        $.get($wrap.data('url') || SPV_SEARCH_URL, { term: q, role: $wrap.data('role') || '' }, function (res) {
            if (!$wrap.hasClass('open')) return;
            let items = res.data || [];
            let html = '';
            if ($wrap.data('role') === 'associate') {
                html += '<div class="hd-ms-item spv-nik-item" data-nik="" data-nama="">Tidak Ada</div>';
            }
            if (!items.length) {
                html += '<div class="hd-ms-empty text-muted" style="padding:6px 10px; font-size:.82rem;">Tidak ditemukan.</div>';
            } else {
                items.forEach(function (u) {
                    html += '<div class="hd-ms-item spv-nik-item" data-nik="' + spvEscape(u.nik) + '" data-nama="' + spvEscape(u.name) + '"' +
                        (u.row ? ' data-row="' + spvEscape(JSON.stringify(u.row)) + '"' : '') + '>';
                    html += '<span><strong>' + spvEscape(u.nik) + '</strong> - ' + spvEscape(u.name) + '</span>';
                    html += '</div>';
                });
            }
            if ($wrap.data('allow-custom') && q !== '') {
                let exact = items.some(function (u) {
                    return String(u.nik).toLowerCase() === q.toLowerCase();
                });
                if (!exact) {
                    html += '<div class="hd-ms-item spv-nik-item" data-nik="' + spvEscape(q) + '" data-nama="" data-custom="1">Gunakan NIK "' + spvEscape(q) + '" (isi manual)</div>';
                }
            }
            $list.html(html);
        }).fail(function () {
            $list.html('<div class="hd-ms-empty text-muted" style="padding:6px 10px; font-size:.82rem;">Gagal memuat data.</div>');
        });
    }

    function spvSetNik(key, nik, nama) {
        let $wrap = $('.spv-nik-dropdown[data-key="' + key + '"]');
        let isEmpty = !nik || nik === '';
        let placeholder = $wrap.data('role') === 'associate' ? 'Tidak Ada' : 'Masukkan NIK';
        $wrap.find('.spv-nik-value').val(isEmpty ? '' : nik);
        $wrap.find('.hd-ms-label').text(isEmpty ? placeholder : (nama ? (nik + ' - ' + nama) : nik));
        $wrap.find('.hd-ms-search').val('');
        $wrap.find('.hd-ms-list').html('<div class="hd-ms-empty text-muted" style="padding:6px 10px; font-size:.82rem;">' + SPV_EMPTY_MSG + '</div>');
        $wrap.removeClass('open');
        let nameMap = { ybs: 'spvNikYbsName', manager: 'spvNikManagerName', associate: 'spvNikAssocManagerName' };
        let $nm = $('#' + nameMap[key]);
        if ($nm.length) $nm.text(isEmpty ? '' : nama);
    }

    function spvResetForm() {
        $('#spvEditId').val('');
        $('#spvFormTitle').text('Tambah Peserta');
        $('#btnSimpanSpv').text('Tambah Peserta');
        ['ybs', 'manager', 'associate'].forEach(function (key) {
            spvSetNik(key, '', '');
        });
    }

    function spvLoadList() {
        $.get(SPV_LIST_URL, function (res) {
            let rows = res.data || [];
            let html = '';
            if (!rows.length) {
                html = '<tr><td colspan="7" class="text-center text-muted">Belum ada data peserta.</td></tr>';
            }
            rows.forEach(function (o) {
                html += '<tr>' +
                    '<td>' + spvEscape(o.nik_yang_bersangkutan) + '</td>' +
                    '<td>' + spvEscape(o.nama_yang_bersangkutan || '-') + '</td>' +
                    '<td>' + spvEscape(o.nik_manager || '-') + '</td>' +
                    '<td>' + spvEscape(o.nama_manager || '-') + '</td>' +
                    '<td>' + spvEscape(o.nik_associate_manager || '-') + '</td>' +
                    '<td>' + spvEscape(o.nama_associate_manager || '-') + '</td>' +
                    '<td>' +
                    '<button class="btn btn-sm btn-warning btn-spv-edit" data-row="' + encodeURIComponent(JSON.stringify(o)) + '">Edit</button> ' +
                    '<button class="btn btn-sm btn-danger btn-spv-delete" data-id="' + o.id + '">Hapus</button>' +
                    '</td></tr>';
            });
            $('#spvTbody').html(html);
        }).fail(function () {
            $('#spvTbody').html('<tr><td colspan="7" class="text-center text-muted">Gagal memuat data.</td></tr>');
        });
    }

    function spvRefreshParticipants() {
        $('#formSearchPrt').trigger('submit');
    }

    $(document).on('click', '.spv-nik-dropdown .hd-ms-btn', function (e) {
        e.stopPropagation();
        let $wrap = $(this).closest('.spv-nik-dropdown');
        $('.spv-nik-dropdown').not($wrap).removeClass('open');
        $wrap.toggleClass('open');
        if ($wrap.hasClass('open')) {
            $wrap.find('.hd-ms-search').focus();
        }
    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest('.spv-nik-dropdown').length) {
            $('.spv-nik-dropdown').removeClass('open');
        }
    });

    $(document).on('keyup', '.spv-nik-dropdown .hd-ms-search', function () {
        let $wrap = $(this).closest('.spv-nik-dropdown');
        let key = $wrap.data('key');
        let q = $(this).val().trim();
        clearTimeout(spvSearchTimeout[key]);
        spvSearchTimeout[key] = setTimeout(function () {
            spvSearchNik($wrap, q);
        }, 250);
    });

    $(document).on('keydown', '.spv-nik-dropdown[data-allow-custom="1"] .hd-ms-search', function (e) {
        if (e.key !== 'Enter' && e.keyCode !== 13) return;
        e.preventDefault();
        let $wrap = $(this).closest('.spv-nik-dropdown');
        let q = $(this).val().trim();
        if (q === '') return;
        spvSetNik($wrap.data('key'), q, '');
    });

    $(document).on('click', '.spv-nik-item', function () {
        let $wrap = $(this).closest('.spv-nik-dropdown');
        spvSetNik($wrap.data('key'), $(this).data('nik'), $(this).data('nama'));
    });

    $(document).on('click', '#btnOpenSpvMapping', function () {
        spvResetForm();
        spvLoadList();
        $('#spvMappingModal').modal('show');
    });

    $(document).on('click', '.btn-spv-edit', function () {
        let o = JSON.parse(decodeURIComponent($(this).data('row')));
        $('#spvEditId').val(o.id);
        $('#spvFormTitle').text('Edit Peserta');
        $('#btnSimpanSpv').text('Simpan Perubahan');
        spvSetNik('ybs', o.nik_yang_bersangkutan, o.nama_yang_bersangkutan || '');
        spvSetNik('manager', o.nik_manager || '', o.nama_manager || '');
        spvSetNik('associate', o.nik_associate_manager || '', o.nama_associate_manager || '');
    });

    $(document).on('click', '.btn-spv-delete', function () {
        let id = $(this).data('id');
        SPV_SWAL.fire({
            title: 'Hapus peserta ini?',
            text: 'Mapping manager & associate manager untuk peserta ini akan dihapus.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal'
        }).then(function (result) {
            if (!result.isConfirmed) return;
            $.ajax({
                url: SPV_DELETE_URL + '/' + id,
                type: 'DELETE',
                data: { _token: spvCsrf() },
                success: function (res) {
                    SPV_SWAL.fire('Berhasil', res.message, 'success');
                    spvLoadList();
                    spvRefreshParticipants();
                },
                error: function (xhr) {
                    SPV_SWAL.fire('Error', xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Gagal menghapus peserta.', 'error');
                }
            });
        });
    });

    $('#btnResetSpv').on('click', function () {
        spvResetForm();
    });

    $('#btnSimpanSpv').on('click', function () {
        let nikYbs = $('#spvNikYbs').val() || '';
        let nikManager = $('#spvNikManager').val() || '';
        let nikAssoc = $('#spvNikAssocManager').val() || '';
        let editId = $('#spvEditId').val();

        if (!nikYbs.trim()) {
            SPV_SWAL.fire('Peringatan', 'NIK peserta wajib diisi.', 'warning');
            return;
        }
        if (!nikManager.trim()) {
            SPV_SWAL.fire('Peringatan', 'NIK manager wajib diisi.', 'warning');
            return;
        }

        let url = editId ? (SPV_UPDATE_URL + '/' + editId) : SPV_LIST_URL;

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                nik_yang_bersangkutan: nikYbs,
                nik_manager: nikManager,
                nik_associate_manager: nikAssoc,
                _token: spvCsrf()
            },
            success: function (res) {
                SPV_SWAL.fire('Berhasil', res.message, 'success');
                spvResetForm();
                spvLoadList();
                spvRefreshParticipants();
            },
            error: function (xhr) {
                SPV_SWAL.fire('Error', xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Gagal menyimpan peserta.', 'error');
            }
        });
    });
});
</script>
