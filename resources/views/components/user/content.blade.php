@props([
	'slider' => null,
	'breadcrumbs' => '',
])

<!-- Content Wrapper. Contains page content -->
<div @class([
	'content-wrapper',
	'custom_header' => !Request::segment(1)
]) style="margin-bottom:0px !important;">
	<!-- Main content -->

	@if ($slider && $slider->count())
		<x-user.carousel
			id="carouselExampleFade"
			class="carousel-fade relative"
			style="position: relative;"
			arrows="1"
		>
			@foreach ($slider as $item)
				<div @class([
					'carousel-item',
					'active' => $loop->first
				])>
					<img
						src="{{ $item->image }}"
						class="d-block w-100 web_image opacity30"
						style="height: 60vh; object-fit: cover;"
						alt="..."
					>
				</div>
			@endforeach  
		</x-user.carousel>

		<div class="searchmaindiv">
			<div class="searchmaindiv_img">
				<img src="/img/{{ isLocale('nl') ? 'dutch_R.svg' : 'English_TM.svg' }}" alt="247Drank" class="" style="">
			</div>
			<form class="form-horizontal" method="post"  id="postcodeFrom">
				@csrf
				<div class="input-group searchmaindiv_input_div input-group-sm mt-2">
					<input type="text" class="form-control postcode" id="postcode" name="postcode" placeholder="{{ __('messages.enteryourpincode') }}">
					<span class="input-group-append" >
						<button type="submit" id="postcodesubmit" class="btn btn-primary btn-flat">
							{{ __('messages.search') }}
						</button>
					</span>
				</div>
				<div class="text-center searchmaindiv_input_div mt-3 primarycolor fz-23" >
					<a href="{{ '/categories' }}" class="btn btn-primary btn-block float-right" >
						{{ __('messages.viewthemenudirectly') }}
					</a>
				</div>
			</form>
		</div>
	@endif
	
	@if (!is_string($breadcrumbs) && !$breadcrumbs->isEmpty())
		@php
			$attr = $breadcrumbs->attributes;
		@endphp
		<section class="content-header pt-4 pt-lg-3">
			<div @class([
				'container-fluid',
				'px-md-3' => $attr->has('col'),
				'px-md-5' => !$attr->has('col')
			])>
				<div class="row justify-content-center">
					<div @class([
						'col-md-12 px-lg-4' => !$attr->has('col'),
						$attr->get('col') => $attr->has('title')
					])>
						@if ($attr->has('title'))
							<h1 class="underline" style="display: inline-block;">{!! $attr->get('title') !!}</h1>
						@endif
						<ol class="breadcrumb float-sm-right">
							@if ($attr->get('home') !== false)
								<li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
							@endif
							{!! $breadcrumbs !!}
						</ol>
					</div>
				</div>
			</div>
		</section>
	@endif

	<section class="content" @style([
		'padding-top: 30px' => !$breadcrumbs || $breadcrumbs->isEmpty()
	])>
		<div @class([
			'container-fluid',
			'px-0' => !Request::segment(1)
		])>
			{{ $slot }}
		</div>
		<!--/. container-fluid -->
	</section>
	<!-- /.content -->

</div>
<!-- /.content-wrapper -->