<!DOCTYPE html>
<html lang="en">
	<head>
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css">
		<link rel="stylesheet" href="{{ asset('css/custom_fr.css') }}">
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<style>
			@import url('https://fonts.googleapis.com/css?family=Montserrat:400,700&display=swap');
			
			* {
				margin: 0;
				padding: 0;
				box-sizing: border-box;
				font-family: 'Montserrat', sans-serif
			}
			
			body {
				display: flex;
				justify-content: center;
				align-items: center;
				min-height: 100vh;
				background-color: #0C4160;
				padding: 30px 10px
			}
			
			.card {
				max-width: 40vw;
				margin: auto;
				color: black;
				border-radius: 20px;
			}
			
			p {
				margin: 0px
			}
			
			.container .h8 {
				font-size: 30px;
				font-weight: 800;
				text-align: center
			}
			
			.btn.btn-primary {
				min-width: 130px;
				height: 52px;
				display: inline-flex;
				align-items: center;
				justify-content: space-between;
				padding: 0 15px;
				background-image: linear-gradient(to right, #77A1D3 0%, #79CBCA 51%, #77A1D3 100%);
				border: none;
				transition: 0.5s;
				background-size: 200% auto
			}
			
			.btn.btn.btn-primary:hover {
				background-position: right center;
				color: #fff;
				text-decoration: none
			}
			
			.btn.btn-primary .fas.fa-arrow-right {
				transition: transform 0.2s ease-in
			}

			.btn.btn-primary:hover .fas.fa-arrow-right {
				transform: translate(40px);
			}
			
			.form-control {
				color: white;
				background-color: #223C60;
				border: 2px solid transparent;
				height: 55px;
				padding-left: 20px;
				vertical-align: middle
			}
			
			.form-control:focus {
				color: white;
				background-color: #0C4160;
				border: 2px solid #2d4dda;
				box-shadow: none
			}
			
			.text {
				font-size: 14px;
				font-weight: 600
			}
			
			::placeholder {
				font-size: 14px;
				font-weight: 600
			}
			
			@media screen and  (min-device-width: 300px) and (max-device-width: 768px){
				.card {
					max-width: 70vw;
					padding: auto;
				}
			
				.form-control::-webkit-input-placeholder {
					font-size: 2em;
				}
		
				.container .h8 {
					font-size: 39px;
				}
		
				.text {
					font-size: 34px;
				}
		
				.form-control {
					height: 80px;
				}
			
				.btn.btn-primary {
					margin-top: 30px;
					height: 75px;
					font-size: 1.4rem;
				}
		
				.container,
				.container-md,
				.container-sm {
					max-width: 750px;
				}
			
				.img-logo {
					height: 75%;
					padding-top: 40px;
				}
			}
		</style>
	</head>
	<body>
		<div class="container p-0 idin_container">
			<div class="card px-4">
				<p class="h8 py-3">Create account using iDIN</p>
				<div class="row gx-3">
					<div class="col-12 mt-3">
						<div class="row">
							<div class="col-sm-4 col-md-2">
								<img src="{{ asset('uploads/Logos_iDIN/iDIN logo rgb 48 pixels.png') }}" class="img-logo">
							</div>
							<div class="col-sm-8 col-md-10">
								<p class="text mb-1">iDIN is a safe way to identify you to the seller. It ensures quick registration and login through your bank.</p>
							</div>
						</div>
					</div>
					<div class="col-12 mt-4">
						<input type="hidden" id="orderId" name="orderId" value="{{ $orderId ?? '' }}">
						<input type="hidden" id="order_key" value="{{ $order_key ?? '' }}">
						<p class="text mb-1">iDIN login</p>
						<select class="form-control mb-3" id="bank_issuer" name="bank_issuer">
							<option class="d-none" value="">Select Your Bank</option>
							@foreach($banklist as $bank)
								<option value="{{ $bank['issuerId'] }}">{{ $bank['issuerName'] }}</option>
							@endforeach
						</select>
					</div>
					<div class="col-12 mt-3 mb-4 text-end">
						<div class="btn btn-primary overflow-hidden" onclick="Checkout()">
							<span class="fw-bold ps-3">Pay</span><span class="fas fa-arrow-right"></span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
		<script src="{{ asset('js/page/root.js') }}"></script>
		<script src="{{ asset('plugins/block-ui/jquery.blockUI.min.js') }}"></script>
		<script src="{{ asset('js/page/common.js') }}"></script>
		<script>
			function Checkout()
			{
				let bank_issuer = $("#bank_issuer").val();
				let payment_method = 'IDIN';
				let order_key = $('#order_key').val();
				let orderId = $('#orderId').val();
				loader_show();

				$.ajax({
					url: '{{ url('paynlPayment') }}',
					type: 'POST',
					data: {
						_token: '{{ csrf_token() }}',
						paymentmethod: payment_method,
						bank_issuer: bank_issuer,
						order_key: order_key,
						OrderId: orderId,
						redirecturl: true
					},
					success: function(response) {
						loader_hide();

						if (response.status == true)
						{
							location.href = response.redirectUrl;
						}
						else
						{
							alert(response.message);
						}
					}
				})
			}

			function register()
			{
				loader_show();
				let bank_issuer = $('#bank_issuer').val();
				let orderId = $('#orderId').val();

				$.ajax({
					url: '{{ url('idinbanktransaction') }}',
					type: 'GET',
					data: {
						bank_issuer: bank_issuer,
						orderId:orderId
					},
					success: function(response) {
						loader_hide();

						if (response.status)
						{
							window.location.href = response.url;
						}
					}
				})
			}
		</script>
	</body>
</html>