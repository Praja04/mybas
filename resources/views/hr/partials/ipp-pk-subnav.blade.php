@php
    $snNav = \App\Support\PmsNav::context();
    $snVisible = $snNav['approver'] || $snNav['am'];
    $snIsPmsManager = $snNav['manager'];
    $snIsAdmin = $snNav['admin'];
    $snOnPk = request()->is('hr/pk-assessment');
    $snTab = (string) request()->query('tab', '');
@endphp
<style>
    .nav.nav-pills.pas-subnav { margin-top: .75rem; }
    .nav.nav-pills.pas-subnav .nav-link { font-size: .85rem; font-weight: 600; color: #a80000; border-radius: 6px; padding: .3rem .9rem; }
    .nav.nav-pills.pas-subnav .nav-link:hover:not(.active) { background: #fdecea; color: #a80000; }
    .nav.nav-pills.pas-subnav .nav-link.active { background: #fdecea; color: #a80000; font-weight: 700; box-shadow: inset 0 0 0 1px #e6b8b8; }
</style>
<ul class="nav nav-pills pas-subnav mb-0" style="gap:.5rem; flex-wrap:wrap; @if(!empty($snPlainBg)) background:#fff; padding:.5rem .75rem; border-radius:8px; box-shadow:0 1px 3px rgba(0,0,0,.08); @endif">
    @if(!empty($snNav['self']))
        <li class="nav-item">
            <a class="nav-link {{ request()->is('hr/ipp-aktual') ? 'active' : '' }}" href="{{ url('/hr/ipp-aktual') }}">
                <i class="fas fa-pen"></i> Isi Aktual IPP
            </a>
        </li>
    @endif
    @if(!empty($snNav['self']))
        <li class="nav-item">
            <a class="nav-link {{ $snOnPk && $snTab !== 'leader' ? 'active' : '' }}" href="{{ url('/hr/pk-assessment') }}">
                <i class="fas fa-star"></i> Penilaian Karya &mdash; Self
            </a>
        </li>
    @endif
    @if(!empty($snNav['self']) && !empty($snNav['leader']))
        <li class="nav-item d-flex align-items-center" style="color:#bbb; font-weight:400; padding:0 .1rem;">|</li>
    @endif
    @if($snVisible)
        <li class="nav-item">
            <a class="nav-link {{ request()->is('hr/ipp-user-approval') ? 'active' : '' }}" href="{{ url('/hr/ipp-user-approval') }}">
                <i class="fas fa-check-double"></i> Approval IPP
            </a>
        </li>
    @endif
    @if(!empty($snNav['leader']))
        <li class="nav-item">
            <a class="nav-link {{ $snOnPk && $snTab === 'leader' ? 'active' : '' }}" href="{{ url('/hr/pk-assessment') }}?tab=leader">
                <i class="fas fa-star-half-alt"></i> Penilaian Karya &mdash; Leader
            </a>
        </li>
    @endif
    @if(!empty($snNav['leader']))
        <li class="nav-item d-flex align-items-center" style="color:#bbb; font-weight:400; padding:0 .1rem;">|</li>
        <li class="nav-item">
            <a class="nav-link {{ request()->is('hr/upload-file-template-ipp-user') ? 'active' : '' }}" href="{{ url('/hr/upload-file-template-ipp-user') }}">
                <i class="fas fa-file-upload"></i> Buat Template IPP
            </a>
        </li>
    @endif
    @if($snIsPmsManager && !$snIsAdmin)
        <li class="nav-item d-flex align-items-center" style="color:#bbb; font-weight:400; padding:0 .1rem;">|</li>
        <li class="nav-item">
            <a class="nav-link {{ request()->is('hr/pms-total-scoring') ? 'active' : '' }}" href="{{ url('/hr/pms-total-scoring') }}">
                <i class="fas fa-chart-line"></i> Total Scoring &mdash; Tim Saya
            </a>
        </li>
    @endif
    @if($snIsAdmin)
        <li class="nav-item d-flex align-items-center" style="color:#bbb; font-weight:400; padding:0 .1rem;">|</li>
        <li class="nav-item">
            <a class="nav-link {{ request()->is('hr/upload-file-template-ipp') ? 'active' : '' }}" href="{{ url('/hr/upload-file-template-ipp') }}">
                <i class="fas fa-folder-open"></i> Template IPP &mdash; Admin HR
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->is('hr/upload-file-template-pk') ? 'active' : '' }}" href="{{ url('/hr/upload-file-template-pk') }}">
                <i class="fas fa-file-alt"></i> Template Penilaian Karya &mdash; Admin HR
            </a>
        </li>
        <li class="nav-item d-flex align-items-center" style="color:#bbb; font-weight:400; padding:0 .1rem;">|</li>
        <li class="nav-item">
            <a class="nav-link {{ request()->is('hr/pk-assessment-hr') ? 'active' : '' }}" href="{{ url('/hr/pk-assessment-hr') }}">
                <i class="fas fa-clipboard-list"></i> Penilaian Karya &mdash; Admin HR
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->is('hr/ipp-approval') ? 'active' : '' }}" href="{{ url('/hr/ipp-approval') }}">
                <i class="fas fa-user-shield"></i> Approval IPP &mdash; Admin HR
            </a>
        </li>
        <li class="nav-item d-flex align-items-center" style="color:#bbb; font-weight:400; padding:0 .1rem;">|</li>
        <li class="nav-item">
            <a class="nav-link {{ request()->is('hr/pms-total-scoring') ? 'active' : '' }}" href="{{ url('/hr/pms-total-scoring') }}">
                <i class="fas fa-chart-line"></i> Total Scoring &mdash; Admin HR
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->is('hr/pms-audit-log') ? 'active' : '' }}" href="{{ url('/hr/pms-audit-log') }}">
                <i class="fas fa-history"></i> Log Aktivitas &mdash; Admin HR
            </a>
        </li>
    @endif
</ul>
