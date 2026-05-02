<style>
	.dropdown-item a{
		color: #000;
	}

	.my_categories .show .custom_nav {
		position: absolute;
		width: 250px;
		display: block !important;
	}

	@media screen and (max-width:991px) {
		.my_categories .show .custom_nav {
			position: fixed;
			top: 0;
			left: 0;
			opacity: 1;
			width: 100%;
			transition: 0.5s;
			display: block !important;
			border-radius:0 16px 16px 0;
		}
	}

	@media screen and (max-width: 400px) {
		.my_categories .show .custom_nav{
			width: 145%;
		}
	}

	.custom_nav ul {
		list-style: none;
		margin: 0;
		background: white;
		height: 100%;
		padding: 0;
	}

	.custom_nav ul li {
		/* Sub Menu */
	}

	.custom_nav ul li a  {
		display: block;
		background: white;
		padding: 10px 15px;
		color: #333;
		text-decoration: none;
		-webkit-transition: 0.2s linear;
		-moz-transition: 0.2s linear;
		-ms-transition: 0.2s linear;
		-o-transition: 0.2s linear;
		transition: 0.2s linear;
	}

	.custom_nav ul li :hover {
		background: #f8f8f8;
		color: #515151;
	}

	.custom_nav ul li a .fa {
		width: 16px;
		text-align: center;
		margin-right: 5px;
		float:right;
	}

	.custom_nav ul ul {
		background-color:#ebebeb;
	}

	.custom_nav ul li ul li a {
		background: #f8f8f8;
		border-left: 4px solid transparent;
		padding: 10px 20px;
	}

	.custom_nav ul li ul li a:hover {
		background: #ebebeb;
		border-left: 4px solid #3498db;
	}

	.has-search .form-control {
			padding-left: 2.375rem;
	}

	.has-search .form-control-feedback {
		width: 2.375rem;
		height: 2.375rem;
		color: #aaa;
		display: flex;
		align-items:center;
		justify-content: center;
	}

	.header_search {
		margin-right:5px;
		background:white;
		border-radius: 0.25rem;
		margin-bottom:0;
	}

	.header_search_input {
		height: 100% !important;
		border: 0;
		padding-left:5px;
	}

	.header_search_input:focus {
		border:0;
	}

	.ui-autocomplete .ui-menu-item{
		color:#333;
		background: #ebebeb;
	}

	.ui-menu{
		margin-top:50px;
		z-index: 9999999;
	}

	@media screen and (min-width: 100px) and (max-width: 500px){
		.main_header_search {
			width:67%;
			display: flex;
			justify-content: flex-end;
			align-items:center;
		} 
	}

	@media screen and (min-width:1001px) and (max-width:1600px) {
		.saperate_headers .main_header_div {
			padding:0 20px!important; 
		}

		.nav_padding_0 {
			padding-left: 0;
		}

		.saperate_headers .nav_padding_0 li a{
			padding-right: 0;
			font-size:15px;
		}

		.saperate_headers .navbar-brand img {
			width:100px;
		}

		.saperate_headers .header_search {
			width:50% !important;
		}

		.saperate_headers .main_header_search {
			display: flex;
			justify-content: flex-end;
			align-items:center;
		}

		.header_profile-user-img {
			width:30px;
		}
	}

	@media screen and (min-width:1001px) and (max-width:1350px) { 
		.saperate_headers .nav_padding_0 li a{
			font-size:13px;
			padding-right:0 !important;
		}
	}
</style>

<nav @class([
	'main-header navbar navbar-expand-lg navbar-light elevation-1',
	'saperate_headers' => Request::segment(1)
]) style="height: 60px;">
	<div class="container-fluid main_header_div px-5">
		@if (Request::segment(1))
			<a href="{{ url('/') }}" class="navbar-brand">
				<img src="{{ asset('img/white_logo.svg') }}" alt="247Drank" class="brand-image">
			</a>
		@endif

		<div class="main_nav_div">
			<div class="collapse navbar-collapse order-3" id="navbarCollapse">
				<!-- Left navbar links -->
				<ul class="navbar-nav text-white nav_padding_0">
					<li class="nav-item">
						<a href="{{ url('/') }}" class="{{ !Request::segment(1) ? 'nav-link extra active' : 'nav-link extra' }}">Home</a>
					</li>
					<div class="my_categories">
						<li class="nav-item custom_navigation">
							<a
								href="#"
								type="button"
								id="dropdownMenuButton"
								data-toggle="dropdown"
								aria-expanded="false"
								@class([
									'nav-link extra',
									'active' => Request::segment(1) == 'products'
								])
							>{{ __('messages.categories') }}</a>
							<ul aria-labelledby="dropdownMenuButton">
								<nav class='custom_nav animated bounceInDown wrapper categories_popup'>
									<ul>
										@foreach ($global['category_nav'] as $category)
											@if ($category->subcat()->get()->count())
												<li class="sub-menu" style="background-color: #ebebeb;">
													<a href="{{ url('products/' . str_replace(' ', '_', $category->category_name)) }}" style="display: inline-block">{{ $category->category_name }}</a>
													<div class="fa fa-caret-down right" style="display: inline-block; float: right; margin-right: 10px; color: black; margin-top: 10px;"></div>
													<ul>
														@foreach ($category->subcat()->get() as $subcat)
                											<li><a href="{{ url('products/' . $subcat->id) }}">{{ $subcat->category_name }}</a></li>
														@endforeach
													</ul>
												</li>
											@else
												<li><a href="{{ url('products/' . str_replace(' ', '_', $category->category_name)) }}">{{ $category->category_name }}</a></li>
											@endif
										@endforeach
										<li><a href="{{ url('products/extra_product') }}">Other Category</a></li>
									</ul>
								</nav>
							</ul>
						</li>
					</div>
					<li class="nav-item">
						<a
							href="{{ url('/contact_us') }}"
							@class([
								'nav-link extra',
								'active' => Request::segment(1) == 'contact_us'
						])>{{ __('messages.contactus') }}</a>
					</li>
					<li class="nav-item">
						<a
							href="{{ url('/cart') }}"
							@class([
								'nav-link extra',
								'active' => Request::segment(1) == 'cart'
						])>
							<i class="fas fa-cart-arrow-down"></i> {{ __('messages.cart') }}
							(<span id="cart_total_item">{{ cart()->count() }}</span>)
						</a>
					</li>
					@if (auth()->check())
						<li class="nav-item">
							<a
								href="{{ url('/favourite') }}"
								@class([
								'nav-link extra',
								'active' => Request::segment(1) == 'favourite'
							])>
								<i class="far fa-heart"></i> {{ __('messages.favourite') }}
								(<span id="favourite_item_total">{{ $global['favourite_count'] }}</span>)
							</a>
						</li>
					@endif
					@if (isLocale('nl'))
						<li class="nav-item">
							<a href="https://www.nix18.nl/" class="nav-link extra" target="_blank">{{ __('messages.nix18') }}</a>
						</li>
						<li class="nav-item">
							<a href="https://www.idin.nl/" class="nav-link extra"  target="_blank">{{ __('messages.idin') }}</a>
						</li>
					@endif
				</ul>
				<div class="navbar_effect">
					<div class="main_line_effect"></div>
				</div>
			</div>
		</div>
		<div class="black_bg"></div>
 
		<!-- Right navbar links -->
		<ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto main_header_search">
			<div class="form-group has-search header_search" style="width: 100%; display: flex; justify-content: space-between; padding: 0 !important; align-items: center;">
				<span class="fa fa-search form-control-feedback"></span>
				<input style="width: 95%; padding:0 !important" type="text" class="form-control header_search_input" id="product_search" name="product_search">
			</div>
			<li class="nav-item dropdown">
				<a class="nav-link text-white py-1 d-flex align-items-center header_profile_div " data-toggle="dropdown" href="#" style="padding: 2px;">
					<button class="locale-flag {{ isLocale('nl') ? 'locale-flag-dutch' : 'locale-flag-eng' }}" style="z-index:-1" ></button>
				</a>
				<div class="dropdown-menu dropdown-menu-right static_remove">
					<a href="{{ route('locale', 'en') }}" class="dropdown-item"> 
						<div class="row">
							<div class="col-4"> <span class="locale-flag locale-flag-eng"></span></div>
							<div class="col-8">English</div>
						</div>
					</a>
					<a href="{{ route('locale', 'nl') }}" class="dropdown-item"> 
						<div class="row">
							<div class="col-4"><span class="locale-flag locale-flag-dutch"></span></div>
							<div class="col-8">Dutch</div>
						</div>
					</a>
				</div>
			</li>

			<!-- Messages Dropdown Menu -->
			<li class="nav-item dropdown">
				@if (auth()->check())
					<a class="nav-link text-white py-1 d-flex align-items-center header_profile_div" data-toggle="dropdown" href="#" style="padding: 0px;">
						<img class="header_profile-user-img img-fluid img-circle" src="{{ auth()->user()->profile->url() }}" alt="User profile picture" style="z-index: -1;" >
						<span class="visible-sm-and-lg">&nbsp;&nbsp;{{ auth()->user()->customer_name }}</span>
					</a>
					<div class="dropdown-menu dropdown-menu-right static_remove">
						<a onclick="getprofiletab()" href="{{ url('/profile') }}" class="dropdown-item">
							<i class="fas fa-user"></i>&nbsp;&nbsp;{{ __('messages.myprofile') }}
						</a>
						<a href="javascript:void(0);" onclick="getchangepasswortab()" class="dropdown-item">
							<i class="fas fa-unlock-alt"></i>&nbsp;&nbsp;{{ __('messages.chnagepassoword') }}
						</a>
						<a href="{{ url('customer/logout') }}" class="dropdown-item">
							<i class="fas fa-sign-out-alt"></i>&nbsp;&nbsp;{{ __('messages.logout') }}
						</a>
					</div>
				@else
					<button type="button" class="btn btn-default page-link" data-toggle="modal" data-target="#loginModule">Login</button>
				@endif
			</li>
			<!-- =========== burger menu =========== -->
			<button
				class="navbar-toggler border-0"
				type="button"
				data-toggle="collapse"
				data-target="#navbarCollapse"
				aria-controls="navbarCollapse"
				aria-expanded="false"
				aria-label="Toggle navigation"
			>
				<div id="nav-icon1">
					<span></span>
					<span></span>
					<span></span>
				</div>
			</button>
		</ul>
	</div>
</nav>

@push('exstraScript')
	<script>
		function getchangepasswortab()
		{
			sessionStorage.setItem('selectedtab', 'changepassword_tab');
			window.location.href = SITE_URL+'profile';
		}

		function getprofiletab()
		{
			sessionStorage.setItem('selectedtab', 'profile_tab');
			window.location.href = SITE_URL+'profile';
		}

		$('.sub-menu ul').hide();
		$(".sub-menu div").click(function () {
			$(this).parent(".sub-menu").children("ul").slideToggle("100");
			$(this).find(".right").toggleClass("fa-caret-up fa-caret-down");
		});

		$(document).ready(function(){
			$( "#product_search" ).autocomplete({
				source: function( request, response ) {
					// Fetch data
					$.ajax({
						url: SITE_URL + 'autocomplete',
						type: 'POST',
						dataType: "json",
						data: {
							_token: $('meta[name=csrf-token]').attr('content'),
							search: request.term
						},
						success: function( data ) {
							response( data );
						}
					});
				},
				select: function (event, ui) {
					// Set selection
					console.log(ui.item);
					$('#product_search').val(ui.item.label ); // display the selected text
					$('#productsearchsubmit').val(ui.item.value); 
					$('#product_search_or_not').val(1);// save selected id to input

					window.location = "/products/" + ui.item.value
					return false;
				} 
			});

			$('#nav-icon1,#nav-icon2,#nav-icon3,#nav-icon4').click(function(){
				$(this).toggleClass('open');
			});
		});
	</script>
@endpush