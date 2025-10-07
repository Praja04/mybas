@extends('layouts.base')

@push('styles')
    <style type="text/css">
        .table tr td, .table tr th {
            padding: 5px;
            border-color: #666
        }
    </style>
@endpush

@section('content')

    <div class="container-fluid">
        <!--begin::Row-->
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-custom">
                    <div class="card-header flex-wrap border-0 pt-6 pb-0">
                        <div class="card-title">
                            <h3 class="card-label">PME
                                <span class="d-block text-muted pt-2 font-size-sm">Data Log 2</span>
                            </h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2">
                                <input placeholder="Select date" readonly required type="text" class="form-control" name="filter_date" id="filter-date">
                            </div>
                            <div class="col-md-2">
                                <a href="{{ url('/pme/data-log/generate') }}/@if(isset($_GET['filter_date'])){{ $_GET['filter_date'] }}@else{{ date('Y-m-d') }}@endif" target="_blank" class="btn btn-info"><i class="flaticon flaticon2-writing"></i>Generate Datalog</a>
                            </div>
                        </div>
                        
                        <hr />

                        <table class="table table-bordered">
                            <thead>
                                <tr class="bg-dark text-white">
                                    <th>No</th>
                                    <th>Power Meter</th>
                                    <th>Source ID</th>
                                    <th>Quantity ID</th>
                                    <th>Dept</th>
                                    <th>SHIFT</th>
                                    <th>Nilai</th>
                                    <th>Nilai Min</th>
                                    <th>Nilai Max</th>				
                                </tr>
                            </thead>
                            <tbody>
                                @php $max_value_before = 0 @endphp
                                @php $power_meter_before = '' @endphp

                                @foreach ($data_logs as $key => $item)
                                    <tr style="background-color:@if( $item->date != $date) #aaa @else @if($item->shift == 1) #ccc @elseif($item->shift == 2) #ddd @else #eee @endif @endif">
                                        <td>{{ $key+1 }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->source_id }}</td>
                                        <td>{{ $item->quantity_id }}</td>
                                        <td>{{ $item->dept }}</td>
                                        <td>
                                            @if( $item->date != $date)
                                            -
                                            @endif
                                            {{ $item->shift }}
                                        </td>
                                        <td class="text-right">{{ number_format($item->value, 2, ',', '.') }}</td>
                                        <td class="text-right">
                                            <span class="@if($item->name == $power_meter_before) @if($item->min_value != $max_value_before) bg-danger rounded p-1 @endif @endif">
                                                {{ number_format($item->min_value, 2, ',', '.') }}
                                            </span>
                                        </td>
                                        <td class="text-right">{{ number_format($item->max_value, 2, ',', '.') }}</td>
                                    </tr>

                                    @php $max_value_before = $item->max_value @endphp
                                    @php $power_meter_before = $item->name @endphp
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
    <script type="text/javascript">
        $('#filter-date').val("{{ date('Y-m-d') }}")

        @isset($_GET['filter_date'])
        $('#filter-date').val("{{ $_GET['filter_date'] }}")
        @endisset
        
        $('#filter-date').datepicker({
            format: 'yyyy-mm-dd',
            todayHighlight: true,
            autoclose: true
        });

        $('#filter-date').on('change', function () {
            var current_url = '{!! url()->current() !!}';
            var next_url = current_url+'/?filter_date='+$('#filter-date').val();
            window.location.href = next_url;
        });

    </script>
@endpush
