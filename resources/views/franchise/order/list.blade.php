@extends('franchise.layout.layout')
@section('pageCSS')
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">

    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
@endsection
@section('header_content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Order List</h1>
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
                    <h3 class="card-title">Order List</h3>

                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive">
                    <table id="table" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Order From</th>
                                <th>Order No.</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Franchisee</th>
                                <th>Delivery Person</th>
                                <th>Order Id</th>
                                <th>Price</th>
                                <th>Order Payment</th>
                                <th>Payment Method</th>
                                <th>Status</th>
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
    <input type='hidden' name='session_status' id='session_status' value="{{ Session::get('fs_status') }}">
    <!-- /.row -->
@endsection
@section('pageJS')
    <script src="{{ asset('plugins/x-editable/bootstrap-editable.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script>
        var table;
        table = $('#table').DataTable({
            "pageLength": 10,
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": SITE_URL + "franchise/order/list",
                "type": "POST",
                "data": function(d) {
                    d._token = $('meta[name=csrf-token]').attr('content');
                    d.status = $('#session_status').val();
                    d.page = 'order-list';
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
                    "mRender": function(data, type, row) {
                        row.channel_image = row.channel_image ? row.channel_image : row.name ? 'Uber.png' : 'drank.png';
                        let html = "<img class='channel_image' src=" + SITE_URL + 'images/channel/' + row.channel_image + ">";

                        return html;
                    }
                },
                {
                    data: 'id'
                },
                {
                    data: 'new_order_date'
                },
                {
                    data: 'customer_name'
                },
                {
                    data: 'franchises_name'
                },
                {
                    data: 'dp_name'
                },
                // {  "mRender": function ( data, type, row ) {
                //   let order='';
                //   if(row.order_channel_id!=""){
                //     order = 'Deliverect';
                //   }else{
                //     order = "-";
                //   }
                //   return order;
                //   }
                //   },
                {
                    "mRender": function(data, type, row) {
                        let html = ''
                        if (row.order_channel_order_id != '') {
                            html = row.order_channel_order_id
                        } else if (row.order_uber_display_id) {
                            html = row.order_uber_display_id
                        } else {
                            html = row.order_takeaway_public_ref
                        }
                        return html;
                    }
                },

                // { data: 'order_channel_order_id' },
                // { data: 'order_final_amount' },
                {
                    "mRender": function(data, type, row) {
                        let html = "€ " + row.order_final_with_discount;

                        return html;
                    }
                },
                {
                    "mRender": function(data, type, row) {
                        let color = '';
                        let status = '';
                        if (row.order_payment_status) {
                            color = '#2c8c1b';
                        } else {
                            color = '#cbce23';
                        }
                        let html = '<div style="color:' + color + '; text-align:center">' + row.order_payment_status_text + '</div>';

                        return html;
                    }
                },
                {
                    data: 'payment_method'
                },
                {
                    "mRender": function(data, type, row) {
                        html = "<a href='#' style='color:" + row.os_color + "''  class='orderstatus' id='status" + row.id + "' status=" + row.order_status + " title='" + row.order_cancelled_reason + "' data-type='select'  data-pk='" + row.id + "'  class='editable editable-click' data-original-title='' title=''>" + row.os_name + "</a>";
                        return html;
                    }
                },
                {
                    "mRender": function(data, type, row) {
                        let html = "<div class='btn-group float-left pr-2'><a href='" + SITE_URL + "franchise/order/view/" + row.id + "'  class='btn btn-xs btn-primary  btn-view text-white'><i class='fas fa-eye'></i></a></div>";

                        return html;
                    }
                },
            ],
            "columnDefs": [{
                "targets": [0, 8, 9, 10, 11], //first column / numbering column
                "orderable": false, //set not orderable
            }, ],
            "drawCallback": function(settings, json) {
                $('.orderstatus').editable({
                    type: "select",
                    mode: 'inline',
                    source: [

                        @foreach ($status_list as $status)
                            {
                                value: '{{ $status->id }}',
                                text: '{{ $status->os_name }}'
                            },
                        @endforeach
                    ],
                    mode: 'popup',
                    url: SITE_URL + 'franchise/order/updatestatus',
                    params: {
                        'updatestatus': 'AjaxEditableCall',
                        '_token': $('meta[name=csrf-token]').attr('content')
                    },
                    success: function(data) {
                        $('#status' + data.id).css({
                            'color': data.color
                        });
                    }

                });
                $(".orderstatus").mouseover(function() {
                    var reason = $(this).attr('title');
                    var status = $(this).attr('status');
                    if (status == '7') {
                        if (reason != "") {
                            $('.orderstatus').tooltip();
                        }
                    }
                });


            }

        }).on('init.dt', function() {
            let html = `<select name="" id="status" class="form-control form-control-sm float-left franchise_search" style="width:40%">
      <option value="">Select status </option>`;
            @foreach ($status_list as $status)
                html += `<option value="{{ $status->id }}" {{ Session::get('fs_status') == $status->id ? 'selected' : '' }}>{{ $status->os_name }}</option>`;
            @endforeach
            html += `</select>
      <button class="btn btn-danger btn-sm float-right btn-reset ml-2">Reset</button>
     `;
            $('#table_filter').before().append(html);
        });
        $(document).on('change', '#status', function() {
            var status = $(this).val();
            var new_url = SITE_URL + "franchise/order/list?status=" + status;
            table.ajax.url(new_url).load();
        })
        $(document).on('click', '.btn-reset', function() {
            $('#status').val('');
            $('#session_status').val('');
            table
                .search('')
                .columns().search('');
            var new_url = SITE_URL + "franchise/order/list";
            table.ajax.url(new_url).load();
        });
        setTimeout(function() {
            // window.location.reload();
        }, 60000);
    </script>
@endsection
