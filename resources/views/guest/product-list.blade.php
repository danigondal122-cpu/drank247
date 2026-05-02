@extends('layouts.user')

<x-plugins
	:vendors="[
		'owlcarousel',
		'bootstrap-touchspin'
	]"
	:js="[
		asset('js/page/cart.js')
	]"
/>

@section('content')
	<x-user.content>
		@if ($products?->count())
			<x-slot:breadcrumbs :title="$category?->category_name ?? 'Extra Item'">
				<li class="breadcrumb-item active">{{ $category?->category_name ?? 'Extra Item' }}</li>
			</x-slot>

			<div class="row">
				<div class="col-md-11 mx-auto">
					<div class="row productlistdiv">
						@foreach ($products as $product)
							<div class="col-md-4 col-lg-3 col-xl-2">
								<x-user.product-card
									class="elevation-1 mb-3"
									:product="$product"
								/>
							</div>
						@endforeach
					</div>
				</div>
			</div>
		@else
			<div class="d-flex justify-content-center align-items-center" style="height: calc(100vh - 90px);">
				<h3 class="mb-5">No Products</h3>
			</div>
		@endif
	</x-user.content>

	<x-common-modal view="user" />
@endsection