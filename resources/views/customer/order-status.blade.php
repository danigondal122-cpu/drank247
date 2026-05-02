@extends('layouts.user')

@push('extraStyle')
	<style>
		.main_success_order {
			height: 100%;
			width: 100%;
			display: flex;
			align-items: center;
			justify-content: center;
		}

		.success_order_card {
			background: #33b579;
			min-width: 400px;
			max-width: 460px;
			min-height: 400px;
			height: auto;
			text-align: center;
			display: flex;
			align-items: center;
			justify-content: center;
			flex-direction: column;
			padding: 30px 40px 48px;
			border-radius: 15px;
			position: relative;
		}

		.cancel_order_card {
			background: #e91362;
		}

		.success_order_card .success_order_card_img {
			width: 200px;
			height: auto;
		}

		.success_order_card .success_order_card_img img {
			width: 100%;
			height: 100%;
			object-fit: cover;
		}

		.success_order_card .success_order_card_contant {
			margin: 30px 0;
			color: white;
		}

		.success_order_card .success_order_card_contant h2 {
			font-size: 30px;
			font-weight: 600;
		}

		.success_order_card .success_order_card_contant p {
			font-size: 20px;
			/* font-weight:600; */
		}

		.white_space {
			white-space: nowrap;
		}

		.success_order_card .success_order_card_btn {
			display: flex;
			width: 100%;
		}

		.success_order_card .success_order_card_btn .order_card_btn {
			background: white;
			/* padding: 14px 30px; */
			border-radius: 100px;
			color: black;
			font-size: 18px;
			font-weight: bold;
			width: 100%;
			height: 50px;
			display: flex;
			align-items: center;
			justify-content: center;
		}

		.fireworks {
			position: absolute;
			width: 150px;
			height: 150px;
			background: #FFEFAD;
			/*     padding-bottom: 100px; */
			-webkit-mask: url('https://imgservices-1252317822.image.myqcloud.com/image/081320210201435/e9951400.png') right top no-repeat;
			-webkit-mask-size: auto 150px;
			animation: fireworks 2s steps(24) infinite, random 8s steps(1) infinite, random_color 1s infinite;
		}

		@keyframes fireworks {
			0% {
				-webkit-mask-position: 0%;
			}

			50%,
			100% {
				-webkit-mask-position: 100% 100%;
			}
		}

		@keyframes random {
			0% {
				transform: translate(0, 0);
			}

			25% {
				transform: translate(200%, 50%) scale(1.8);
			}

			50% {
				transform: translate(80%, 80%) scale(1.5);
			}

			75% {
				transform: translate(20%, 60%) scale(1.65);
			}
		}

		@keyframes random_color {
			0% {
				background-color: #FFEFAD;
			}

			25% {
				background-color: #ffadad;
			}

			50% {
				background-color: #aeadff;
			}

			75% {
				background-color: #adffd9;
			}
		}

		@media screen and (prefers-reduced-motion) {

			/* 禁用不必要的动画 */
			.fireworks {
				animation: none;
			}
		}

		@media screen and (max-width: 400px) {

			.success_order_card {
				min-width: 90%;
				max-width: 100%;
			}
		}
	</style>
@endpush

@section('content')
	<x-user.content>
		@if ($status == 'Success' || $status == 'Pending')
			<main class="main_success_order" style="padding-bottom: 35px;">
				<div class="success_order_card">
					<div class="success_order_card_img">
						<img src="{{ asset('images/icon/happy.png') }}">
						<div class="fireworks" style="left: -20%; top: 5%;"></div>
						<div class="fireworks" style="right: 30%; top: 13%; animation-delay: -0.4s;"></div>
						<div class="fireworks" style="left: 5%; top: 23%; animation-delay: -1.7s;"></div>
						<div class="fireworks" style="right: 45%; top: 8%; animation-delay: -3.1s;"></div>
					</div>
					<div class="success_order_card_contant">
						<h2>Success!</h2>
						<p class="mb-0">
							Congratulations...!
							<br>
							{{ $message }}
						</p>
					</div>
					<div class="success_order_card_btn">
						<a href="javascript:;" class="order_card_btn" onclick="trackOrder()">
							Track Delivery
						</a>
					</div>
				</div>
			</main>
		@else
			<main class="main_success_order" style="padding-bottom: 35px;">
				<div class="success_order_card cancel_order_card">
					<div class="success_order_card_img">
						<img src="{{ asset('images/icon/sad_box.png') }}">
					</div>
					<div class="success_order_card_contant">
						<h2>Oops...</h2>
						<p class="white_space mb-0">
							{{$message}}
							<br>
							Please try again.
						</p>
					</div>
					<div class="success_order_card_btn">
						<a href="javascript:;" class="order_card_btn" onclick="tryAgain()">
						Try again
						</a>
					</div>
				</div>
			</main>
		@endif
	</x-user.content>
@endsection

@push('exstraScript')
	<script>
		function trackOrder() {
			sessionStorage.setItem('selectedtab', 'orderhistory_tab');
			window.location.href = SITE_URL + 'profile';
		}

		function tryAgain() {
			sessionStorage.setItem('selectedtab', 'orderhistory_tab');
			window.location.href = SITE_URL + 'cart';
		}
	</script>
@endpush
