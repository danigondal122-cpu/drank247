@extends('franchise.layout.layout')
@section('pageCSS')
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
@endsection
@section('header_content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">View Order</h1>
                </div>
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                {{--
                    <div class="card-header">
                        <h3 class="card-title">Order Detail</h3>
                    </div>
                --}}
                <div class="card-body col-sm-12 col-md-12">
                    @if ($row->order_status == '7')
                        <div class="card">
                            <div class="card-header" style="background-color:#f2f2f2;">
                                <h3 class="card-title">Reason of Rejection</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-2">
                                        <strong>{{ $row->order_cancelled_reason }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="card">
                        <div class="card-header" style="background-color:#f2f2f2;">
                            <h3 class="card-title">Order Detail</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-2">
                                    <strong>Order No.</strong>
                                </div>
                                <div class="col-md-4">
                                    <p class="text-muted">{{ $row->id }}</p>
                                </div>
                                <div class="col-md-2">
                                    <strong>Order Payment </strong>
                                </div>
                                <div class="col-md-4">
                                    <p class="text-muted">{{ $row->order_payment_status_text }}</p>
                                </div>
                            </div>
                            <div class="row mt-2">
                                @if ($row->order_receipt_id != '')
                                    <div class="col-md-2">
                                        <strong>Order Receipt Id</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="text-muted">{{ $row->order_receipt_id }}</p>
                                    </div>
                                @endif
                                @if ($row->order_note != '')
                                    <div class="col-md-2">
                                        <strong>Order Note </strong>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="text-muted">{{ $row->order_note }}</p>
                                    </div>
                                @endif
                            </div>
                            @if ($row->order_channel_order_id != '')
                                <div class="row mt-2">
                                    <div class="col-md-2">
                                        <strong>Deliverect Order Id </strong>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="text-muted">{{ $row->order_channel_order_id }}</p>
                                    </div>
                                    <div class="col-md-2">
                                        <strong>Channel Id</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="text-muted">{{ $row->channel_id }}</p>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-2">
                                        <strong>Order From</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <img class="channel_image" src="{{ url('/images/channel/' . ($row->channel?->channel_image ? $row->channel->channel_image : 'drank.png')) }}">
                                    </div>
                                </div>
                            @endif
                            @if ($row->order_uber_id != '')
                                <div class="row mt-2">
                                    <div class="col-md-2">
                                        <strong>Store Id </strong>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="text-muted">{{ $row->uberStore->name }}</p>
                                    </div>
                                    <div class="col-md-2">
                                        <strong>Uber Eats Order Id</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="text-muted">{{ $row->order_uber_id }}</p>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-2">
                                        <strong>Order From</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <img class="channel_image" src="{{ asset('images/channel/Uber.png') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <strong>Uber Eats Display Id</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="text-muted">{{ $row->order_uber_display_id }}</p>
                                    </div>
                                </div>
                            @endif

                            @if ($row->order_takeaway_id != '')
                                <div class="row mt-2">
                                    <div class="col-md-2">
                                        <strong>Store Id </strong>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="text-muted">{{ $row->order_store_id }}</p>
                                    </div>
                                    <div class="col-md-2">
                                        <strong>Takeaway Order Id</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="text-muted">{{ $row->order_takeaway_id }}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2">
                                        <strong>Order From</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <img class="channel_image" src="{{ url('/images/channel/take_away.svg') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <strong>Takeaway Order Key</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="text-muted">{{ $row->order_takeaway_key }}</p>
                                    </div>
                                </div>
                            @endif

                            <div class="row mt-2">
                                <div class="col-md-2">
                                    <strong>Order date</strong>
                                </div>
                                <div class="col-md-4">
                                    <p class="text-muted">{{ $row->new_order_date }}</p>
                                </div>
                                <div class="col-md-2">
                                    <strong>Order Delivery Time</strong>
                                </div>
                                <div class="col-md-4">
                                    <p class="text-muted">{{ $row->delivery_date }}</p>
                                </div>
                                <div class="col-md-2">
                                    <strong>Payment Method</strong>
                                </div>
                                <div class="col-md-4">
                                    <p class="text-muted">{{ $row->payment_method }}</p>
                                </div>
                                @if ($row->failed_reason != '')
                                    <div class="col-md-2">
                                        <strong>Failed Reason</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="text-muted">{{ $row->failed_reason }}</p>
                                    </div>
                                @endif
                            </div>
                            @if (count($rejected_by) != 0)
                                <div class="row">
                                    <div class="col-md-2">
                                        <strong>Rejected By (Delivery Person)</strong>
                                    </div>
                                </div>
                                @foreach ($rejected_by as $key => $value)
                                    <div class="row mt-2">
                                        <div class="col-md-12">
                                            {{ $key + 1 }}. {{ $value['dp_name'] }} @if ($last_dp_id == $value['id'])
                                                <p class="text-muted" style="display:contents !important">({{ $row->rejected_reason }})</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header" style="background-color:#f2f2f2;">
                            <h3 class="card-title">Customer Detail</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-2">
                                    <strong>Customer Name</strong>
                                </div>
                                <div class="col-md-4">
                                    <p class="text-muted">{{ $row->customer->customer_name }}</p>
                                </div>
                                <div class="col-md-2">
                                    <strong>Customer Email</strong>
                                </div>
                                <div class="col-md-4">
                                    <p class="text-muted">{{ $row->customer->customer_email }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <strong>Customer Contact No.</strong>
                                </div>
                                <div class="col-md-4">
                                    <p class="text-muted">{{ $row->customer->customer_contact_no }}</p>
                                </div>
                                <div class="col-md-2">
                                    <strong>Customer Phone Code</strong>
                                </div>
                                <div class="col-md-4">
                                    <p class="text-muted">{{ $row->customer->phone_code }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <strong>PostCode</strong>
                                </div>
                                <div class="col-md-4">
                                    <p class="text-muted">{{ $row->address->post_code }}</p>
                                </div>
                                <div class="col-md-2">
                                    <strong>Delivery Address</strong>
                                </div>
                                <div class="col-md-4">
                                    <p class="text-muted">{{ $row->address->address }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if ($row->delivery_person_id != '')
                        <div class="card">
                            <div class="card-header" style="background-color:#f2f2f2;">
                                <h3 class="card-title">Delivery Person Detail</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-2">
                                        <strong>Delivery Person</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="text-muted">{{ $row->deliveryPerson->dp_name }}</p>
                                    </div>
                                    <div class="col-md-2">
                                        <strong> Email</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="text-muted">{{ $row->deliveryPerson->dp_email }}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2">
                                        <strong>Contact No</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="text-muted">{{ $row->deliveryPerson->dp_contact_no }}</p>
                                    </div>
                                </div>
                                @if ($row->od_end_time != '' && $row->od_start_time)
                                    <div class="col-md-2">
                                        <strong>Order Time</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="text-muted">{{ $row->TotalOrderTime }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                    <div class="card">
                        <!-- /.card-header -->
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table m-0">
                                    <thead>
                                        <tr style="background-color:#f2f2f2;">
                                            <th>image</th>
                                            <th>Name</th>
                                            <th>Qty</th>
                                            <th>Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($order_details as $order_detail)
                                            <tr>
                                                <td>
                                                    <div class="product-img">
                                                        <img src="{{ $order_detail->product->image }}" alt="Product Image" class="img-size-50">
                                                    </div>
                                                </td>
                                                <td>{{ $order_detail->product->product_name }}</td>
                                                <td>{{ $order_detail->od_qty }}</td>
                                                <td>€ {{ str_replace('.', ',', number_format($order_detail->od_vat_total, 2)) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th>Total</th>
                                            <th>€ {{ str_replace('.', ',', number_format($row->order_price, 2)) }}</th>
                                        </tr>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th>Delivery Charge</th>
                                            <th>€ {{ str_replace('.', ',', number_format($row->order_delivery_charge, 2)) }}</th>
                                        </tr>
                                        {{-- <tr>
                                            <th></th>
                                            <th></th>
                                            <th>Final Amount</th>
                                            <th>€
                                                {{ number_format($row->order_final_amount,2)}} </th>
                                        </tr> --}}
                                        {{-- @if ($row->promo_code_id != '') --}}
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th>Discount</th>
                                            <th>€ {{ str_replace('.', ',', number_format($row->order_discount, 2)) }}</th>
                                        </tr>
                                        {{-- @endif --}}
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th>Final Amount</th>
                                            <th>€ {{ str_replace('.', ',', number_format($row->order_final_with_discount, 2)) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <!-- /.table-responsive -->
                        </div>
                        <!-- /.card-footer -->
                    </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                    <a href="{{ url('franchise/order/list') }}" class="btn btn-secondary text-white"> <i
                            class="fas fa-arrow-left"></i>&nbsp;&nbsp;Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('pageJS')
    <script src="{{ asset('plugins/select2/js/select2.min.js') }}"></script>
@endsection
