<div class="container-fluid">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Buat Berita Acara S.O.P Karyawan</h4>
            </div>
    
            <div class="card-body">
                <form wire:submit.prevent="AddBaSopKaryawan" method="POST" autocomplete="off">
                    @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="nama" class="form-label">Nama Karyawan</label>
                                    <input type="text" class="form-control" wire:model="nama" placeholder="Masukan Nama Karyawan">
                                    <p class="text-danger ml-2">
                                        @error('nama')
                                            {{ $message }}
                                        @enderror
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="nik" class="form-label">Nik (Nomor Induk Karyawan)</label>
                                    <input type="text" wire:model="nik" class="form-control" placeholder="Masukan Nomor NIK (Nomor Induk Karyawan)">
                                    <p class="text-danger ml-2">
                                        @error('nik')
                                            {{ $message }}
                                        @enderror
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="jabatan" class="form-label">Jabatan / Bagian</label>
                                    <input type="text" wire:model="jabatan" class="form-control" placeholder="Masukan Jabatan / Bagian Karyawan">
                                    <p class="text-danger ml-2">
                                        @error('jabatan')
                                            {{ $message }}
                                        @enderror
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                                    <select class="form-select mb-3" wire:model="jenis_kelamin">
                                        <option value="">Pilih Jenis Kelamin</option>
                                        <option value="laki-laki">Laki - Laki</option>
                                        <option value="perempuan">Perempuan</option>
                                    </select>
                                    <p class="text-danger ml-2">
                                        @error('jenis_kelamin')
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
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="nama_area" class="form-label">Nama Area</label>
                                    <input type="text" wire:model="nama_area" class="form-control" placeholder="Masukan Nama Area">
                                    <p class="text-danger ml-2">
                                        @error('nama_area')
                                            {{ $message }}
                                        @enderror
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="barang" class="form-label">Barang</label>
                                    <input type="text" wire:model="barang" class="form-control" placeholder="Masukan Nama Barang">
                                    <p class="text-danger ml-2">
                                        @error('barang')
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