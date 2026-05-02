<!DOCTYPE html>
<html lang="en">
@include('customerservice.layout.header')

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-dark navbar-pink">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
                </li>
            </ul>

            <div class="col-md-9 text-center text-white">
                <p class="h4">{{ auth('customer_service')->user()->cs_name }}</p>
            </div>
            {{-- <div class="col-md-2 text-white" style="text-align: right;">
     
      <input type="checkbox"  style="border-radius: 20rem;" style="width:10px !important;" id="customSwitch3" name="customSwitch3"    data-on="Online" data-off="Offline" data-toggle="toggle" data-onstyle="success" data-offstyle="danger" onchange="onoff();"  {{auth('customer_service')->user()->cs_onoff=="online" ? 'checked': ''}} style="width:100px;">
     <div class="custom-control custom-switch custom-switch-xl custom-switch-off-danger custom-switch-on-success">
        <input type="checkbox" class="custom-control-input" onchange="onoff();"  id="customSwitch3" name="customSwitch3" {{auth('customer_service')->user()->cs_onoff=="online" ? 'checked': ''}}>
          <label class="custom-control-label" for="customSwitch3"></label>
        </div>
     </div> --}}
            <div class="col-md-2 text-right text-white">
                <div class="row">
                    <div class="col-2 mt-2 text-right">
                        <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                            <input type="checkbox" class="custom-control-input scheduleonoff" id="customSwitch3"
                                onchange="onoff();"
                                {{ auth('customer_service')->user()->is_verified == 'online' ? 'checked' : '' }}>
                            <label class="custom-control-label" for="customSwitch3"></label>
                        </div>
                    </div>
                    <div class="col-10 text-right">
                        <ul class="navbar-nav ml-auto">
                            <!-- Notifications Dropdown Menu -->
                            <li class="nav-item dropdown notification_list">
                                <a class="nav-link" data-toggle="dropdown" href="#">
                                    <i class="far fa-bell" style="font-size:30px;"></i>
                                    <span class="badge badge-warning navbar-badge">{{ $global['n_count'] }}</span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                    <span class="dropdown-item dropdown-header">{{ $global['n_count'] }}
                                        Notifications</span>
                                    <div class="dropdown-divider"></div>
                                    <div style="height:300px;overflow-y:scroll">
                                        @foreach ($global['Notification'] as $item)
                                            <a href="#" class="dropdown-item">
                                                <i class="fas fa-bell mr-2"> {{ $item['nt_text'] }}</i>
                                                <span class="float-right text-muted text-sm"></span>
                                            </a>
                                            <div class="dropdown-divider"></div>
                                        @endforeach
                                    </div>
                                    {{-- <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a> --}}
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>


            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <!-- Messages Dropdown Menu -->
                <li class="nav-item dropdown">

                    <a class="nav-link text-white py-1" data-toggle="dropdown" href="#">
                        <i class="fas fa-2x fa-user-circle"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a href="{{ url('customer_service/profile') }}" class="dropdown-item"> <i
                                class="fas fa-user"></i> &nbsp;&nbsp;My profile</a>
                        <a href="{{ url('customer_service/changepassword') }}" class="dropdown-item"><i
                                class="fas fa-unlock-alt"></i> &nbsp;&nbsp;Change Password</a>
                        <a href="{{ url('customer_service/logout') }}" class="dropdown-item"><i
                                class="fas fa-sign-out-alt"></i> &nbsp;&nbsp;Logout</a>
                    </div>
                </li>
            </ul>
        </nav>
        <!-- /.navbar -->
        <!-- Main Sidebar Container -->
        @include('customerservice.layout.sidebar')
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            @yield('header_content')
            <!-- /.content-header -->
            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    @yield('content')
                </div>
                <!--/. container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->

        <!-- Main Footer -->
        @include('customerservice.layout.footer')
    </div>
    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->
    <!-- AdminLTE App -->
    <script src="{{ asset('js/page/root.js') }}"></script>
    <script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
    <!-- jQueryConfirm -->
    <script src="{{ asset('plugins/jquery-confirm/jquery-confirm.min.js') }}"></script>
    <script src="{{ asset('js/adminlte.js') }}"></script>
    <script src="{{ asset('plugins/block-ui/jquery.blockUI.min.js') }}"></script>
    <script src="{{ asset('js/page/common.js') }}"></script>
    <script>
        function onoff() {
            loader_show();
            var checkBox = document.getElementById("customSwitch3");
            var value = (checkBox.checked == true) ? 'online' : 'offline'
            $.ajax({
                url: SITE_URL + 'customer_service/updateonoff',
                type: 'POST',
                data: 'value=' + value + '&_token=' + $('meta[name=csrf-token]').attr('content'),
                success: function(obj) {
                    loader_hide();
                    // location.reload();

                },

            })
        }
        $(document).ready(function() {
            $('.nav-item.notification_list a').click(function() {
                $.ajax({
                    url: SITE_URL + 'customer_service/notificationread',
                    type: 'POST',
                    data: '&_token=' + $('meta[name=csrf-token]').attr('content'),
                    success: function(obj) {


                    },

                })
            });
        });
    </script>
    @yield('pageJS')
</body>

</html>
