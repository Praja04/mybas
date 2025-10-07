@extends('layouts.base')

@section('content')
    <div class="container-fluid">

        <!--begin::Row-->
        <div class="row">

            <div class="col-lg-12">
                <!--begin::Advance Table Widget 4-->
                <div class="card card-custom card-stretch gutter-b">
                    <!--begin::Header-->
                    <div class="card-header border-0 py-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">Edit Jumlah Pesanan
                            </span>
                        </h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ url('/ecafesedaap/update-jumlah-pesanan') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="exampleSelectl">Tanggal Update Pesanan</label>
                                <input type="date" name="tanggal_pesan" class="form-control" required id="date">
                            </div>

                            <div class="row">
                                <!-- Kolom Kiri untuk Staff -->
                                <div class="col-md-6">
                                    <h3>Staff</h3>
                                    <input type="hidden" name="kategori[staff]" value="staff">

                                    <!-- Input Shift dan Jumlah untuk Staff -->
                                    @for ($i = 1; $i <= 3; $i++)
                                        <div class="form-group">
                                            <label for="shift{{ $i }}-staff">Shift
                                                {{ $i }}</label>
                                            <input type="hidden" name="shift[staff][{{ $i }}]"
                                                value="{{ $i }}">
                                            <input type="number" class="form-control"
                                                name="shift_qty[staff][{{ $i }}]" placeholder="Jumlah Porsi"
                                                id="shift{{ $i }}-staff">
                                        </div>
                                    @endfor

                                    <div class="form-group">
                                        <label>Total Keseluruhan Staff</label>
                                        <input type="number" id="sum_shift_qty" class="form-control" disabled>
                                    </div>
                                </div>

                                <!-- Kolom Kanan untuk Non-Staff -->
                                <div class="col-md-6">
                                    <h3>Non-Staff</h3>
                                    <input type="hidden" name="kategori[non-staff]" value="non-staff">

                                    <!-- Input Shift dan Jumlah untuk Non-Staff -->
                                    @for ($i = 1; $i <= 3; $i++)
                                        <div class="form-group">
                                            <label for="shift{{ $i }}-non-staff">Shift
                                                {{ $i }}</label>
                                            <input type="hidden" name="shift[non-staff][{{ $i }}]"
                                                value="{{ $i }}">
                                            <input type="number" class="form-control"
                                                name="shift_qty[non-staff][{{ $i }}]" placeholder="Jumlah Porsi"
                                                id="shift{{ $i }}-non-staff">
                                        </div>
                                    @endfor

                                    <div class="form-group">
                                        <label>Total Keseluruhan Non Staff</label>
                                        <input type="number" id="sum_non_shift_qty" class="form-control" disabled>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg">Update</button>

                        </form>
                    </div>
                </div>


                <!--end::Body-->
            </div>
            <!--end::Advance Table Widget 4-->
        </div>
    </div>
    <!--end::Row-->
    <!--end::Dashboard-->
    @push('scripts')
        <script>
            function hitungTotalShift() {
                var total = 0;

                $('input[name^="shift_qty[staff]"]').each(function() {
                    total += parseInt($(this).val()) || 0;
                });

                $('#sum_shift_qty').val(total);
            }

            $(document).on('input', 'input[name^="shift_qty[staff]"]', hitungTotalShift);

            function hitungTotalNonShift() {
                var total = 0;

                $('input[name^="shift_qty[non-staff]"]').each(function() {
                    total += parseInt($(this).val()) || 0;
                });

                $('#sum_non_shift_qty').val(total);
            }

            $(document).on('input', 'input[name^="shift_qty[non-staff]"]', hitungTotalNonShift);
        </script>
        <script>
            $(document).ready(function() {
                $("#date").change(function() {
                    var selectedDate = $(this).val();
                    var cautionFoodImageUrl = "{{ asset('assets/foto/alert/caution-food.png') }}";

                    $.ajax({
                        url: "/ecafesedaap/edit-jumlah-pesanan",
                        method: "GET",
                        data: {
                            date: selectedDate
                        },
                        success: function(response) {
                            if (response.length === 0) {
                            Swal.fire({
                                imageUrl: cautionFoodImageUrl,
                                imageWidth: 120,
                                title: 'Informasi',
                                text: 'Data pesanan pada tanggal ' + selectedDate + ' belum ada.'
                            });
                            return; 
                        }

                            var sumShiftQtyStaff = 0;
                            var sumShiftQtyNonStaff = 0;

                            // Loop through the response data for staff
                            for (var i = 1; i <= 3; i++) {
                                var staffData = response.find(item => item.shift === i && item
                                    .kategori === 'staff');
                                if (staffData) {
                                    // Update the input field for staff
                                    $("#shift" + i + "-staff").val(staffData.jumlah);
                                    sumShiftQtyStaff += staffData.jumlah;
                                }
                            }

                            // Loop through the response data for non-staff
                            for (var i = 1; i <= 3; i++) {
                                var nonStaffData = response.find(item => item.shift === i && item
                                    .kategori === 'non-staff');
                                if (nonStaffData) {
                                    // Update the input field for non-staff
                                    $("#shift" + i + "-non-staff").val(nonStaffData.jumlah);
                                    sumShiftQtyNonStaff += nonStaffData.jumlah;
                                }
                            }

                            // Update the total keseluruhan input fields
                            $("#sum_shift_qty").val(sumShiftQtyStaff);
                            $("#sum_non_shift_qty").val(sumShiftQtyNonStaff);
                        },
                        error: function(xhr, status, error) {
                            console.error("Error: " + error);
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
