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
      </div><!-- /.row -->$
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
                                <th>Ware house</th>
                                <th>Main Ware House</th>
                                <th>247Ware House</th>
                                <th>Customer Price</th>
                                <th>Franchise Price</th>
                            </tr>
                        </thead>
                        <tbody>

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
        var table;
        table = $('#table').DataTable({
            "pageLength": 10,
            'bJQueryUI': true,

            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "rowId": 'product_rowid',
            "dom": "<'row'<'col-sm-6 col-md-2'l>>" +
                "<'row'<'col-sm-12 col-md-12'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>", //Initial no order.
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": SITE_URL + "admin/warehouseproduct/list",
                "type": "POST",
                "data": function(d) {
                    d._token = $('meta[name=csrf-token]').attr('content');
                    // etc
                },
                dataFilter: function(data) {
                    var json = jQuery.parseJSON(data);
                    json.recordsTotal = json.total;
                    json.recordsFiltered = json.total;
                    json.data = json.data;
                    return JSON.stringify(json); // return JSON string
                }
            },
            columns: [{
                    data: 'product_name'
                },
                {
                    data: 'product_article_number'
                },
                {
                    "mRender": function(data, type, row) {
                        let html = "<img class='image' style='width:35px' src=" + SITE_URL + 'uploads/warehouse/' + row.wh_logo + ">";
                        return html;
                    }
                },
                {
                    "mRender": function(data, type, row) {
                        let id = "main_price_" + row.id;
                        let html = "<input type='text' class='form-control changeMainPrice' id=" + id + " data-type='main_price' data-id=" + row.id + " value=" + row.main_price + " >";
                        return html;
                    }
                },
                {
                    "mRender": function(data, type, row) {
                        let id = "drank247_price_" + row.id;
                        let html = "<input type='text' class='form-control changeMainPrice' id=" + id + " data-type='drank247_price' data-id=" + row.id + " value=" + row.drank247_price + " >";
                        return html;
                    }
                },
                {
                    "mRender": function(data, type, row) {
                        let id = "customer_price_" + row.id;
                        let html = "<input type='text' class='form-control changeMainPrice' id=" + id + " data-type='customer_price' data-id=" + row.id + " value=" + row.customer_price + " >";
                        return html;
                    }
                },
                {
                    "mRender": function(data, type, row) {
                        let id = "franchise_price_" + row.id;
                        let html = "<input type='text' class='form-control changeMainPrice' id=" + id + " data-type='franchise_price' data-id=" + row.id + " value=" + row.franchise_price + " >";
                        return html;
                    }
                },
            ],
            "columnDefs": [{
                    "width": "10%",
                    "targets": 0,
                    "orderable": false
                },
                {
                    "width": "10%",
                    "targets": 1,
                    "orderable": false
                },
                {
                    "width": "10%",
                    "targets": 2,
                    "orderable": false
                },
                {
                    "width": "10%",
                    "targets": 3,
                    "orderable": false
                },
                {
                    "width": "10%",
                    "targets": 4,
                    "orderable": false
                },
                {
                    "width": "10%",
                    "targets": 5,
                    "orderable": false
                },
                {
                    "width": "10%",
                    "targets": 6,
                    "orderable": false
                }
            ],
        }).on('init.dt', function() {
            let html = `
      <select name="" id="order_from" class="col-md-4 form-control form-control-sm float-left ml-2 order_from" style="width:40%">
      <option value="">Order From</option>`;
            @foreach ($warehouse as $wh)
                html += `<option value="{{ $wh->id }}">{{ $wh->wh_name }}</option>`;
            @endforeach
            html += `</select>
      <button class="btn btn-danger btn-sm float-right btn-reset ml-2">Reset</button>
     `;
            $('#table_filter').before().append(html);
        });
        $(document).on('change', '#categiry_id,#order_from', function() {

            var order_from = $('#order_from').val();
            var new_url = SITE_URL + "admin/warehouseproduct/list?order_from=" + order_from;
            table.ajax.url(new_url).load();
        })
        $(document).on('click', '.btn-reset', function() {
            $('#order_from').val('');
            table
                .search('')
                .columns().search('');
            var new_url = SITE_URL + "admin/warehouseproduct/list";
            table.ajax.url(new_url).load();
        });
        $(document).on('keyup', '.changeMainPrice', function() {
            var product_id = $(this).data('id');
            var type = $(this).data('type');
            if (type == 'main_price') {
                var value = $('#main_price_' + product_id).val();
            } else if (type == 'drank247_price') {
                var value = $('#drank247_price_' + product_id).val();
            } else if (type == 'customer_price') {
                var value = $('#customer_price_' + product_id).val();
            } else if (type == 'franchise_price') {
                var value = $('#franchise_price_' + product_id).val();
            }

            $.ajax({
                url: SITE_URL + 'admin/warehouseproduct/changeProductPrice',
                type: 'POST',
                data: {
                    'product_id': product_id,
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
