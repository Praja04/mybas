@extends('layouts.base')

@section('content')
    <div class="container-fluid">
        <!--begin::Row-->
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Upload Pesanan <span style="color: red">Catering</span></h4>
                </div>
                <div class="card-body">
                    <form id="uploadForm">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label style="font-weight: bold" for="kategori">Kategori</label>
                                    <select class="form-control" id="kategori" name="kategori">
                                        <option value="">Pilih</option>
                                        <option value="staff">Staff</option>
                                        <option value="non-staff">Non Staff</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label style="font-weight: bold" for="tanggalUpload">Tanggal Upload Pesanan</label>
                                    <input type="date" class="form-control" id="tanggalUpload" name="tanggalUpload">
                                </div>

                                <button type="button" class="btn btn-primary mb-2" id="addShiftButton">Tambah
                                    Shift</button>

                                <div class="form-group pt-2" id="shiftSection">
                                    <!-- Shift dan Qty input akan di-append di sini -->
                                </div>

                                <div class="form-group">
                                    <label style="font-weight: bold" for="totalQty">Total Qty</label>
                                    <input type="text" class="form-control" id="totalQty" disabled>
                                </div>


                            </div>
                        </div>

                        <button type="submit" class="btn btn-success w-100">Simpan</button>
                    </form>
                </div>
            </div>
        </section>
        <!--end::Row-->
    </div>

    <div class="container-fluid mt-5">
        <!-- Dashboard Statistics -->
        <div class="row mb-5">
            <div class="col-md-3">
                <div class="card bg-light-primary border-0">
                    <div class="card-body">
                        <h6 class="text-primary">Total Scan Hari Ini</h6>
                        <h2 class="font-weight-bolder">{{ $summary['total_scan'] }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-light-success border-0">
                    <div class="card-body">
                        <h6 class="text-success">Total Quota (Pesanan)</h6>
                        <h2 class="font-weight-bolder">{{ $summary['total_quota'] }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-light-danger border-0">
                    <div class="card-body">
                        <h6 class="text-danger">Lebihan Porsi</h6>
                        <h2 class="font-weight-bolder">{{ $summary['lebihan'] }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-light-info border-0">
                    <div class="card-body">
                        <h6 class="text-info">Staff</h6>
                        <h2 class="font-weight-bolder">{{ $summary['per_kategori']['staff'] }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-light-warning border-0">
                    <div class="card-body">
                        <h6 class="text-warning">Non-Staff</h6>
                        <h2 class="font-weight-bolder">{{ $summary['per_kategori']['non-staff'] }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-white">Log Scan Jatah Makan - Hari Ini</h5>
                <span class="badge badge-light">{{ date('d M Y') }}</span>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-hover" id="table-kantin">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>NIK</th>
                            <th>Kategori</th>
                            <th>Waktu Scan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($dataScan as $key => $item)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $item->nama }}</td>
                                <td>{{ $item->nik }}</td>
                                <td><span class="badge badge-info">{{ $item->kategori }}</span></td>
                                <td>{{ date('H:i:s', strtotime($item->waktu)) }}</td>
                                <td>
                                    <span class="text-success"><i class="fa fa-check-circle"></i> Berhasil</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Initialize DataTables
                if (!$.fn.DataTable.isDataTable('#table-kantin')) {
                    $('#table-kantin').DataTable({
                        responsive: true,
                        pageLength: 10,
                        order: [[4, 'desc']], 
                        language: {
                            search: "Global Search:",
                            lengthMenu: "Tampil _MENU_ data",
                            info: "Data _START_ - _END_ dari _TOTAL_",
                            paginate: {
                                previous: "Prev",
                                next: "Next"
                            }
                        }
                    });
                }

                // Existing Shift Logic
                document.getElementById('addShiftButton').addEventListener('click', function() {
                    var shiftSection = document.getElementById('shiftSection');
                    var newInputGroup = document.createElement('div');
                    newInputGroup.classList.add('d-flex', 'mb-2');

                    newInputGroup.innerHTML = `
                    <select class="form-control mr-2" name="shift[]">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                    </select>
                    <input type="number" class="form-control mr-2 qty-input" name="qty[]" placeholder="Qty" oninput="updateTotalQty()">
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeInputGroup(this)">Hapus</button>
                `;
                    shiftSection.appendChild(newInputGroup);
                });
            });

            function removeInputGroup(btn) {
                btn.parentElement.remove();
                updateTotalQty();
            }

            function updateTotalQty() {
                var qtyInputs = document.querySelectorAll('.qty-input');
                var totalQty = 0;
                qtyInputs.forEach(function(input) {
                    var qty = parseInt(input.value) || 0;
                    totalQty += qty;
                });
                document.getElementById('totalQty').value = totalQty;
            }
        </script>
    @endpush
@endsection
