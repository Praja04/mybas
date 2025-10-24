{{-- <div class="card card-custom">
    <form action="{{ url('edoc/post_kedatangan') }}" method="POST" enctype="multipart/form-data" id="PostKedatanganForm">
        @csrf
        <div class="card-body">
            <div class="form-group mb-8">
                <div class="alert alert-custom alert-default" role="alert">
                    <div class="alert-icon"><i class="flaticon-warning text-primary"></i></div>
                    <div class="alert-text">
                        <h3>
                            FORM KEDATANGAN BARANG
                        </h3>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label for="tanggal-kedatangan" class="col-sm-2 col-form-label text-right">Tanggal Kedatangan
                </label>
                <div class="col-sm-10">
                    <input class="form-control" type="date" name="tanggal_kedatangan" id="tanggal-kedatangan"
                        value="{{ date('Y-m-d') }}" required />
                    <small class="form-text text-danger">*Wajib</small>
                </div>
            </div>
            <div class="form-group row">
                <label for="dept-penerima" class="col-sm-2 col-form-label text-right">Departemen Penerima</label>
                <div class="col-sm-10">
                    <select class="form-control" name="dept_penerima" id="dept-penerima" style="width: 100%;" required>
                        <option value="" selected disabled>Pilih Departemen</option>
                        @foreach ($dept as $item)
                            @if ($item->status == 1)
                                <option value="{{ $item->name }}">{{ $item->name }}</option>
                            @endif
                        @endforeach
                    </select>
                    <small class="form-text text-danger">*Wajib</small>
                </div>
            </div>
            <div class="form-group row">
                <label for="nama-penerima" class="col-sm-2 col-form-label text-right">Nama Penerima</label>
                <div class="col-sm-10">
                    <input class="form-control" type="text" name="nama_penerima" id="nama-penerima"
                        placeholder="Contoh : Muhammad Machbub Marzuqi" required />
                    <small class="form-text text-danger">*Wajib</small>
                </div>
            </div>
            <div class="form-group row">
                <label for="nama-pt-pengirim" class="col-sm-2 col-form-label text-right">Nama PT Pengirim</label>
                <div class="col-sm-10">
                    <input class="form-control" type="text" name="nama_pt_pengirim" id="nama-pt-pengirim" required
                        placeholder="Nama PT yang mengirimkan barang atau dokumen" />
                    <small class="form-text text-danger">*Wajib</small>
                </div>
            </div>
            <div class="form-group row">
                <label for="no-identitas-kurir" class="col-sm-2 col-form-label text-right">No Identitas Pengantar
                    <br />( kurir )</label>
                <div class="col-sm-10">
                    <input class="form-control" type="text" name="no_identitas_kurir" id="no-identitas-kurir"
                        required placeholder="Nomor KTP / Nomor SIM" />
                    <small class="form-text text-danger">*Wajib</small>
                </div>
            </div>
            <div class="form-group row">
                <label for="foto-kartu-identitas-kurir" class="col-sm-2 col-form-label text-right">Foto Kartu Identitas
                    <br />( kurir )</label>
                <div class="col-sm-10">
                    <div id="foto-ktp-results"></div>
                    <input type="text" name="foto_kartu_identitas_kurir" class="ktpValuePicture" hidden
                        id="foto-kartu-identitas-kurir" />
                    <button class="btn btn-secondary btn-sm" type="button" onClick="openFotoKTPCamera()">Ambil
                        Foto</button>
                    <small class="form-text text-danger">*Wajib</small>
                </div>
            </div>
            <div class="form-group row">
                <label for="nama-kurir" class="col-sm-2 col-form-label text-right">Nama Pengantar <br />( kurir
                    )</label>
                <div class="col-sm-10">
                    <input class="form-control" type="text" name="nama_kurir" id="nama-kurir" required
                        placeholder="Nama orang yang mengirimkan barang atau dokumen" />
                    <small class="form-text text-danger">*Wajib</small>
                </div>
            </div>
            <div class="form-group row">
                <label for="no-hp-kurir" class="col-sm-2 col-form-label text-right">No. HP Pengantar <br />( kurir
                    )</label>
                <div class="col-sm-10">
                    <input class="form-control" type="text" name="no_hp_kurir" id="no-hp-kurir" required
                        placeholder="Contoh : 081238273282" />
                    <small class="form-text text-danger">*Wajib</small>
                </div>
            </div>
            <div class=" form-group row">
                <label for="jenis" class="col-sm-2 col-form-label text-right">Jenis Barang</label>
                <div class="col-sm-10">
                    <select class="form-control" name="jenis" id="jenis" required>
                        <option value="" selected disabled>Pilih Jenis Barang</option>
                        <option value="Barang">Barang</option>
                        <option value="Dokumen">Dokumen</option>
                    </select>
                    <small class="form-text text-danger">*Wajib</small>
                </div>
            </div>
            <div class="form-group row">
                <label for="keterangan" class="col-sm-2 col-form-label text-right">Keterangan</label>
                <div class="col-sm-10">
                    <textarea class="form-control" name="keterangan" id="keterangan" rows="5" placeholder="Masukan keterangan"></textarea>
                    <small class="form-text text-muted">*Opsional</small>
                </div>
            </div>
            <div class="float-right mb-4">
                <button type="submit" class="btn btn-info btn-lg mr-2 SaveKedatangan" style="border-radius: 10px;">
                    Simpan</button>
            </div>
        </div>
    </form>
</div> --}}
