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

    <script>
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

        function removeInputGroup(btn) {
            btn.parentElement.remove();
            updateTotalQty(); // Update total qty when an input group is removed
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
@endsection
