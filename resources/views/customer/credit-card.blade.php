<!DOCTYPE html>
<html lang="en">
	<head>
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css">
		<link rel="stylesheet" href="{{ asset('plugins/jquery-confirm/jquery-confirm.min.css') }}">
		<link rel="stylesheet" href="{{ asset('css/custom_fr.css') }}">
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
				max-width: 500px;
				margin: auto;
				color: black;
				border-radius: 20 px
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
				width: 100%;
				height: 70px;
				display: flex;
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
		
			.btn.btn-primary:hover .fas.fa-arrow-right {
				transform: translate(15px);
				transition: transform 0.2s ease-in
			}
		
			.form-control {
				color: white;
				background-color: #223C60;
				border: 2px solid transparent;
				height: 60px;
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
		
			@media screen and (min-device-width: 300px) and (max-device-width: 768px) {
				.card {
					max-width: 730px;
					/* height: 64%; */
					padding: 50px;
				}
		
				.form-control::-webkit-input-placeholder {
					/* WebKit browsers */
					font-size: 2em;
					/* 1em -> input font-size * 1 -> 40px * 1 = 40px */
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
					height: 80px;
					display: flex;
					align-items: center;
					justify-content: center;
					font-size: 2.7rem;
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
		<div class="container p-0">
			<div class="card px-4">
				<p class="h8 py-3">Payment Details</p>
				<div class="row gx-3">
					<div class="col-12">
						<div class="d-flex flex-column">
							<input type="hidden" id="orderId" value="{{ isset($orderId) ? $orderId : '' }}">
							<input type="hidden" id="payment_method">
							<p class="text mb-1">Person Name</p> <input class="form-control mb-3" type="text"
								placeholder="Name" id="username" name="username">
						</div>
					</div>
					<div class="col-12">
						<div class="d-flex flex-column">
							<p class="text mb-1">Card Number</p> <input class="form-control mb-3" type="text"
								placeholder="1234 5678 435678" id="input-cc" name="cardNumber">
						</div>
		
					</div>
					<div class="col-12 card_identify" style="display: none;">
						<div class="flex-column">
							<img class="mb-3" src="{{ url('uploads/paymentMethodicon/mastercard.png') }}"
								style="width: 50px;">
						</div>
					</div>
					<div class="col-6">
						<div class="d-flex flex-column">
							<p class="text mb-1">Expiry</p> <input class="form-control mb-3" type="text" placeholder="MM/YY"
								id="expiry" name="expiry">
						</div>
					</div>
					<div class="col-6">
						<div class="d-flex flex-column">
							<p class="text mb-1">CVV/CVC</p> <input class="form-control mb-3 pt-2 cvv" type="password"
								placeholder="***" id="cvv">
						</div>
					</div>
					<div class="col-12">
						<div class="btn btn-primary mb-3" onclick="Checkout()"> <span class="ps-3">Pay</span> <span
								class="fas fa-arrow-right"></span> </div>
					</div>
				</div>
			</div>
		</div>
		
		<script src="https://use.fontawesome.com/releases/v5.7.2/css/all.css"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
		<script src="{{ asset('js/page/root.js') }}"></script>
		<script src="{{ env('CM_CREDITCARD_JS') }}{{ env('CM_TEST_ACCOUNT_NAME') }}"></script>
		<script src="{{ asset('plugins/block-ui/jquery.blockUI.min.js') }}"></script>
		<script src="{{ url('plugins/jquery-confirm/jquery-confirm.min.js') }}"></script>
		<script src="{{ url('js/page/common.js') }}"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/cleave.js/1.0.2/cleave.min.js"></script>
		<script>
			$("#input-cc").keyup(function() {
				if ($(this).val().length > 0) {
					var cc_type = 'unknown';
					var cleave = new Cleave('#input-cc', {
						creditCard: true,
						// delimiter: '-',
						onCreditCardTypeChanged: function(type) {
							console.log(type);
							cc_type = type;
						}
					});
		
					let img_path = '';
					if (cc_type == 'visa') {
						img_path = '<?= url('uploads/paymentMethodicon/visa.png') ?>';
					} else if (cc_type == 'mastercard') {
						img_path = '<?= url('uploads/paymentMethodicon/mastercard.png') ?>';
					} else if (cc_type == 'maestro') {
						img_path = '<?= url('uploads/paymentMethodicon/maestro.png') ?>';
					} else if (cc_type == 'amex') {
						img_path = '<?= url('uploads/paymentMethodicon/amx.png') ?>';
					} else if (cc_type == 'discover') {
						img_path = '<?= url('uploads/paymentMethodicon/discover.png') ?>';
					} else {
						$('.card_identify').hide();
						return
					}
		
					$('.card_identify img').attr('src', img_path);
					$('.card_identify').show();
					$('#payment_method').val(cc_type);
				} else {
					$('.card_identify').hide();
				}
			});
		
			$("#expiry").keyup(function() {
				let expiry = $('#expiry').val();
		
				if (expiry.length == 2 && expiry.indexOf('/') == -1) {
					$('#expiry').val(expiry + '/');
				}
			});
		
		
		
			function Checkout() {
		
				let username = $("#username").val();
				let cardnumber = $("#input-cc").val();
				// console.log(cardnumber);
				let expiry = $("#expiry").val().split('/');
		
				let Expirationmonth = expiry[0];
				let ExpirationYear = expiry[1];
				let cvv = $("#cvv").val();
				let payment_method = $("#payment_method").val();
				cardnumber = cardnumber.replace(/ /g, '')
				let key = cseEncrypt(username, cardnumber, Expirationmonth, ExpirationYear, cvv);
				console.log(username, cardnumber, Expirationmonth, ExpirationYear, cvv)
				console.log(key);
		
				let OrderId = $('#orderId').val();
				loader_show();
				// return
				$.ajax({
					url: '<?= url('makePayment') ?>',
					type: 'GET',
					data: {
						paymentmethod: payment_method,
						key: key,
						OrderId: OrderId
					},
					success: function(response) {
		
						if (response.status == true) {
							loader_hide();
							let resData = response.data.urls[0];
							var form = document.createElement('form');
							document.body.appendChild(form);
							form.method = 'post';
							form.action = resData.url;
							for (postParameters in resData.parameters) {
								var input = document.createElement('input');
								input.type = 'hidden';
								input.name = postParameters;
								input.value = resData.parameters[postParameters];
								form.appendChild(input);
							}
							form.submit();
							// loader_hide();
							// window.location.href = response.redirectUrl;
		
							// console.log(response);
		
						}
						if (response.status == false) {
							loader_hide();
							console.log(response.message);
							$.alert(response.message);
		
						}
					}
				})
		
			}
		</script>
	</body>
</html>