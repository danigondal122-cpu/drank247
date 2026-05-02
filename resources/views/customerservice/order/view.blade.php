@extends('customerservice.layout.layout')
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
                <div class="card-header">
                    <h3 class="card-title">Order Detail</h3>
                    @if ($row->order_status != '1')
                        <button type="button" id="approved" disabled class="btn btn-info float-right mr-2"><i
                                class="fa fa-check mr-2"></i>Approved</button>
                    @elseif($row->order_status != '7')
                        <button type="button" id="approved" onclick="orderApproved('{{ $row->id }}');"
                            class="btn btn-info float-right mr-2 ">Approve</button>
                    @endif
                    @if ($row->order_status == '7')
                        <button type="button" id="cancelled" disabled class="btn btn-danger float-right mr-2"><i
                                class="fa fa-check mr-2"></i>Reject</button>
                    @elseif($row->order_status != '2')
                        <button type="button" id="cancelled" onclick="showCancelledPopup('{{ $row->id }}');"
                            class="btn btn-danger float-right mr-3">Reject</button>
                    @endif
                </div>
                <div class="card-body col-sm-12 col-md-12">
                    @if ($row->manual == 0)
                        <div class="text-center mb-2">
                            <h5 class="text-danger">Please verify customer address properly and then assign order !</h5>
                        </div>
                    @endif
                    @if ($row->order_status == '7')
                        <div class="card">
                            <div class="card-header" style="background-color:#f2f2f2;">
                                <h3 class="card-title">Reason of Rejection</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-2">
                                        {{ $row->order_cancelled_reason }}
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
                            {{-- <div class="row">
                                <div class="col-md-2">
                                    <strong>Order Status</strong>
                                </div>
                                <div class="col-md-4">
                                    @php
                                        $status = App\Enums\OrderStatusEnum::tryFrom($row->order_status);
                                        $statusColor = $status?->getColor();
                                    @endphp
                                    <div class="badge badge-secondary px-2 py-1" @style(
                                        'background-color: '.$status?->getColor() ?? 'inherit',
                                            'color: '.$status?->geTextColorInBadge() ?? 'inherit',
                                    )>{{ $status->getLabel() }}</div>
                                </div>
                            </div> --}}

                            {{-- @foreach (App\Enums\OrderStatusEnum::cases() as $enum)
                                <br>
                                <div class="badge badge-secondary px-2 py-1" @style([
                                    'background-color: '.$enum?->getColor() ?? 'inherit',
                                    'color: '.$enum?->geTextColorInBadge() ?? 'inherit',
                                ])>{{ $enum->getLabel() }}</div>
                            @endforeach --}}
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
                            <div class="row">
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
                                        <p class="text-muted">{{ $row->order_channel_id }}</p>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-2">
                                        <strong>Order From</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <img class="channel_image"
                                            src="{{ url('/images/channel/') . '/' . ($row->channel_image = $row->channel_image ? $row->channel_image : 'drank.png') }}">
                                    </div>
                                </div>
                            @endif
                            @if ($row->order_uber_id != '')
                                <div class="row mt-2">
                                    <div class="col-md-2">
                                        <strong>Store Id </strong>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="text-muted">{{ $row->name }}</p>
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
                                        <img class="channel_image" src="{{ url('/images/channel/Uber.png') }}">
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
                                                <p class="text-muted" style="display:contents !important">
                                                    ({{ $row->rejected_reason }})
                                                </p>
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
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-2">
                                        <strong>Customer Name</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="text-muted">{{ $row->customer_name }}</p>
                                    </div>
                                    <div class="col-md-2">
                                        <strong>Customer Email</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="text-muted">{{ $row->customer_email }}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2">
                                        <strong>Customer Contact No.</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="text-muted">{{ $row->customer_contact_no }}</p>
                                    </div>
                                    <div class="col-md-2">
                                        <strong>Customer Phone Code</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="text-muted">{{ $row->phone_code }}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2">
                                        <strong>PostCode</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="text-muted">{{ $row->post_code }}</p>
                                    </div>
                                    <div class="col-md-2">
                                        <strong>Delivery Address</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="text-muted">{{ $row->address }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if ($row->franchise_id != '')
                        <div class="card">
                            <div class="card-header" style="background-color:#f2f2f2;">
                                <h3 class="card-title">Franchise Detail</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-2">
                                        <strong>Franchises Name</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="text-muted">{{ $row->franchises_name }}</p>
                                    </div>
                                    <div class="col-md-2">
                                        <strong>Franchises Email</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="text-muted">{{ $row->franchises_email }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if ($row->delivery_person_id != '')
                        <div class="card">
                            <div class="card-header" style="background-color:#f2f2f2;">
                                <h3 class="card-title">Delivery Person</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-2">
                                        <strong>Delivery Person</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="text-muted">{{ $row->dp_name }}</p>
                                    </div>
                                    <div class="col-md-2">
                                        <strong> Email</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="text-muted">{{ $row->dp_email }}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2">
                                        <strong>Contact No</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="text-muted">{{ $row->dp_contact_no }}</p>
                                    </div>
                                </div>
                                @if ($row->od_endtime != '' && $row->od_startime)
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
                                            <th>Image</th>
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
                                            <th>€ {{ str_replace('.', ',', number_format($row->order_deliverycharge, 2)) }}
                                            </th>
                                        </tr>
                                        {{--
                  <tr>
                    <th></th>
                    <th></th>
                    <th>Final Amount</th>
                    <th>€ {{str_replace('.',',', number_format($row->order_final_amount,2))}}</th>
                  </tr>
                  --}}
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
                                            <th>€
                                                {{ str_replace('.', ',', number_format($row->order_final_with_discount, 2)) }}
                                            </th>
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
            </div>
            <div class="card-footer">
                <a href="{{ url('customer_service/order/list') }}" class="btn btn-secondary text-white"> <i
                        class="fas fa-arrow-left"></i>&nbsp;&nbsp;Back</a>
            </div>
        </div>
    </div>
    <div id="commonModal" class="modal fade" role="dialog">
        <div class="modal-dialog" style="width:auto;">
            <div class="modal-content" id="commonModalHtml"></div>
        </div>
    </div>
@endsection
@section('pageJS')
    <script src="{{ asset('plugins/select2/js/select2.min.js') }}"></script>
    <script>
        function orderApproved(id) {
            loader_show();

            $.ajax({
                url: SITE_URL + 'customer_service/order/orderapprovedPopup',
                type: 'POST',
                data: 'id=' + id + '&_token=' + $('meta[name=csrf-token]').attr('content'),
                success: function(obj) {
                    loader_hide();
                    // messageAlert('Success',obj.msg,'fa-check','success')
                    //   setTimeout(function () {
                    //     window.location = SITE_URL + obj.page;
                    //   }, 1500)
                    $('#commonModalHtml').html(obj);
                    $('#commonModal').modal('show');
                },
                error: function(res) {
                    var statusMessage = 'Something went wrong';
                    if (res.status === 404) {
                        statusMessage = 'Order not found. Refresh the page';
                    }
                    alert(statusMessage);
                    loader_hide();
                }
            })
        }

        function orderCancelled(id) {
            //  $("#cancelled").html('<i class="fa fa-check mr-2"></i>Reject') ;
            loader_show();
            $.ajax({
                url: SITE_URL + 'customer_service/order/orderCancelled',
                type: 'POST',
                data: 'id=' + id + '&_token=' + $('meta[name=csrf-token]').attr('content'),
                success: function(obj) {
                    loader_hide();
                    messageAlert('Success', obj.msg, 'fa-check', 'success')
                    setTimeout(function() {
                        window.location = SITE_URL + obj.page;
                    }, 1500)
                }
            })
        }

        function showCancelledPopup(id) {
            loader_show();
            $.ajax({

                url: SITE_URL + 'customer_service/order/showCancelledPopup',
                type: 'POST',
                data: 'oid=' + id + '&_token=' + $('meta[name=csrf-token]').attr('content'),
                success: function(obj) {
                    loader_hide();
                    $('#commonModalHtml').html(obj);
                    $('#commonModal').modal('show');
                }
            });

        }
    </script>

    {{-- #form_cancelledpopup script: --}}
    <script>
        function showOtherinput() {
            var value = $("input[name='cancelledreason']:checked").val();
            if (value == 'other') {
                $('#otherinput').show();
            } else {
                $('#otherinput').hide();
            }

        }
        $(document).on('submit', '#form_cancelledpopup', function(e) {

            e.preventDefault();
            loader_show();
            var data = new FormData(this);

            $('#form_cancelledpopup .is-invalid').removeClass('is-invalid');
            $('#form_cancelledpopup .text-danger').remove();
            $.ajax({
                url: SITE_URL + 'customer_service/order/orderCancelled',
                type: 'POST',
                data: data,
                success: function(obj) {


                    if (obj.status) {
                        messageAlert('Success', obj.msg, 'fa-check', 'success')
                        $('#form_cancelledpopup')[0].reset();
                        setTimeout(function() {
                            window.location = SITE_URL + obj.page;
                        }, 1500)
                    }
                },
                error: function(res) {
                    test = res;
                    if (res.status == 422) {
                        var obj = res.responseJSON;
                        for (key in obj.errors) {
                            $('#' + key).addClass('is-invalid');
                            $('#' + key + '_error').after('<p class="text-danger">' + obj.errors[key] +'</p>');
                                console.log(key, '#' + key + '_error', obj.errors[key]);
                        }
                        return;
                    }

                    alert('Something went wrong');
                },
                complete: function() {
                    loader_hide();
                },
                cache: false,
                contentType: false,
                processData: false
            })
        })
    </script>

    {{-- #form_approved script: --}}
    <script>
        $(document).on('submit', '#form_approved', function(e) {

            e.preventDefault();
            loader_show();
            var data = new FormData(this);

            $('#form_approved .is-invalid').removeClass('is-invalid');
            $('#form_approved .text-danger').remove();
            $.ajax({
                url: SITE_URL + 'customer_service/order/orderApproved',
                type: 'POST',
                data: data,
                success: function(obj) {

                    if (!obj.status && obj.type == 'validation') {
                        loader_hide();
                        for (key in obj.errors) {
                            $('#' + key).addClass('is-invalid');
                            $('#' + key + '_error').after('<p class="text-danger">' + obj.errors[key] +
                                '</p>');
                        }
                    }
                    if (obj.status) {
                        loader_hide();
                        $("#approved").html('<i class="fa fa-check mr-2"></i>Approved')
                        messageAlert('Success', obj.msg, 'fa-check', 'success')
                        $('#form_approved')[0].reset();
                        setTimeout(function() {
                            window.location = SITE_URL + obj.page;
                        }, 1500)
                    }
                },
                cache: false,
                contentType: false,
                processData: false
            })
        })
    </script>
@endsection
