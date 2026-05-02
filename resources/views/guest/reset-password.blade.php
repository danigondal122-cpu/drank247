<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>247drank | Reset Password</title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta name="viewport" content="width=device-width, initial-scale=1">

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
    @if ($customer)
        <body class="hold-transition login-page">
            <div class="login-box">
                <div class="login-logo">
                    <img src="{{ asset('img/247-Drank-Logo.png') }}" style="text-align: center; height: auto; width: 220px; margin-top: 50px;">
                </div>
                <!-- /.login-logo -->
                <div class="card">
                    <div class="card-body login-card-body">
                        <p class="login-box-msg"><b>Reset Password</b></p>
                        <form action="#" method="post" id="resetForm">
                            @csrf
                            <div id="message_div"></div>
                            <input type="hidden" name="resetkey" id="resetkey" value="{{ $token }}">
                            <input type="hidden" name="id" id="id" value="{{ $id }}">

                            <div class="input-group">
                                <input type="password" name="new_password" id="new_password" class="form-control" placeholder="New Password">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-envelope"></span>
                                    </div>
                                </div>
                            </div>
                            <span id="new_password_error" class="text-danger"></span>
                            <div class="input-group mt-3">
                                <input type="password" name="new_password_confirmation" id="confirm_password" class="form-control" placeholder=" Confirm Password">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-lock"></span>
                                    </div>
                                </div>
                            </div>
                            <span id="confirm_password_error" class="text-danger"></span>
                            <div class="row justify-content-end mt-3">
                                <div class="col-4">
                                    <button type="submit" class="btn btn-primary btn-block">Submit</button>
                                </div>
                            </div>
                        </form>
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
            <script src="{{ asset('js/page/customer_login.js') }}"></script>
        </body>
    @else
        <body class="hold-transition">
            <div style="background-color: #eee; text-align: center; width: 100%;">
                <img src="{{ asset('img/247-Drank-Logo.png') }}" style="text-align: center; max-height: 120px; margin: 20px;">
            </div>
            <h1 style="text-align: center; color: #e91362; margin: 30px;">
                Your Reset Password link is Expired!!<br>
                <a href="{{ route('homepage') }}">
                    <button type="button" class="btn" style="width: 30%; margin-top: 30px; background-color: #e91362; color: #fff;">Go to Login page</button>
                </a>
            </h1>
            <div style="text-align: center; width: 30%"></div>
        </body>
    @endif
</html>
