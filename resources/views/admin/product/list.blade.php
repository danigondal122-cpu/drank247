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
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Product List</h1>
                </div>
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6 col-xs-5 col-sm-5" style="display:inline-block;">
                            <h3 class="card-title">Product List</h3>
                        </div>
                        <div class="col-md-6 col-xs-7 col-sm-7" style="display:inline-block;">
                            <a href="{{ url('admin/product/add') }}" class="btn btn-primary btn-sm float-right">Add
                                Product</a>
                            <a href="javascript:;" onclick="syncProduct();" class="btn btn-primary btn-sm float-right"
                                style="margin-right:5px;cursor:not-allowed;">Sync Product to Deliverect</a>
                            <a href="javascript:;" disabled onclick="getProductFromStock();"
                                class="btn btn-primary btn-sm float-right" style="margin-right:5px;cursor:not-allowed;">Get
                                Product From Stock Api</a>
                        </div>
                    </div>

                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive">
                    <table id="table" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Article Number</th>
                                <th>Category Name</th>
                                <th>Description</th>
                                {{-- <th>Current Stock</th> --}}
                                <th>Action</th>
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
                "url": SITE_URL + "admin/product/list",
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
                    data: 'category_name'
                },
                {
                    data: 'description'
                },
                //  { data: 'current_stock' },
                {
                    "mRender": function(data, type, row) {
                        let html = "<div class='btn-group float-left pr-2'><a href='" + SITE_URL +
                            "admin/product/edit/" + row.id +
                            "' class='btn btn-xs btn-secondary btn-edit text-white'><i class='fa fa-pencil-alt'></i></a></div>";
                        html +=
                            "<div class='btn-group float-left pr-2'><a href='javascript:;' onclick='deleteProduct(" +
                            row.id +
                            ")' class='btn btn-xs btn-danger btn-delete text-white'><i class='fa fa-trash-alt'></i></a></div>";
                        return html;
                    }
                },
            ],
            "columnDefs": [{
                    "width": "30%",
                    "targets": 0,
                    "orderable": true
                },
                {
                    "width": "10%",
                    "targets": 1,
                    "orderable": true
                },
                {
                    "width": "20%",
                    "targets": 2,
                    "orderable": true
                },
                {
                    "width": "30%",
                    "targets": 3,
                    "orderable": false
                },
                {
                    "width": "10%",
                    "targets": 4,
                    "orderable": false
                }
            ],
            "initComplete": function(settings, json) {
                if ($('#categiry_id').val() != "") {
                    $('#table').find('tbody').sortable({
                        connectWith: 'tbody',
                        opacity: 0.6,
                        cursor: 'move',
                        forcePlaceholderSize: true,

                        update: function(e) {
                            var serialized = $('#table tbody').sortable('serialize');
                            console.log(serialized);

                            $.ajax({
                                data: serialized + '&_token=' + $('meta[name=csrf-token]')
                                    .attr('content'),
                                type: 'POST',
                                url: SITE_URL + "admin/product/updateProductOrder",
                                success: function(response) {
                                    loader_hide();
                                }
                            });
                        }
                    });
                }
            }

        }).on('init.dt', function() {
            let html = `
      <select name="" id="categiry_id" class="col-md-4 form-control form-control-sm float-left category_search" style="width:40%">
      <option value="">Select Category</option>`;

            html += `{!! $html !!}`;

            html += `</select>
      <select name="" id="order_from" class="col-md-4 form-control form-control-sm float-left ml-2 order_from" style="width:40%">
      <option value="">Order From</option>`;
            @foreach ($warehouse as $wh)
                html +=
                    `<option value="{{ $wh->id }}" @if (Session::get('order_from') == $wh->id) selected @endif >{{ $wh->wh_name }}</option>`;
            @endforeach
            html += `</select>
      <button class="btn btn-danger btn-sm float-right btn-reset ml-2">Reset</button>
     `;
            $('#table_filter').before().append(html);
        });
        $(document).on('change', '#categiry_id,#order_from', function() {
            var client_id = $('#categiry_id').val();
            var order_from = $('#order_from').val();
            var new_url = SITE_URL + "admin/product/list?cat_id=" + client_id + '&order_from=' + order_from;
            table.ajax.url(new_url).load();
        })
        $(document).on('click', '.btn-reset', function() {
            $('#categiry_id').val('');
            $('#order_from').val('');
            table
                .search('')
                .columns().search('');
            var session_destroy = SITE_URL + "admin/product/sessionDestroy";
            window.location.href = session_destroy;
            //var new_url = SITE_URL + "admin/product/list";
            //table.ajax.url(new_url).load();
        });

        function deleteProduct(id) {
            $.confirm({
                title: '',
                content: 'Are you sure to delete?',
                closeIcon: true,
                buttons: {
                    confirm: {
                        text: 'Delete',
                        btnClass: 'btn-danger',
                        action: function() {
                            $.ajax({
                                url: SITE_URL + 'admin/product/delete',
                                type: 'POST',
                                data: 'id=' + id + '&_token=' + $('meta[name=csrf-token]').attr(
                                    'content'),
                                success: function(obj) {
                                    if (obj.status == true) {
                                        table.draw();
                                        Toast.fire({
                                            type: obj.msgType,
                                            title: obj.msg
                                        })
                                    } else {
                                        $.alert('Something went wrong');
                                    }
                                }
                            });
                        }
                    },
                    Reject: {
                        text: 'Cancel',
                        btnClass: 'btn-default',
                        action: function() {}
                    },
                }
            });
        }

        function syncProduct() {
            loader_show();

            $.ajax({
                url: SITE_URL + 'syncProduct',
                type: 'POST',
                data: '_token=' + $('meta[name=csrf-token]').attr('content'),
                success: function(obj) {
                    if (obj.status == true) {
                        loader_hide();

                        messageAlert('Success', obj.msg, 'fa-check', 'success');
                        location.reload();
                    } else {
                        $.alert('Something went wrong');
                    }
                }
            });
        }

        function getProductFromStock() {
            loader_show();
            $.ajax({
                url: SITE_URL + 'getProductFromStock',
                type: 'POST',
                data: '_token=' + $('meta[name=csrf-token]').attr('content'),
                success: function(obj) {
                    if (obj.status == true) {
                        loader_hide();
                        messageAlert('Success', obj.msg, 'fa-check', 'success');
                        location.reload();
                    } else {
                        $.alert('Something went wrong');
                    }
                }
            });
        }
    </script>
@endsection
