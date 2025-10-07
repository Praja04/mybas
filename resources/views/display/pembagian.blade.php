@extends('layouts.base-display')

@section('title', 'PEMBAGIAN UNTUK KARYAWAN')

@push('styles')
    <link rel="stylesheet" href="{{ url('/assets/plugins/custom/datatables/datatables.bundle.css') }}">
@endpush

@push('styles')
    <style type="text/css">
        .hide {
            opacity: 0 !important;
        }

        #data-karyawan {
            opacity: 1;
            transition-duration: 0.5s;
        }

        #error {
            opacity: 1;
            transition-duration: 0.5s;
        }

        .pembagian-item {
            margin-bottom: 30px;
        }

        .pembagian-item a {
            padding: 10px !important;
            border: 1px solid #eee;
            border-radius: 5px;
        }

        .pembagian-item a:hover {
            background-color: rgba(0, 0, 0, .05);
        }

        .pembagian-list {
            list-style-type: none;
            padding-left: 0
        }

    </style>
@endpush

@section('content')

    <div class="container-fluid">
        @if ($lokasi == '')
        <div class="row">
            <div class="col-6">
                <input id="kode-lokasi" type="text" placeholder="Kode Lokasi" class="form-control">
            </div>
            <div class="col-2">
                <button class="btn btn-success" onClick="goForward()">Go</button>
            </div>
        </div>
        @else
        <!--begin::Row-->
        <div class="row">
            <div class="col-lg-12">
                <!--begin::Advance Table Widget 4-->
                <div class="card card-custom card-stretch gutter-b" id="container-pembagian">
                    <!--begin::Header-->
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">PENGAMBILAN UNTUK KARYAWAN ({{ $lokasi ?? '' }}) <input type="hidden" id="lokasi" value="{{ $lokasi ?? '' }}"> <a href="{{ url('/display/pembagian') }}" class="btn btn-secondary btn-sm">Ganti Kode Lokasi</a></span>
                            <span class="text-muted mt-3 font-weight-bold font-size-sm">Pembagian product & seragam untuk karyawan</span>
                        </h3>
                    </div>
                    <!--end::Header-->
                    <!--begin::Body-->
                    <div class="card-body">
                        <div class="row">
                            <div id="start-pembagian" class="col-md-12">
                                <ul class="pembagian-list">
                                    @foreach ($pembagian as $key => $p)
                                        <li class="pembagian-item"><a onClick="startPembagian('{{ $p->id   }}', '{{ $p->keterangan }}')" href="javascript:">{{ $key+1 }}. {{ $p->keterangan }} <small class="ml-10">{{ formatTanggalIndonesia2($p->tanggal_pembagian) }}</small></a></li>
                                    @endforeach
                                </ul>
                            </div>
                            <div id="pembagian" class="col-md-12" style="display: none">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <form action="" id="scan-id-card-form">
                                                    <input name="id_card" id="id-card" type="number" class="form-control" autofocus placeholder="Scan Id Card">  
                                                </form>
                                            </div>
                                            <div class="col-md-2">
                                                <button onClick="location.reload()" type="button" class="btn btn-outline-secondary"><i class="flaticon2-refresh"></i> Refresh Halaman</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 row">
                                        <div id="data-karyawan" class="col-md-8 mt-5 hide">
                                            <div class="card card-custom card-fit card-border">
                                                <div class="card-header">
                                                    <div class="card-title">
                                                        <span class="card-icon">
                                                            <i class="flaticon2-user"></i>
                                                        </span>
                                                        <h3 class="card-label">Informasi Karyawan</h3>
                                                    </div>
                                                </div>
                                                <div class="card-body pt-2">
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <div class="d-flex align-items-center">
                                                                <div style="width: 200px !important">
                                                                    <img id="foto-diri" style="width: 100% !important; border-radius: 10px">
                                                                </div>
                                                            </div>
                                                            <div class="pt-8 pb-2" style="font-size: 20px">
                                                                <div class="mb-2">
                                                                    <span class="font-weight-bold mr-2">NIK :</span>
                                                                    <span id="nik"></span>
                                                                    <input type="hidden" name="nik" id="nik-input">
                                                                </div>
                                                                <div class="mb-4">
                                                                    <span class="font-weight-bold mr-2">NAMA :</span>
                                                                    <span id="nama"></span>
                                                                </div>
                                                                <div class="mb-4">
                                                                    <span class="font-weight-bold mr-2">DEPT :</span>
                                                                    <span id="department"></span>
                                                                </div>
                                                                <div class="mb-4">
                                                                    <span class="font-weight-bold mr-2">KET :</span>
                                                                    <span id="keterangan"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="alert alert-danger py-2 mt-1 hide" id="error">
                                                                        <span></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <div class="symbol symbol-70 symbol-2by3" id="success-icon" style="display: none">
                                                                        <i class="symbol-label la la-check-square-o fa-7x text-success"></i>
                                                                        <i class="mt-1 text-success">Berhasil</i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mt-5">
                                            <div class="card card-custom card-fit card-border">
                                                <div class="card-body">
                                                    <h5><span id="deskripsi-pembagian"></span><input type="hidden" id="deskripsi-pembagian-input"></h5>
                                                    <small>PIC : <span id="pic"></span><input type="hidden" id="pic-input"></small>
                                                    <button class="btn btn-outline-success btn-sm ml-5" onClick="gantiKeterangan()">Ganti</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Advance Table Widget 4-->
            </div>
        </div>
        @endif
        <!--end::Row-->
        <!--end::Dashboard-->
        <input type="hidden" id="temp-id-card">
    </div>

@endsection

@push('scripts')
    <script src="{{ URL::to('/assets/plugins/global/soundmanager2-nodebug-jsmin.js') }}"></script>
    <script>
            /*!
    * jQuery blockUI plugin
    * Version 2.70.0-2014.11.23
    * Requires jQuery v1.7 or later
    *
    * Examples at: http://malsup.com/jquery/block/
    * Copyright (c) 2007-2013 M. Alsup
    * Dual licensed under the MIT and GPL licenses:
    * http://www.opensource.org/licenses/mit-license.php
    * http://www.gnu.org/licenses/gpl.html
    *
    * Thanks to Amir-Hossein Sobhi for some excellent contributions!
    */

    ;(function() {
    /*jshint eqeqeq:false curly:false latedef:false */
    "use strict";

        function setup($) {
            $.fn._fadeIn = $.fn.fadeIn;

            var noOp = $.noop || function() {};

            // this bit is to ensure we don't call setExpression when we shouldn't (with extra muscle to handle
            // confusing userAgent strings on Vista)
            var msie = /MSIE/.test(navigator.userAgent);
            var ie6  = /MSIE 6.0/.test(navigator.userAgent) && ! /MSIE 8.0/.test(navigator.userAgent);
            var mode = document.documentMode || 0;
            var setExpr = $.isFunction( document.createElement('div').style.setExpression );

            // global $ methods for blocking/unblocking the entire page
            $.blockUI   = function(opts) { install(window, opts); };
            $.unblockUI = function(opts) { remove(window, opts); };

            // convenience method for quick growl-like notifications  (http://www.google.com/search?q=growl)
            $.growlUI = function(title, message, timeout, onClose) {
                var $m = $('<div class="growlUI"></div>');
                if (title) $m.append('<h1>'+title+'</h1>');
                if (message) $m.append('<h2>'+message+'</h2>');
                if (timeout === undefined) timeout = 3000;

                // Added by konapun: Set timeout to 30 seconds if this growl is moused over, like normal toast notifications
                var callBlock = function(opts) {
                    opts = opts || {};

                    $.blockUI({
                        message: $m,
                        fadeIn : typeof opts.fadeIn  !== 'undefined' ? opts.fadeIn  : 700,
                        fadeOut: typeof opts.fadeOut !== 'undefined' ? opts.fadeOut : 1000,
                        timeout: typeof opts.timeout !== 'undefined' ? opts.timeout : timeout,
                        centerY: false,
                        showOverlay: false,
                        onUnblock: onClose,
                        css: $.blockUI.defaults.growlCSS
                    });
                };

                callBlock();
                var nonmousedOpacity = $m.css('opacity');
                $m.mouseover(function() {
                    callBlock({
                        fadeIn: 0,
                        timeout: 30000
                    });

                    var displayBlock = $('.blockMsg');
                    displayBlock.stop(); // cancel fadeout if it has started
                    displayBlock.fadeTo(300, 1); // make it easier to read the message by removing transparency
                }).mouseout(function() {
                    $('.blockMsg').fadeOut(1000);
                });
                // End konapun additions
            };

            // plugin method for blocking element content
            $.fn.block = function(opts) {
                if ( this[0] === window ) {
                    $.blockUI( opts );
                    return this;
                }
                var fullOpts = $.extend({}, $.blockUI.defaults, opts || {});
                this.each(function() {
                    var $el = $(this);
                    if (fullOpts.ignoreIfBlocked && $el.data('blockUI.isBlocked'))
                        return;
                    $el.unblock({ fadeOut: 0 });
                });

                return this.each(function() {
                    if ($.css(this,'position') == 'static') {
                        this.style.position = 'relative';
                        $(this).data('blockUI.static', true);
                    }
                    this.style.zoom = 1; // force 'hasLayout' in ie
                    install(this, opts);
                });
            };

            // plugin method for unblocking element content
            $.fn.unblock = function(opts) {
                if ( this[0] === window ) {
                    $.unblockUI( opts );
                    return this;
                }
                return this.each(function() {
                    remove(this, opts);
                });
            };

            $.blockUI.version = 2.70; // 2nd generation blocking at no extra cost!

            // override these in your code to change the default behavior and style
            $.blockUI.defaults = {
                // message displayed when blocking (use null for no message)
                message:  '<h1>Please wait...</h1>',

                title: null,		// title string; only used when theme == true
                draggable: true,	// only used when theme == true (requires jquery-ui.js to be loaded)

                theme: false, // set to true to use with jQuery UI themes

                // styles for the message when blocking; if you wish to disable
                // these and use an external stylesheet then do this in your code:
                // $.blockUI.defaults.css = {};
                css: {
                    padding:	0,
                    margin:		0,
                    width:		'30%',
                    top:		'40%',
                    left:		'35%',
                    textAlign:	'center',
                    color:		'#000',
                    border:		'3px solid #aaa',
                    backgroundColor:'#fff',
                    cursor:		'wait'
                },

                // minimal style set used when themes are used
                themedCSS: {
                    width:	'30%',
                    top:	'40%',
                    left:	'35%'
                },

                // styles for the overlay
                overlayCSS:  {
                    backgroundColor:	'#000',
                    opacity:			0.6,
                    cursor:				'wait'
                },

                // style to replace wait cursor before unblocking to correct issue
                // of lingering wait cursor
                cursorReset: 'default',

                // styles applied when using $.growlUI
                growlCSS: {
                    width:		'350px',
                    top:		'10px',
                    left:		'',
                    right:		'10px',
                    border:		'none',
                    padding:	'5px',
                    opacity:	0.6,
                    cursor:		'default',
                    color:		'#fff',
                    backgroundColor: '#000',
                    '-webkit-border-radius':'10px',
                    '-moz-border-radius':	'10px',
                    'border-radius':		'10px'
                },

                // IE issues: 'about:blank' fails on HTTPS and javascript:false is s-l-o-w
                // (hat tip to Jorge H. N. de Vasconcelos)
                /*jshint scripturl:true */
                iframeSrc: /^https/i.test(window.location.href || '') ? 'javascript:false' : 'about:blank',

                // force usage of iframe in non-IE browsers (handy for blocking applets)
                forceIframe: false,

                // z-index for the blocking overlay
                baseZ: 1000,

                // set these to true to have the message automatically centered
                centerX: true, // <-- only effects element blocking (page block controlled via css above)
                centerY: true,

                // allow body element to be stetched in ie6; this makes blocking look better
                // on "short" pages.  disable if you wish to prevent changes to the body height
                allowBodyStretch: true,

                // enable if you want key and mouse events to be disabled for content that is blocked
                bindEvents: true,

                // be default blockUI will supress tab navigation from leaving blocking content
                // (if bindEvents is true)
                constrainTabKey: true,

                // fadeIn time in millis; set to 0 to disable fadeIn on block
                fadeIn:  200,

                // fadeOut time in millis; set to 0 to disable fadeOut on unblock
                fadeOut:  400,

                // time in millis to wait before auto-unblocking; set to 0 to disable auto-unblock
                timeout: 0,

                // disable if you don't want to show the overlay
                showOverlay: true,

                // if true, focus will be placed in the first available input field when
                // page blocking
                focusInput: true,

                // elements that can receive focus
                focusableElements: ':input:enabled:visible',

                // suppresses the use of overlay styles on FF/Linux (due to performance issues with opacity)
                // no longer needed in 2012
                // applyPlatformOpacityRules: true,

                // callback method invoked when fadeIn has completed and blocking message is visible
                onBlock: null,

                // callback method invoked when unblocking has completed; the callback is
                // passed the element that has been unblocked (which is the window object for page
                // blocks) and the options that were passed to the unblock call:
                //	onUnblock(element, options)
                onUnblock: null,

                // callback method invoked when the overlay area is clicked.
                // setting this will turn the cursor to a pointer, otherwise cursor defined in overlayCss will be used.
                onOverlayClick: null,

                // don't ask; if you really must know: http://groups.google.com/group/jquery-en/browse_thread/thread/36640a8730503595/2f6a79a77a78e493#2f6a79a77a78e493
                quirksmodeOffsetHack: 4,

                // class name of the message block
                blockMsgClass: 'blockMsg',

                // if it is already blocked, then ignore it (don't unblock and reblock)
                ignoreIfBlocked: false
            };

            // private data and functions follow...

            var pageBlock = null;
            var pageBlockEls = [];

            function install(el, opts) {
                var css, themedCSS;
                var full = (el == window);
                var msg = (opts && opts.message !== undefined ? opts.message : undefined);
                opts = $.extend({}, $.blockUI.defaults, opts || {});

                if (opts.ignoreIfBlocked && $(el).data('blockUI.isBlocked'))
                    return;

                opts.overlayCSS = $.extend({}, $.blockUI.defaults.overlayCSS, opts.overlayCSS || {});
                css = $.extend({}, $.blockUI.defaults.css, opts.css || {});
                if (opts.onOverlayClick)
                    opts.overlayCSS.cursor = 'pointer';

                themedCSS = $.extend({}, $.blockUI.defaults.themedCSS, opts.themedCSS || {});
                msg = msg === undefined ? opts.message : msg;

                // remove the current block (if there is one)
                if (full && pageBlock)
                    remove(window, {fadeOut:0});

                // if an existing element is being used as the blocking content then we capture
                // its current place in the DOM (and current display style) so we can restore
                // it when we unblock
                if (msg && typeof msg != 'string' && (msg.parentNode || msg.jquery)) {
                    var node = msg.jquery ? msg[0] : msg;
                    var data = {};
                    $(el).data('blockUI.history', data);
                    data.el = node;
                    data.parent = node.parentNode;
                    data.display = node.style.display;
                    data.position = node.style.position;
                    if (data.parent)
                        data.parent.removeChild(node);
                }

                $(el).data('blockUI.onUnblock', opts.onUnblock);
                var z = opts.baseZ;

                // blockUI uses 3 layers for blocking, for simplicity they are all used on every platform;
                // layer1 is the iframe layer which is used to supress bleed through of underlying content
                // layer2 is the overlay layer which has opacity and a wait cursor (by default)
                // layer3 is the message content that is displayed while blocking
                var lyr1, lyr2, lyr3, s;
                if (msie || opts.forceIframe)
                    lyr1 = $('<iframe class="blockUI" style="z-index:'+ (z++) +';display:none;border:none;margin:0;padding:0;position:absolute;width:100%;height:100%;top:0;left:0" src="'+opts.iframeSrc+'"></iframe>');
                else
                    lyr1 = $('<div class="blockUI" style="display:none"></div>');

                if (opts.theme)
                    lyr2 = $('<div class="blockUI blockOverlay ui-widget-overlay" style="z-index:'+ (z++) +';display:none"></div>');
                else
                    lyr2 = $('<div class="blockUI blockOverlay" style="z-index:'+ (z++) +';display:none;border:none;margin:0;padding:0;width:100%;height:100%;top:0;left:0"></div>');

                if (opts.theme && full) {
                    s = '<div class="blockUI ' + opts.blockMsgClass + ' blockPage ui-dialog ui-widget ui-corner-all" style="z-index:'+(z+10)+';display:none;position:fixed">';
                    if ( opts.title ) {
                        s += '<div class="ui-widget-header ui-dialog-titlebar ui-corner-all blockTitle">'+(opts.title || '&nbsp;')+'</div>';
                    }
                    s += '<div class="ui-widget-content ui-dialog-content"></div>';
                    s += '</div>';
                }
                else if (opts.theme) {
                    s = '<div class="blockUI ' + opts.blockMsgClass + ' blockElement ui-dialog ui-widget ui-corner-all" style="z-index:'+(z+10)+';display:none;position:absolute">';
                    if ( opts.title ) {
                        s += '<div class="ui-widget-header ui-dialog-titlebar ui-corner-all blockTitle">'+(opts.title || '&nbsp;')+'</div>';
                    }
                    s += '<div class="ui-widget-content ui-dialog-content"></div>';
                    s += '</div>';
                }
                else if (full) {
                    s = '<div class="blockUI ' + opts.blockMsgClass + ' blockPage" style="z-index:'+(z+10)+';display:none;position:fixed"></div>';
                }
                else {
                    s = '<div class="blockUI ' + opts.blockMsgClass + ' blockElement" style="z-index:'+(z+10)+';display:none;position:absolute"></div>';
                }
                lyr3 = $(s);

                // if we have a message, style it
                if (msg) {
                    if (opts.theme) {
                        lyr3.css(themedCSS);
                        lyr3.addClass('ui-widget-content');
                    }
                    else
                        lyr3.css(css);
                }

                // style the overlay
                if (!opts.theme /*&& (!opts.applyPlatformOpacityRules)*/)
                    lyr2.css(opts.overlayCSS);
                lyr2.css('position', full ? 'fixed' : 'absolute');

                // make iframe layer transparent in IE
                if (msie || opts.forceIframe)
                    lyr1.css('opacity',0.0);

                //$([lyr1[0],lyr2[0],lyr3[0]]).appendTo(full ? 'body' : el);
                var layers = [lyr1,lyr2,lyr3], $par = full ? $('body') : $(el);
                $.each(layers, function() {
                    this.appendTo($par);
                });

                if (opts.theme && opts.draggable && $.fn.draggable) {
                    lyr3.draggable({
                        handle: '.ui-dialog-titlebar',
                        cancel: 'li'
                    });
                }

                // ie7 must use absolute positioning in quirks mode and to account for activex issues (when scrolling)
                var expr = setExpr && (!$.support.boxModel || $('object,embed', full ? null : el).length > 0);
                if (ie6 || expr) {
                    // give body 100% height
                    if (full && opts.allowBodyStretch && $.support.boxModel)
                        $('html,body').css('height','100%');

                    // fix ie6 issue when blocked element has a border width
                    if ((ie6 || !$.support.boxModel) && !full) {
                        var t = sz(el,'borderTopWidth'), l = sz(el,'borderLeftWidth');
                        var fixT = t ? '(0 - '+t+')' : 0;
                        var fixL = l ? '(0 - '+l+')' : 0;
                    }

                    // simulate fixed position
                    $.each(layers, function(i,o) {
                        var s = o[0].style;
                        s.position = 'absolute';
                        if (i < 2) {
                            if (full)
                                s.setExpression('height','Math.max(document.body.scrollHeight, document.body.offsetHeight) - (jQuery.support.boxModel?0:'+opts.quirksmodeOffsetHack+') + "px"');
                            else
                                s.setExpression('height','this.parentNode.offsetHeight + "px"');
                            if (full)
                                s.setExpression('width','jQuery.support.boxModel && document.documentElement.clientWidth || document.body.clientWidth + "px"');
                            else
                                s.setExpression('width','this.parentNode.offsetWidth + "px"');
                            if (fixL) s.setExpression('left', fixL);
                            if (fixT) s.setExpression('top', fixT);
                        }
                        else if (opts.centerY) {
                            if (full) s.setExpression('top','(document.documentElement.clientHeight || document.body.clientHeight) / 2 - (this.offsetHeight / 2) + (blah = document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop) + "px"');
                            s.marginTop = 0;
                        }
                        else if (!opts.centerY && full) {
                            var top = (opts.css && opts.css.top) ? parseInt(opts.css.top, 10) : 0;
                            var expression = '((document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop) + '+top+') + "px"';
                            s.setExpression('top',expression);
                        }
                    });
                }

                // show the message
                if (msg) {
                    if (opts.theme)
                        lyr3.find('.ui-widget-content').append(msg);
                    else
                        lyr3.append(msg);
                    if (msg.jquery || msg.nodeType)
                        $(msg).show();
                }

                if ((msie || opts.forceIframe) && opts.showOverlay)
                    lyr1.show(); // opacity is zero
                if (opts.fadeIn) {
                    var cb = opts.onBlock ? opts.onBlock : noOp;
                    var cb1 = (opts.showOverlay && !msg) ? cb : noOp;
                    var cb2 = msg ? cb : noOp;
                    if (opts.showOverlay)
                        lyr2._fadeIn(opts.fadeIn, cb1);
                    if (msg)
                        lyr3._fadeIn(opts.fadeIn, cb2);
                }
                else {
                    if (opts.showOverlay)
                        lyr2.show();
                    if (msg)
                        lyr3.show();
                    if (opts.onBlock)
                        opts.onBlock.bind(lyr3)();
                }

                // bind key and mouse events
                bind(1, el, opts);

                if (full) {
                    pageBlock = lyr3[0];
                    pageBlockEls = $(opts.focusableElements,pageBlock);
                    if (opts.focusInput)
                        setTimeout(focus, 20);
                }
                else
                    center(lyr3[0], opts.centerX, opts.centerY);

                if (opts.timeout) {
                    // auto-unblock
                    var to = setTimeout(function() {
                        if (full)
                            $.unblockUI(opts);
                        else
                            $(el).unblock(opts);
                    }, opts.timeout);
                    $(el).data('blockUI.timeout', to);
                }
            }

            // remove the block
            function remove(el, opts) {
                var count;
                var full = (el == window);
                var $el = $(el);
                var data = $el.data('blockUI.history');
                var to = $el.data('blockUI.timeout');
                if (to) {
                    clearTimeout(to);
                    $el.removeData('blockUI.timeout');
                }
                opts = $.extend({}, $.blockUI.defaults, opts || {});
                bind(0, el, opts); // unbind events

                if (opts.onUnblock === null) {
                    opts.onUnblock = $el.data('blockUI.onUnblock');
                    $el.removeData('blockUI.onUnblock');
                }

                var els;
                if (full) // crazy selector to handle odd field errors in ie6/7
                    els = $('body').children().filter('.blockUI').add('body > .blockUI');
                else
                    els = $el.find('>.blockUI');

                // fix cursor issue
                if ( opts.cursorReset ) {
                    if ( els.length > 1 )
                        els[1].style.cursor = opts.cursorReset;
                    if ( els.length > 2 )
                        els[2].style.cursor = opts.cursorReset;
                }

                if (full)
                    pageBlock = pageBlockEls = null;

                if (opts.fadeOut) {
                    count = els.length;
                    els.stop().fadeOut(opts.fadeOut, function() {
                        if ( --count === 0)
                            reset(els,data,opts,el);
                    });
                }
                else
                    reset(els, data, opts, el);
            }

            // move blocking element back into the DOM where it started
            function reset(els,data,opts,el) {
                var $el = $(el);
                if ( $el.data('blockUI.isBlocked') )
                    return;

                els.each(function(i,o) {
                    // remove via DOM calls so we don't lose event handlers
                    if (this.parentNode)
                        this.parentNode.removeChild(this);
                });

                if (data && data.el) {
                    data.el.style.display = data.display;
                    data.el.style.position = data.position;
                    data.el.style.cursor = 'default'; // #59
                    if (data.parent)
                        data.parent.appendChild(data.el);
                    $el.removeData('blockUI.history');
                }

                if ($el.data('blockUI.static')) {
                    $el.css('position', 'static'); // #22
                }

                if (typeof opts.onUnblock == 'function')
                    opts.onUnblock(el,opts);

                // fix issue in Safari 6 where block artifacts remain until reflow
                var body = $(document.body), w = body.width(), cssW = body[0].style.width;
                body.width(w-1).width(w);
                body[0].style.width = cssW;
            }

            // bind/unbind the handler
            function bind(b, el, opts) {
                var full = el == window, $el = $(el);

                // don't bother unbinding if there is nothing to unbind
                if (!b && (full && !pageBlock || !full && !$el.data('blockUI.isBlocked')))
                    return;

                $el.data('blockUI.isBlocked', b);

                // don't bind events when overlay is not in use or if bindEvents is false
                if (!full || !opts.bindEvents || (b && !opts.showOverlay))
                    return;

                // bind anchors and inputs for mouse and key events
                var events = 'mousedown mouseup keydown keypress keyup touchstart touchend touchmove';
                if (b)
                    $(document).bind(events, opts, handler);
                else
                    $(document).unbind(events, handler);

            // former impl...
            //		var $e = $('a,:input');
            //		b ? $e.bind(events, opts, handler) : $e.unbind(events, handler);
            }

            // event handler to suppress keyboard/mouse events when blocking
            function handler(e) {
                // allow tab navigation (conditionally)
                if (e.type === 'keydown' && e.keyCode && e.keyCode == 9) {
                    if (pageBlock && e.data.constrainTabKey) {
                        var els = pageBlockEls;
                        var fwd = !e.shiftKey && e.target === els[els.length-1];
                        var back = e.shiftKey && e.target === els[0];
                        if (fwd || back) {
                            setTimeout(function(){focus(back);},10);
                            return false;
                        }
                    }
                }
                var opts = e.data;
                var target = $(e.target);
                if (target.hasClass('blockOverlay') && opts.onOverlayClick)
                    opts.onOverlayClick(e);

                // allow events within the message content
                if (target.parents('div.' + opts.blockMsgClass).length > 0)
                    return true;

                // allow events for content that is not being blocked
                return target.parents().children().filter('div.blockUI').length === 0;
            }

            function focus(back) {
                if (!pageBlockEls)
                    return;
                var e = pageBlockEls[back===true ? pageBlockEls.length-1 : 0];
                if (e)
                    e.focus();
            }

            function center(el, x, y) {
                var p = el.parentNode, s = el.style;
                var l = ((p.offsetWidth - el.offsetWidth)/2) - sz(p,'borderLeftWidth');
                var t = ((p.offsetHeight - el.offsetHeight)/2) - sz(p,'borderTopWidth');
                if (x) s.left = l > 0 ? (l+'px') : '0';
                if (y) s.top  = t > 0 ? (t+'px') : '0';
            }

            function sz(el, p) {
                return parseInt($.css(el,p),10)||0;
            }

        }


        /*global define:true */
        if (typeof define === 'function' && define.amd && define.amd.jQuery) {
            define(['jquery'], setup);
        } else {
            setup(jQuery);
        }

    })();
    </script>
    <script type="text/javascript">
    
        function goForward()
        {
            var lokasi = $("#kode-lokasi").val();
            location.href = "{{ url('/display/pembagian') }}/"+lokasi;
        }

        function gantiKeterangan()
        {
            localStorage.removeItem("pembagian");
            location.reload();
        }

        function cek_pembagian_pic()
        {
            if(localStorage.getItem("pembagian")) {
                var pembagian = JSON.parse(localStorage.getItem("pembagian"));
                $("#deskripsi-pembagian").text(pembagian.keterangan);
                $("#deskripsi-pembagian-input").val(pembagian.id);
                $("#pic").text(pembagian.pic);
                $("#pic-input").val(pembagian.pic);
                $("#start-pembagian").hide();
                $("#pembagian").show();

                $("#id-card").focus();
            }
        }

        cek_pembagian_pic();

        function play_success_sound()
        {
            // soundManager.onready(function() {
		    //     soundManager.createSound({
		    //         // id: 'sk4Audio',
		    //         url: "{{ url('/assets/media/sounds/ecafesedaap-scan-success.mp3') }}",
		    //         autoLoad: true,
		    //         autoPlay: true,
		    //         volume: 100,
		    //     })
		    // });
        }

        function play_failed_sound()
        {
            // soundManager.onready(function() {
		    //     soundManager.createSound({
		    //         // id: 'sk4Audio',
		    //         url: "{{ url('/assets/media/sounds/ecafesedaap-scan-failed.mp3') }}",
		    //         autoLoad: true,
		    //         autoPlay: true,
		    //         volume: 100,
		    //     })
		    // });
        }

        function startPembagian(id,keterangan)
        {

            var pic = prompt("Mohon isi nama PIC");
            if(pic != null && pic != '') {
                $("#deskripsi-pembagian").text(keterangan);
                $("#deskripsi-pembagian-input").val(id);
                $("#pic").text(pic);
                $("#pic-input").val(pic);
                $("#start-pembagian").hide();
                $("#pembagian").show();

                // Simpan ke localStorage untuk agar tidak pengulangan
                var pembagian = {
                    keterangan : keterangan,
                    id : id,
                    pic : pic
                }

                localStorage.setItem("pembagian", JSON.stringify(pembagian));

                $("#id-card").focus();
            }
        }

        function trim(string) {
            return string.trim();
        }

        function hasClass(el, className) {
            if (!el) {
                return;
            }

            return el.classList ? el.classList.contains(className) : new RegExp('\\b' + className + '\\b').test(el.className);
        }

        function addClass(el, className) {
            if (!el || typeof className === 'undefined') {
                return;
            }

            var classNames = className.split(' ');

            if (el.classList) {
                for (var i = 0; i < classNames.length; i++) {
                    if (classNames[i] && classNames[i].length > 0) {
                        el.classList.add(trim(classNames[i]));
                    }
                }
            } else if (!hasClass(el, className)) {
                for (var x = 0; x < classNames.length; x++) {
                    el.className += ' ' + trim(classNames[x]);
                }
            }
        }

        function actualCss(el, prop, cache) {
            var css = '';

            if (el instanceof HTMLElement === false) {
                return;
            }

            if (!el.getAttribute('kt-hidden-' + prop) || cache === false) {
                var value;

                // the element is hidden so:
                // making the el block so we can meassure its height but still be hidden
                css = el.style.cssText;
                el.style.cssText = 'position: absolute; visibility: hidden; display: block;';

                if (prop == 'width') {
                    value = el.offsetWidth;
                } else if (prop == 'height') {
                    value = el.offsetHeight;
                }

                el.style.cssText = css;

                // store it in cache
                el.setAttribute('kt-hidden-' + prop, value);

                return parseFloat(value);
            } else {
                // store it in cache
                return parseFloat(el.getAttribute('kt-hidden-' + prop));
            }
        }

        function actualWidth(el, cache) {
            return actualCss(el, 'width', cache);
        }

        function remove(el) {
            if (el && el.parentNode) {
                el.parentNode.removeChild(el);
            }
        }

        function css(el, styleProp, value) {
            if (!el) {
                return;
            }

            if (value !== undefined) {
                el.style[styleProp] = value;
            } else {
                var defaultView = (el.ownerDocument || document).defaultView;
                // W3C standard way:
                if (defaultView && defaultView.getComputedStyle) {
                    // sanitize property name to css notation
                    // (hyphen separated words eg. font-Size)
                    styleProp = styleProp.replace(/([A-Z])/g, "-$1").toLowerCase();
                    return defaultView.getComputedStyle(el, null).getPropertyValue(styleProp);
                } else if (el.currentStyle) { // IE
                    // sanitize property name to camelCase
                    styleProp = styleProp.replace(/\-(\w)/g, function(str, letter) {
                        return letter.toUpperCase();
                    });
                    value = el.currentStyle[styleProp];
                    // convert other units to pixels on IE
                    if (/^\d+(em|pt|%|ex)?$/i.test(value)) {
                        return (function(value) {
                            var oldLeft = el.style.left,
                                oldRsLeft = el.runtimeStyle.left;
                            el.runtimeStyle.left = el.currentStyle.left;
                            el.style.left = value || 0;
                            value = el.style.pixelLeft + "px";
                            el.style.left = oldLeft;
                            el.runtimeStyle.left = oldRsLeft;
                            return value;
                        })(value);
                    }
                    return value;
                }
            }
        }

        function block(target, options) {
            var el = $(target);

            options = $.extend(true, {
                opacity: 0.05,
                overlayColor: '#000000',
                type: '',
                size: '',
                state: 'primary',
                centerX: true,
                centerY: true,
                message: '',
                shadow: true,
                width: 'auto'
            }, options);

            var html;
            var version = options.type ? 'spinner-' + options.type : '';
            var state = options.state ? 'spinner-' + options.state : '';
            var size = options.size ? 'spinner-' + options.size : '';
            var spinner = '<span class="spinner ' + version + ' ' + state + ' ' + size + '"></span';

            if (options.message && options.message.length > 0) {
                var classes = 'blockui ' + (options.shadow === false ? 'blockui' : '');

                html = '<div class="' + classes + '"><span>' + options.message + '</span>' + spinner + '</div>';

                var el = document.createElement('div');

                $('body').prepend(el);
                addClass(el, classes);
                el.innerHTML = html;
                options.width = actualWidth(el) + 10;
                remove(el);

                if (target == 'body') {
                    html = '<div class="' + classes + '" style="margin-left:-' + (options.width / 2) + 'px;"><span>' + options.message + '</span><span>' + spinner + '</span></div>';
                }
            } else {
                html = spinner;
            }

            var params = {
                message: html,
                centerY: options.centerY,
                centerX: options.centerX,
                css: {
                    top: '30%',
                    left: '50%',
                    border: '0',
                    padding: '0',
                    backgroundColor: 'none',
                    width: options.width
                },
                overlayCSS: {
                    backgroundColor: options.overlayColor,
                    opacity: options.opacity,
                    cursor: 'wait',
                    zIndex: (target == 'body' ? 1100 : 10)
                },
                onUnblock: function() {
                    if (el && el[0]) {
                        css(el[0], 'position', '');
                        css(el[0], 'zoom', '');
                    }
                }
            };

            if (target == 'body') {
                params.css.top = '50%';
                $.blockUI(params);
            } else {
                var el = $(target);
                el.block(params);
            }
        }

        function unblock(target) {
            if (target && target != 'body') {
                $(target).unblock();
            } else {
                $.unblockUI();
            }
        }

        $("#scan-id-card-form").on("submit", function(e) {
            e.preventDefault();

            // if($("#nik-input").val() != "") {
            //     $("#scan-ktp-button").focus();
            //     return false;
            // }

            block("#container-pembagian", {
                overlayColor: "#000000",
                state : "danger",
                opacity : 0.2,
                message : "Loading..."
            });

            var id_card = $("#id-card").val();
            $("#id-card").val("");

            if(id_card != $("#temp-id-card").val()) {
                $("#temp-id-card").val(id_card);
                $.ajax({
                    url: "{{ url('display/pembagian/scan') }}",
                    type: "POST",
                    dataType: "JSON",
                    data: {
                        id_card : id_card,
                        id_pembagian : $("#deskripsi-pembagian-input").val(),
                        lokasi: $('#lokasi').val(),
                        pic : $("#pic-input").val()
                    },
                    success: function ( response ) {
                        unblock("#container-pembagian");
                        // console.log( response );
                        if(response.success == 1) {
                            play_success_sound();
                            var user = response.data;
                            $("#nama").text(user.nama);
                            $("#bagian").text(user.dept);
                            $("#department").text(user.department);
                            $("#keterangan").text(user.keterangan);
                            $("#nik").text(user.nik);
                            $("#nik-input").val(user.nik);
                            $("#foto-diri").attr("src", 'data:image/jpg;base64,'+user.foto)
                            $("#data-karyawan").removeClass("hide");
                            $("#error").addClass("hide");

                            $("#success-icon").show();
                        }else{
                            play_failed_sound();
                            $("#error span").text(response.message);
                            var user = response.data;
                            $("#nama").text(user.nama);
                            $("#bagian").text('');
                            $("#department").text('');
                            $("#keterangan").text('');
                            $("#nik").text(user.nik);
                            $("#nik-input").val(user.nik);
                            $("#foto-diri").attr("src", 'data:image/jpg;base64,'+user.foto)
                            $("#data-karyawan").removeClass("hide");
                            $("#error").removeClass("hide");
                            // setTimeout(function() {
                                // $("#error").addClass("hide");
                            // }, 2000);

                            $("#id-card").focus();

                            $("#success-icon").hide();
                        }
                    },
                    error: function ( error ) {
                        play_failed_sound();
                        unblock("#container-pembagian");
                        console.log( error );
                    }
                })
            }else{
                $("#id-card").val("");
                unblock("#container-pembagian");
            }
        });

        // function confirm() {
        //     $.ajax({
        //         url: "{{ url('display/pembagian/confirm') }}",
        //         type: "POST",
        //         dataType: "JSON",
        //         data: {
        //             nik : $("#nik-input").val(),
        //             id_pembagian : $("#deskripsi-pembagian-input").val(),
        //             pic : $("#pic-input").val()
        //         },
        //         success: function ( response ) {
        //             if(response.success == 1) {
        //                 $("#nik-input").val("");
        //                 Swal.fire('Berhasil!', 'pembagian id card berhasil', 'success').then(function () {
        //                     $("#data-karyawan").addClass("hide");
        //                     $("#id-card").focus();
        //                 });
        //             }
        //         },
        //         error: function ( error ) {
        //             console.log( error );
        //         }
        //     })
        // };
    </script>
@endpush