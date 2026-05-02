<div class="card-body productlistdiv" style="background-color: #f4f6f9">
	<input type="hidden" name="order_id" id="order_id" value="{{ $order->id }}">
	<div class="callout p-3" style="border-top: 5px solid {{ $status?->os_color ?? '#e9ecef' }}; border-left: 0px;">
		<div class="row">
			<div class="col-xs-4 col-sm-6 col-lg-8">
				<h6>Order No: {{ $order->id }} </h6>
			</div>
			@if ($status)
				<div class="col-xs-4 col-sm-6 col-lg-4 order_status">
					<span class="badge p-2" style="background-color: {{ $status->os_color }}; font-size: 10px;">
						{{ $status->os_name }}
					</span>
				</div>
			@endif
		</div>
		<div class="row mt-2">
			<div class="col-12">
				<p class="mb-1">Price: &euro; {{ number_format($order->order_final_with_discount, 2) }}</p>
			</div>
			@if ($order->payment_method)
				<div class="col-12">
					<p class="mb-1">Payment Method: {{ $order->payment_method }}</p>
				</div>
			@endif
			<div class="col-12">
				<p>Address: {{ $order->address->address }}</p>
			</div>
		</div>
		<div class="my-3">{!! QrCode::size(100)->generate($order->id) !!}</div>

		<div class="card-body p-0">
			<div class="table-responsive">
				<table class="order_detail_table table m-0">
					<thead>
						<tr style="background-color: #f2f2f2;">
							<th>Image</th>
							<th>Product</th>
							<th>Qty</th>
							<th>Price</th>
							<th>Total</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($details as $detail)
							<tr>
								<td>
									<div class="product-img">
										<img src="{{ asset($detail->product->image) }}" alt="Product Image"class="img-size-50">
									</div>
								</td>
								<td>{{ $detail->product->product_name }}</td>
								<td>{{ $detail->od_qty }}</td>
								<td>
									<div class="sparkbar" data-color="#00a65a" data-height="20">
										&euro; {{ number_format($detail->od_vat_price, 2) }}
									</div>
								</td>
								<td>&euro; {{ $detail->od_vat_total }}</td>
							</tr>
						@endforeach
						<tr>
							<th class="text-right" colspan="4">Total:</th>
							<th>&euro; {{ number_format($order->order_price, 2) }}</th>
						</tr>
						<tr>
							<th class="text-right" colspan="4">Delivery Charge:</th>
							<th>&euro; {{ number_format($order->order_delivery_charge, 2) }}</th>
						</tr>

						@if ($order->promo_code_id)
							<tr>
								<th class="text-right" colspan="4">Discount:</th>
								<th>&euro; {{ number_format($order->order_discount, 2) }}</th>
							</tr>
						@endif

						<tr>
							<th class="text-right" colspan="4">Final Amount:</th>
							<th>&euro; {{ number_format($order->order_final_with_discount, 2) }}</th>
						</tr>
					</tbody>
				</table>
			</div>
			<!-- /.table-responsive -->
		</div>

		@if ($order->order_status == 6)
			<div class="card mt-2 mb-0">
				<div class="card-header" style="background-color: #f2f2f2;">
					<b>Rate and Review</b>
				</div>
				<div class="card-body">
					<h5 class="card-title">
						<div class="my-rating-4" data-rating="{{ $review?->rate ?? '' }}">
						</div>
					</h5>
					<form class="form-horizontal" id="orderReview">
						@csrf
						<input type="hidden" name="order_id" id="order_id" value="{{ $order->id }}">
						<input type="hidden" name="dp_id" id="dp_id" value="{{ $order->delivery_person_id }}">
						<input type="hidden" name="rate" id="rate" value="">

						<textarea class="form-control mt-3" type="text" name="review" id="review" placeholder="Please enter your Review">{{ $review?->review ?? '' }}</textarea>

						<button type="submit" class="btn btn-primary btn-sm mt-3">
							<i class="fas fa-save mr-2"></i>Submit
						</button>
					</form>
				</div>
			</div>
		@endif
	</div>
	<a href="{{ url('profile') }}" class="btn btn-secondary text-white">
		<i class="fas fa-arrow-left mr-2"></i>Back
	</a>
</div>

<script>
	$('#orderReview').on('submit', function(e) {
		e.preventDefault();
		loader_show();
		var data = new FormData(this);
		$('.is-invalid').removeClass('is-invalid');
		$('.text-danger').html('');
		$.ajax({
			url: '{{ route('customer.rate.review') }}',
			type: 'POST',
			data: data,
			cache: false,
			contentType: false,
			processData: false,
			success: function(obj) {
				loader_hide();
				if (!obj.status && obj.type == 'VALIDATION')
				{
					for (key in obj.errors) {
						$('#orderReview #' + key).addClass('is-invalid');
						$('#orderReview #' + key + '_error').html(obj.errors[key]);
					}
				}
				else if (!obj.status && obj.type == 'SYSTEM')
				{
					$.alert(obj.errors)
				}
				if (obj.status)
				{
					console.log('Looged in');
					// location.reload();
					// window.location = SITE_URL + 'admin/dashboard';
				}
			}
		});
	})

	$(".my-rating-4").starRating({
		totalStars: 5,
		emptyColor: 'lightgray',
		initialRating: 1,
		strokeWidth: 0,
		useGradient: false,
		disableAfterRate: false,
		callback: function(currentRating) {
			console.log(currentRating);
			$('#rate').val(currentRating);
			$.ajax({
				url: '{{ route('customer.rate.review') }}',
				type: 'POST',
				data: 'order_id=' + $('#order_id').val() + '&dp_id=' + $('#dp_id').val() +
					'&currentRating=' + currentRating + '&_token=' + $('meta[name=csrf-token]')
					.attr('content'),
				success: function(obj) {}
			});
		},
	});
	$('.my-rating-4').starRating('setRating', 2, ROUND);
</script>
