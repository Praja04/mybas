<!-- resources/views/components/spinner.blade.php -->
<div class="spinner-container" id="spinner-container">
    <div class="spinner">
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
    </div>
    <div class="bounce-text" style="text-align: center; margin-top: 10px; font-weight: bold; margin-left: 35px; font-weight: 900;">Mohon tunggu sebentar ya . . .</div>
</div>

{{-- modal informasi --}}
<div class="modal fade" id="exampleModalCenter" tabindex="-1" aria-labelledby="exampleModalCenterTitle" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center" id="exampleModalCenterTitle">Hanya Informasi</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <div class="modal-body text-center">
                <img style="max-width: 100%; max-height: 180px; display: block; margin: 0 auto;" alt="karakter" src="{{ url('/') }}/assets/media/logos/karakter_lab_cari.png">
                <p style="margin-top: 10px;">.<em><b> Note:</b> Jika data readsensors tidak dapat tampil, silakan hubungi PIC sistem boiler agar dapat segera diperbaiki.</em></p>

            </div>            
            <div class="modal-footer">
                <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                    <i class="bx bx-x d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Close</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function hideLoadingSpinner() {
        var spinnerContainer = document.getElementById('spinner-container');
        spinnerContainer.style.display = 'none';
        setTimeout(function () {
            $('#exampleModalCenter').modal('show');
        }, 2000);
    }

    window.addEventListener('load', function () {
        setTimeout(function () {
            hideLoadingSpinner();
        }, 1500); 
    });
</script>

<style>
   /* efek loading screen */
    .spinner-container {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(5px);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 99;
    }

    .spinner {
        width: 70.4px;
        height: 70.4px;
        --clr: rgb(0, 60, 255);
        --clr-alpha: transparent;
        animation: spinner 1.6s infinite ease;
        transform-style: preserve-3d;
    }

    .spinner > div {
        background-color: var(--clr-alpha);
        height: 100%;
        position: absolute;
        width: 100%;
        border: 3.5px solid var(--clr);
    }

    .spinner div:nth-of-type(1) {
        transform: translateZ(-35.2px) rotateY(180deg);
    }

    .spinner div:nth-of-type(2) {
        transform: rotateY(-270deg) translateX(50%);
        transform-origin: top right;
    }

    .spinner div:nth-of-type(3) {
        transform: rotateY(270deg) translateX(-50%);
        transform-origin: center left;
    }

    .spinner div:nth-of-type(4) {
        transform: rotateX(90deg) translateY(-50%);
        transform-origin: top center;
    }

    .spinner div:nth-of-type(5) {
        transform: rotateX(-90deg) translateY(50%);
        transform-origin: bottom center;
    }

    .spinner div:nth-of-type(6) {
        transform: translateZ(35.2px);
    }

    @keyframes spinner {
        0% {
            transform: rotate(45deg) rotateX(-25deg) rotateY(25deg);
        }

        50% {
            transform: rotate(45deg) rotateX(-385deg) rotateY(25deg);
        }

        100% {
            transform: rotate(45deg) rotateX(-385deg) rotateY(385deg);
        }
    }

    @keyframes bounce {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-20px);
    }
    60% {
        transform: translateY(-10px);
    }
    }

    #spinner-container .bounce-text {
    animation: bounce 2s infinite;
    }
</style>
