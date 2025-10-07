@extends('layouts.base')

@push('styles')
    <link rel="stylesheet" href="{{ url('/assets/plugins/custom/datatables/datatables.bundle.css') }}">
@endpush

@section('content')
    <div class="container-fluid">

        <!--begin::Row-->
        <div class="row">

            <div class="col-lg-12">
                <div class="card card-custom">
                    <div class="card-header flex-wrap border-0 pt-6 pb-0">
                        <div class="card-title">
                            <h3 class="card-label">Klinik
                                <span class="d-block text-muted pt-2 font-size-sm">Laporan Obat</span>
                            </h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-1">
                                <label for="filter-start-date" class="pt-2">Start Date</label>
                            </div>
                            <div class="col-2">
                                <input id="filter-start-date" type="text" class="form-control form-control-sm date">
                            </div>
                            <div class="col-1">
                                <label for="filter-end-date" class="pt-2">End Date</label>
                            </div>
                            <div class="col-2">
                                <input id="filter-end-date" type="text" class="form-control form-control-sm date">
                            </div>
                        </div>
                        <hr>
                        <table class="table table-bordered table-hover table-thin">
                            <thead>
                                <tr>
                                    <th>NO</th>
                                    <th>OBAT</th>
                                    <th>HARGA</th>
                                    <th>QTY</th>
                                    <th>Sub Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($obat as $key => $o)
                                    @php
                                        $quantity = $o->pemeriksaan->whereBetween('tanggal_pemeriksaan', [isset($_GET['filter_start_date']) ? $_GET['filter_start_date'] : '2021-01-01', isset($_GET['filter_end_date']) ? $_GET['filter_end_date'] : '2021-01-02'])->sum('pivot.quantity');
                                    @endphp
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $o->nama_obat }}</td>
                                        <td class="text-right">Rp. {{ number_format($o->harga, 0, ',', '.') }}</td>
                                        <td class="text-right">{{ $quantity }}</td>
                                        <td class="text-right">Rp. {{ number_format($o->harga * $quantity, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Row-->
        <!--end::Dashboard-->
    </div>
@endsection

@push('scripts')
    <script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script>
        $("#filter-start-date").val(
            "{{ isset($_GET['filter_start_date']) ? $_GET['filter_start_date'] : date('Y-m-') . '1' }}")

        $("#filter-end-date").val("{{ isset($_GET['filter_end_date']) ? $_GET['filter_end_date'] : date('Y-m-') . '29' }}")

        @if (!isset($_GET['filter_start_date']) || !isset($_GET['filter_start_date']))

            window.location.href = "{{ url('/klinik/laporan-obat') }}?filter_start_date=" + $("#filter-start-date").val() +
                "&filter_end_date=" + $("#filter-end-date").val()
        @endif

        $("#filter-start-date, #filter-end-date").on("change", function() {
            window.location.href = "{{ url('/klinik/laporan-obat') }}?filter_start_date=" + $("#filter-start-date")
                .val() + "&filter_end_date=" + $("#filter-end-date").val()
        })

        $(".date").datepicker({
            format: "yyyy-mm-dd",
            autoclose: true
        })
        $('.table').DataTable({
            paging: false
        });
    </script>
@endpush
