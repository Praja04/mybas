@extends('hr-connect.layouts.base')

@push('styles')
    <link href="{{ asset('assets/velzon/css/calendar-gc.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .gc-calendar {
            padding: 1rem;
            background-color: #fff;
            border-radius: 8px;
        }

        .gc-calendar .gc-calendar-header button.prev,
        .gc-calendar .gc-calendar-header button.next {
            padding: 4px 12px;
            margin-right: 5px;
            background-color: #f3f6f9;
            border: 1px solid #e4e6ef;
            border-radius: 4px;
            color: #3f4254;
            transition: all 0.3s ease;
        }

        .gc-calendar .gc-calendar-header button.prev:hover,
        .gc-calendar .gc-calendar-header button.next:hover {
            background-color: #e4e6ef;
        }

        .gc-calendar .gc-calendar-header .gc-calendar-month-year {
            font-size: 20px;
            font-weight: 600;
            min-width: 200px;
            color: #181c32;
        }

        .gc-calendar table.calendar th {
            padding: 12px;
            font-weight: 600;
            color: #a1a5b7;
            text-transform: uppercase;
            font-size: 0.85rem;
        }

        .gc-calendar table.calendar td {
            padding: 15px;
            vertical-align: top;
            height: 100px;
        }

        .gc-calendar table.calendar td .day-number {
            font-size: 16px;
            font-weight: 500;
            color: #3f4254;
        }

        .gc-calendar table.calendar td.today {
            background-color: #f1faff;
        }

        .gc-calendar .event {
            margin-top: 5px;
            display: block;
            width: 100%;
            text-align: left;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
        }

        #calendar {
            width: 100%;
        }

        /* Modal Styling */
        .report-item ul {
            list-style: none;
            padding-left: 0;
            margin-bottom: 0;
        }

        .report-item ul li {
            padding: 8px 12px;
            border-bottom: 1px solid #f3f6f9;
            font-size: 0.95rem;
        }

        .report-item ul li:last-child {
            border-bottom: none;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm border-0">
                    <div class="card-header border-bottom p-4 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0" style="font-weight: 600;">
                            <i class="ri-calendar-event-line text-primary me-2"></i> Report Kalender Karyawan
                        </h5>
                        <div class="d-flex gap-2 align-items-center" style="font-size: 0.85rem;">
                            <span class="badge bg-soft-primary text-primary"><i
                                    class="mdi mdi-checkbox-blank-circle me-1"></i> Masuk</span>
                            <span class="badge bg-soft-danger text-danger"><i
                                    class="mdi mdi-checkbox-blank-circle me-1"></i> Keluar</span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="showModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header p-3 bg-light">
                        <h5 class="modal-title fw-bold" id="modal-title"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                    </div>
                    <div class="modal-body p-0" style="overflow-y: auto; max-height: 50vh;">
                        <div id="containerModal">
                        </div>
                    </div>
                    <div class="modal-footer p-2 bg-light">
                        <button type="button" class="btn btn-sm btn-soft-secondary w-100"
                            data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/velzon/js/pages/calendar-gc.min.js') }}"></script>
    <script>
        $(document).ready(function() {

            // Siapkan Data Event untuk Kalender
            let events = [
                @php
                    $unique_dates_in = $karyawan_masuk->unique('tanggal_masuk');
                    $unique_dates_out = $karyawan_keluar->unique('tanggal_keluar');
                @endphp

                // Event Karyawan Masuk
                @foreach ($unique_dates_in as $date)
                    {
                        date: new Date("{{ \Carbon\Carbon::parse($date->tanggal_masuk)->format('Y-m-d') }}"),
                        eventName: "{{ $karyawan_masuk->where('tanggal_masuk', $date->tanggal_masuk)->count() }} Masuk",
                        className: "event btn-soft-primary text-primary fw-bold border border-primary",
                        onclick: function(e, data) {
                            loadDetailData(
                                "{{ \Carbon\Carbon::parse($date->tanggal_masuk)->format('Y-m-d') }}",
                                'IN');
                        },
                        dateColor: "blue"
                    },
                @endforeach

                // Event Karyawan Keluar
                @foreach ($unique_dates_out as $date)
                    {
                        date: new Date("{{ \Carbon\Carbon::parse($date->tanggal_keluar)->format('Y-m-d') }}"),
                        eventName: "{{ $karyawan_keluar->where('tanggal_keluar', $date->tanggal_keluar)->count() }} Keluar",
                        className: "event btn-soft-danger text-danger fw-bold border border-danger mt-1",
                        onclick: function(e, data) {
                            loadDetailData(
                                "{{ \Carbon\Carbon::parse($date->tanggal_keluar)->format('Y-m-d') }}",
                                'OUT');
                        },
                        dateColor: "red"
                    },
                @endforeach
            ];

            // Inisialisasi Kalender
            $("#calendar").calendarGC({
                dayNames: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
                monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus',
                    'September', 'Oktober', 'November', 'Desember'
                ],
                events: events
            });

            // Reusable AJAX Function untuk Modal
            function loadDetailData(dateStr, type) {
                let urlEndpoint = (type === 'IN') ? '/hr-connect/report/getDataReportIn/' :
                    '/hr-connect/report/getDataReportOut/';
                let titleStr = (type === 'IN') ?
                    '<i class="ri-login-circle-line text-primary me-1"></i> Karyawan Masuk' :
                    '<i class="ri-logout-circle-line text-danger me-1"></i> Karyawan Keluar';

                $.ajax({
                    type: "GET",
                    url: urlEndpoint + dateStr,
                    beforeSend: function() {
                        $("#modal-title").html(titleStr);
                        $("#containerModal").html(
                            '<div class="text-center p-4"><div class="spinner-border text-primary" role="status"></div></div>'
                            );
                        $("#showModal").modal("show");
                    },
                    success: function(res) {
                        $("#containerModal").empty();

                        if (res.length === 0) {
                            $("#containerModal").html(
                                '<div class="text-center p-3 text-muted">Tidak ada data</div>');
                            return;
                        }

                        let htmlContent = '<div class="report-item"><ul>';
                        res.forEach(function(item) {
                            // Menampilkan nama, NIK, dan Divisinya
                            let dept = item.kode_bagian ?
                                `<span class="badge bg-light text-dark ms-1">${item.kode_bagian}</span>` :
                                '';
                            htmlContent +=
                                `<li><span class="fw-medium">${item.nama}</span> <br><small class="text-muted">${item.nik}</small> ${dept}</li>`;
                        });
                        htmlContent += '</ul></div>';

                        $("#containerModal").html(htmlContent);
                    },
                    error: function(xhr) {
                        $("#containerModal").html(
                            '<div class="text-center p-3 text-danger">Gagal memuat data.</div>');
                    }
                });
            }

        });
    </script>
@endpush
