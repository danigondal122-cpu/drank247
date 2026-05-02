@extends('admin.layout.layout')
@section('pageCSS')
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/x-editable/bootstrap-editable.css') }}">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-touchspin/4.3.0/jquery.bootstrap-touchspin.min.css"
        integrity="sha512-0GlDFjxPsBIRh0ZGa2IMkNT54XGNaGqeJQLtMAw6EMEDQJ0WqpnU6COVA91cUS0CeVA5HtfBfzS9rlJR3bPMyw=="
        crossorigin="anonymous" />
@endsection
<style>

</style>
@section('header_content')
    {{-- <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">Product List</h1>
        </div>
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div> --}}
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card-body col-sm-12 col-md-12">
                <div class="card">
                    <div class="card-body">

                        <div class="card">
                            <div class="card-header" style="background-color:#f2f2f2;">
                                <h3 class="card-title">Stock Order Detail</h3>
                            </div>
                            <div class="card-body col-sm-12 col-md-12">
                                <div class="row">
                                    Order No : {{ $order_no }}
                                </div>
                                <div class="row">
                                    Ware House : {{ $warehouse['wh_name'] ?? '' }}
                                </div>
                                {{-- <div class="row">
                                    Franchise : {{ $franchise->franchises_name ?? '' }} {{ $franchise->deleted_at ? "(Deleted at $franchise->deleted_at)" : '' }}
                                </div> --}}

                            </div>
                        </div>

                        <div class="row">
                            <table class="table m-0">
                                <thead>
                                    <tr style="background-color:#f2f2f2;">
                                        <th>Product Name</th>
                                        <th>Article Number</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($list as $value)
                                        <tr>
                                            <td>{{ $value->product_name }}</td>
                                            <td>{{ $value->product_article_number }}</td>
                                            <td>{{ $value->fs_qty }}</td>
                                            <td>{{ $value->price }}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="3">Total</td>
                                        <td>{{ $amount }}</td>

                                    </tr>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="{{ url('admin/warehousestockorder/list') }}" class="btn btn-secondary text-white"> <i
                                class="fas fa-arrow-left"></i>&nbsp;&nbsp;Back</a>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
@endsection
@section('pageJS')
@endsection
