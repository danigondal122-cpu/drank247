<!DOCTYPE html>
<html lang="en">
	@include('partials.user.header')

	<body class="hold-transition layout-top-nav layout-fixed layout-navbar-fixed layout-footer-fixed">
		<div class="wrapper" style="overflow-x: hidden !important;">
			<!-- Navbar -->
			@include('partials.user.navbar')
			<!-- /.navbar -->
			
			@yield('content')

			@if(!auth('customer')->check())
				@include('partials.user.auth-modal')
			@endif
			
			<!-- Main Footer -->
			@include('partials.user.footer')
		</div>
		<!-- ./wrapper -->

		<!-- AdminLTE App -->
		<script src="{{ asset('js/page/root.js') }}"></script>
		<!-- jQueryConfirm -->
		<script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
		<script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
		<script src="{{ asset('plugins/jquery-confirm/jquery-confirm.min.js') }}"></script>
		<script src="{{ asset('js/adminlte.js') }}"></script>
		<script src="{{ asset('js/page/common.js') }}"></script>
		@stack('pageJS')
		@stack('exstraScript')
	</body>
</html>