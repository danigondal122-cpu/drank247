@props([
	'id' => 'exampleCarousel',
	'arrows' => 0,
	'indicators' => false
])

<div
	id="{{ $id }}"
	{{ $attributes->merge(['class' => 'carousel slide']) }}
	data-ride="carousel"
	@if ($attributes->has('style'))
		style="{{ $attributes->get('style') }}"
	@endif
>
	@if ($indicators)
		<ol class="carousel-indicators">
			@if (!$indicators?->isEmpty())
				{!! $indicators !!}
			@endif
		</ol>
	@endif

	<div class="carousel-inner">
		{{ $slot }}
	</div>

	@if ($arrows)
		<a class="carousel-control-prev" href="#{{ $id }}" role="button" data-slide="prev">
			@if ($arrows == 1)
				<span class="carousel-control-prev-icon" aria-hidden="true"></span>
			@elseif ($arrows == 2)
				<span class="carousel-control-prev-icon carousel_cusotom_icon_pre" style="background-image: url('{{ asset('images/icon/previous_buttons.svg')}}');" aria-hidden="true"></span>
			@elseif ($arrows == 3)
				<span class="carousel-control-prev-icon carousel_cusotom_icon_pre" style="background-image: url('{{ asset('images/icon/pre.png')}}');" aria-hidden="true"></span>
			@endif

			<span class="sr-only">Previous</span>
		</a>
		<a class="carousel-control-next" href="#{{ $id }}" role="button" data-slide="next">
			@if ($arrows == 1)
				<span class="carousel-control-next-icon" aria-hidden="true"></span>
			@elseif ($arrows == 2)
				<span class="carousel-control-next-icon carousel_cusotom_icon_next" style="background-image: url('{{ asset('images/icon/next_buttons.svg')}}');"  aria-hidden="true"></span>
			@elseif ($arrows == 3)
				<span class="carousel-control-next-icon carousel_cusotom_icon_next" style="background-image: url('{{ asset('images/icon/next.png')}}');" aria-hidden="true"></span>
			@endif

			<span class="sr-only">Next</span>
		</a>
	@endif
</div>