@extends('system5r.layouts.base')

@section('title', 'Approval Penilaian')

@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h3 class="mb-3">APPROVAL PENILAIAN 5R</h3>
            <table class="table table-hover">
                <thead>
                    <tr class="pas-background-color text-white">
                        <th>TAHUN</th>
                        <th>GROUP</th>
                        <th>PERIODE</th>
                        <th>STATUS</th>
                        <th>KOMPLAIN DEADLINE</th>
                        <th>AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $d)
                    <tr>
                        <td>{{ $d->periode->jadwal->tahun }}</td>
                        <td>{{ $d->group->nama_group }}</td>
                        <td>{{ $d->periode->nama_periode }}</td>
                        <td>
                            @if($d->status == 'waiting')
                            <span class="badge badge-soft-primary">Waiting Approval</span>
                            @elseif($d->status == 'complaining')
                            <span class="badge badge-soft-warning">Complaining</span>
                            @elseif($d->status == 'solved')
                            <span class="badge badge-soft-secondary">Complain Solved</span>
                            @else
                            <span class="badge badge-soft-success">Approved</span>
                            @endif
                        </td>
                        <td>
                            {{ formatTanggalIndonesia2($d->komplain_deadline) }}
                        </td>
                        <td style="width: 5%">
                            <a href="{{ route('5r-system.approval-penilaian.view', ['id_jawaban_group' => encrypt($d->id_jawaban_group)]) }}" class="btn btn-sm btn-primary btn-icon waves-effect waves-light">
                                <i class="mdi mdi-eye-arrow-right"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script>
        $('.table').DataTable()
    </script>
@endpush
