<script>
$(function () {
    // Cegah Esc menutup Bootstrap modal saat popup SweetAlert sedang terbuka.
    const FOREMAN_SWAL = Swal.mixin({ keydownListenerCapture: true });
    const FOREMAN_YBS_SEARCH_URL = "{{ url('/hr/upload-file-template-pk/otorisasi-foreman/search-nik') }}";
    const FOREMAN_ATASAN_SEARCH_URL = "{{ url('/hr/upload-file-template-pk/otorisasi/search-nik') }}";
    const FOREMAN_LIST_URL = "{{ url('/hr/upload-file-template-pk/otorisasi-foreman') }}";
    const FOREMAN_UPDATE_URL = "{{ url('/hr/upload-file-template-pk/otorisasi-foreman/update') }}";
    const FOREMAN_DELETE_URL = "{{ url('/hr/upload-file-template-pk/otorisasi-foreman/delete') }}";
    const FOREMAN_EMPTY_MSG = 'Ketik minimal 2 karakter untuk mencari NIK / Nama...';
    let foremanSearchTimeout = {};

    function foremanEscape(s) {
        return String(s === null || s === undefined ? '' : s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function foremanCsrf() {
        return $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').first().val() || '';
    }

    function foremanSearchNik($wrap, q) {
        let $list = $wrap.find('.hd-ms-list');
        if (q.length < 2 && !$wrap.data('role')) {
            $list.html('<div class="hd-ms-empty text-muted" style="padding:6px 10px; font-size:.82rem;">' + FOREMAN_EMPTY_MSG + '</div>');
            return;
        }
        $list.html('<div class="hd-ms-empty text-muted" style="padding:6px 10px; font-size:.82rem;">Memuat...</div>');
        let url = $wrap.data('url') || ($wrap.data('role') ? FOREMAN_ATASAN_SEARCH_URL : FOREMAN_YBS_SEARCH_URL);
        $.get(url, { term: q, role: $wrap.data('role') || '' }, function (res) {
            if (!$wrap.hasClass('open')) return;
            let items = res.data || [];
            let html = '';
            if ($wrap.data('role') === 'associate') {
                html += '<div class="hd-ms-item foreman-nik-item" data-nik="" data-nama="">Tidak Ada</div>';
            }
            if (!items.length) {
                html += '<div class="hd-ms-empty text-muted" style="padding:6px 10px; font-size:.82rem;">Tidak ditemukan.</div>';
            } else {
                items.forEach(function (u) {
                    html += '<div class="hd-ms-item foreman-nik-item" data-nik="' + foremanEscape(u.nik) + '" data-nama="' + foremanEscape(u.name) + '">';
                    html += '<span><strong>' + foremanEscape(u.nik) + '</strong> - ' + foremanEscape(u.name) + '</span>';
                    html += '</div>';
                });
            }
            $list.html(html);
        }).fail(function () {
            $list.html('<div class="hd-ms-empty text-muted" style="padding:6px 10px; font-size:.82rem;">Gagal memuat data.</div>');
        });
    }

    function foremanSetNik(key, nik, nama) {
        let $wrap = $('.foreman-nik-dropdown[data-key="' + key + '"]');
        let isEmpty = !nik || nik === '';
        let placeholder = $wrap.data('role') === 'associate' ? 'Tidak Ada' : 'Masukkan NIK';
        $wrap.find('.foreman-nik-value').val(isEmpty ? '' : nik);
        $wrap.find('.hd-ms-label').text(isEmpty ? placeholder : (nama ? (nik + ' - ' + nama) : nik));
        $wrap.find('.hd-ms-search').val('');
        $wrap.find('.hd-ms-list').html('<div class="hd-ms-empty text-muted" style="padding:6px 10px; font-size:.82rem;">' + FOREMAN_EMPTY_MSG + '</div>');
        $wrap.removeClass('open');
        let nameMap = {
            foreman_ybs: 'foremanNikYbsName',
            foreman_manager: 'foremanNikManagerName',
            foreman_associate: 'foremanNikAssocManagerName'
        };
        let $nm = $('#' + nameMap[key]);
        if ($nm.length) $nm.text(isEmpty ? '' : nama);
    }

    function foremanResetForm() {
        $('#foremanEditId').val('');
        $('#foremanFormTitle').text('Tambah Peserta');
        $('#btnSimpanForeman').text('Tambah Peserta');
        ['foreman_ybs', 'foreman_manager', 'foreman_associate'].forEach(function (key) {
            foremanSetNik(key, '', '');
        });
    }

    function foremanLoadList() {
        $.get(FOREMAN_LIST_URL, function (res) {
            let rows = res.data || [];
            let html = '';
            if (!rows.length) {
                html = '<tr><td colspan="7" class="text-center text-muted">Belum ada data peserta.</td></tr>';
            }
            rows.forEach(function (o) {
                html += '<tr>' +
                    '<td>' + foremanEscape(o.nik_yang_bersangkutan) + '</td>' +
                    '<td>' + foremanEscape(o.nama_yang_bersangkutan || '-') + '</td>' +
                    '<td>' + foremanEscape(o.nik_manager || '-') + '</td>' +
                    '<td>' + foremanEscape(o.nama_manager || '-') + '</td>' +
                    '<td>' + foremanEscape(o.nik_associate_manager || '-') + '</td>' +
                    '<td>' + foremanEscape(o.nama_associate_manager || '-') + '</td>' +
                    '<td>' +
                    '<button class="btn btn-sm btn-warning btn-foreman-edit" data-row="' + encodeURIComponent(JSON.stringify(o)) + '">Edit</button> ' +
                    '<button class="btn btn-sm btn-danger btn-foreman-delete" data-id="' + o.id + '">Hapus</button>' +
                    '</td></tr>';
            });
            $('#foremanTbody').html(html);
        }).fail(function () {
            $('#foremanTbody').html('<tr><td colspan="7" class="text-center text-muted">Gagal memuat data.</td></tr>');
        });
    }

    function foremanRefreshParticipants() {
        $('#formSearchPrt').trigger('submit');
    }

    $(document).on('click', '.foreman-nik-dropdown .hd-ms-btn', function (e) {
        e.stopPropagation();
        let $wrap = $(this).closest('.foreman-nik-dropdown');
        $('.foreman-nik-dropdown').not($wrap).removeClass('open');
        $wrap.toggleClass('open');
        if ($wrap.hasClass('open')) {
            $wrap.find('.hd-ms-search').focus();
        }
    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest('.foreman-nik-dropdown').length) {
            $('.foreman-nik-dropdown').removeClass('open');
        }
    });

    $(document).on('keyup', '.foreman-nik-dropdown .hd-ms-search', function () {
        let $wrap = $(this).closest('.foreman-nik-dropdown');
        let key = $wrap.data('key');
        let q = $(this).val().trim();
        clearTimeout(foremanSearchTimeout[key]);
        foremanSearchTimeout[key] = setTimeout(function () {
            foremanSearchNik($wrap, q);
        }, 250);
    });

    $(document).on('click', '.foreman-nik-item', function () {
        let $wrap = $(this).closest('.foreman-nik-dropdown');
        foremanSetNik($wrap.data('key'), $(this).data('nik'), $(this).data('nama'));
    });

    $(document).on('click', '#btnOpenForemanMapping', function () {
        foremanResetForm();
        foremanLoadList();
        $('#foremanMappingModal').modal('show');
    });

    $(document).on('click', '.btn-foreman-edit', function () {
        let o = JSON.parse(decodeURIComponent($(this).data('row')));
        $('#foremanEditId').val(o.id);
        $('#foremanFormTitle').text('Edit Peserta');
        $('#btnSimpanForeman').text('Simpan Perubahan');
        foremanSetNik('foreman_ybs', o.nik_yang_bersangkutan, o.nama_yang_bersangkutan || '');
        foremanSetNik('foreman_manager', o.nik_manager || '', o.nama_manager || '');
        foremanSetNik('foreman_associate', o.nik_associate_manager || '', o.nama_associate_manager || '');
    });

    $(document).on('click', '.btn-foreman-delete', function () {
        let id = $(this).data('id');
        FOREMAN_SWAL.fire({
            title: 'Hapus peserta ini?',
            text: 'Mapping Manager & Associate Manager (Foreman) untuk peserta ini akan dihapus.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal'
        }).then(function (result) {
            if (!result.isConfirmed) return;
            $.ajax({
                url: FOREMAN_DELETE_URL + '/' + id,
                type: 'DELETE',
                data: { _token: foremanCsrf() },
                success: function (res) {
                    FOREMAN_SWAL.fire('Berhasil', res.message, 'success');
                    foremanLoadList();
                    foremanRefreshParticipants();
                },
                error: function (xhr) {
                    FOREMAN_SWAL.fire('Error', xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Gagal menghapus peserta.', 'error');
                }
            });
        });
    });

    $('#btnResetForeman').on('click', function () {
        foremanResetForm();
    });

    $('#btnSimpanForeman').on('click', function () {
        let nikYbs = $('#foremanNikYbs').val() || '';
        let nikManager = $('#foremanNikManager').val() || '';
        let nikAssoc = $('#foremanNikAssocManager').val() || '';
        let editId = $('#foremanEditId').val();

        if (!nikYbs.trim()) {
            FOREMAN_SWAL.fire('Peringatan', 'NIK peserta wajib diisi.', 'warning');
            return;
        }
        if (!nikManager.trim()) {
            FOREMAN_SWAL.fire('Peringatan', 'NIK Manager wajib diisi.', 'warning');
            return;
        }

        let url = editId ? (FOREMAN_UPDATE_URL + '/' + editId) : FOREMAN_LIST_URL;

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                nik_yang_bersangkutan: nikYbs,
                nik_manager: nikManager,
                nik_associate_manager: nikAssoc,
                _token: foremanCsrf()
            },
            success: function (res) {
                FOREMAN_SWAL.fire('Berhasil', res.message, 'success');
                foremanResetForm();
                foremanLoadList();
                foremanRefreshParticipants();
            },
            error: function (xhr) {
                FOREMAN_SWAL.fire('Error', xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Gagal menyimpan peserta.', 'error');
            }
        });
    });
});
</script>
