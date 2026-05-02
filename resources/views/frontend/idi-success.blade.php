<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css">
<script src="https://use.fontawesome.com/releases/v5.7.2/css/all.css"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<link rel="stylesheet" href="{{ asset('css/custom_fr.css') }}">

<style>
    body {

        min-height: 100vh;
        background-color: #FFF;

    }

    .card {
        max-width: 500px;
        margin: auto;
        color: black;
        border-radius: 20 px
    }

    .card p {
        margin-top: 50px;
        font-size: 30px;
    }

    .header_section {
        background-color: #e91362;
        height: 5%;
    }
</style>


<div class="header_section"></div>

<div class="container p-0">
    <div class="card px-4">
        <p>You can now close this window.</p>
    </div>
</div>

<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('js/page/root.js') }}"></script>
<script src="{{ asset('plugins/block-ui/jquery.blockUI.min.js') }}"></script>
<script src="{{ url('js/page/common.js') }}"></script>
<script>
    loader_show();

    setTimeout(function() {
        loader_hide();
    }, 6000);
</script>
