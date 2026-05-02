@extends('admin.layout.layout')
@section('pageCSS')
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/x-editable/bootstrap-editable.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-touchspin/4.3.0/jquery.bootstrap-touchspin.min.css" integrity="sha512-0GlDFjxPsBIRh0ZGa2IMkNT54XGNaGqeJQLtMAw6EMEDQJ0WqpnU6COVA91cUS0CeVA5HtfBfzS9rlJR3bPMyw==" crossorigin="anonymous" />
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
            <div class="card" style="margin-top:20px">
                {{-- <div class="card-header">
          <div class="row">
          <div class="col-md-6 col-xs-5 col-sm-5" style="display:inline-block;"><h3 class="card-title">Product List</h3></div>
          <div class="col-md-6 col-xs-7 col-sm-7" style="display:inline-block;">
           </div>
        </div>

        </div> --}}
                <!-- /.card-header -->
                <div class="card-body table-responsive">
                    <table id="table" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Article Number</th>
                                @foreach ($franchise as $value)
                                    <th colspan="3">{{ $value['franchises_name'] }}</th>
                                @endforeach
                            </tr>
                            <tr>
                                <th></th>
                                <th></th>
                                @foreach ($franchise as $value)
                                    <th>Max Stock Order</th>
                                    <th>Current Stock</th>
                                    <th>Min Stock</th>
                                @endforeach
                            </tr>

                        </thead>
                        <tbody>

                            @foreach ($product as $key => $value)
                                <tr>
                                    <td>{{ $value['product_name'] }}</td>
                                    <td>{{ $value['product_article_number'] }}</td>
                                    @foreach ($value['franchise'] as $key1 => $value1)
                                        <td><input type='text' class="form-control changeStock" data-id='{{ $value['id'] . '&' . $value1['franchise_id'] }}' data-type='max_stock_order' id='max_stock_order_{{ $value['id'] . '' . $value1['franchise_id'] }}' value='{{ $value1['max_stock_order'] }}'></td>
                                        <td><input type='text' class="form-control changeStock" data-id='{{ $value['id'] . '&' . $value1['franchise_id'] }}'data-type='current_stock' id='current_stock_{{ $value['id'] . '' . $value1['franchise_id'] }}' value='{{ $value1['stock_current'] }}'></td>
                                        <td><input type='text' class="form-control changeStock" data-id='{{ $value['id'] . '&' . $value1['franchise_id'] }}' data-type='min_stock' id='min_stock_{{ $value['id'] . '' . $value1['franchise_id'] }}' value='{{ $value1['stock_minimum'] }}'></td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
@endsection
@section('pageJS')
    <script src="{{ asset('plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('plugins/x-editable/bootstrap-editable.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script>
        $('#table').DataTable({
            "bSort": false
        });
        $(document).on('keyup', '.changeStock', function() {
            var product_fran_id = $(this).data('id');
            let getid = product_fran_id.replace('&', '');
            var type = $(this).data('type');

            if (type == "min_stock") {
                var value = $('#min_stock_' + getid).val();
            } else if (type == 'max_stock_order') {
                var value = $('#max_stock_order_' + getid).val();
            } else {
                var value = $('#current_stock_' + getid).val();
            }
            $.ajax({
                url: SITE_URL + 'admin/warehousestock/changeStock',
                type: 'POST',
                data: {
                    'product_fran_id': product_fran_id,
                    '_token': $('meta[name=csrf-token]').attr('content'),
                    'type': type,
                    'value': value
                },
                success: function(obj) {

                }
            });
        })
    </script>
@endsection
