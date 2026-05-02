<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>247drank | Log in</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/jquery-confirm/jquery-confirm.min.css') }}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>

<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-logo">
            <img src="{{ asset('img/247-Drank-Logo.png') }}" style="text-align: center;height:auto;width:220px;margin-top:50px;">
        </div>
        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Enter Your Email ID</p>

                <form action="#" method="post" id="forgotForm">
                    @csrf
                    <div id="message_div"></div>
                    <div class="input-group">
                        <input type="text" name="email" id="email" class="form-control" placeholder="Email">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <span id="email_error" class="text-danger"></span>
                    <div class="row mt-3">
                        <div class="col-8">
                        </div>
                        <!-- /.col -->
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block">Submit</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>

                <p class="mb-1">
                    <a href="{{ url('admin/login') }}">Go To Login Page</a>
                </p>
            </div>
            <!-- /.login-card-body -->
        </div>
    </div>
    <!-- /.login-box -->

    <!-- jQuery -->
    <script src="{{ asset('js/page/root.js') }}"></script>
    <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('js/adminlte.min.js') }}"></script>
    <script src="{{ asset('plugins/block-ui/jquery.blockUI.min.js') }}"></script>
    <script src="{{ asset('js/page/common.js') }}"></script>
    <script>
        const urlAuth = SITE_URL + {{ Js::from($authGuard) }};
        $(function() {
            $('#forgotForm').on('submit', function(e) {
                e.preventDefault();
                loader_show();
                let formData = $(this).serialize();
                $('.is-invalid').removeClass('is-invalid');
                $('.text-danger').html('');
                $.ajax({
                    url: urlAuth + '/forgot-password',
                    type: 'POST',
                    data: formData,
                    success: function(obj) {
                        if (!obj.status && obj.type == 'VALIDATION') {
                            loader_hide();
                            for (key in obj.errors) {
                                $('#' + key).addClass('is-invalid');
                                $('#' + key + '_error').html(obj.errors[key]);
                            }
                        }
                        if (obj.status) {
                            loader_hide();
                            console.log('Looged in');
                            successMessage(obj.message);
                            setTimeout(function() {
                                window.location = urlAuth + '/login';
                            }, 3000);
                        }
                    },
                    error: function() {

                    }
                });
            });
        });
    </script>
</body>

</html>
