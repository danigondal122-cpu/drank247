@extends('layouts.user')

@push('extraStyle')
	<style>
		.card {
			border-radius: .25rem;
		}

		.px-5 {
			padding-left:1rem !important;
			padding-right:1rem !important;
		}

		.captcha img {
			width: 40%;
		}

		.bg-error {
			background-color: #d03636 !important;
			color: #FFF;
		}

		.toasts-top-right.fixed {
			margin-top: 15px;
			margin-right: 15px;
		}
	</style>
@endpush

<x-plugins
	vendors="google-recaptcha" 
	:js="[
		asset('js/page/contactus.js')
	]"
/>

@section('content')
	<x-user.content>
		<x-slot:breadcrumbs col="col-md-6" :title="__('messages.contactus')">
			<li class="breadcrumb-item active">{{ __('messages.contactus') }}</li>
		</x-slot>

		<div class="row justify-content-center">
			<div class="col-md-6">
				<div class="card">
					<div class="card-body">
						<div class="tab-content">
							<div class="tab-pane active" id="profile">
								<form class="form-horizontal" id="contact_usform">
									@csrf
									<div class="form-group row">
										<label for="inputName" class="col-sm-2 col-form-label">{{ __('messages.name') }}</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="name" name="name" placeholder="{{ __('messages.name') }}" value="" autocomplete="">
											<span id="name_error" class="text-danger"></span>
										</div>
									</div>
									<div class="form-group row">
										<label for="inputEmail" class="col-sm-2 col-form-label">{{ __('messages.email') }}</label>
										<div class="col-sm-10">
											<input type="text" id="email" name="email" class="form-control" placeholder="{{ __('messages.email') }}" autocomplete="">
											<span id="email_error" class="text-danger"></span>
										</div>
									</div>
									<div class="form-group row">
										<label class="col-sm-2 col-form-label">{{ __('messages.contactno') }}</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="contact_no" name="contact_no" placeholder="{{ __('messages.contactno') }}" value="" autocomplete="">
											<span id="contact_no_error" class="text-danger"></span>
										</div>
									</div>
									<div class="form-group row">
										<label class="col-sm-2 col-form-label">{{ __('messages.subject') }}</label>
										<div class="col-sm-10">
											<select  class="form-control select2" id="subject" name="subject" onchange="getOtherFeild();" >
												<option value="">{{ __('messages.choosesubject') }}</option>
												<option value="0">{{ __('messages.question0') }}</option>
												<option value="1">{{ __('messages.question1') }}</option>
												<option value="2">{{ __('messages.question2') }}</option>
												<option value="3">{{ __('messages.question3') }}</option>
												<option value="4">{{ __('messages.question4') }}</option>
												<option value="other">{{ __('messages.question5') }}</option>
											</select>
											<span id="subject_error" class="text-danger"></span>
										</div>
									</div>
									<div class="form-group row" id="OtherFeildDiv"  style="display:none;">
										<label class="col-sm-2 col-form-label"></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="other_subject" name="other_subject" placeholder="Please enter Subject" value="" autocomplete="">
											<span id="other_subject_error" class="text-danger"></span>
										</div>
									</div>
									<div class="form-group row">
										<label class="col-sm-2 col-form-label">{{ __('messages.message') }}</label>
										<div class="col-sm-10">
											<textarea type="text" class="form-control" id="message" name="message" placeholder="{{ __('messages.pleaseentermessage') }}" value="" autocomplete=""></textarea>
											<span id="message_error" class="text-danger"></span>
										</div>
									</div>
									<div class="form-group row">
										<label class="col-sm-2 col-form-label"></label>
										<div class="col-sm-10">
											<div class="captcha">
												<div id="html_element"></div>
												<span id="g-recaptcha-response_error" class="text-danger"></span>
											</div>
										</div>
									</div>
									<div class="form-group row">
										<div class="offset-sm-2 col-sm-10">
											<button type="submit" class="btn btn-secondary">{{ __('messages.submit') }}</button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</x-user.content>
@endsection

@push('exstraScript')
	<script type="text/javascript">
		var onloadCallback = function() {
			grecaptcha.render('html_element', {
			'sitekey' : '{{ env("NOCAPTCHA_SITEKEY") }}'
			});
		};

		function getOtherFeild()
		{
			var subject = $('#subject').val();

			if (subject == 'other')
			{
				$('#OtherFeildDiv').show();
			}
			else
			{
				$('#OtherFeildDiv').hide();
			}
		}

		$('#contact_usform').on('submit', function (e) {
			e.preventDefault();
			loader_show();
			var data = new FormData(this);
			var form = $(this);

			$('.is-invalid').removeClass('is-invalid');
			$('.text-danger').html('');

			$.ajax({
				url: '{{ route('ajax.contact.us') }}',
				type: 'POST',
				data: data,
				cache: false,
				contentType: false,
				processData: false,
				success: function (obj) {
					if (!obj.status)
					{
						var ToastsTitle = 'Error!';

						if (obj.type == 'VALIDATION')
						{
							ToastsTitle = 'Validation Error!';

							for (key in obj.errors)
							{
								$('#contact_usform #' + key).addClass('is-invalid');
								$('#contact_usform #' + key+'_error').html(obj.errors[key]);
							}
						}

						$(document).Toasts('create', {
							class: 'bg-error',
							title: ToastsTitle,
							autohide: true,
							delay: 3000,
						});
					}
					else
					{
						$(document).Toasts('create', {
							class: 'bg-success',
							title: obj.msg,
							autohide: true,
							delay: 3000,
						});

						form[0].reset();

						if (obj.page)
						{
							window.location = SITE_URL + obj.page;
						}
					}

					loader_hide();
				}
			});
		});
	</script>
@endpush