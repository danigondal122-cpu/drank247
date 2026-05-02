<style>
	.mainproduct_table td {
		border-top: none !important;
	}

	table td {
		vertical-align: baseline !important;
	}
</style>
<div class="modal-content">
	<div class="modal-header">
		<h4 class="modal-title"><b> {{ __('messages.customizedproductdetail') }}</b></h4>
		<button type="button" class="close" data-dismiss="modal">&times;</button>
	</div>
	<div class="modal-body">
		<div class="card">
			<div class="card-body p-0">
				<div class="row p-3">
					<div class="col-xs-6 col-sm-2 col-md-2 col-lg-2 text-center">
						<img src="{{ $mainProduct->image }}" alt="Product Image" class="img-size-50">
					</div>
					<div class="col-xs-6 col-sm-4 col-md-5 col-lg-6 text-center"> 
						<div class="row"> 
							<div class="col-12 col-md-12 col-xl d-flex align-items-center" style="padding-right: 0px; margin-bottom: 15px; justify-content: center;">
								<i class="fas fa-euro-sign"></i>&nbsp;{{ number_format($mainProduct->vat_price,2) }}
							</div>
							<div class="d-flex col-12 col-md-12 col-xl-6" style="margin-bottom:15px">
								<div class="input-group bootstrap-touchspin bootstrap-touchspin-injected">
									<input
										id="product_qty{{ $mainProduct->id }}"
										class="customized_value form-control text-center p-0"
										type="number"
										name="qty"
										value="{{ $mainProduct->qty }}"
										data-price="{{ $mainProduct->product_price }}"
										data-category="Extra Product" 
										data-product-image="{{ $mainProduct->image }}"
										data-productname="{{ $mainProduct->product_name }}" 
										data-productid="{{ $mainProduct->id }}"
										data-vatprice="{{ $mainProduct->vat_price }}"
									>
								</div>
							</div>
							<div class="col-12 col-md-12 col-xl d-flex align-items-center" style="padding-left:0px;margin-bottom:15px;justify-content: center;">
								<i class="fas fa-euro-sign"></i>&nbsp; 
								<span id="vattaxamount{{ $mainProduct->id }}">{{ number_format($mainProduct->vat_total,2) }}</span>
							</div>
						</div>
					</div>
					<div class="col-7 col-md-3 col-sm-2 col-xs-6 col-lg-2" style="text-align:right;margin-top:8px;">
						<button
							type="button"
							id="customizeditemTotal{{ $mainProduct->id }}"
							class="customizeditemTotal btn btn-primary btn-sm"
							data-rowId="{{ $mainProduct->rowId }}"
							data-price="{{ $mainProduct->product_price }}"
							data-category="Extra Product"
							data-product-image="{{ $mainProduct->image }}"
							data-productname="{{ $mainProduct->product_name }}"
							data-productid="{{ $mainProduct->id }}"
							data-vatprice="{{ $mainProduct->vat_price }}"
						>{{ __('messages.addtocart') }}</button>
					</div>
					<div class="col-5 col-md-2 col-sm-2 col-xs-6 col-lg-2" style="text-align:right;margin-top:8px;">
						<button
							type="button"
							id="remove_Customized_Item{{ $mainProduct->id }}"
							class="remove_Customized_Item btn btn-primary btn-sm"
							style="display:{{ $mainProduct->rowId ? 'block' : 'none' }}"
							data-rowId="{{ $mainProduct->rowId }}"
							data-price="{{ $mainProduct->product_price }}"
							data-category="Extra Product"
							data-product-image="{{ $mainProduct->image }}"
							data-productname="{{ $mainProduct->product_name }}"
							data-productid="{{ $mainProduct->id }}"
							data-vatprice="{{ $mainProduct->vat_price }}"
						>{{ __('messages.remove') }}</button>
					</div>
				</div>
			</div>
		</div>
		
		<div class="card">
			<div class="card-header border-transparent">
				<h3 class="card-title">Extra Products</h3>
				<div class="card-tools">
					<button type="button" class="btn btn-tool" data-card-widget="collapse">
						<i class="fas fa-minus"></i>
					</button>
				</div>
			</div>
			<div class="card-body p-0">
				@foreach ($extraProducts as $product)
					<div class="row p-3" style="border-bottom:1px solid #f2f2f2">
						<div class="col-3 col-md-2 col-sm-2 col-xs-6 col-lg-2 align-self-center" style="text-align:center;" >
							<img src="{{ $product->image }}" alt="Product Image" class="img-size-50">
						</div>
						<div class="col-8 col-md-5 col-sm-4 col-xs-6 col-lg-6 align-self-center" style="text-align:center;" > 
							<div class="row">
								<div class="col-12 col-md-12 col-xl d-flex align-items-center" style="padding-right:0px;margin-bottom:15px;justify-content: center;">
									<i class="fas fa-euro-sign"></i>&nbsp;{{ number_format($product->vat_price,2) }}
								</div>
								<div class="d-flex col-12 col-md-12 col-xl-6" style="margin-bottom:15px">
									<div class="input-group bootstrap-touchspin bootstrap-touchspin-injected">
										<input
											type="number"
											id="product_qty{{ $product->id }}"
											class="customized_value form-control text-center p-0"
											name="qty"
											value="{{ $product->qty }}"
											data-price="{{ $product->product_price }}"
											data-category="Extra Product" 
											data-product-image="{{ $product->image }}"
											data-productname="{{ $product->product_name }}" 
											data-productid="{{ $product->id }}"
											data-vatprice="{{ $product->vat_price }}"
										>
									</div>
								</div>
								<div class="col-12 col-md-12 col-xl d-flex align-items-center" style="padding-left:0px;margin-bottom:15px;justify-content: center;">
									<i class="fas fa-euro-sign"></i>&nbsp; 
									<span id="vattaxamount{{ $product->id }}">{{ number_format($product->vat_total,2) }}</span>
								</div>
							</div>
						</div>
						<div class="col-7 col-md-3 col-sm-2 col-xs-6 col-lg-2" style="text-align:right;margin-top:8px;">
							<button
								type="button"
								id="customizeditemTotal{{ $product->id }}"
								class="customizeditemTotal btn btn-primary btn-sm"
								data-rowId="{{ $product->rowId }}"
								data-price="{{ $product->product_price }}"
								data-category="Extra Product"
								data-product-image="{{ $product->image }}"
								data-productname="{{ $product->product_name }}"
								data-productid="{{ $product->id }}"
								data-vatprice="{{ $product->vat_price }}"
							>Add To Cart</button>
						</div>
						<div class="col-5 col-md-2 col-sm-2 col-xs-6 col-lg-2" style="text-align:right;margin-top:8px;">
							<button
								type="button"
								id="remove_Customized_Item{{ $product->id }}"
								class="remove_Customized_Item btn btn-primary btn-sm"
								style="display:{{ $product->rowId ? 'block' : 'none' }}"
								data-rowId="{{ $product->rowId }}"
								data-price="{{ $product->product_price }}"
								data-category="Extra Product"
								data-product-image="{{ $product->image }}"
								data-productname="{{ $product->product_name }}"
								data-productid="{{ $product->id }}"
								data-vatprice="{{ $product->vat_price }}"
							>Remove</button>
						</div>
					</div>
				@endforeach
			</div>
		</div>
	</div>
</div>
<script>
	$("input[name='qty']").TouchSpin();
</script>
	

