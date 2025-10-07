<!-- resources/views/components/spinner.blade.php -->
<div class="spinner-container" id="spinner-container">
        <div class="spinner"></div>
        <div class="spinner-text">Mohon ditunggu ...</div>
</div>

<script>
    function hideLoadingSpinner() {
        var spinnerContainer = document.getElementById('spinner-container');
        spinnerContainer.style.display = 'none';
    }

    window.addEventListener('load', function () {
        setTimeout(function () {
            hideLoadingSpinner();
        }, 1000);
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
        background-color: rgba(22, 21, 21, 0.764);
        backdrop-filter: blur(5px);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 99;
    }

    .spinner {
    width: 70px;
    height: 70px;
    position: relative;
    }

    .spinner:before {
    content: "";
    width: 70px;
    height: 70px;
    border-radius: 50%;
    border: 6px solid #007bff;
    position: absolute;
    top: 0;
    left: 0;
    animation: pulse 1s ease-in-out infinite;
    }

    .spinner:after {
    content: "";
    width: 70px;
    height: 70px;
    border-radius: 50%;
    border: 6px solid transparent;
    border-top-color: #007bff;
    position: absolute;
    top: 0;
    left: 0;
    animation: spin 2s linear infinite;
    }

    .spinner-text {
    font-size: 24px;
    margin-top: 20px;
    margin-left: 20px;
    color: #007bff;
    font-family: Arial, sans-serif;
    text-align: center;
    text-transform: uppercase;
    }

    @keyframes pulse {
    0% {
        transform: scale(0.6);
        opacity: 1;
    }
    50% {
        transform: scale(1.2);
        opacity: 0;
    }
    100% {
        transform: scale(0.6);
        opacity: 1;
    }
    }

    @keyframes spin {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
    }

    .content {
    display: none;
    }

    .spinner .spinner-container {
    display: none;
    }

    .loaded .content {
    display: block;
    }

</style>
