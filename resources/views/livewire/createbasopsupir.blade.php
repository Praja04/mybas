

<div class="container-fluid">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Buat Berita Acara S.O.P Supir</h4>
            </div>
    
            <div class="card-body">
                <form wire:submit.prevent="AddBaSopSupir" method="POST" autocomplete="off">
                    @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="nama" class="form-label">Nama Supir</label>
                                    <input type="text" class="form-control" wire:model="nama" placeholder="Masukan Nama">
                                    <p class="text-danger ml-2">
                                        @error('nama')
                                            {{ $message }}
                                        @enderror
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="ekspedisi" class="form-label">Ekspedisi</label>
                                    <input type="text" class="form-control" wire:model="ekspedisi" placeholder="Tempat Ekspedisi">
                                    <p class="text-danger ml-2">
                                        @error('ekspedisi')
                                            {{ $message }}
                                        @enderror
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="no_ktp" class="form-label">Nomor KTP (Kartu Tanda Penduduk)</label>
                                    <input type="text" class="form-control" wire:model="no_ktp" placeholder="Masukan Nomor KTP">
                                    <p class="text-danger ml-2">
                                        @error('no_ktp')
                                            {{ $message }}
                                        @enderror
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="no_polisi" class="form-label">Nomor Polisi</label>
                                    <input type="text" class="form-control" wire:model="no_polisi" placeholder="Masukan Nomor Polisi">
                                    <p class="text-danger ml-2">
                                        @error('no_polisi')
                                            {{ $message }}
                                        @enderror
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="no_handphone" class="form-label">Nomor Handphone</label>
                                    <input type="text" class="form-control" wire:model="no_handphone" placeholder="Masukan Nomor Handphone">
                                    <p class="text-danger ml-2">
                                        @error('no_handphone')
                                            {{ $message }}
                                        @enderror
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="no_kartu" class="form-label">Nomor Kartu</label>
                                    <input type="text" class="form-control" wire:model="no_kartu" placeholder="Masukan Nomor Kartu">
                                    <p class="text-danger ml-2">
                                        @error('no_kartu')
                                            {{ $message }}
                                        @enderror
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="no_kartu" class="form-label">Jenis Kartu</label>
                                    <input type="text" class="form-control" wire:model="jenis_kartu" placeholder="Masukan Jenis Kartu">
                                    <p class="text-danger ml-2">
                                        @error('no_kartu')
                                            {{ $message }}
                                        @enderror
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="no_kartu" class="form-label">Harga Kartu</label>
                                    <input type="number" class="form-control" wire:model="harga_kartu" placeholder="Masukan harga Kehilangan kartu">
                                    <p class="text-danger ml-2">
                                        @error('no_kartu')
                                            {{ $message }}
                                        @enderror
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="alamat" class="form-label">Alamat</label>
                                    <textarea class="form-control" wire:model="alamat" placeholder="Masukan Alamat" rows="3"></textarea>
                                    <p class="text-danger ml-2">
                                        @error('alamat')
                                            {{ $message }}
                                        @enderror
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="shift" class="form-label">Shift</label>
                                    <select class="form-select mb-3" wire:model="shift">
                                        <option value="">Pilih Shift</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                    </select>
                                    <p class="text-danger ml-2">
                                        @error('shift')
                                            {{ $message }}
                                        @enderror
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="nama_pembuat" class="form-label">Nama Pembuat</label>
                                    <input type="text" wire:model="nama_pembuat" class="form-control" placeholder="Masukan Nama Pembuat Dokumen">
                                    <p class="text-danger ml-2">
                                        @error('nama_pembuat')
                                            {{ $message }}
                                        @enderror
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="jabatan_pembuat" class="form-label">Jabatan Pembuat</label>
                                    <input type="text" wire:model="jabatan_pembuat" class="form-control" placeholder="Masukan Jabatan Pembuat Dokumen">
                                    <p class="text-danger ml-2">
                                        @error('jabatan_pembuat')
                                            {{ $message }}
                                        @enderror
                                    </p>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="d-flex">
                                    <div class="flex">
                                        <div class="d-flex justify-content-center align-items-center">
                                            <button type="submit" class="btn btn-md btn-primary">
                                                Simpan
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    </div>
            </div>
        </div>
</div>

