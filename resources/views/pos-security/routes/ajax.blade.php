<script>
    var API_GET_VISITOR_DETAIL_SUPPLIER = '{{ route('ajax.pos-security.visitor-transaksi.detail') }}';

    var API_ABSENSI_REST_LOG_SEARCH = '{{ route('ajax.pos-security.absensirestlog.search') }}';

    var API_FORM_SEARCH_SUPPLIER = '{{ route('ajax.pos-security.visitor-transaksi.search') }}';

    var API_FORM_SEARCH_KENDARAAN_IN = '{{ route('ajax.pos-security.cek-kendaraan.search-in') }}';
    var API_FORM_SEARCH_KENDARAAN_OUT = '{{ route('ajax.pos-security.cek-kendaraan.search-out') }}';
    var API_CEK_KENDARAAN_SHOW = '{{ route('ajax.pos-security.cek-kendaraan.show') }}';

    var API_BLACKLIST_SHOW = '{{ route('ajax.pos-security.blacklist.show') }}';

    var API_DASHBOARD_FILTER = '{{ route('ajax.pos-security.dashboard.filter') }}';
    var API_DASHBOARD_FILTER_STATISTIK = '{{ route('ajax.pos-security.dashboard.statistik') }}';

    var API_FORM_CREATE_SECURITY = '{{ route('ajax.pos-security.master.security.store') }}';
    var API_FORM_TOGGLE_SECURITY = '{{ route('ajax.pos-security.master.security.toggle', ':id') }}';
    var API_FORM_EDIT_SECURITY = '{{ route('ajax.pos-security.master.security.edit', ':id') }}';
    var API_FORM_UPDATE_SECURITY = '{{ route('ajax.pos-security.master.security.update', ':id') }}';

    var API_RESET_KARTU = '{{ route('pos-security.kartu.reset') }}';

    var API_BLOCK_SUPPLIER = '{{ route('ajax.pos-security.visitor-transaksi.block') }}';
    var API_REPORT_LOST_CARD_SUPPLIER = '{{ route('ajax.pos-security.visitor-transaksi.reportLost') }}';
    var API_GET_VISITOR_DETAIL_VENDOR = '{{ route('ajax.pos-security.vendor-transaksi.detail') }}';
    var API_BLOCK_VENDOR = '{{ route('ajax.pos-security.vendor-transaksi.block') }}';
</script>

{{-- <script>
    var API_AJAX_GA_LOGISTIK_KARTU_GENERATE = '{{ route('ajax.pos-security.kartuqr.generate') }}';
    var API_AJAX_GA_LOGISTIK_KARTU_IN_AKTIF = '{{ route('ajax.pos-security.kartuqr.inactive') }}';
    var API_AJAX_GA_LOGISTIK_KARTU_IN_BLOCK = '{{ route('ajax.pos-security.kartuqr.inblock') }}';
    var API_AJAX_GA_LOGISTIK_KARTU_ADD_KARTU = '{{ route('ajax.pos-security.kartuqr.add-kartu') }}';

    // untuk get datanya all API_AJAX_GA_LOGISTIK_KARTU_GET_KARTU akan tampill all kartu aktif
    // untuk get datanya where qr_code API_AJAX_GA_LOGISTIK_KARTU_GET_KARTU kirim params ?qr_code=
    var API_AJAX_GA_LOGISTIK_KARTU_GET_KARTU = '{{ route('ajax.pos-security.kartuqr.get-kartu') }}';
    var API_AJAX_GA_LOGISTIK_KARTU_UPDATE_KARTU = '{{ route('ajax.pos-security.kartuqr.update-kartu') }}';

    // SELECT 2 DEPARTEMENT
    var API_AJAX_GA_LOGISTIK_DEPARTEMENT = '{{ route('ajax.pos-security.departement.get_select2') }}';

    var VAR_REALTIME_SUPPLIER = '{{ route('ajax.pos-security.visitor-transaksi.search') }}';

    var API_FORM_SEARCH_SUPPLIER = '{{ route('ajax.pos-security.visitor-transaksi.search') }}';
    var API_FORM_KEMBALIKAN_KARTU_SUPPLIER = '{{ route('ajax.pos-security.visitor-transaksi.kembali_kartu') }}';

    var API_BLOCK_SUPPLIER = '{{ route('ajax.pos-security.visitor-transaksi.block') }}';
    var API_REPORT_LOST_CARD_SUPPLIER = '{{ route('ajax.pos-security.visitor-transaksi.reportLost') }}';
    var API_GET_VISITOR_DETAIL_SUPPLIER = '{{ route('ajax.pos-security.visitor-transaksi.detail') }}';

    var API_FORM_SEARCH_TAMU = '{{ route('ajax.pos-security.vendor-transaksi.search_vendor') }}';
    var API_FORM_KEMBALIKAN_KARTU_TAMU = '{{ route('ajax.pos-security.vendor-transaksi.kembali_kartu') }}';

    var API_BLACKLIST_SHOW = '{{ route('ajax.pos-security.blacklist.show') }}';

    var API_DASHBOARD_FILTER = '{{ route('ajax.pos-security.dashboard.filter') }}';
    var API_DASHBOARD_FILTER_STATISTIK = '{{ route('ajax.pos-security.dashboard.statistik') }}';

    var API_ABSENSI_REST_LOG_SEARCH = '{{ route('ajax.pos-security.absensirestlog.search') }}';
</script> --}}
