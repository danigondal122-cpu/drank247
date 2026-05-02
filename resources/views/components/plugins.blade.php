@props([
	'vendors' => [],
	'css' => [],
	'js' => []
])

@php
	$plugins = [
		'owlcarousel' => [
			'css' => '<link rel="stylesheet" href="' . asset('plugins/owlcarousel/assets/owl.carousel.min.css'). '">
  					  <link rel="stylesheet" href="' . asset('plugins/owlcarousel/assets/owl.theme.default.min.css') . '">',
			'js' => '<script src="' . asset('plugins/owlcarousel/owl.carousel.js') . '"></script>',
		],
		'bootstrap-touchspin' => [
			'css' => '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-touchspin/4.3.0/jquery.bootstrap-touchspin.min.css" integrity="sha512-0GlDFjxPsBIRh0ZGa2IMkNT54XGNaGqeJQLtMAw6EMEDQJ0WqpnU6COVA91cUS0CeVA5HtfBfzS9rlJR3bPMyw==" crossorigin="anonymous" />',
			'js' => '<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-touchspin/4.3.0/jquery.bootstrap-touchspin.min.js" integrity="sha512-0hFHNPMD0WpvGGNbOaTXP0pTO9NkUeVSqW5uFG2f5F9nKyDuHE3T4xnfKhAhnAZWZIO/gBLacwVvxxq0HuZNqw==" crossorigin="anonymous"></script>',
		],
		'star-rating' => [
			'css' => '<link rel="stylesheet" href="' . asset('plugins/star-rating/star-rating-svg.css') . '" />',
			'js' => '<script src="' . asset('plugins/star-rating/jquery.star-rating-svg.js') . '"></script>',
		],
		'google-recaptcha' => [
			'js' => '<script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>',
		],
	];
	$session = session()->get('plugins') ?? [];
	$data = is_array($vendors) ? $vendors : [$vendors];

	if ($css && is_array($css))
	{
		$data = array_merge($data, $css);
	}

	if ($js && is_array($js))
	{
		$data = array_merge($data, $js);
	}
@endphp

@if ($data)
	@foreach ($data as $item)
		@if (!in_array($item, $session))
			@if (isset($plugins[$item]))
				@push('pageCSS')
					{!! $plugins[$item]['css'] ?? null !!}
				@endpush

				@push('pageJS')
					{!! $plugins[$item]['js'] ?? null !!}
				@endpush
			@elseif (str($item)->contains('.css'))
				@push('pageCSS')
					@if (str($item)->startsWith('http'))
						<link rel="stylesheet" href="{{ $item }}" />
					@else
						{!! $item !!}
					@endif
				@endpush
			@elseif (str($item)->contains('.js'))
				@push('pageJS')
					@if (str($item)->startsWith('http'))
						<script src="{{ $item }}"></script>
					@else
						{!! $item !!}
					@endif
				@endpush
			@endif

			@php
				$session[] = $item;
			@endphp
		@endif
	@endforeach

	@php
		session()->put('plugins', array_unique($session));
	@endphp
@endif