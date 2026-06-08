<div class="modal fade" id="modalPlotting" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-white border-0 pt-8 px-8 pb-0">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-45 symbol-light-primary mr-4" data-toggle="tooltip"
                        title="Daftarkan alokasi baru atau pindahkan (relokasi) loker karyawan">
                        <span class="symbol-label">
                            <i class="flaticon2-plus text-primary icon-lg"></i>
                        </span>
                    </div>
                    <div>
                        <h5 class="font-weight-bolder text-dark mb-0" id="modalPlottingTitle">
                            Formulir Alokasi & Relokasi Loker
                        </h5>
                        <small class="text-muted font-weight-bold">Pindai Kartu Identitas (RFID) atau ketik NIK
                            Karyawan</small>
                    </div>
                </div>
                <button type="button" class="close btn btn-xs btn-icon btn-light btn-hover-primary"
                    data-dismiss="modal">
                    <i class="ki ki-close"></i>
                </button>
            </div>

            <form id="formPlotting">
                @csrf
                <div class="modal-body p-8">
                    <div class="row">
                        <div class="col-md-7 pr-md-10 border-right">
                            <div class="form-group mb-8">
                                <label class="font-weight-bold text-dark-75 mb-2">Identitas Karyawan</label>
                                <div class="input-group input-group-lg input-search-nik shadow-none"
                                    data-toggle="tooltip" data-placement="top"
                                    title="Silakan pindai (tap) kartu pada RFID Reader atau ketik NIK secara manual">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-0 px-5">
                                            <i class="fas fa-id-card text-primary"></i>
                                        </span>
                                    </div>
                                    <input type="text" name="nik" id="plot_nik"
                                        class="form-control border-0 bg-light font-weight-bold pl-4"
                                        placeholder="Pindai Kartu atau Ketik NIK..." required autocomplete="off"
                                        style="height: 55px;">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary font-weight-bolder px-8" type="button"
                                            id="btnCariKaryawan" onclick="cariKaryawan()">
                                            <i class="fas fa-search mr-2"></i> CARI
                                        </button>
                                    </div>
                                </div>
                                <span class="form-text text-muted font-size-xs mt-3 ml-1">
                                    <i class="fa fa-info-circle text-primary mr-1"></i>
                                    Sistem mendukung <b>RFID Scanner</b>. Klik kolom NIK lalu pindai kartu identitas.
                                </span>
                            </div>

                            <div class="row mb-4">
                                <div class="col-6">
                                    <label class="text-muted font-size-sm mb-1">Nama Lengkap</label>
                                    <input type="text" id="plot_nama" name="nama"
                                        class="form-control form-control-solid font-weight-bolder border-0" readonly
                                        placeholder="Data Otomatis">
                                </div>
                                <div class="col-6">
                                    <label class="text-muted font-size-sm mb-1">Departemen (Sistem)</label>
                                    <input type="text" id="plot_dept"
                                        class="form-control form-control-solid font-weight-bolder border-0" readonly
                                        placeholder="Data Otomatis">
                                </div>
                            </div>

                            <div class="form-group mb-4" data-toggle="tooltip" data-placement="top"
                                title="Loker saat ini akan otomatis berstatus tersedia (kosong) setelah proses penempatan baru disimpan">
                                <label class="text-danger font-weight-bolder font-size-sm">Informasi Loker Saat
                                    Ini</label>
                                <div
                                    class="d-flex align-items-center bg-light-danger rounded-xl p-4 border border-danger-o-20">
                                    <div class="symbol symbol-30 symbol-danger mr-3">
                                        <span class="symbol-label"><i class="fas fa-history font-size-sm"></i></span>
                                    </div>
                                    <input type="text" id="plot_loker_lama"
                                        class="form-control-plaintext font-weight-bolder text-danger py-0" readonly
                                        value="Belum dialokasikan">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <label class="text-muted font-size-sm mb-1">Jenis Kelamin</label>

                                    <div id="plot_gender_label_container">
                                        <input type="text" id="plot_gender_label"
                                            class="form-control form-control-solid border-0" readonly placeholder="-">
                                    </div>

                                    <div id="plot_gender_select_container" style="display: none;">
                                        <select id="plot_gender_val_manual"
                                            class="form-control font-weight-bolder border-primary">
                                            <option value="" selected disabled>Pilih Jenis Kelamin</option>
                                            <option value="L">LAKI-LAKI</option>
                                            <option value="P">PEREMPUAN</option>
                                        </select>
                                    </div>

                                    <input type="hidden" name="gender" id="plot_gender_val">
                                </div>

                                <div class="col-6">
                                    <label class="text-muted font-size-sm mb-1">Kategori</label>

                                    <div id="plot_kategori_label_container">
                                        <input type="text" id="plot_kategori_label"
                                            class="form-control form-control-solid border-0" readonly placeholder="-">
                                    </div>

                                    <div id="plot_kategori_select_container" style="display: none;">
                                        <select id="plot_kategori_val_manual"
                                            class="form-control font-weight-bolder border-primary">
                                            <option value="" selected disabled>Pilih Kategori</option>
                                            <option value="staff">STAFF</option>
                                            <option value="non_staff">NON-STAFF</option>
                                            <option value="mitra_kerja">MITRA KERJA</option>
                                        </select>
                                    </div>

                                    <input type="hidden" name="kategori_karyawan" id="plot_kategori_val">
                                </div>

                                <div class="col-12 mt-4" id="plot_divisi_wrapper" style="display: none;">
                                    <label class="text-muted font-size-sm mb-1">Divisi (Khusus Pengelompokan
                                        Loker)</label>

                                    <div id="plot_divisi_label_container" style="display: none;">
                                        <input type="text" id="plot_divisi_label"
                                            class="form-control form-control-solid border-0" readonly placeholder="-">
                                    </div>

                                    <div id="plot_divisi_select_container" style="display: none;">
                                        <select id="plot_divisi_val_manual"
                                            class="form-control font-weight-bolder border-primary">
                                            <option value="" selected disabled>Pilih Divisi Penempatan</option>
                                        </select>
                                    </div>
                                    <input type="hidden" name="dept" id="plot_divisi_val">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-5 pl-md-10 text-center d-flex flex-column justify-content-between">
                            <div>
                                <h6 class="font-weight-bolder text-dark mb-5 text-left">Preview & Validasi</h6>
                                <div class="symbol symbol-120 symbol-lg-150 mb-6 shadow-sm p-1 bg-white"
                                    style="border-radius: 20px; border: 1px solid #eee;" data-toggle="tooltip"
                                    title="Pastikan foto sesuai dengan pemegang kartu ID">
                                    <img id="plot_foto_img" src="{{ asset('assets/media/users/default.jpg') }}"
                                        alt="Foto" style="border-radius: 18px; object-fit: cover;">
                                </div>

                                <div class="form-group text-left mt-5">
                                    <label class="font-weight-bolder text-primary mb-2">Pemilihan Loker Tujuan</label>
                                    <div class="input-group input-group-solid shadow-none"
                                        style="border-radius: 12px; overflow: hidden;" data-toggle="tooltip"
                                        title="Hanya menampilkan daftar loker yang tersedia sesuai kriteria karyawan">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light-primary border-0">
                                                <i class="fas fa-sign-in-alt text-primary"></i>
                                            </span>
                                        </div>
                                        <select name="no_loker" id="select_no_loker"
                                            class="form-control border-0 font-weight-bolder" style="height: 50px;">
                                            <option value="">-- Pilih Loker Tersedia --</option>
                                        </select>
                                    </div>
                                    <p class="text-muted font-size-xs mt-3">
                                        <span class="badge badge-light-primary badge-inline">Smart Filtering</span><br>
                                        Loker difilter secara sistematis menyesuaikan regulasi area dan kategori
                                        karyawan.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light-o-50 border-0 py-6 px-8">
                    <button type="button" class="btn btn-text-dark-50 btn-hover-light-primary font-weight-bold px-10"
                        data-dismiss="modal">Batal</button>
                    <button type="button" id="btnSimpanPlot" onclick="simpanPlotting()"
                        class="btn btn-primary font-weight-bolder px-12 shadow-sm" disabled data-toggle="tooltip"
                        data-theme="dark" title="Tombol akan aktif setelah NIK tervalidasi dan Loker tujuan terpilih">
                        <i class="fas fa-check-double mr-2"></i> Simpan Penempatan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        let isRelokasi = false;
        let tempDivisi = '';

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

        $(document).ready(function() {
            $('#plot_nik').on('keypress', function(e) {
                if (e.which == 13) {
                    e.preventDefault();
                    cariKaryawan();
                }
            });

            $('#select_no_loker').on('change', function() {
                const val = $(this).val();
                const btn = $('#btnSimpanPlot');
                if (val && val !== '') {
                    btn.removeAttr('disabled').removeClass('btn-light').addClass('btn-primary shadow-sm');
                } else {
                    btn.attr('disabled', true).addClass('btn-light').removeClass('btn-primary shadow-sm');
                }
            });

            $('#plot_gender_val_manual, #plot_kategori_val_manual').on('change', function() {
                let genderVal = $('#plot_gender_select_container').is(':visible') ? $(
                    '#plot_gender_val_manual').val() : $('#plot_gender_val').val();
                let katVal = $('#plot_kategori_select_container').is(':visible') ? $(
                    '#plot_kategori_val_manual').val() : $('#plot_kategori_val').val();

                if (isRelokasi) {
                    $('#plot_divisi_wrapper').hide();
                    if (katVal === 'staff') {
                        $('#plot_divisi_val').val('-');
                    } else {
                        $('#plot_divisi_val').val(tempDivisi);
                    }
                    checkAndLoadLockers();
                } else {
                    if (genderVal && katVal) {
                        if (katVal === 'staff') {
                            $('#plot_divisi_wrapper').hide();
                            $('#plot_divisi_val').val('-');
                            checkAndLoadLockers();
                        } else {
                            $('#plot_divisi_wrapper').show();
                            loadDivisiList();
                        }
                    } else {
                        $('#plot_divisi_wrapper').hide();
                    }
                }
            });

            $('#plot_divisi_val_manual').on('change', function() {
                $('#plot_divisi_val').val($(this).val());
                checkAndLoadLockers();
            });
        });

        function openModalPlotting(defaultNik = '') {
            $('#formPlotting')[0].reset();
            resetPlotFields();
            $('#plot_nik').prop('readonly', false).removeClass('bg-light');

            $('#plot_foto_img').attr('src', state.defaultFoto);

            if (defaultNik) {
                $('#plot_nik').val(defaultNik).prop('readonly', true).addClass('bg-light');
                $('#modalPlotting').modal('show');
                setTimeout(() => {
                    cariKaryawan();
                }, 500);
            } else {
                $('#modalPlotting').modal('show');
            }
        }

        function loadDivisiList() {
            let dropdown = $('#plot_divisi_val_manual');
            let kategori = $('#plot_kategori_select_container').is(':visible') ? $('#plot_kategori_val_manual').val() : $(
                '#plot_kategori_val').val();

            if (!kategori || kategori === 'staff') return;

            dropdown.empty().append('<option value="" selected disabled>⏳ Memuat Divisi...</option>');

            $.get(`{{ url('loker/divisi-list') }}/${kategori}`)
                .done(function(data) {
                    dropdown.empty().append('<option value="" selected disabled>Pilih Divisi Penempatan</option>');
                    if (data.length > 0) {
                        data.forEach(item => {
                            let selected = (tempDivisi && tempDivisi.toUpperCase() === item.value
                                .toUpperCase()) ? 'selected' : '';
                            dropdown.append(`<option value="${item.value}" ${selected}>${item.label}</option>`);
                        });

                        if (tempDivisi && dropdown.find(`option[value='${tempDivisi}']`).length > 0) {
                            dropdown.val(tempDivisi).trigger('change');
                        }
                    } else {
                        dropdown.append('<option value="" disabled>❌ Data Divisi Kosong</option>');
                    }
                })
                .fail(function() {
                    dropdown.empty().append('<option value="" disabled>Gagal memuat divisi</option>');
                });
        }

        function cariKaryawan() {
            const nikOrRfid = $('#plot_nik').val();
            if (!nikOrRfid) return;

            KTApp.block('#modalPlotting .modal-content', {
                message: 'Memverifikasi data...'
            });

            $.get(`{{ url('loker/search-karyawan') }}/${nikOrRfid}`)
                .done(function(res) {
                    KTApp.unblock('#modalPlotting .modal-content');
                    if (res.success) {
                        const d = res.data;

                        isRelokasi = d.no_loker ? true : false;
                        tempDivisi = d.divisi && d.divisi !== '-' ? d.divisi : '';

                        $('#plot_nik').val(d.nik);
                        $('#plot_nama').val(d.nama);
                        $('#plot_dept').val(getDivisiLabel(d.divisi));

                        $('#plot_kategori_val').val(d.kategori);
                        $('#plot_kategori_label').val(d.kategori ? d.kategori.replace('_', ' ').toUpperCase() : '-');

                        $('#plot_foto_img').attr('src', d.foto ? d.foto : state.defaultFoto);

                        let readyToLoad = true;

                        if (d.is_gender_empty) {
                            $('#plot_gender_val').val('');
                            $('#plot_gender_label_container').hide();
                            $('#plot_gender_select_container').show();
                            $('#plot_gender_val_manual').val('').addClass('is-invalid');
                            readyToLoad = false;
                        } else {
                            $('#plot_gender_val').val(d.gender);
                            $('#plot_gender_select_container').hide();
                            $('#plot_gender_label_container').show();
                            $('#plot_gender_label').val(d.gender === 'L' ? 'LAKI-LAKI' : 'PEREMPUAN');
                        }

                        if (d.is_category_empty) {
                            $('#plot_kategori_val').val('');
                            $('#plot_kategori_label_container').hide();
                            $('#plot_kategori_select_container').show();
                            $('#plot_kategori_val_manual').val('').addClass('is-invalid');
                            readyToLoad = false;
                        } else {
                            $('#plot_kategori_val').val(d.kategori);
                            $('#plot_kategori_select_container').hide();
                            $('#plot_kategori_label_container').show();
                            $('#plot_kategori_label').val(d.kategori === 'staff' ? 'STAFF' : (d.kategori ===
                                'mitra_kerja' ? 'MITRA KERJA' : 'NON-STAFF'));
                        }

                        if (isRelokasi) {
                            $('#plot_divisi_wrapper').hide();
                            $('#plot_divisi_val').val(d.divisi);
                        } else {
                            $('#plot_divisi_val').val('');
                            $('#plot_divisi_label_container').hide();
                            $('#plot_divisi_select_container').show();
                            $('#plot_divisi_val_manual').empty().append(
                                '<option value="" selected disabled>Pilih Divisi Penempatan</option>');
                            $('#plot_divisi_wrapper').hide();

                            let genderVal = $('#plot_gender_select_container').is(':visible') ? $(
                                '#plot_gender_val_manual').val() : d.gender;
                            let katVal = $('#plot_kategori_select_container').is(':visible') ? $(
                                '#plot_kategori_val_manual').val() : d.kategori;

                            if (genderVal && katVal) {
                                if (katVal === 'staff') {
                                    $('#plot_divisi_wrapper').hide();
                                    $('#plot_divisi_val').val('-');
                                } else {
                                    $('#plot_divisi_wrapper').show();
                                    loadDivisiList();
                                }
                            }
                        }

                        if (readyToLoad && isRelokasi) {
                            checkAndLoadLockers();
                        }

                        if (d.no_loker) {
                            $('#plot_loker_lama').val("LOKER " + d.no_loker);
                            $('#modalPlottingTitle').text("Formulir Relokasi Loker");
                        } else {
                            $('#plot_loker_lama').val("Belum Memiliki Loker");
                            $('#modalPlottingTitle').text("Formulir Penempatan Baru");
                        }
                    } else {
                        resetPlotFields();
                        Swal.fire('Informasi', res.message, 'info');
                    }
                })
                .fail(() => {
                    KTApp.unblock('#modalPlotting .modal-content');
                    resetPlotFields();
                    Swal.fire('Error', 'Sistem gagal memverifikasi data karyawan.', 'error');
                });
        }

        function resetPlotFields() {
            isRelokasi = false;
            tempDivisi = '';
            $('#plot_nama, #plot_dept, #plot_loker_lama, #plot_gender_label, #plot_kategori_label, #plot_divisi_label').val(
                '-');
            $('#plot_gender_val, #plot_kategori_val, #plot_divisi_val').val('');
            $('#plot_gender_val_manual, #plot_kategori_val_manual, #plot_divisi_val_manual').val('');
            $('#plot_divisi_wrapper').hide();
            $('#plot_foto_img').attr('src', state.defaultFoto);
            $('#select_no_loker').empty().append('<option value="">-- Pilih Loker --</option>');
            $('#btnSimpanPlot').attr('disabled', true).addClass('btn-light').removeClass('btn-primary shadow-sm');
            $('#modalPlottingTitle').text("Formulir Alokasi Loker");
        }

        function checkAndLoadLockers() {
            let gender = $('#plot_gender_select_container').is(':visible') ? $('#plot_gender_val_manual').val() : $(
                '#plot_gender_val').val();
            let kategori = $('#plot_kategori_select_container').is(':visible') ? $('#plot_kategori_val_manual').val() : $(
                '#plot_kategori_val').val();
            let divisi = $('#plot_divisi_select_container').is(':visible') ? $('#plot_divisi_val_manual').val() : $(
                '#plot_divisi_val').val();
            let nik = $('#plot_nik').val();

            if (gender && kategori && nik) {
                if (kategori !== 'staff' && !divisi) return;

                loadAvailableLockers(gender, kategori, divisi).then(() => {
                    getSuggestion(nik, gender, kategori, divisi);
                });
            }
        }

        function loadAvailableLockers(gender, kategori, divisi) {
            return new Promise((resolve, reject) => {
                let dropdown = $('#select_no_loker');
                dropdown.empty().append('<option value="" selected disabled>⏳ Menyiapkan daftar loker...</option>');

                let url = `{{ url('loker/available') }}/${gender}/${kategori || 'non_staff'}`;
                if (kategori !== 'staff' && divisi) {
                    url += `?divisi=${encodeURIComponent(divisi)}`;
                }

                $.get(url)
                    .done(function(data) {
                        dropdown.empty().append('<option value="">-- Pilih Loker --</option>');
                        if (data.length > 0) {
                            data.forEach(item => {
                                dropdown.append(
                                    `<option value="${item.no_loker}">Loker ${item.no_loker}</option>`
                                );
                            });
                        } else {
                            dropdown.append(
                                '<option value="" selected disabled>❌ Tidak ada loker tersedia</option>');
                        }
                        resolve();
                    })
                    .fail(reject);
            });
        }

        function getSuggestion(nik, gender, kategori, divisi) {
            $.get("{{ route('loker.api-suggest-loker') }}", {
                    nik: nik,
                    gender: gender,
                    kategori: kategori,
                    divisi: divisi
                })
                .done(function(res) {
                    if (res.rekomendasi_loker && res.rekomendasi_loker !== 'penuh') {
                        const recoValue = res.rekomendasi_loker.toString();
                        let targetSelect = $('#select_no_loker');

                        if (targetSelect.find(`option[value='${recoValue}']`).length > 0) {
                            targetSelect.val(recoValue).trigger('change');
                        } else {
                            console.warn("Nomor loker rekomendasi tidak ditemukan di daftar.")
                        }
                    }
                });
        }

        function simpanPlotting() {
            let genderFinal = $('#plot_gender_select_container').is(':visible') ? $('#plot_gender_val_manual').val() : $(
                '#plot_gender_val').val();
            let kategoriFinal = $('#plot_kategori_select_container').is(':visible') ? $('#plot_kategori_val_manual').val() :
                $('#plot_kategori_val').val();
            let divisiFinal = $('#plot_divisi_select_container').is(':visible') ? $('#plot_divisi_val_manual').val() : $(
                '#plot_divisi_val').val();

            if (!genderFinal) {
                Swal.fire('Validasi Gagal', 'Data Gender belum ditentukan!', 'warning');
                return;
            }
            if (!kategoriFinal) {
                Swal.fire('Validasi Gagal', 'Data Kategori belum ditentukan!', 'warning');
                return;
            }
            if (kategoriFinal !== 'staff' && !divisiFinal) {
                Swal.fire('Validasi Gagal', 'Data Divisi wajib dipilih untuk Non-Staff / Mitra!', 'warning');
                return;
            }

            $('#plot_gender_val').val(genderFinal);
            $('#plot_kategori_val').val(kategoriFinal);
            $('#plot_divisi_val').val(kategoriFinal === 'staff' ? '-' : divisiFinal);

            let formData = $('#formPlotting').serialize();

            KTApp.block('#modalPlotting .modal-content', {
                message: 'Merekam data penempatan...'
            });

            $.post("{{ route('loker.store') }}", formData)
                .done(function(res) {
                    KTApp.unblock('#modalPlotting .modal-content');
                    if (res.status === 'success') {
                        Swal.fire({
                                icon: 'success',
                                title: 'Penempatan Berhasil!',
                                timer: 1500,
                                showConfirmButton: false
                            })
                            .then(() => location.reload());
                    } else {
                        Swal.fire('Proses Gagal', res.message, 'error');
                    }
                }).fail(() => {
                    KTApp.unblock('#modalPlotting .modal-content');
                    Swal.fire('Error', 'Terjadi kesalahan sistem saat menyimpan data.', 'error');
                });
        }

        function konfirmasiTarikKunci(id, nama) {
            Swal.fire({
                title: 'Konfirmasi Pencabutan Fasilitas',
                text: `Apakah Anda yakin ingin mencabut hak fasilitas loker untuk ${nama}?`,
                icon: 'warning',
                input: 'text',
                inputPlaceholder: 'Ketik alasan: Resign / Mutasi / Pelanggaran...',
                showCancelButton: true,
                confirmButtonText: 'Ya, Cabut Fasilitas',
                confirmButtonColor: '#EF4444',
                cancelButtonText: 'Batal',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Alasan pencabutan fasilitas wajib diisi!'
                    }
                }
            }).then((res) => {
                if (res.isConfirmed) {
                    const alasanPenarikan = res.value;
                    KTApp.blockPage({
                        message: 'Memproses pengosongan loker...'
                    });

                    $.post("{{ route('loker.tarik-kunci') }}", {
                            id: id,
                            alasan: alasanPenarikan
                        })
                        .done(function(res) {
                            KTApp.unblockPage();
                            if (res.status === 'success') {
                                Swal.fire({
                                        icon: 'success',
                                        title: 'Pencabutan Berhasil',
                                        text: 'Fasilitas loker telah dicabut.',
                                        timer: 1500,
                                        showConfirmButton: false
                                    })
                                    .then(() => location.reload());
                            }
                        }).fail(() => {
                            KTApp.unblockPage();
                            Swal.fire('Error', 'Gagal mencabut fasilitas loker.', 'error');
                        });
                }
            });
        }

        function pindahLoker(nik) {
            $('#modalDetail').modal('hide');
            setTimeout(() => openModalPlotting(nik), 400);
        }
    </script>
@endpush
