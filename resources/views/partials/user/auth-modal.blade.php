<style>
	#loginModule, #registerModule, #forgotPasswordModule {
		overflow: auto;
	}

	.modal-content {
		border-radius: 0.8rem;
	}
	
	#loginModule .modal-body,
	#registerModule .modal-body,
	#forgotPasswordModule .modal-body {
		padding:0 40px;
	}

	.close {
		position: absolute;
		top: 10px;
		right: 15px;
	}

	.sign_in_popup {
		padding:0 40px;
		padding-bottom:20px;
	}

	.sign_in_popup p {
		margin-bottom:5px !important;
	}

	.sign_in_popup p a {
		font-size:13px;
		color:black;
	}

	.sign_in_popup p a:hover {
		color:#d42f7a;
	}

	.forogt_modal {
		width:360px;
	}

	.forogt_modal .modal-footer{
		border:0;
		padding-top:35px;
		padding-left: 35px;
	}

	.forogt_modal .modal-footer .btn {
		padding: 0.375rem 1.75rem;
	}
</style>

<x-plugins :js="[asset('js/page/customer_login.js')]" />

<div class="modal fade" id="loginModule" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm modal-dialog-centered">
		<div class="modal-content">
			<form action="#" method="POST" id="loginForm" class="mb-2">
				@csrf
				<div class="modal-header py-2">
					<h5 class="text-center w-100 modal-title" id="exampleModalLabel">Login</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body py-1">
					<div class="login-card-body p-0">
						<div class="social-auth-links text-center mb-3">
							<div class="d-flex mb-3">
								<a href="{{ url('auth/facebook') }}" class="btn btn-block btn-primary btn-facebook w-50 mt-2 mr-2">
									<i class="fab fa-facebook mr-2"></i> Facebook
								</a>
								<a href="{{ url('auth/google') }}" class="btn btn-block btn-danger w-50">
									<i class="fab fa-google-plus mr-2"></i> Google
								</a>
							</div>
							<p>----------- OR -----------</p>
						</div>
					</div>
					<div class="form-group mb-1">
						<label for="recipient-name" class="col-form-label">{{ __('messages.email')}}:</label>
						<input type="email" class="form-control" id="email" name="email" placeholder="Email">
						<span id="email_error" class="text-danger error_span"></span>
					</div>
					<div class="form-group mb-1">
						<label for="message-text" class="col-form-label">{{ __('messages.password')}}:</label>
						<input type="password" name="password" id="password" class="form-control" placeholder="Password">
						<span id="password_error" class="text-danger error_span"></span>
					</div>
					<div class="row pt-3">
						<div class="col-12">
							<button type="submit" class="btn btn-primary btn-block">{{ __('messages.signin')}}</button>
						</div>
						<!-- /.col -->
					</div>
				</div>
			</form>
			<div class="sign_in_popup">
				<p class="mb-0">
					<a
						href="javascript:;"
						class="text-center"
						data-dismiss="modal"
						data-toggle="modal"
						data-target="#forgotPasswordModule"
					>{{ __('messages.iforgotmypassword')}}</a>
				</p>
				<p class="mb-1">
					<a
						href="javascript:;"
						class="text-center"
						data-dismiss="modal"
						data-toggle="modal"
						data-target="#registerModule"
					>{{ __('messages.notamember')}}</a>
				</p>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="registerModule" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm modal-dialog-centered">
		<div class="modal-content">
			<form action="#" method="POST" id="registerForm" class="mb-2">
				@csrf
				<div class="modal-header py-2">
					<h5 class="text-center w-100 modal-title" id="exampleModalLabel">{{ __('messages.register')}}</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body py-1">
					<div class="login-card-body p-0">
						<div class="social-auth-links text-center mb-3">
							<div class="d-flex mb-3">
								<a href="{{ url('auth/facebook') }}" class="btn btn-block btn-primary btn-facebook w-50 mt-2 mr-2">
									<i class="fab fa-facebook mr-2"></i> Facebook
								</a>
								<a href="{{ url('auth/google') }}" class="btn btn-block btn-danger w-50">
									<i class="fab fa-google-plus mr-2"></i> Google
								</a>
							</div>
							<p>----------- OR -----------</p>
						</div>
					</div>
					<div class="form-group mb-1">
						<label for="recipient-name" class="col-form-label">{{ __('messages.name')}}:</label>
						<input type="text" class="form-control" id="name" name="name" placeholder="Name">
						<span id="name_error" class="text-danger error_span"></span>
					</div>
					<div class="form-group mb-1">
						<label for="recipient-name" class="col-form-label">{{ __('messages.email')}}:</label>
						<input type="email" class="form-control" id="email" name="email" placeholder="Email">
						<span id="email_error" class="text-danger error_span"></span>
					</div>
					<div class="form-group mb-1">
						<label for="message-text" class="col-form-label">{{ __('messages.password')}}:</label>
						<input type="password" class="form-control" name="password" id="password" placeholder="Password">
						<span id="password_error" class="text-danger error_span"></span>
					</div>
					<div class="form-group mb-1">
						<label for="message-text" class="col-form-label">{{ __('messages.accounttype')}}:</label><br>
						<input type="radio" id="Personal" name="type"  value="0" {{ (empty($row)==false && $row->vat=="9")?'checked':'checked'}}>
						<label for="Personal" class="mr-2">{{ __('messages.personal')}}</label>
						<input type="radio" id="Business" name="type" value="1">
						<label for="Business">{{ __('messages.business')}}</label>
					</div>
					<div class="row pt-3">
						<div class="col-12">
							<button type="submit" class="btn btn-primary btn-block">{{ __('messages.signin')}}</button>
						</div>
						<!-- /.col -->
					</div>
				</div>
			</form>
			<div class="sign_in_popup">
				<p class="mb-1">
					<a
						href="javascript:;"
						class="text-center"
						data-dismiss="modal"
						data-toggle="modal"
							data-target="#loginModule"
					>{{ __('messages.alreadymember')}}</a>
				</p>
			</div>
		</div>
	</div>
</div>
  
<div class="modal fade" id="forgotPasswordModule" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog  modal-dialog-centered forogt_modal">
		<div class="modal-content">
			<form action="#" method="POST" id="forgotForm" class="mb-3">
				@csrf
				<div class="modal-header">
					<h5 class="modal-title text-center w-100" id="exampleModalLabel">Forgot Password</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body" style="padding 20px 25px;">
					<div class="form-group mb-1">
						<label for="recipient-name" class="col-form-label">Enter your Email address and we'll send you a link to reset your password.</label>
						<input type="email" class="form-control" id="email" name="email" placeholder="Email">
						<span id="email_error" class="text-danger error_span"></span>
					</div>
				</div>
				<div class="modal-footer">
					<div class="col-12">
						<button type="submit" class="btn btn-primary">Submit</button>
						<!-- /.col -->
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

@push('exstraScript')
	<script>
		$("#loginModule, #registerModule").on('hide.bs.modal', function() {
			$('.error_span').text('');
			$('.is-invalid').removeClass('is-invalid');
		});
	</script>
@endpush