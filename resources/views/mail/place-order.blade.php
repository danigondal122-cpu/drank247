@extends('layouts.mail')

@push('exstraStyle')
	<style type="text/css">
		.table-responsive {
			display: block;
			width: 100%;
			overflow-x: auto;
			-webkit-overflow-scrolling: touch;
		}
		
		.table {
			width: 100%;
		}
	</style>
@endpush

@section('content')
	<tr>
		<td height="30" style="font-size: 16px; color: #4d4d4d; font-family: Open Sans; padding: 10px 20px; font-weight: bold">Dear {{ $data['customer']->customer_name }},</td>
	</tr>

	<tr><td height="10" style="padding: 0px 20px;">Your Order Has Been Confirmed</td></tr>

	<tr>
		<td height="30" style="padding: 15px 20px;">
			<table class="table" width="100%" style="border: 1px solid #f2f2f2;" cellpadding="4" cellspacing="0">
					<tbody>
						<tr>
							<td style="padding: 15px 20px;"><b>Order No:</b></td>
							<td><b>{{ $data['order']->id }}</b></td>
							<td style="text-align: right;"><b>Scan QR code</b></td>
						</tr>
						<tr>
							<td style="padding: 0px 20px; vertical-align: top"><b>Address:</b></td>
							<td style="vertical-align: top;">{{ $data['address']->address }}</td>
							<td style="text-align: right; vertical-align: top;">
								<a target="_blank">
									{!! QrCode::size(100)->generate($data['order']->id) !!}
								</a>
							</td>
						</tr>
					</tbody>
			</table>
		</td>
	</tr>
	<tr>
		<td height="30" style="padding: 10px 20px;">
			<table class="table" width="100%" cellpadding="4" cellspacing="0">
				<thead>
					<tr style="background-color: #f2f2f2;">
						<th style="border: 1px solid #eee;">Image</th>
						<th style="border: 1px solid #eee;">Product</th>
						<th style="border: 1px solid #eee;">Qty</th>
						<th style="border: 1px solid #eee;">Price</th>
					</tr>
				</thead>
				<tbody>
					@foreach($data['cart'] as $item)
						<tr>
							<td style="border: 1px solid #eee; text-align: center;">
								<img src="{{ $item->product->image }}" alt="Product Image" class="img-size-50" width="50px;">
							</td>
							<td style="border: 1px solid #eee;">{{ $item->product->product_name }}</td>
							<td style="text-align: center; border: 1px solid #eee;">{{ $item->od_qty }}</td>
							<td style="border: 1px solid #eee; text-align: right">&euro; {{ number_format($item->od_vat_total, 2) }}</td>
						</tr>
					@endforeach
				</tbody>
			</table>
			<table class="table" width="100%" style="text-align: right; margin-top: 10px;">
				<tbody>
					<tr>
						<td><b>Total:</b></td>
						<td><b>&euro; {{ number_format($data['order']->order_price, 2) }}</b></td>
					</tr>
					<tr>
						<td><b>Delivery Charge:</b></td>
						<td><b>&euro; {{ number_format($data['order']->order_delivery_charge, 2) }}</b></td>
					</tr>
					@if ($data['order']->promo_code_id)
						<tr>
							<td><b>Discount:</b></td>
							<td><b>&euro; {{ number_format($data['order']->order_discount, 2) }}</b></td>
						</tr>
					@endif
					<tr>
						<td><b>Final Charge:</b></td>
						<td><b>&euro; {{ number_format($data['order']->order_final_with_discount, 2) }}</b></td>
					</tr>
				</tbody>
			</table>
		</td>
	</tr>
	<tr><td height="20"></td></tr>
@endsection