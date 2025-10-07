@extends('hr-connect.layouts.base')

@push('styles')
<link href="{{ asset('assets/velzon/css/calendar-gc.min.css') }}" rel="stylesheet" type="text/css" />
<style>
.gc-calendar {
    padding: 0.5rem;
}

.gc-calendar .gc-calendar-header button.prev,
.gc-calendar .gc-calendar-header button.next {
    padding: 2px 10px;
    margin-right: 5px;
}

.gc-calendar .gc-calendar-header .gc-calendar-month-year {
    font-size: 24px;
    min-width: 180px;
}

.gc-calendar table.calendar th,
.gc-calendar table.calendar td {
    padding: 15px;
}

/* Mengurangi ukuran teks pada kalender */
.gc-calendar table.calendar th,
.gc-calendar table.calendar td .day-number {
    font-size: 18px;
}

#calendar {
    width: 100%;
    max-width: 400px; 
}
fieldset legend {
    border-bottom: 1px solid #ccc;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                {{-- <div class="col-lg-3">
                    <div class="pe-2 me-n1 mb-3" data-simplebar style="height: 430px">
                        <div class="card mb-3">                        
                            <div class="card-body">   
                                <fieldset>
                                    <legend>Karyawan Masuk</legend>
                                    <div class="d-flex mb-3">                                
                                        <div class="flex-grow-1">
                                            <i class="mdi mdi-checkbox-blank-circle me-2 text-primary"></i>
                                            <span class="fw-medium">{{ \Carbon\Carbon::parse(now())->format('d F Y') }} </span>
                                        </div>                            
                                    </div>            
                                    @if($karyawan_masuk->count() === 0)
                                    @else                
                                    <h5 class="card-title text-center fs-16 mb-3"> <b>{{ $karyawan_masuk->count() }} orang</b></h5>  
                                    @endif                         
                                    <marquee behavior="scroll" direction="left" scrollamount="3">
                                        @forelse($karyawan_masuk as $km)
                                        {{ $km->nama }} ({{ $km->nik }})&nbsp;&nbsp;&nbsp;
                                        @empty
                                        Belum ada karyawan masuk
                                        @endforelse
                                    </marquee>
                                </fieldset>                         
                            </div>                    
                        </div>
                        <div class="card mb-3">                        
                            <div class="card-body">   
                                <fieldset>
                                    <legend>Karyawan Keluar</legend>
                                    <div class="d-flex mb-3">                                
                                        <div class="flex-grow-1">
                                            <i class="mdi mdi-checkbox-blank-circle me-2 text-danger"></i>
                                            <span class="fw-medium">{{ \Carbon\Carbon::parse(now())->format('d F Y') }} </span>
                                        </div>                            
                                    </div>                            
                                    @if($karyawan_masuk->count() === 0)
                                    @else                
                                    <h5 class="card-title text-center fs-16 mb-3"> <b>{{ $karyawan_masuk->count() }} orang</b></h5>   
                                    @endif                           
                                    <marquee behavior="scroll" direction="left" scrollamount="3">
                                        @forelse($karyawan_keluar as $km)
                                        {{ $km->nama }} ({{ $km->nik }})&nbsp;&nbsp;&nbsp;
                                        @empty
                                        Belum ada karyawan keluar
                                        @endforelse
                                    </marquee>
                                </fieldset>                         
                            </div>                    
                        </div>
                    </div>
                </div> --}}
                <div class="col-lg-8">
                    <div class="card p-3">
                        <div class="card-body">
                            <div id="calendar"></div>
                        </div>
                    </div>
                </div><!-- end col -->
            </div>
            <!--end row-->

            <div class="modal fade" id="showModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0">
                        <div class="modal-header p-3 bg-soft-info">
                            <h5 class="modal-title" id="modal-title"></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                        </div>
                        <div class="modal-body p-4" style="overflow-y: auto; max-height: 50vh;">
                            <form class="needs-validation" name="event-form" id="form-event" novalidate>
                                <div class="row event-form">
                                    <div class="col-12">
                                        <div id="containerModal"></div>
                                    </div>
                                </div>
                                <!--end row-->
                                <div class="hstack gap-2 justify-content-end">
                                    <button type="button" class="btn btn-soft-danger" data-bs-dismiss="modal" aria-hidden="true"><i class="ri-close-line align-bottom"></i> Close</button>
                                </div>
                            </form>
                        </div>
                    </div> <!-- end modal-content-->
                </div> <!-- end modal dialog-->
            </div> <!-- end modal-->
        </div>
    </div> 
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/velzon/js/pages/calendar-gc.min.js') }}"></script>
<script>
let events = [
    @php
        $unique_dates_in = $karyawan_masuk->unique('tanggal_masuk');
        $unique_dates_out = $karyawan_keluar->unique('tanggal_keluar');
    @endphp

    @foreach($unique_dates_in as $date)
        {
            date: new Date("{{ \Carbon\Carbon::parse($date->tanggal_masuk)->format('Y-m-d') }}"),
            eventName: "{{ $karyawan_masuk->where('tanggal_masuk', $date->tanggal_masuk)->count() }} orang",
            className: "btn btn-sm btn-primary",
            onclick: function(e, data) { 
                let id = "{{ \Carbon\Carbon::parse($date->tanggal_masuk)->format('Y-m-d') }}";
                $.ajax({
                    type: "GET",
                    url: "/hr-connect/report/getDataReportIn/" + id,
                    success: function(res){
                        $("#containerModal").empty();
                        $("#modal-title").text("Karyawan Masuk");

                        res.forEach(function(item) {
                            $("#containerModal").append(`
                                <div class="report-item">
                                    <ul>
                                        <li>${item.nama} (${item.nik})</li>
                                    </ul>
                                </div>
                            `);
                        });
                    },
                    error: function(xhr){
                        alert(xhr.responseText);
                    }
                }); 
                
                $("#showModal").modal("show"); 
            },
            dateColor: "blue"
        },
    @endforeach
    
    @foreach($unique_dates_out as $date)
        {
            date: new Date("{{ \Carbon\Carbon::parse($date->tanggal_keluar)->format('Y-m-d') }}"),
            eventName: "{{ $karyawan_keluar->where('tanggal_keluar', $date->tanggal_keluar)->count() }} orang",
            className: "btn btn-sm btn-danger",
            onclick: function(e, data) { 
                let id = "{{ \Carbon\Carbon::parse($date->tanggal_keluar)->format('Y-m-d') }}";
                $.ajax({
                    type: "GET",
                    url: "/hr-connect/report/getDataReportOut/" + id,
                    success: function(res){
                        $("#containerModal").empty();
                        $("#modal-title").text("Karyawan Keluar");

                        res.forEach(function(item) {
                            $("#containerModal").append(`
                                <div class="report-item">
                                    <ul>
                                        <li>${item.nama} (${item.nik})</li>
                                    </ul>
                                </div>
                            `);
                        });
                    },
                    error: function(xhr){
                        alert(xhr.responseText);
                    }
                }); 

                $("#showModal").modal("show"); 
            },
            dateColor: "blue"
        },
    @endforeach
];
$("#calendar").calendarGC({
    dayNames: [
        'Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'
    ],
    events: events
});
</script>
@endpush