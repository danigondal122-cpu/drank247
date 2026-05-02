@extends('admin.layout.layout')
@section('pageCSS')
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
@endsection
@section('header_content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">View Stock Order</h1>
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
                    <div class="card">
                        <div class="card-header" style="background-color:#f2f2f2;">
                            <h3 class="card-title">Stock Order Detail</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-2">
                                    <strong>Order No.</strong>
                                </div>
                                <div class="col-md-4">
                                    <p class="text-muted">{{ $row->id }}</p>
                                </div>
                                @if ($row->order_to == 0)
                                    <div class="col-md-2">
                                        <strong>Order Reference No. </strong>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="text-muted">{{ $row->order_reference }}</p>
                                    </div>
                                @endif
                            </div>
                            <div class="row">
                                @if ($row->order_to == 0)
                                    <div class="col-md-2">
                                        <strong>Order Type</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="text-muted">{{ $row->order_type == 'D' ? 'Delivery' : 'Pickup' }}</p>
                                    </div>
                                @endif
                                <div class="col-md-2">
                                    <strong>Order Date</strong>
                                </div>
                                <div class="col-md-4">
                                    <p class="text-muted">{{ $row->order_date }}</p>
                                </div>
                            </div>
                            <div class="row">
                                @if ($row->order_to == 0)
                                    <div class="col-md-3">
                                        <strong>Order Pickup Delivery Date</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="text-muted">{{ $row->pickup_date }}</p>
                                    </div>
                                @endif
                                {{--
                     <div class="col-md-2">
                        <strong>Order date</strong>
                     </div>
                     <div class="col-md-4">
                        <p class="text-muted">{{$row->created_at}}</p>
                     </div>
                     --}}
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <strong>Franchise Name</strong>
                                </div>
                                <div class="col-md-4">
                                    <p class="text-muted">{{ $row->franchises_name }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <table class="table m-0">
                                    <thead>
                                        <tr style="background-color:#f2f2f2;">
                                            <th>Product Name</th>
                                            <th>Product Article Number</th>
                                            <th>Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($orderdetail as $value)
                                            <tr>
                                                <td>{{ $value->product_name }}</td>
                                                <td>{{ $value->product_article_number }}</td>
                                                <td>{{ $value->qty }}</td>
                                            </tr>
                                        @endforeach
                                </table>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="{{ url('admin/stockorder/list') }}" class="btn btn-secondary text-white"> <i
                                    class="fas fa-arrow-left"></i>&nbsp;&nbsp;Back</a>
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
            </div>
        </div>
    </div>
@endsection
@section('pageJS')
    <script src="{{ asset('plugins/select2/js/select2.min.js') }}"></script>
@endsection
