<div class="modal fade" id="modalDetail" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content bas-modal">

            <input type="hidden" id="detail_kode_rak">
            <input type="hidden" id="detail_no_loker">

            <div class="modal-header bas-modal-header">
                <div>
                    <h5 class="bas-modal-title">
                        <i class="fas fa-th-large mr-2"></i>
                        Detail Loker <span id="detail_no_label"></span>
                    </h5>
                    <div class="bas-modal-sub">Data terkini pemegang hak guna fasilitas loker</div>
                </div>

                <button type="button" class="bas-modal-close" data-dismiss="modal" data-toggle="tooltip"
                    title="Tutup Jendela">
                    <i class="ki ki-close"></i>
                </button>
            </div>

            <div class="modal-body bas-modal-body" style="overflow: hidden;">

                <div class="table-responsive">
                    <table class="bas-table" id="table_detail"
                        style="width: 100%; min-width: 800px; table-layout: auto;">
                        <thead>
                            <tr>
                                <th style="width: 100px;">NIK</th>
                                <th style="min-width: 200px;">Nama</th>
                                <th style="width: 150px;">Kategori</th>
                                <th style="width: 150px;">Divisi</th>
                                <th style="width: 150px;">Tgl. Penempatan</th>
                                <th class="text-right kolom-aksi" style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="detail_penghuni_list">
                        </tbody>
                    </table>
                </div>

            </div>

            <div class="modal-footer bas-modal-footer">
                @if (in_array('loker_operator', $permissions))
                    <div class="d-flex align-items-center">
                        <button type="button" id="btn_rusak" class="bas-btn bas-btn-outline-danger mr-2"
                            data-toggle="tooltip" title="Tandai loker ini dalam masa pemeliharaan">
                            <i class="fas fa-wrench mr-2"></i> Laporkan Pemeliharaan
                        </button>
                        <button type="button" id="btn_aktif" class="bas-btn bas-btn-primary mr-2"
                            style="display: none;" data-toggle="tooltip"
                            title="Aktifkan kembali loker untuk dialokasikan">
                            <i class="fas fa-check-circle mr-2"></i> Selesai Pemeliharaan
                        </button>
                    </div>
                @endif

                <button type="button" class="bas-btn bas-btn-outline" data-dismiss="modal">
                    Tutup
                </button>
            </div>

        </div>
    </div>
</div>

@push('scripts')
    <style>
        .table-responsive {
            display: block;
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin-bottom: 1rem;
        }

        .bas-table {
            min-width: 800px !important;
            width: 100%;
            border-collapse: collapse;
        }

        .text-nowrap {
            white-space: nowrap !important;
            vertical-align: middle !important;
        }

        .bas-table-wrap {
            overflow: visible !important;
        }
    </style>

    <script>
        function getDivisiLabel(value) {
            if (!value) return '-';
            let val = value.toUpperCase().trim();
            switch (val) {
                case 'PRD BAS':
                    return 'PRD (Produksi)';
                case 'QCB BAS':
                    return 'QC (Quality Control)';
                case 'HELPER PRD - FO':
                    return 'PRD - FORTUNA (FO)';
                case 'HELPER PRD - KMJ':
                    return 'PRD - KMJ';
                case 'HELPER QC - KMJ':
                    return 'QC - KMJ';
                default:
                    return val;
            }
        }

        function showDetail(genderCode, no) {
            state.gender = genderCode;
            state.lokerNo = no;
            const label = genderCode === 'L' ? ' (Pria)' : ' (Wanita)';

            $('#detail_no_label').text(`#${no}${label}`);
            $('#detail_penghuni_list').html(
                '<tr><td colspan="6" class="text-center p-5"><i class="fas fa-spinner fa-spin mr-2"></i> Sinkronisasi Data...</td></tr>'
            );
            $('#btn_rusak, #btn_aktif').hide();

            $('#modalDetail').modal('show');

            $.get(`{{ url('loker/detail') }}/${genderCode}/${no}`)
                .done(function(res) {
                    let html = '';

                    if (canOperator) {
                        $('.kolom-aksi').show();

                        if (res.status_unit === 'rusak') {
                            $('#btn_aktif').show();
                            $('#btn_rusak').hide();
                        } else {
                            $('#btn_rusak').show();
                            $('#btn_aktif').hide();
                        }
                    } else {
                        $('.kolom-aksi').hide();
                        $('#btn_rusak, #btn_aktif').hide();
                    }

                    if (res.data && res.data.length > 0) {
                        res.data.forEach(p => {
                            let rowContent = `
                            <td class="font-weight-bold text-primary">${p.nik}</td>
                            <td style="min-width: 150px;">
                                <span class="text-dark-75 font-weight-bolder d-block font-size-lg">${p.nama}</span>
                            </td>
                            <td><span class="label label-inline label-light-success font-weight-bold">${p.kategori.toUpperCase()}</span></td>
                            <td><span class="text-muted font-weight-bold">${getDivisiLabel(p.divisi)}</span></td>
                            <td>${p.tgl_masuk}</td>
                        `;

                            if (canOperator) {
                                rowContent += `
                            <td class="text-right text-nowrap" style="width: 100px;">
                                <button class="btn btn-icon btn-light-primary btn-xs mr-1" onclick="pindahLoker('${p.nik}')" title="Mutasi / Relokasi">
                                    <i class="flaticon-refresh"></i>
                                </button>
                                <button class="btn btn-icon btn-light-danger btn-xs" onclick="konfirmasiTarikKunci('${p.id}', '${p.nama}')" title="Cabut Hak Fasilitas">
                                    <i class="flaticon2-logout-1"></i>
                                </button>
                            </td>
                            `;
                            }

                            html += `<tr>${rowContent}</tr>`;
                        });
                    } else {
                        let totalCol = canOperator ? 6 : 5;
                        html =
                            `<tr><td colspan="${totalCol}" class="text-center p-10 text-muted">Loker Kosong / Belum Ada Penghuni</td></tr>`;
                    }

                    $('#detail_penghuni_list').html(html);

                    if (canOperator) {
                        $('#btn_rusak').off('click').on('click', () => updateStatusUnit('rusak', genderCode, no));
                        $('#btn_aktif').off('click').on('click', () => updateStatusUnit('aktif', genderCode, no));
                    }

                    if (typeof refreshTooltips === 'function') {
                        refreshTooltips();
                    }
                })
                .fail(() => {
                    $('#modalDetail').modal('hide');
                    Swal.fire('Error', 'Gagal memuat detail informasi loker.', 'error');
                });
        }

        function updateStatusUnit(status, gender, no) {
            Swal.fire({
                title: status === 'rusak' ? 'Laporkan Kebutuhan Pemeliharaan' : 'Loker Siap Digunakan',
                text: status === 'rusak' ? `Tandai loker ${no} dalam masa pemeliharaan?` :
                    `Aktifkan kembali loker ${no} untuk dialokasikan?`,
                icon: status === 'rusak' ? 'error' : 'question',
                input: status === 'rusak' ? 'text' : null,
                inputPlaceholder: 'Jelaskan detail pemeliharaan yang dibutuhkan...',
                showCancelButton: true,
                confirmButtonText: 'Ya, Konfirmasi',
                confirmButtonColor: status === 'rusak' ? '#EF4444' : '#10B981',
                cancelButtonText: 'Batal',
                inputValidator: (value) => {
                    if (status === 'rusak' && !value) {
                        return 'Keterangan pemeliharaan wajib diisi!';
                    }
                }
            }).then((res) => {
                if (res.isConfirmed) {
                    KTApp.blockPage({
                        message: 'Memperbarui status loker...'
                    });

                    $.post("{{ url('loker/update-status') }}", {
                            _token: "{{ csrf_token() }}",
                            status: status,
                            gender: gender,
                            no_loker: no,
                            alasan: res.value
                        })
                        .done(function(res) {
                            KTApp.unblockPage();
                            Swal.fire('Pembaruan Berhasil', res.message, 'success')
                                .then(() => location.reload());
                        })
                        .fail((xhr) => {
                            KTApp.unblockPage();
                            $('#modalDetail').modal('show');
                            Swal.fire('Gagal', 'Sistem gagal memperbarui status loker.', 'error');
                        });
                } else {
                    $('#modalDetail').modal('show');
                }
            });
        }
    </script>
@endpush
