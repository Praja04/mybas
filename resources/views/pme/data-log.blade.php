@extends('layouts.base')

@section('content')

    <div class="container-fluid">

        <!--begin::Row-->
        <div class="row">

            <div class="col-lg-12">
                <div class="card card-custom">
                    <div class="card-header flex-wrap border-0 pt-6 pb-0">
                        <div class="card-title">
                            <h3 class="card-label">PME
                                <span class="d-block text-muted pt-2 font-size-sm">Data Log</span>
                            </h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2">
                                <input placeholder="Select date" readonly required type="text" class="form-control" name="filter_date" id="filter-date">
                            </div>
                            <div class="col-md-2">
                                <select class="form-control" name="filter_shift" id="filter-shift">
                                    <option value="ns1">NS 1</option>
                                    <option value="ns2">NS 2</option>
                                    <option value="ns3">NS 3</option>
                                    <option value="ss1">SS 1</option>
                                    <option value="ss2">SS 2</option>
                                    <option value="ss3">SS 3</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ url('/pme/data-log/generate') }}/@if(isset($_GET['filter_date'])){{ $_GET['filter_date'] }}@else{{ date('Y-m-d') }}@endif" target="_blank" class="btn btn-info"><i class="flaticon flaticon2-writing"></i>Generate Datalog</a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ url('/pme/data-log/generate2') }}/@if(isset($_GET['filter_date'])){{ $_GET['filter_date'] }}@else{{ date('Y-m-d') }}@endif/@if(isset($_GET['filter_shift'])){{ $_GET['filter_shift'] }}@else{{ 'ns1' }}@endif" target="_blank" class="btn btn-info"><i class="flaticon flaticon2-writing"></i>Generate Datalog2</a>
                            </div>
                        </div>
                        <hr />
                        <div class="bg-diagonal bg-diagonal-primary h-40px">
                            <h3 class="position-relative text-white mt-2 ml-5">SUMMARY</h3>
                        </div>
                        <div id="summary" class="my-5">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th colspan="5" class="bg-dark text-white">Listrik</th>
                                                <th colspan="2" class="bg-dark text-white">Proporsional</th>
                                                <th class="bg-dark text-white"></th>
                                            </tr>
                                            <tr class="bg-dark text-white">
                                                <th width="5px">No</th>
                                                <th width="10px">Dept</th>
                                                <th width="11px" style="white-space: nowrap">Source ID</th>
                                                <th width="11px" style="white-space: nowrap">Quantiti ID</th>
                                                <th>Nilai</th>
                                                <th>%</th>
                                                <th>Nilai</th>
                                                <th>DIFF</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $total = 0;
                                                $total_persentase = 0;
                                                $total_proporsional = 0;
                                            @endphp

                                            @foreach ($data_logs2_listrik as $key => $data)

                                                @if($data->dept != 'ALL')
                                                    @php
                                                        $total = $total+$data->Value;
                                                        $total_persentase = $total_persentase+($data->Value/$all_count_listrik)*100;
                                                        $total_proporsional = $total_proporsional+($data->Value/$all_count_listrik)*$all_count_listrik_proporsional;
                                                    @endphp
                                                @endif
                                                    <tr>
                                                        <td>{{ $key+1 }}</td>
                                                        <td><strong>{{ $data->dept }}</strong></td>
                                                        <td><strong>{{ $data->SourceID }}</strong></td>
                                                        <td><strong>{{ $data->QuantityID }}</strong></td>
                                                        <td><strong>{{ number_format($data->Value, 2, ',', '.') }}</strong></td>
                                                        <td class="bg-warning text-dark"><strong>@if($data->dept != 'ALL') {{ round(($data->Value/$all_count_listrik)*100, 2) }}% @endif</strong></td>
                                                        <td class="bg-warning text-dark"><strong>@if($data->dept != 'ALL') {{ number_format(($data->Value/$all_count_listrik)*$all_count_listrik_proporsional, 2, ',', '.') }} @endif</strong></td>
                                                        <td><strong>@if($data->dept != 'ALL') {{ number_format((($data->Value/$all_count_listrik)*$all_count_listrik_proporsional)-$data->Value, 2, ',', '.') }} @endif</strong></td>
                                                    </tr>
                                                @if($key+1 == count($data_logs2_listrik))
                                                    <tr>
                                                        <th colspan="4"><strong>Total</strong></th>
                                                        <th>{{ number_format($total, 2, ',', '.') }}</th>
                                                        <th class="bg-primary text-white">{{  round((($all_count_listrik_proporsional-$all_count_listrik)/$all_count_listrik)*100, 1) }}%</th>
                                                        <th class="bg-warning text-dark">{{ number_format($total_proporsional, 2, ',', '.') }}</th>
                                                        <th class="bg-primary text-white">{{ number_format($all_count_listrik_proporsional-$all_count_listrik, 2, ',', '.') }}</th>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    @php
                                        $steam_total = 0;
                                    @endphp

                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th colspan="5" class="bg-dark text-white">Steam Incoming</th>
                                            </tr>
                                            <tr class="bg-dark text-white">
                                                <th width="5px">No</th>
                                                <th width="10px">Dept</th>
                                                <th width="11px" style="white-space: nowrap">Source ID</th>
                                                <th width="11px" style="white-space: nowrap">Quantiti ID</th>
                                                <th>Nilai</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $total = 0;
                                            @endphp

                                            @foreach ($data_logs2_steam_incoming as $key => $data)
                                                @php
                                                    $total = $total+$data->Value;
                                                @endphp
                                                <tr>
                                                    <td>{{ $key+1 }}</td>
                                                    <td><strong>{{ $data->dept }}</strong></td>
                                                    <td><strong>{{ $data->SourceID }}</strong></td>
                                                    <td><strong>{{ $data->QuantityID }}</strong></td>
                                                    <td><strong>{{ number_format($data->Value, 2, ',', '.') }}</strong></td>
                                                </tr>
                                                @if($key+1 == count($data_logs2_steam_incoming))
                                                    <tr>
                                                        <th colspan="4"><strong>Total</strong></th>
                                                        <th>{{ number_format($total, 2, ',', '.') }}</th>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th colspan="5" class="bg-dark text-white">Steam Outgoing</th>
                                                <th colspan="2" class="bg-dark text-white">Proporsional</th>
                                                <th class="bg-dark text-white"></th>
                                            </tr>
                                            <tr class="bg-dark text-white">
                                                <th width="5px">No</th>
                                                <th width="10px">Dept</th>
                                                <th width="11px" style="white-space: nowrap">Source ID</th>
                                                <th width="11px" style="white-space: nowrap">Quantiti ID</th>
                                                <th>Nilai</th>
                                                <th>%</th>
                                                <th>Nilai</th>
                                                <th>DIFF</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $total = 0;
                                                $total_persentase = 0;
                                                $total_proporsional = 0;
                                            @endphp

                                            {{-- {{ dd($data_logs2_steam_outgoing ) }} --}}

                                            @foreach ($data_logs2_steam_outgoing as $key => $data)
                                                @php
                                                    $total = $total+$data->Value;
                                                    $total_persentase = $data->Value == 0 ? 0 : $total_persentase+($data->Value/$all_count_steam)*100;
                                                    $total_proporsional = $data->Value == 0 ? 0 : $total_proporsional+($data->Value/$all_count_steam)*$all_count_steam_proporsional;
                                                @endphp
                                                <tr>
                                                    <td>{{ $key+1 }}</td>
                                                    <td><strong>{{ $data->dept }}</strong></td>
                                                    <td><strong>{{ $data->SourceID }}</strong></td>
                                                    <td><strong>{{ $data->QuantityID }}</strong></td>
                                                    <td><strong>{{ number_format($data->Value, 2, ',', '.') }}</strong></td>
                                                    <td class="bg-warning text-dark"><strong>{{ $data->Value == 0 ? 0 : round(($data->Value/$all_count_steam)*100, 2) }}%</strong></td>
                                                    <td class="bg-warning text-dark"><strong>{{ $data->Value == 0 ? 0 : number_format(($data->Value/$all_count_steam)*$all_count_steam_proporsional, 2, ',', '.') }}</strong></td>
                                                    <td><strong>{{ $data->Value == 0 ? 0 : number_format((($data->Value/$all_count_steam)*$all_count_steam_proporsional)-$data->Value, 2, ',', '.') }}</strong></td>
                                                </tr>
                                                @if($key+1 == count($data_logs2_steam_outgoing))
                                                    <tr>
                                                        <th colspan="4"><strong>Total</strong></th>
                                                        <th>{{ number_format($total, 2, ',', '.') }}</th>
                                                        <th class="bg-warning text-dark">{{ round($total_persentase, 0) }}%</th>
                                                        <th class="bg-warning text-dark">{{ number_format($total_proporsional, 2, ',', '.') }}</th>
                                                        <th>{{ number_format($all_count_steam_proporsional-$all_count_steam, 2, ',', '.') }}</th>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="bg-diagonal bg-diagonal-primary h-40px">
                            <h3 class="position-relative text-white mt-2 ml-5">DETAIL</h3>
                        </div>
                        <div id="detail" class="my-5">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr class="bg-dark text-white">
                                        <th width="5px">No</th>
                                        <th>Power Meter</th>
                                        <th>Source ID</th>
                                        <th>Quantity ID</th>
                                        <th>Dept</th>
                                        <th>Nilai</th>
                                        <th>Nilai Min</th>
                                        <th>Nilai Max</th>
                                    </tr>
                                </thead>
                                @php
                                    $dummy_dept = '';
                                @endphp

                                @php
                                    $count = 0;
                                    $no = 1;
                                @endphp

                                @foreach ($data_logs as $key => $data)
                                    
                                    @if($data->dept != $dummy_dept)
                                        @if($key != 0)
                                            <tr class="bg-secondary">
                                                <td colspan="7"><strong>Total</strong></td>
                                                <td class="text-right"><strong>{{ number_format($count, 2, ',', '.') }}</strong></td>
                                            </tr>
                                        @endif
                                        <tr class="bg-dark text-white">
                                            <td colspan="8"><strong>{{ $data->dept }}</strong></td>
                                        </tr>
                                        @php
                                            $count = 0;
                                            $no = 1;
                                        @endphp
                                    @endif

                                    <tr>
                                        <td>{{ $no }}</td>
                                        <td>{{ $data->name }}</td>
                                        <td>{{ $data->source_id }}</td>
                                        <td>{{ $data->quantity_id }}</td>
                                        <td>{{ $data->dept }}</td>
                                        <td class="text-right">{{ number_format($data->value, 2, ',', '.') }}</td>
                                        <td class="text-right">{{ number_format($data->min_value, 2, ',', '.') }}</td>
                                        <td class="text-right">{{ number_format($data->max_value, 2, ',', '.') }}</td>
                                    </tr>

                                    @php
                                        $count = $count + $data->value;
                                        $dummy_dept = $data->dept;
                                        $no++;
                                    @endphp

                                    @if(count($data_logs) == $key+1)
                                    <tr class="bg-secondary">
                                        <td colspan="7"><strong>Total</strong></td>
                                        <td class="text-right"><strong>{{ number_format($count, 2, ',', '.') }}</strong></td>
                                    </tr>
                                    @endif

                                @endforeach
                            </table>
                        </div>
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

        @isset($_GET['filter_shift'])
        $('#filter-shift').val("{{ $_GET['filter_shift'] }}")
        @endisset
        $('#filter-date').datepicker({
            format: 'yyyy-mm-dd',
            todayHighlight: true,
            autoclose: true
        });
        $('#filter-date,#filter-shift').on('change', function () {
            var current_url = '{!! url()->current() !!}';
            var next_url = current_url+'/?filter_date='+$('#filter-date').val()+'&filter_shift='+$('#filter-shift').val();
            window.location.href = next_url;
        });
    </script>

@endpush
