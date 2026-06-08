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

                            <span class="bas-loker-indicator"></span>

                            @if ($count > 0 && $status != 'rusak')
                                <div class="bas-loker-kat">{{ $namaKategori }}</div>
                            @else
                                <div class="bas-loker-kat" style="visibility:hidden;">—</div>
                            @endif

                            <div class="bas-loker-no">{{ $loker['no'] }}</div>

                            <div class="bas-loker-badge">{{ $statusLabel }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="empty-state d-none text-center py-12">
                <div class="bas-empty-icon mb-4"><i class="fas fa-search"></i></div>
                <p class="bas-empty-text">Nomor loker tidak ditemukan.</p>
            </div>

        </div>
    @endforeach
</div>

@push('scripts')
    <style>
        .bas-loker-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
            gap: 12px;
        }

        .bas-loker-card {
            position: relative;
            border-radius: var(--bas-radius-lg);
            padding: 16px 12px 14px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            cursor: pointer;
            border: 1.5px solid var(--bas-border);
            background: var(--bas-surface);
            transition: all 0.22s ease;
            min-height: 120px;
            justify-content: center;
            overflow: hidden;
        }

        .bas-loker-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.10);
            z-index: 1;
        }

        .bas-loker-card:active {
            transform: translateY(-2px) scale(0.98);
        }

        .bas-loker-kosong {
            background: var(--bas-surface);
            border-color: var(--bas-border);
        }

        .bas-loker-kosong:hover {
            border-color: #D1D5DB;
        }

        .bas-loker-terisi {
            background: #F0FDF4;
            border-color: #86EFAC;
        }

        .bas-loker-terisi:hover {
            border-color: var(--bas-success);
            box-shadow: 0 8px 24px rgba(16, 185, 129, 0.15);
        }

        .bas-loker-penuh {
            background: #FFF5F5;
            border-color: #FCA5A5;
        }

        .bas-loker-penuh:hover {
            border-color: var(--bas-danger);
            box-shadow: 0 8px 24px rgba(239, 68, 68, 0.15);
        }

        .bas-loker-rusak {
            background: #F9FAFB;
            border-color: #D1D5DB;
            opacity: 0.75;
        }

        .bas-loker-rusak:hover {
            opacity: 0.9;
            border-color: #9CA3AF;
        }

        .bas-loker-indicator {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .bas-loker-kosong .bas-loker-indicator {
            background: #D1D5DB;
        }

        .bas-loker-terisi .bas-loker-indicator {
            background: var(--bas-success);
        }

        .bas-loker-penuh .bas-loker-indicator {
            background: var(--bas-danger);
        }

        .bas-loker-rusak .bas-loker-indicator {
            background: #9CA3AF;
        }

        .bas-loker-kat {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.4px;
            text-transform: uppercase;
            color: var(--bas-neutral);
            margin-bottom: 5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }

        .bas-loker-terisi .bas-loker-kat {
            color: #065F46;
        }

        .bas-loker-penuh .bas-loker-kat {
            color: #991B1B;
        }

        .bas-loker-no {
            font-size: 17px;
            font-weight: 700;
            line-height: 1.1;
            color: var(--bas-dark);
            margin-bottom: 7px;
            letter-spacing: -0.3px;
        }

        .bas-loker-terisi .bas-loker-no {
            color: #065F46;
        }

        .bas-loker-penuh .bas-loker-no {
            color: #991B1B;
        }

        .bas-loker-rusak .bas-loker-no {
            color: #6B7280;
        }

        .bas-loker-badge {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            padding: 3px 9px;
            border-radius: 99px;
            display: inline-block;
        }

        .bas-loker-kosong .bas-loker-badge {
            background: var(--bas-neutral-light);
            color: #6B7280;
            border: 1px solid var(--bas-border);
        }

        .bas-loker-terisi .bas-loker-badge {
            background: #DCFCE7;
            color: #166534;
            border: 1px solid #86EFAC;
        }

        .bas-loker-penuh .bas-loker-badge {
            background: #FEE2E2;
            color: #991B1B;
            border: 1px solid #FCA5A5;
        }

        .bas-loker-rusak .bas-loker-badge {
            background: #F3F4F6;
            color: #6B7280;
            border: 1px solid #D1D5DB;
        }

        .bas-empty-icon {
            width: 56px;
            height: 56px;
            background: var(--bas-neutral-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: var(--bas-neutral);
            margin: 0 auto;
        }

        .bas-empty-text {
            font-size: 14px;
            color: var(--bas-neutral);
            margin: 0;
        }

        @media (max-width: 576px) {
            .bas-loker-grid {
                grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
                gap: 8px;
            }
        }
    </style>
@endpush
