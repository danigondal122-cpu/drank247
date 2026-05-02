@extends('layouts.user')

@push('extraStyle')
	<style>
		@media screen and (min-device-width: 320px) and (max-device-width: 500px) {
			.content .SmallDivPadding {
				padding: 0 !important;
			}
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
	<x-user.content>
		<x-slot:breadcrumbs col="col-md-9" title="Favourite Item">
			<li class="breadcrumb-item active">Favourite Items</li>
		</x-slot>

		@if ($favourites?->count())
			<div class="row justify-content-center px-5">
				<div class="col-md-10 mx-auto px-4 main_favpad">
					<div class="row productlistdiv">
						@foreach ($favourites as $item)
							<div class="col-md-4 col-lg-3 col-xl-2" id="cardId{{ $item->product->id }}">
								<x-user.product-card
									class="elevation-1 mb-3"
									:product="$item->product"
								/>
							</div>
						@endforeach
					</div>
				</div>
			</div>
		@else
			<div class="text-center align-items-center">
				<h3>No Products</h3>
			</div>
		@endif
	</x-user.content>

	<x-common-modal view="user" />
@endsection