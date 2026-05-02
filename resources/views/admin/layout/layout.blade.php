<!DOCTYPE html>
<html lang="en">
@include('admin.layout.header')
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
    <div class="col-md-10 text-center text-white"><p class="h4">{{auth('admin')->user()->name}}</p></div>
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Messages Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link text-white py-1" data-toggle="dropdown" href="#">
          <i class="fas fa-2x fa-user-circle"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-right">
          <a href="{{url('admin/profile')}}" class="dropdown-item"> <i class="fas fa-user"></i> &nbsp;&nbsp;My profile</a>
          <a href="{{url('admin/change-password')}}" class="dropdown-item"><i class="fas fa-unlock-alt"></i> &nbsp;&nbsp;Change Password</a>
          <a href="{{url('admin/logout')}}" class="dropdown-item"><i class="fas fa-sign-out-alt"></i> &nbsp;&nbsp;Logout</a>
        </div>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->
  <!-- Main Sidebar Container -->
  @include('admin.layout.sidebar')
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
  @include('admin.layout.footer')
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->
<!-- AdminLTE App -->

<script src="{{asset('plugins/sweetalert2/sweetalert2.min.js')}}"></script>
<script src="{{asset('plugins/toastr/toastr.min.js')}}"></script>
<!-- jQueryConfirm -->
<script src="{{asset('plugins/jquery-confirm/jquery-confirm.min.js')}}"></script>
<script src="{{asset('js/adminlte.min.js')}}"></script>
<script src="{{asset('plugins/block-ui/jquery.blockUI.min.js')}}"></script>
<script src="{{asset('js/page/common.js')}}"></script>
@yield('pageJS')
</body>
</html>
