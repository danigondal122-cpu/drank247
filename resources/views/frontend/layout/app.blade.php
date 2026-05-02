<!DOCTYPE html>
<html lang="en">
@include('frontend.layout.header')

<body class="hold-transition layout-top-nav layout-fixed layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper" style="overflow-x:hidden !important;">
        @if (Route::current()->getName() != 'welcome')
            <!-- Navbar -->
            @include('frontend.layout.nav_bar')
        @endif
        <!-- /.navbar -->
        <!-- Content Wrapper. Contains page content -->
        @if (Route::current()->getName() != 'welcome')
            <div class="content-wrapper {{ Request::segment(1) != '' ? '' : 'custom_header' }}"
                style="margin-bottom:0px !important;">
                <!-- Main content -->
                @yield('slider')
                <section class="content-header">
                    <div class="container-fluid px-5">
                        @yield('header_content')
                    </div>
                </section>
        @endif
        <section class="content">
            <div class="container-fluid SmallDivPadding">
                @yield('content')
            </div>
            <!--/. container-fluid -->
        </section>
        <!-- /.content -->

    </div>
    @if (Route::current()->getName() != 'welcome')
        <!-- /.content-wrapper -->
        <!-- Main Footer -->
        @if (!auth()->check())
            @include('frontend.modals')
        @endif
        @include('frontend.layout.footer')
        </div>
    @endif
    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->
    <!-- AdminLTE App -->
    <script src="{{ asset('js/page/root.js') }}"></script>
    <script src="{{ asset('js/page/customer_login.js') }}"></script>
    <script>
        $("#loginModule,#registerModule").on('hide.bs.modal', function() {
            $('.error_span').text('')
            $('.is-invalid').removeClass('is-invalid')
        });
    </script>

    <!-- jQueryConfirm -->
    <script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
    <script src="{{ asset('plugins/jquery-confirm/jquery-confirm.min.js') }}"></script>
    <script src="{{ asset('js/adminlte.js') }}"></script>
    <script src="{{ asset('js/page/common.js') }}"></script>
    @yield('pageJS')
</body>

</html>
