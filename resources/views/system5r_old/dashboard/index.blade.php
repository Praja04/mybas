@extends('system5r.layouts.base')

@section('title', 'Dashboard')

@push('styles')
<link href="https://unpkg.com/video.js/dist/video-js.css" rel="stylesheet">
<style>
.video-js.vjs-theme-city {
    background-color: #292b2c; 
    color: #fff;
}

.video-js.vjs-theme-city .vjs-control-bar {
    background: rgba(45, 45, 45, 0.7);
}

.video-js.vjs-theme-city .vjs-big-play-button {
    font-size: 3em;
    color: #fff;
    border-color: #fff;
    background-color: rgba(255, 255, 255, 0.2);
}

.video-js.vjs-16-9 {
    max-height: 100%; 
}
</style>
@endpush

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body p-0">
                    <div class="alert alert-warning border-0 rounded-0 m-0 d-flex align-items-center" role="alert">
                        <div class="flex-grow-1 text-truncate">
                            5R System
                        </div>
                    </div>
            
                    <div class="row align-items-end">
                        <div class="col-sm-8">
                            <div class="p-3">
                                <p class="fs-16 lh-base">
                                    System penilaian 5R.
                                    <ul>
                                        <li>RINGKAS</li>
                                        <li>RAPI</li>
                                        <li>RESIK</li>
                                        <li>RAWAT</li>
                                        <li>RAJIN</li>
                                    </ul>
                                </p>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="px-3">
                                <img src="{{ asset('assets/velzon/images/verification-img.png') }}" class="img-fluid" alt="">
                            </div>
                        </div>
                    </div>
                </div> <!-- end card-body-->
            </div>
        </div>
    </div>
    {{-- <div class="col-md-4">
        <div class="card">
            <div class="card-body p-0">
                <div class="alert alert-warning border-0 rounded-0 m-0 d-flex align-items-center" role="alert">
                    <div class="flex-grow-1 text-truncate">
                        5R System
                    </div>
                </div>
        
                <div class="row align-items-end">
                    <div class="col-sm-8">
                        <div class="p-3">
                            <p class="fs-16 lh-base">
                                System penilaian 5R.
                                <ul>
                                    <li>RINGKAS</li>
                                    <li>RAPI</li>
                                    <li>RESIK</li>
                                    <li>RAWAT</li>
                                    <li>RAJIN</li>
                                </ul>
                            </p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="px-3">
                            <img src="{{ asset('assets/velzon/images/verification-img.png') }}" class="img-fluid" alt="">
                        </div>
                    </div>
                </div>
            </div> <!-- end card-body-->
        </div>
    </div>

    <!-- Third Card -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-body p-0">
                <div class="alert alert-warning border-0 rounded-0 m-0 d-flex align-items-center" role="alert">
                    <div class="flex-grow-1 text-truncate">
                        5R System
                    </div>
                </div>
        
                <div class="row align-items-end">
                    <div class="col-sm-8">
                        <div class="p-3">
                            <p class="fs-16 lh-base">
                                System penilaian 5R.
                                <ul>
                                    <li>RINGKAS</li>
                                    <li>RAPI</li>
                                    <li>RESIK</li>
                                    <li>RAWAT</li>
                                    <li>RAJIN</li>
                                </ul>
                            </p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="px-3">
                            <img src="{{ asset('assets/velzon/images/verification-img.png') }}" class="img-fluid" alt="">
                        </div>
                    </div>
                </div>
            </div> 
        </div>
    </div> --}}
</div>

<!-- Tutorial Modal -->
<div class="modal fade" id="tutorialModal" tabindex="-1" role="dialog" aria-labelledby="tutorialModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditDataCommitteeLabel">{{ $modalTitle ?? 'Tutorial' }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div data-vjs-player>
                    @if(isset($videoPath))
                    <video-js 
                        id="my_video_player" 
                        class="video-js vjs-16-9"
                        controls 
                        preload="auto" 
                        poster="{{ $thumbnailPath }}" 
                        data-setup='{}'>
                            <source src="{{ $videoPath }}" type="video/mp4">
                        <p class="vjs-no-js">
                            To view this video please enable JavaScript, and consider upgrading to a
                            web browser that <a href="https://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
                        </p>
                    </video-js>
                    @else
                    <div style="text-align: center;">
                        <img src="{{ $thumbnailPath }}" alt="Info" width="100%" height="100%">
                    </div>
                    @endif
                </div>
            </div>
            {{-- <div class="modal-body">
                <div id="carouselExample">
                    {{-- <div class="carousel-inner">
                        <div class="carousel-item active"> --}}
                            {{-- <video width="100%" height="415" controls>
                                @if(isset($videoPath))
                                    <source src="{{ $videoPath }}" type="video/mp4">
                                @else
                                    Your browser does not support the video tag.
                                @endif
                            </video> --}}
                            {{-- <div class="carousel-caption d-none d-md-block">
                                <h5>Penilaian Juri</h5>
                            </div> --}}
                        {{-- </div>
                        <div class="carousel-item">
                            <video width="100%" height="315" controls>
                                <source src="{{ asset('assets/velzon/video/UserKomite.mov') }}" type="video/quicktime">
                                Your browser does not support the video tag.
                            </video>
                            <div class="carousel-caption d-none d-md-block">
                                <h5>Report Penilaian Komite</h5>
                            </div>
                        </div> --}}
                        <!-- Tambahkan video-video lainnya sesuai kebutuhan -->
                    {{-- </div> --}}
                    {{-- <a class="carousel-control-prev" href="#carouselExample" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#carouselExample" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a> --}}
                {{-- </div>
            </div> --}}
        </div>
    </div>
</div>



@endsection

@push('scripts')
<script src="https://unpkg.com/video.js/dist/video.js"></script>
<script>
    $(document).ready(function() {
        $('#tutorialModal').modal('show');

        $('#tutorialModal').on('hidden.bs.modal', function () {
            var player = videojs('my_video_player');
            player.pause(); 
            player.currentTime(0);
        });
    });

    videojs('my_video_player', {
        aspectRatio: '16:9',
        fluid: true
    });
</script>
{{-- dashboard index test --}}
@endpush
