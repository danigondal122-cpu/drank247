@extends('layouts.user')

@props([
	'how_it_works' => [
		[
			'image' => asset('images/icon/location2.svg'),
			'title' => __('messages.works1'),
			'description' => __('messages.description1')
		],
		[
			'image' => asset('images/icon/wine3.svg'),
			'title' => __('messages.works2'),
			'description' => __('messages.description2')
		],
		[
			'image' => asset('images/icon/delivery2.svg'),
			'title' => __('messages.works3'),
			'description' => __('messages.description3')
		]
	]
])

@push('extraStyle')
	<style>
		@media screen and (min-width: 100px) and (max-width: 500px) {
			.main_header_search {
				width: 96% !important;
				display: flex;
				justify-content: flex-end;
				align-items: center;
			}
		}

		.content-header{
			padding:0px !important;
		}

		.container{
			padding:0px !important;
		}
		
		.content{
			padding:0px !important;
		}

		#company-logo .st0 {
			fill: #e91362 !important;
		}

		.layout-navbar-fixed .wrapper .content-wrapper {
			margin-top: calc(3.5rem + 1px) !important;
		}

		.opacity30 {
			opacity: 0.3;
			filter: alpha(opacity=30); /* For IE8 and earlier */
		}

		.carousel-inner .carousel-item.active, .carousel-inner .carousel-item-next, .carousel-inner .carousel-item-prev {
			transition: .5s !important;
		}
	</style>
@endpush

<x-plugins
	vendors="bootstrap-touchspin"
	:js="[
		asset('js/page/cart.js')
	]"
/>

@section('content')
	<x-user.content :slider="$banner">
		@if ($popular_product->count())
			<div class="main_div">
				<div class="box" style="background: #e91362;"></div>
				<div class="box stack-top">
					<div class="row">
						<div class="col-md-1"></div>
						<div class="col-md-10 px-5">
							<h3 class="title text-white mb-4" style="display: inline-block;">
								{{ __('messages.popularproducts') }}
							</h3>
							<x-user.carousel
								id="myCarousel_product"
								class="carousel-multi-item"
								arrows="2"
							>
								@foreach ($popular_product as $product)
									<div @class([
										'carousel-item',
										'active' => $loop->first
									])>
										<div class="col-12 col-sm-12 col-lg-3 col-md-4 col-xl-2 align-items-stretch">
											<x-user.product-card :product="$product" />
										</div>
									</div>
								@endforeach
							</x-user.carousel>
						</div>
						<div class="col-md-1"></div>
					</div>
				</div>
			</div>
		@endif

		<div class="col-lg-12 px-5 SmallDivPadding" style="margin-top:100px;">
			<div class="my-2">
				<div class="row justify-content-center">
					<div class="col-md-10 px-5 SmallDivPadding">
						<h3 class="title text-center align-self-center mb-5 mt-4">
							<b style="color:#e91362">{{ __('messages.howitworks') }}</b>
						</h3>
						<x-user.carousel
							id="carousel3"
							class="carousel-multi-item"
							arrows="3"
						>
							<x-slot:indicators>
								<li data-target="#carousel3" data-slide-to="0" class="active"></li>
								<li data-target="#carousel3" data-slide-to="1"></li>
								<li data-target="#carousel3" data-slide-to="2"></li>
							</x-slot>

							@foreach ($how_it_works as $item)
								<div @class([
									'carousel-item',
									'active' => $loop->first
								])>
									<div class="col-12 col-sm-12  col-lg-4 col-md-4 col-xl-4">
										<div class="h-100" style="width: 100%;">
											<div class="px-2 text-center" style="height: 120px;">
												<img src="{{ $item['image'] }}" class="card-img-top cart-item-img" style="max-height: 120px; margin-left: auto; margin-right: auto;" alt="">
											</div>
											<div class="text-center">
												<span class="dot">{{ $loop->iteration + 1 }}</span>
											</div>
											<hr class="card-border">
											<div class="card-body px-2  py-3">
												<div class="main_card_text_h">
													<h5 class="card-text text-center works_card_text" style="color:#d42f7a;"><b>{{ $item['title'] }}</b></h5>
												</div>
												<div class="main_card_text_p">
													<p class="card-text text-center works_card_text" title="" >{{ $item['description'] }}</p>
												</div>
											</div>
										</div>
									</div>
								</div>
							@endforeach
						</x-user.carousel>
						<h3 class="title text-center align-self-center mt-5 mb-4">
							<b style="color:#e91362">{{ __('messages.easyasthat') }}</b>
						</h3>
					</div>
				</div>
			</div>
		</div>
		
		<div class="" style="background-color:#e91362;">
			<div class="row">
				<div class="col-md-5 dw_aap-div align-self-center text-center">
					<span class="s_firstclass" style="color:#fff;">Download The App</span>
					<span class="s_secondclass" style="color:#fff;"><i>Click, sit back and enjoy.</i></span>
					<img class="lazy lazy-loaded" src="{{ asset('images/icon/google.png') }}" alt="Download the app android" data-was-processed="true">
					<img class="lazy lazy-loaded" src="{{ asset('images/icon/apple.png') }}"  alt="Download the app ios" data-was-processed="true">
				</div>
				<div class="col-md-7 mt-5 align-self-stretch">
					<img class="main_dw_app"  src="{{ asset('images/icon/Mobile.png') }}" width="90%" alt="Download the app android" data-was-processed="true">
				</div>
			</div>
		</div>
	</x-user.content>

	<x-common-modal view="user" />
@endsection

@push('exstraScript')
	<script>
		$(document).on('submit', '#postcodeFrom', function (e) {
			e.preventDefault();
			
			var data = new FormData(this);
			$('.is-invalid').removeClass('is-invalid');
			$('.text-danger').html('');

			$.ajax({
				url: SITE_URL + 'customer/checkPostcode',
				type: 'POST',
				data: data,
				cache: false,
				contentType: false,
				processData: false,
				success: function (obj) {
					if (!obj.status && obj.type == 'validation')
					{
						loader_hide();
						for (key in obj.errors) {
							$('#' + key).addClass('is-invalid');
							$('#postcode').focus();
						}
					}
					else if(!obj.status && obj.type == 'invalidPostcode')
					{
						$.alert(obj.message)
					}

					if (obj.status)
					{
						$.alert(obj.message)
					}
				},
			});
		});

		$('#myCarousel').carousel({ interval: false })
		$('#myCarousel_product').carousel({ interval: false })
		$('#carousel3').carousel({ interval: false })
		$('#myCarousel_product.carousel .carousel-item').each(function() {
			var minPerSlide = 6;
			var next = $(this).next();
			if (!next.length) {
				next = $(this).siblings(':first');
			}
			next.children(':first-child').clone().appendTo($(this));
		
			for (var i = 0; i < minPerSlide; i++) {
				next = next.next();
				if (!next.length) {
					next = $(this).siblings(':first');
				}
		
				next.children(':first-child').clone().appendTo($(this));
			}
		});

		$('#carousel3.carousel .carousel-item').each(function() {
			var minPerSlide = 3;
			var next = $(this).next();
			if (!next.length) {
				next = $(this).siblings(':first');
			}
			next.children(':first-child').clone().appendTo($(this));
		
			for (var i = 0; i < minPerSlide; i++) {
				next = next.next();
				if (!next.length) {
					next = $(this).siblings(':first');
				}
		
				next.children(':first-child').clone().appendTo($(this));
			}
		});

		jQuery('img.svg').each(function(){
			var $img = jQuery(this);
			var imgID = $img.attr('id');
			var imgClass = $img.attr('class');
			var imgURL = $img.attr('src');

			jQuery.get(imgURL, function(data) {
				// Get the SVG tag, ignore the rest
				var $svg = jQuery(data).find('svg');

				// Add replaced image's ID to the new SVG
				if(typeof imgID !== 'undefined') {
					$svg = $svg.attr('id', imgID);
				}
				// Add replaced image's classes to the new SVG
				if(typeof imgClass !== 'undefined') {
					$svg = $svg.attr('class', imgClass+' replaced-svg');
				}

				// Remove any invalid XML tags as per http://validator.w3.org
				$svg = $svg.removeAttr('xmlns:a');

				// Replace image with new SVG
				$img.replaceWith($svg);

			}, 'xml');

		});
	</script>
@endpush