@extends('admin.layout.layout')
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
                    <a onclick="ExportExcel('{{ auth('admin')->user()->admin_id }}')"
                        class="btn btn-primary btn-sm float-right" style="color:#fff;margin-right:10px;">Export</a>
                    {{-- <a  href="javascript:;" onclick="syncOrder();" class="btn btn-primary btn-sm float-right" style="margin-right:5px;">Sync Order</a> --}}
                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive">
                    <table id="table" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Order From</th>
                                <th>Order No.</th>
                                <th>Date</th>
                                @if ($is_accountant != 1)
                                    <th>Customer</th>
                                @endif
                                <th>Franchisee</th>
                                <th>Delivery Person</th>
                                {{-- <th>Order From</th> --}}
                                <th>Order Id</th>
                                <th>Price</th>
                                @if ($is_accountant == 1)
                                    <!-- <th>Articles 0%</th> -->
                                    <th>Discount</th>
                                    <th>Articles 9%</th>
                                    <th>Articles 21%</th>
                                    <th>Delivery charge</th>
                                @endif
                                <th>Order Payment</th>
                                <th>Payment Method</th>
                                <th class="text-center">Status</th>
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
    <input type='hidden' name='session_frs_id' id='session_frs_id' value="{{ Session::get('ad_frs_id') }}">
    <input type='hidden' name='session_status' id='session_status' value="{{ Session::get('ad_status') }}">
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
            "dom": "<'row'<'col-sm-6 col-md-2'l>>" +
                "<'row'<'col-sm-12 col-md-12'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>", //Initial no order.
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": SITE_URL + "admin/order/list",
                "type": "POST",
                "data": function(d) {
                    d._token = $('meta[name=csrf-token]').attr('content');
                    d.frs_id = $('#session_frs_id').val();
                    d.status = $('#session_status').val();
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
                        row.channel_image = row.channel_image ? row.channel_image : row.name ? 'Uber.png' :
                            'drank.png';
                        let html = "<img class='channel_image' src=" + SITE_URL + 'images/channel/' + row
                            .channel_image + ">";

                        return html;
                    }
                },
                {
                    "mRender": function(data, type, row) {
                        return row.order_id; // Menampilkan `order_id` di datatable
                    }
                },
                {
                    data: 'new_order_date'
                },
                @if ($is_accountant != 1)
                    {
                        data: 'customer_name'
                    },
                @endif {
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
                // },
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
                {
                    "mRender": function(data, type, row) {
                        let order_final_with_discount = row.order_final_with_discount;
                        let html = "€ " + parseFloat(order_final_with_discount).toFixed(2);

                        return html;
                    }
                },
                @if ($is_accountant == 1)
                    {
                        "mRender": function(data, type, row) {
                            //  let html = "€ "+row.product_price_0;
                            let order_discount = row.order_discount;
                            let html = "€ " + parseFloat(order_discount).toFixed(2);

                            return html;
                        }
                    }, {
                        "mRender": function(data, type, row) {
                            let html = "€ " + row.product_price_9;

                            return html;
                        }
                    }, {
                        "mRender": function(data, type, row) {
                            let html = "€ " + row.product_price_21;

                            return html;
                        }
                    }, {
                        "mRender": function(data, type, row) {
                            let order_delivery_charge = row.order_delivery_charge;
                            let html = "€ " + parseFloat(order_delivery_charge).toFixed(2);

                            return html;
                        }
                    },
                @endif {
                    "mRender": function(data, type, row) {
                        let color = '';
                        let status = '';
                        if (row.order_payment_status) {
                            color = '#2c8c1b';
                        } else {
                            color = '#cbce23';

                        }
                        let html = '<div style="color:' + color + '; text-align:center">' + row
                            .order_payment_status_text + '</div>';

                        return html;
                    }
                },
                {
                    data: 'payment_method'
                },
                {
                    "mRender": function(data, type, row) {
                        html = "<a href='#' style='color:" + row.os_color +
                            "''  class='orderstatus' id='status" + row.order_id + "' status=" + row
                            .order_status + " title='" + row.order_cancelled_reason +
                            "' data-type='select'  data-pk='" + row.order_id +
                            "'  class='editable editable-click' data-original-title='' title=''>" + row
                            .os_name + "</a>";
                        return html;
                    }
                },
                {
                    "mRender": function(data, type, row) {
                        let html = "<div class='btn-group float-left pr-2'><a href='" + SITE_URL +
                            "admin/order/view/" + row.order_id +
                            "'  class='btn btn-xs btn-primary  btn-view text-white'><i class='fas fa-eye'></i></a></div>";

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
                    url: SITE_URL + 'admin/order/updatestatus',
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
            let html = `
      <select name="" id="frs_id" class="col-md-4 form-control form-control-sm float-left fran_search" style="width:40%">
      <option value="">Select Franchise </option>`;
            @foreach ($Franchise as $fra)
                html +=
                    `<option value="{{ $fra->id }}" {{ Session::get('ad_frs_id') == $fra->id ? 'selected' : '' }}>{{ $fra->franchises_name }}</option>`;
            @endforeach
            html += `</select>

   <select name="" id="status" class="col-md-4 form-control form-control-sm float-left ml-2 status_search" style="width:40%">
      <option value="">Select status </option>`;
            @foreach ($status_list as $status)
                html +=
                    `<option value="{{ $status->id }}" {{ Session::get('ad_status') == $status->id ? 'selected' : '' }}>{{ $status->os_name }}</option>`;
            @endforeach
            html += `</select>
      <button class="btn btn-danger btn-sm float-right btn-reset ml-2">Reset</button>
     `;
            $('#table_filter').before().append(html);
        });
        $(document).on('change', '#frs_id,#status', function() {
            var frs_id = $('#frs_id').val();
            var status = $('#status').val();
            var new_url = SITE_URL + "admin/order/list?frs_id=" + frs_id + '&status=' + status;
            table.ajax.url(new_url).load();
        })
        $(document).on('click', '.btn-reset', function() {
            $('#frs_id').val('');
            $('#status').val('');
            $('#session_frs_id').val('');
            $('#session_status').val('');
            table
                .search('')
                .columns().search('');
            var new_url = SITE_URL + "admin/order/list";
            table.ajax.url(new_url).load();
        });

        function deleteStock(id) {
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
                                url: SITE_URL + 'admin/order/delete',
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

        function syncOrder() {
            loader_show();
            $.ajax({
                url: SITE_URL + 'admin/product/syncOrder',
                type: 'POST',
                data: '_token=' + $('meta[name=csrf-token]').attr('content'),
                success: function(obj) {
                    if (obj.status == true) {
                        loader_hide();
                        messageAlert('Success', obj.msg, 'fa-check', 'success')
                    } else {
                        $.alert('Something went wrong');
                    }
                }
            });
        }
        //  setTimeout(function() {
        //   window.location.reload();
        //  }, 60000);
        function ExportExcel(f_id) {
            var frs_id = $('#frs_id').val();
            var status = $('#status').val();
            loader_show();
            $.ajax({
                url: SITE_URL + 'admin/order/export',
                type: 'POST',
                data: 'frs_id=' + frs_id + '&status=' + status + '&_token=' + $('meta[name=csrf-token]').attr(
                    'content'),
                success: function(obj) {
                    if (obj.status) {
                        console.log(obj);
                        location.href = obj.url;
                        loader_hide();
                    }
                }
            });

        }
    </script>
@endsection
