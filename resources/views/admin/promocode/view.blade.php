@extends('admin.layout.layout')
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
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Order Detail</h3>
                </div>
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
                            <h3 class="card-title">Customer Detail</h3>
                        </div>
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
                                    <strong>Customer Contact No</strong>
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
                    @if ($row->od_deliverypersonid != '')
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
                            </div>
                        </div>
                    @endif



                    <div class="card" style="margin-top:15px;">
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
                                        @foreach ($orderdetail as $value)
                                            <tr>
                                                <td>
                                                    <div class="product-img">
                                                        <img src="{{ asset('uploads/product/thumb/' . $value->image) }}"
                                                            alt="Product Image" class="img-size-50">
                                                    </div>
                                                </td>
                                                <td>{{ $value->product_name }}</td>
                                                <td>{{ $value->od_qty }}</td>
                                                <td>€ {{ number_format($value->od_vattotal, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th>Total</th>
                                            <th>€ {{ number_format($row->order_price, 2) }}</th>
                                        </tr>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th>Delivery Charge</th>
                                            <th>€ {{ number_format($row->order_delivery_charge, 2) }}</th>
                                        </tr>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th>Final Amount</th>
                                            <th>€ {{ number_format($row->order_finalamount, 2) }}</th>
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
                    <a href="{{ url('admin/order/list') }}" class="btn btn-secondary text-white"> <i
                            class="fas fa-arrow-left"></i>&nbsp;&nbsp;Back</a>
                </div>

            </div>
        </div>
    </div>
@endsection
@section('pageJS')
    <script src="{{ asset('plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('js/page/stock.js') }}"></script>
@endsection
