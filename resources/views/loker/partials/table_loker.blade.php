<div class="tab-content">
    @foreach ($dashboardData as $label => $data)
        @php $genderKey = ($label == 'Pria') ? 'L' : 'P'; @endphp
        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="tab_content_{{ $genderKey }}"
            role="tabpanel">

            <div class="bas-loker-grid" id="container_{{ $genderKey }}">
                @foreach ($data['lockers'] as $loker)
                    @php
                        $count = $loker['count'];
                        $status = $loker['status'];
                        $kat = strtolower(trim($loker['kategori']));

                        if ($status == 'rusak') {
                            $cardClass = 'bas-loker-rusak';
                            $statusLabel = 'PERBAIKAN';
                        } elseif ($status == 'penuh') {
                            $cardClass = 'bas-loker-penuh';
                            $statusLabel = 'PENUH';
                        } elseif ($count > 0) {
                            $cardClass = 'bas-loker-terisi';
                            $statusLabel = 'TERISI (1/2)';
                        } else {
                            $cardClass = 'bas-loker-kosong';
                            $statusLabel = 'KOSONG';
                        }

                        $namaKategori = strtoupper(str_replace('_', ' ', $kat)) ?: 'NON STAFF';
                    @endphp

                    <div class="loker-wrapper">
                        @php
                            $tooltipText = '';
                            if ($status == 'rusak') {
                                $tooltipText = 'Unit dalam perbaikan. Klik untuk ubah status menjadi aktif.';
                            } elseif ($status == 'penuh') {
                                $tooltipText = 'Unit penuh. Klik untuk lihat detail penghuni.';
                            } elseif ($count > 0) {
                                $tooltipText = 'Terisi 1 orang. Klik untuk detail atau tambah penghuni.';
                            } else {
                                $tooltipText = 'Unit kosong. Belum memiliki penghuni';
                            }
                        @endphp

                        <div class="bas-loker-card {{ $cardClass }}"
                            onclick="showDetail('{{ $genderKey }}', '{{ $loker['no'] }}')" data-toggle="tooltip"
                            data-theme="dark" title="{{ $tooltipText }}" data-no="{{ $loker['no'] }}"
                            data-kategori="{{ $kat }}" data-nik="{{ $loker['nik'] ?? '' }}"
                            data-nama="{{ strtolower($loker['nama'] ?? '') }}">

                            {{-- Status indicator dot --}}
                            <span class="bas-loker-indicator"></span>

                            {{-- Kategori (hanya jika ada isi & bukan rusak) --}}
                            @if ($count > 0 && $status != 'rusak')
                                <div class="bas-loker-kat">{{ $namaKategori }}</div>
                            @else
                                <div class="bas-loker-kat" style="visibility:hidden;">—</div>
                            @endif

                            {{-- Nomor Loker --}}
                            <div class="bas-loker-no">{{ $loker['no'] }}</div>

                            {{-- Badge Status --}}
                            <div class="bas-loker-badge">{{ $statusLabel }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Empty state saat search tidak ketemu --}}
            <div class="empty-state d-none text-center py-12">
                <div class="bas-empty-icon mb-4"><i class="fas fa-search"></i></div>
                <p class="bas-empty-text">Nomor loker tidak ditemukan.</p>
            </div>

        </div>
    @endforeach
</div>
