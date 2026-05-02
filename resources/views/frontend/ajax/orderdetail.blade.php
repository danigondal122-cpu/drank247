<div class="card-body productlistdiv" style="background-color: #f4f6f9">

    <input type="hidden" name="order_id" id="order_id" value="{{ $detail->id }}">
    {{-- <input type="hidden" name="dp_id" id="dp_id" value="{{ $detail->od_deliverypersonid }}"> --}}
    <div class="callout col-md-12" style="border-top: 5px solid {{ $detail->os_color }};border-left:0px;">
        <div class="col-md-12 col-xs-12">
            <div class="row">
                <div class="col-sm-6 col-xs-4 col-md-6 col-lg-8">
                    <h6>Order No: {{ $orderdetail[0]->order_id }} </h6>
                </div>
                <div class="col-sm-6 col-xs-4 col-md-6 col-lg-4 order_status"> <span class="badge"
                        style="background-color:{{ $orderdetail[0]->os_color }};">{{ $orderdetail[0]->os_name }}</span>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12" style="display:inline-block;">
                    <p>Price: &euro; {{ number_format($orderdetail[0]->order_final_with_discount, 2) }}</p>
                </div>
                @if ($orderdetail[0]->payment_method)
                    <div class="col-md-12" style="display:inline-block;">
                        <p>Payment Method: {{ $orderdetail[0]->payment_method }}</p>
                    </div>
                @endif

                <div class="col-md-12">Address: {{ $orderdetail[0]->address }}</div>
            </div>
            <div class="row">
                <div class="" style="margin:10px;"> {!! QrCode::size(100)->generate($orderdetail[0]->order_id) !!} </div>
            </div>

            <div class="col-md-12" style="margin-top:20px;">
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
                                @foreach ($orderdetail as $value)
                                    {{-- @dd($product) --}}
                                    <tr>
                                        <td>
                                            <div class="product-img">
                                                {{-- <img src="{{ asset($value->image) }}" alt="Product Image"
                                                class="img-size-50"> --}}
                                            </div>
                                        </td>
                                        <td>{{ $value->product_name }}</td>
                                        <td>{{ $value->od_qty }}</td>
                                        <td>
                                            <div class="sparkbar" data-color="#00a65a" data-height="20">&euro;
                                                {{ number_format($value->product_price, 2) }}</div>
                                        </td>
                                        <td>{{ $value->od_total }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>

                                    <th>Total:</th>
                                    <th>&euro;
                                        @php
                                            $total = 0;
                                        @endphp
                                        @foreach ($orderdetail as $value)
                                            @php
                                                $total += $value->od_total;
                                            @endphp
                                        @endforeach
                                        {{ number_format($total, 2) }}
                                    </th>

                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <th>Delivery Charge:</th>
                                    <th>&euro; {{ number_format($orderdetail[0]->order_delivery_charge, 2) }}</th>
                                </tr>

                                @if ($orderdetail[0]->promo_code_id != '')
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <th>Discount:</th>
                                        <th>&euro; {{ number_format($orderdetail[0]->order_discount, 2) }}</th>
                                    </tr>
                                @endif

                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <th>Final Amount:</th>
                                    <th>&euro; {{ number_format($orderdetail[0]->order_final_with_discount, 2) }}</th>

                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.table-responsive -->
                </div>
            </div>

            @if ($orderdetail[0]->order_status == '6')
                <div class="card mt-2">
                    <div class="card-header" style="background-color: #f2f2f2;">
                        <b> Rate and Review</b>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">

                            <div class="my-rating-4" data-rating="{{ empty($review) == false ? $review->rate : '' }}">
                            </div>
                        </h5>
                        <form class="form-horizontal" id="orderReview">
                            @csrf

                            <input type="hidden" name="order_id" id="order_id"
                                value="{{ $orderdetail[0]->order_id }}">
                            <input type="hidden" name="rate" id="rate" value="">
                            <input type="hidden" name="dp_id" id="dp_id"
                                value="{{ $orderdetail[0]->delivery_person_id }}">

                            <textarea class="form-control mt-3" type="text" name="review" id="review" placeholder="Please enter your Review">{{ empty($review) == false ? $review->review : '' }}</textarea>
                            <button type="submit" class="btn btn-primary btn-xs mt-3"><i
                                    class="fas fa-save "></i>&nbsp;&nbsp;Submit</button>

                        </form>
                    </div>
            @endif
        </div>
    </div>
    <a href="{{ url('profile') }}" class="btn btn-secondary text-white"> <i
            class="fas fa-arrow-left"></i>&nbsp;&nbsp;Back</a>
</div>

<script>
    $('#orderReview').on('submit', function(e) {
        e.preventDefault();
        loader_show();
        var data = new FormData(this);
        $('.is-invalid').removeClass('is-invalid');
        $('.text-danger').html('');
        $.ajax({
            url: SITE_URL + 'customer/addreview',
            type: 'POST',
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            success: function(obj) {
                if (!obj.status && obj.type == 'VALIDATION') {
                    loader_hide();
                    for (key in obj.errors) {
                        $('#orderReview #' + key).addClass('is-invalid');
                        $('#orderReview #' + key + '_error').html(obj.errors[key]);
                    }
                }
                if (obj.status) {
                    console.log('Looged in');
                    loader_hide();
                    // location.reload();
                    // window.location = SITE_URL + 'admin/dashboard';
                }
            },
            error: function() {

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
                url: SITE_URL + 'customer/rateandreview',
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
