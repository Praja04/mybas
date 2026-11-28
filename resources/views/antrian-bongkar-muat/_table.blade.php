<div class="table-responsive">
    <table class="table table-head-custom table-vertical-center">
        <thead>
            <tr class="text-left text-uppercase">
                <th style="min-width: 100px">No. Antrian</th>
                <th style="min-width: 100px">Status</th>
                <th style="min-width: 120px">Daftar</th>
                <th style="min-width: 120px">Selesai</th>
                <th class="text-right" style="min-width: 80px">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $antrian)
            <tr>
                <td>
                    <span class="text-dark-75 font-weight-bolder d-block font-size-lg">{{ $antrian->nomor_antrian }}</span>
                </td>
                <td>
                    @if($antrian->status == 'waiting')
                        <span class="label label-lg label-light-warning label-inline">Menunggu</span>
                    @elseif($antrian->status == 'called')
                        <span class="label label-lg label-light-primary label-inline">Dipanggil</span>
                    @elseif($antrian->status == 'serving')
                        <span class="label label-lg label-light-success label-inline">Proses</span>
                    @elseif($antrian->status == 'skipped')
                        <span class="label label-lg label-light-danger label-inline">Dilewati</span>
                    @elseif($antrian->status == 'completed')
                        <span class="label label-lg label-light-info label-inline">Selesai</span>
                    @endif
                </td>
                <td>
                    <span class="text-muted font-weight-bold">{{ $antrian->created_at->format('H:i:s') }}</span>
                </td>
                <td>
                    <span class="text-muted font-weight-bold">
                        {{ $antrian->waktu_selesai ? \Carbon\Carbon::parse($antrian->waktu_selesai)->format('H:i:s') : '-' }}
                    </span>
                </td>
                <td class="text-right">
                    @if($antrian->status != 'completed' && $antrian->status != 'skipped')
                        <form action="{{ route('antrian-bongkar-muat.panggil-ulang', $antrian->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-icon btn-light-warning btn-sm mr-1" title="Panggil Ulang">
                                <i class="flaticon2-speaker"></i>
                            </button>
                        </form>
                    @endif

                    @if($antrian->status != 'completed' && $antrian->status != 'skipped')
                        <form action="{{ route('antrian-bongkar-muat.lewati', $antrian->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-icon btn-light-danger btn-sm" title="Lewati" onclick="return confirm('Lewati antrian ini?')">
                                <i class="flaticon2-cross"></i>
                            </button>
                        </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center text-muted">Belum ada antrian.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
