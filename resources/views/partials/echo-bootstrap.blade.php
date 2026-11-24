@php
    $pusherKey = config('broadcasting.connections.pusher.key');
    $pusherDriver = config('broadcasting.default');
    $pusherSdk = class_exists(\Pusher\Pusher::class);
@endphp
@if($pusherKey && $pusherDriver === 'pusher' && $pusherSdk)
<script src="{{ asset('portal/shared/lib/pusher/pusher.min.js') }}"></script>
<script src="{{ asset('portal/shared/lib/websocket/echo.iife.js') }}"></script>
<script>
(function () {
    if (typeof Echo === 'undefined' || typeof Pusher === 'undefined') {
        return;
    }
    var isHttps = window.location.protocol === 'https:';
    var pagePort = parseInt(window.location.port, 10) || (isHttps ? 443 : 80);
    var directWsPort = {{ (int) config('broadcasting.connections.pusher.options.port', env('LARAVEL_WEBSOCKETS_PORT', 6001)) }};
    var usePageProxy = isHttps;
    var csrfEl = document.querySelector('meta[name="csrf-token"]');
    window.Pusher = Pusher;
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: @json($pusherKey),
        cluster: @json(config('broadcasting.connections.pusher.options.cluster', 'mt1')),
        wsHost: window.location.hostname,
        wsPort: usePageProxy ? pagePort : directWsPort,
        wssPort: usePageProxy ? pagePort : directWsPort,
        forceTLS: usePageProxy,
        encrypted: usePageProxy,
        disableStats: true,
        enabledTransports: ['ws', 'wss'],
        authEndpoint: '/broadcasting/auth',
        auth: {
            headers: {
                'X-CSRF-TOKEN': csrfEl ? csrfEl.getAttribute('content') : ''
            }
        }
    });
})();
</script>
@endif
