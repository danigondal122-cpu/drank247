@props([
	'product' => null,
	'detail' => false,
	'addIcon' => 'small'
])

@if ($product)
	<div {{ $attributes->merge(['class' => 'card']) }}>
		<div class="card-header" style="border-bottom: none;">
			@if ($product->is_popular)
				<span class="popular_product cursor-pointer">
					<img src="{{ asset('images/icon/most_popular1.png') }}">
				</span>
			@endif
			<span
				@class([
					'add-to-cart-btn cursor-pointer',
					'add_to_cart_product' => !$product->extraProducts()->count()
				])
				@if ($product->extraProducts()->count())
					onclick="customizedProduct({{ $product->id }})"
				@endif
				data-product="{{ $product->id }}" 
				data-product-price="{{ $product->product_price }}"
				data-vat-price="{{ $product->vat_price }}" 
				data-product-name="{{ $product->product_name }}"
				data-product-image="{{ $product->image }}"
				data-category="{{ $product->category_name }}"
				@if ($detail)
					data-categoryid="{{ $product->category_id }}"
				@endif
			>
				<img
					src="{{ asset('images/icon/add_cart.png') }}"
					@if ($addIcon == 'small')
						width="25px;" height="25px;"
					@endif
				>
			</span>
		</div>

		@if ($detail)
			<div class="d-flex align-self-center" style="height: 200px;">
				<img
					src="{{ $product->image }}"
					class="card-img-top cart-item-img p-2"
					style="max-height: 200px;"
					alt="..."
				>
			</div>
			<hr class="card-border">

			<div class="card-body p-3">
				<div class="col-12">
					@if ($product->category()->first()?->category_name)
						<div class="row">
							<div class="col-md-5"><b>{{ __('messages.categoryname') }}:</b></div>
							<div class="col-md-6">{{ $product->category()->first()->category_name }}</div>
						</div>
					@endif
					<div class="row">
						<div class="col-md-5"><b>{{ __('messages.productname') }}:</b></div>
						<div class="col-md-6">{{ $product->product_name }}</div>
					</div>
					@if ($product->description)
						<div class="row">
							<div class="col-md-5"><b>{{ __('messages.description') }}:</b></div>
							<div class="col-md-6">{{ $product->description }}</div>
						</div>
					@endif
					@if ($product->alcohol)
						<div class="row">
							<div class="col-md-5"><b>{{ __('messages.alcohol') }}:</b></div>
							<div class="col-md-6">
								{{ $product->alcohol }}
								@if (!in_array($product->alcohol, ['Yes', 'No'])) % @endif
							</div>
						</div>
					@endif
					@if ($product->allergens()->get()->count())
						<div class="row">
							<div class="col-md-5"><b>{{ __('messages.allergen') }}:</b></div>
							<div class="col-md-6">
								{{ $product->allergens()->get()->pluck('name')->implode(', ') }}
							</div>
						</div>
					@endif
					<div class="row">
						<div class="col-md-5"><b>{{ __('messages.priceincludingvat') }}:</b></div>
						<div class="col-md-6">€ {{ $product->vat_price }}</div>
					</div>
				</div>
			</div>
		@else
			<div class="d-flex p-1" style="height: 150px; padding-top: 15px !important;">
				<img
					src="{{ $product->image }}"
					class="card-img-top product_img"
					onclick="showProductDetailpoup({{ $product->id }});"
					alt="..."
				>
			</div>
			<hr class="card-border">
			
			@if (auth()->check())
				<span
					class="add-to-fav-btn add_product_as_favourite"
					data-product="{{ $product->id }}"
					data-product-price="{{ $product->product_price }}"
					data-vat-price="{{ $product->vat_price }}"
					data-product-name="{{ $product->product_name }}"
					data-product-image="{{ $product->image }}"
					data-category="{{ $product->category_name }}"
					data-categoryid="{{ $product->category_id }}"
				>
					@if ($product->isFavourite())
						<i class="fas fa-heart"></i>
					@else
						<i class="far fa-heart"></i>
					@endif
				</span>
			@else 
				<span class="add-to-fav-btn" data-toggle="modal" data-target="#loginModule">
					<i class="far fa-heart"></i>
				</span>
			@endif
	
			<div class="card-body p-2">
				<p class="font-1rem text-ellipsis text-center mb-1" style="color: #d42f7a">
					<b><span title="{{ $product->product_name }}">{{ $product->product_name }}</span></b>
				</p>
				<p class="text-center font-2rem mb-0">
					<i class="fas fa-euro-sign"></i> <span class="">{{ $product->vat_price }}</span>
				</p>
			</div>
		@endif
	</div>
@endif