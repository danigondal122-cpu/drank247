@extends('admin.layout.layout')
@section('pageCSS')
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
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
                                <th><input type="checkbox" class="select_all_order" value="1"
                                        {{ Session::get('select_all_checked') != '' ? 'checked' : '' }}></th>
                                <th>Order From</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Order Id</th>
                                <th>Price</th>
                                <th>Order Payment</th>
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

    <input type='hidden' name='session_month' id='session_month'
        value="{{ Session::get('fs_month') != '' ? Session::get('fs_month') : date('m') }}">
    <input type='hidden' name='session_year' id='session_year'
        value="{{ Session::get('fs_year') != '' ? Session::get('fs_year') : date('Y') }}">
    <input type='hidden' name='session_week' id='session_week'
        value="{{ Session::get('fs_week') != '' ? Session::get('fs_week') : '' }}">
    <input type='hidden' name='session_f_id' id='session_f_id'
        value="{{ Session::get('fs_id') != '' ? Session::get('fs_id') : '' }}">
    <input type='hidden' name='session_c_id' id='session_c_id'
        value="{{ Session::get('fs_cid') != '' ? Session::get('fs_cid') : '' }}">
    <!-- /.row -->
@endsection
@section('pageJS')
    <script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/x-editable/bootstrap-editable.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script>
        var table;
        table = $('#table').DataTable({
            "pageLength": 50,
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "dom": "<'row'<'col-sm-6 col-md-2'l><'col-sm-6 col-md-10'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": SITE_URL + "admin/order/orderlist",
                "type": "POST",
                "data": function(d) {
                    d._token = $('meta[name=csrf-token]').attr('content');
                    d.month = $('#session_month').val();
                    d.year = $('#session_year').val();
                    d.week = $('#session_week').val();
                    d.franchise_id = $('#session_f_id').val();
                    d.cs_id = $('#session_c_id').val();
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
                        let checked = '';
                        if (row.checked == true) {
                            checked = 'checked';
                        }
                        let html =
                            `<input type="checkbox" class="order_id_array" name="order_id[]" value="${row.order_channel_order_id}" ${checked}>`;
                        return html;
                    }
                },
                {
                    "mRender": function(data, type, row) {
                        row.channel_image = row.channel_image ? row.channel_image : row.name ? 'Uber.png' :
                            'drank.png';
                        let html = "<img class='channel_image' src=" + SITE_URL + 'images/channel/' + row
                            .channel_image + ">";

                        return html;
                    }
                },

                {
                    data: 'new_order_date'
                },
                {
                    data: 'customer_name'
                },
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
                        let html = '<div style="color:' + color + '; text-align:center">' + row
                            .order_payment_status_text + '</div>';

                        return html;
                    }
                },

            ],
            "columnDefs": [{
                "targets": [0, 1, 2, 3, 4, 5, 6], //first column / numbering column
                "orderable": false, //set not orderable
            }, ],
            "drawCallback": function(settings, json) {


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
            let html =
                `<select name="" id="year" class="form-control form-control-sm float-left franchise_search" style="width:10%;margin-right:5px;"><option value="">Select Year</option><option value="2020" {{ Session::get('fs_year') == '2020' ? 'selected' : (date('Y') == 2020 ? 'selected' : '') }}>2020</option><option value="2021" {{ Session::get('fs_year') == '2021' ? 'selected' : (date('Y') == 2021 ? 'selected' : '') }}>2021</option><option value="2022" {{ Session::get('fs_year') == '2022' ? 'selected' : (date('Y') == 2022 ? 'selected' : '') }}>2022</option><option value="2023" {{ Session::get('fs_year') == '2023' ? 'selected' : (date('Y') == 2023 ? 'selected' : '') }}>2023</option><option value="2024" {{ Session::get('fs_year') == '2024' ? 'selected' : (date('Y') == 2024 ? 'selected' : '') }}>2024</option></select>`;
            html +=
                `<select name="" id="channel" class="form-control form-control-sm float-left franchise_search" style="width:10%;margin-right:5px;"><option value="">Select Channel</option>`;
            @foreach ($channel_list as $key => $c)
                html +=
                    `<option value="{{ $c->channel_id }}" {{ Session::get('fs_cid') == $c->channel_id ? 'selected' : '' }}>{{ $c->channel_name }}</option>`;
            @endforeach
            html += `</select>`
            html +=
                `<select name="" id="month" class="form-control form-control-sm float-left franchise_search" style="width:10%;margin-right:5px;"><option value="">Select Month</option>`;
            @foreach ($monthlist as $key => $month)
                html +=
                    `<option value="{{ $key }}" {{ Session::get('fs_month') == $key ? 'selected' : (date('m') == $key ? 'selected' : '') }}>{{ $month }}</option>`;
            @endforeach
            html += `</select>`
            html +=
                `<select name="" id="franchise" class="form-control form-control-sm float-left franchise_search" style="width:15%;margin-right:5px;"><option value="">Select Franchise</option>`;
            @foreach ($Franchise as $key => $f)
                html +=
                    `<option value="{{ $f['id'] }}" {{ Session::get('fs_id') == $f['id'] ? 'selected' : '' }}>{{ $f['franchises_name'] }}</option>`;
            @endforeach
            html += `</select>`
            html +=
                `<select name="" id="week" class="form-control form-control-sm float-left franchise_search" style="width:15%;margin-right:5px;"><option value="">Select Week</option>`;
            @foreach ($weeklist as $key => $week)
                html +=
                    `<option value="{{ $key }}" {{ Session::get('fs_week') == $key ? 'selected' : '' }}>{{ $week }}</option>`;
            @endforeach
            html += `</select>
      <a class="btn btn-danger float-right btn-sm ml-2 generate_pdf" style="display:{{ Session::get('fs_id') == '' ? 'none' : 'block' }}" href="javascript:void(0)" onclick="changeinvoicePdf()">Generate Invoice</a>
      <button class="btn btn-danger btn-sm float-right btn-reset ml-2">Reset</button>
     `;
            $('#table_filter').before().append(html);
            //  $( "#year" ).trigger( "change" );
            $("#month").trigger("change");
        });


        $(document).on('change', '#year,#month,#week,#franchise,#channel', function() {
            var year = $('#year').val();
            var month = $('#month').val();
            var channel = $('#channel').val();
            var week = $('#week').val();
            var franchise = $('#franchise').val();

            $('#session_year').val(year);
            $('#session_month').val(month);
            $('#session_week').val(week);
            $('#session_f_id').val(franchise);
            $('#session_c_id').val(channel);
            var new_url = SITE_URL + "admin/order/orderlist?year=" + year + "&month=" + month + "&week=" + week +
                "&f_id=" + franchise + "&cs_id=" + channel;
            table.ajax.url(new_url).load();
        })

        $(document).on('change', '#franchise', function() {
            $('.generate_pdf').css('display', 'block');
        });

        $(document).on('click', '.btn-reset', function() {
            $('#year').val('');
            $('#month').val('');
            $('#week').val('');
            $('#franchise').val('');
            $('#channel').val('');
            $('#session_month').val('');
            $('#session_week').val('');
            $('#session_year').val('');
            $('#session_f_id').val('');
            $('#session_c_id').val('');
            $('.generate_pdf').hide();
            table
                .search('')
                .columns().search('');
            var new_url = SITE_URL + "admin/order/orderlist";
            table.ajax.url(new_url).load();
        });

        $(document).on('click', '.order_id_array', function() {
            var orderId = $(this).val();
            $.ajax({
                url: SITE_URL + 'admin/saveOrderchannel',
                type: 'get',
                data: {
                    orderId: orderId
                },
                dataType: 'json',
                success: function(obj) {

                }
            });

        });

        function changeinvoicePdf() {
            loader_show();
            var year = $('#year').val();
            var month = $('#month').val();
            var franchise = $('#franchise').val();
            var week = $('#week').val();
            var channel = $('#channel').val();
            if (week == '') {
                alert('Please Select Week')
                loader_hide();
                return
            }

            if (franchise == '') {
                alert('Please Select Franchise')
                loader_hide();
                return
            }

            $.ajax({
                url: SITE_URL + 'admin/edit-franchise-invoice',
                type: 'get',
                data: {
                    year: year,
                    month: month,
                    franchise: franchise,
                    week: week,
                    channel: channel
                },
                dataType: 'json',
                success: function(obj) {

                    if (obj.status == true) {

                    } else {
                        $.alert('Something went wrong');
                    }
                    loader_hide();
                }
            });
        }



        $(document).on('click', '.select_all_order', function() {
            var orderId = $(this).val();
            $.ajax({
                url: SITE_URL + 'admin/saveallOrderchannel',
                type: 'get',
                dataType: 'json',
                success: function(obj) {
                    $('#table').DataTable().ajax.reload();
                }
            });

        });
    </script>
@endsection
