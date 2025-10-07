<div class="container-fluid">
    <form method="get" action="{{ route('excel-report-kejadian') }}" id="excelkejadian">
        {{csrf_field()}}
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1 text-center">Export Excel</h4>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="form-group">
                        <div class="input-group">
                            <label class="input-group-text" for="inputGroupSelect01">From Export</label>
                            <input id="startDate" name="startDate" type="date" class="form-control" />
                        </div>
                    </div>
                    <div class="form-group" style="margin-left: -120px;">
                        <div class="input-group">
                            <label class="input-group-text" for="inputGroupSelect01">To Export</label>
                            <input id="endDate"  name="endDate" type="date" class="form-control" />
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <button type="submit" style="width: 100%;" class="btn btn-success btn-md"><i class="ri-file-excel-2-fill" style="margin-top: 10px; margin-right: 4px;"></i> Export Excel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <form action="{{route('ba-list-laporankejadian')}}" method="get">
        {{csrf_field()}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0 flex-grow-1 text-center">Filter Data List Berita Acara Laporan Kejadian</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-4">
                            <div class="input-group">
                                <label class="input-group-text" for="inputGroupSelect01">Tanggal</label>
                                <input type="date" name="created_at" value="{{isset($_GET['created_at']) ? $_GET['created_at'] : ''}}" class="form-control" id="exampleInputdate">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="input-group">
                                <label class="input-group-text" for="inputGroupSelect01">Jenis Kejadian</label>
                                <select class="form-select" id="inputGroupSelect01" name="jenis_kejadian">
                                    <option value="">Semua Jenis Kejadian</option>
                                    <option {{ Request('jenis_kejadian') == 'kecelakaan lalu lintas' ? 'selected' : '' }} value="kecelakaan lalu lintas">Kecelakaan Lalu Lintas</option>
                                    <option {{ Request('jenis_kejadian') == 'penemuan barang' ? 'selected' : '' }} value="penemuan barang">Penemuan Barang</option>
                                    <option {{ Request('jenis_kejadian') == 'kecelakaan kerja' ? 'selected' : '' }} value="kecelakaan kerja">Kecelakaan Kerja</option>
                                    <option {{ Request('jenis_kejadian') == 'pencurian' ? 'selected' : '' }} value="pencurian">Pencurian</option>
                                    <option {{ Request('jenis_kejadian') == 'perkelahian' ? 'selected' : '' }} value="perkelahian">Perkelahian</option>
                                    <option {{ Request('jenis_kejadian') == 'tindak kekerasan' ? 'selected' : '' }} value="tindak kekerasan">Tindak Kekerasan</option>
                                    <option {{ Request('jenis_kejadian') == 'kebakaran' ? 'selected' : '' }} value="kebakaran">Kebakaran</option>
                                    <option {{ Request('jenis_kejadian') == 'demonstrasi' ? 'selected' : '' }} value="demonstrasi">Demonstrasi</option>
                                    <option {{ Request('jenis_kejadian') == 'tindakan asusila' ? 'selected' : '' }} value="tindakan asusila">Tindakan Asusila</option>
                                    <option {{ Request('jenis_kejadian') == 'pengerusakan' ? 'selected' : '' }} value="pengerusakan">Pengerusakan</option>
                                    <option {{ Request('jenis_kejadian') == 'tindakan indispliner' ? 'selected' : '' }} value="tindakan indispliner">Tindakan Indispliner</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="input-group">
                                <label class="input-group-text" for="inputGroupSelect01">Status Terlapor</label>
                                <select class="form-select" id="inputGroupSelect01" name="status_terlapor">
                                    <option value="">Semua Status Terlapor</option>
                                    <option {{ Request('status_terlapor') == 'sudah kawin' ? 'selected' : '' }} value="sudah kawin">Sudah Kawin</option>
                                    <option {{ Request('status_terlapor') == 'belum kawin' ? 'selected' : '' }} value="belum kawin">Belum Kawin</option>
                                    <option {{ Request('status_terlapor') == 'janda/duda' ? 'selected' : '' }} value="janda/duda">Janda/Duda</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-2 mt-3">
                            <button type="submit" class="btn btn-md btn-primary"><i class="ri-filter-3-line" style="margin-top: 10px; margin-right: 4px;"></i> Filter</button>
                        </div>
                    </div>
                </div>
        </div>
    </form>
    <div class="card">
        <div class="card-header align-items-center d-flex">
            <h4 class="card-title mb-0 flex-grow-1 text-center">List Berita Acara Laporan Kejadian</h4>
            <a href="{{ route('listlaporankejadian.trash') }}" class="btn btn-md btn-success"><i class="ri-recycle-fill" style="margin-top: 8px; margin-right: 4px;"></i> Recycling</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="bakejadian" class="table table-md table-bordered border-secondary table-nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col" class="text-center">No</th>
                            <th scope="col" class="text-center">ID Kejadian</th>
                            <th scope="col" class="text-center">Jenis Kejadian</th>
                            <th scope="col" class="text-center">Nama Korban</th>
                            <th scope="col" class="text-center">Nik Korban</th>
                            <th scope="col" class="text-center">Perusahaan Korban</th>
                            <th scope="col" class="text-center">Bagian Korban</th>
                            {{-- <th scope="col">Fakta Kejadian</th>
                            <th scope="col">Saksi Kejadian</th> --}}
                            <th scope="col" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($balaporankejadian as $item)
                        <tr>
                            <td scope="row" class="text-center">{{ $loop->iteration }}</td>
                            <td scope="row" class="text-center">{{ $item->lk_id }}</td>
                            <td scope="row" class="text-center">{{ $item->jenis_kejadian }}</td>
                            <td scope="row" class="text-center">{{ $item->nama_korban }}</td>
                            <td scope="row" class="text-center">{{ $item->nik_korban }}</td>
                            <td scope="row" class="text-center">{{ $item->perusahaan_korban }}</td>
                            <td scope="row" class="text-center">{{ $item->bagian_korban }}</td>
                            {{-- <td>
                            @foreach ($item->faktas as $data)
                            <ul>
                                <li>{{ $data->keterangan_fakta }}</li>
                            </ul>
                            @endforeach
                            </td>
                            <td>
                            @foreach ($item->saksis as $data2)
                            <ul>
                                <li>
                                    <p>Nama : {{ $data2->nama_saksi }}</p>
                                    <p>Nik : {{ $data2->nik_saksi }}</p>
                                </li>
                            </ul>
                            @endforeach
                            </td> --}}
                            <td scope="row" class="text-center">
                                <div class="hstack gap-3 fs-15 d-flex justify-content-center">
                                    @if (in_array('hs_edit_lk', $permissions))
                                    <a href="{{ route('edit-laporan-kejadian', ['lk_id'=>$item->lk_id]) }}" class="btn btn-outline-warning"><i class="ri-pencil-fill"></i></a>
                                    @endif
                                    @if (in_array('hs_hapus_lk', $permissions))
                                    <form action="{{ route('hapus-kejadian', ['lk_id'=>$item->lk_id]) }}" method="post">
                                        <button class="btn btn-outline-danger" onclick="return confirm('Apakah anda yakin ingin menghapus data BA Laporan Kejadian ini?');" type="submit"><i class="ri-delete-bin-2-line"></i></button>
                                        @csrf
                                        @method('delete')
                                    </form>
                                    @endif
                                    <a href="{{ route('printpdf.laporankejadian',$item->lk_id) }}" class="btn btn-outline-success"><i class=" ri-file-download-line"></i></a>
                                </div>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="11">
                                    <div class="d-flex justify-content-center">
                                        <img src="/found.png" style="width: 200px; margin: 50px;">
                                    </div>
                                    <div class="text-center">
                                        <h1>Data tidak ditemukan</h1>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
            </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Validasi Data Input Export Excel
    $("#excelkejadian").submit(function() {
            // Mengambil data
            var startDate = $("#startDate").val();
            var endDate = $("#endDate").val();

            // Validasi
            if(startDate == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'From export wajib di isi, untuk export data Berita Acara Laporan Kejadian dari tanggal berapa ke dalam report excel',
                    icon: 'warning',
                });
                return false;
            }else if(endDate == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'To export wajib di isi, untuk export data Berita Acara Laporan Kejadian sampai tanggal berapa ke dalam report excel',
                    icon: 'warning',
                });
                return false;
            }
    })
</script>

<script>
    $(document).ready(function () {
        $('#bakejadian').DataTable();
    });
</script>
@endpush