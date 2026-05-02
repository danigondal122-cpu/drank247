@extends('customerservice.layout.layout')
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
                                <!-- <th>Contact No</th> -->
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
    <input type='hidden' name='session_frs_id' id='session_frs_id' value="{{ Session::get('cs_frs_id') }}">
    <input type='hidden' name='session_status' id='session_status' value="{{ Session::get('cs_status') }}">
    <input type='hidden' name='session_dp_id' id='session_dp_id' value="{{ Session::get('cs_dp_id') }}">
    <div id="commonModal" class="modal fade" role="dialog">
        <div class="modal-dialog" style="width:auto;">
            <div class="modal-content" id="commonModalHtml"></div>
        </div>
    </div>

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
            "dom": "<'row'<'col-sm-6 col-md-2'l>>" +
                "<'row'<'col-sm-12 col-md-12'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>", //Initial no order.
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": SITE_URL + "customer_service/order/list",
                "type": "POST",
                "data": function(d) {
                    d._token = $('meta[name=csrf-token]').attr('content');
                    d.frs_id = $('#session_frs_id').val();
                    d.status = $('#session_status').val();
                    d.dp_id = $('#session_dp_id').val();
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
                        if (row.order_payment_status_text == 'YES') {
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
                // {data:'customer_contactno'},
                {
                    "mRender": function(data, type, row) {
                        html = "<a href='#' style='color:" + row.os_color +
                            "''  class='orderstatus' id='status" + row.id + "' status=" + row
                            .order_status + "  title='" + row.order_cancelled_reason +
                            "' data-type='select'  data-pk='" + row.id +
                            "'  class='editable editable-click' data-original-title='' title=''>" + row
                            .os_name + "</a>";
                        return html;
                    }
                },

                {
                    "mRender": function(data, type, row) {
                        let html = "<div class='btn-group float-left pr-2'><a  onclick='Reassign(" + row
                            .id +
                            ")' class='btn btn-xs btn-secondary btn-edit text-white'><i class='fa fa-tasks'></i></a></div>";
                        html += "<div class='btn-group float-left'><a href='" + SITE_URL +
                            "customer_service/order/view/" + row.id +
                            "'  class='btn btn-xs btn-primary  btn-view text-white'><i class='fas fa-eye'></i></a></div>";
                        return html;


                        //  let html = "<div class='btn-group float-left pr-2'><a href='"+SITE_URL+"customer_service/order/view/"+row.id+"'  class='btn btn-xs btn-primary  btn-view text-white'><i class='fas fa-eye'></i></a></div>";

                        //   return html;
                    }
                },
            ],
            "columnDefs": [{
                    "width": "8%",
                    "targets": 0,
                    "orderable": false
                },
                {
                    "width": "8",
                    "targets": 1,
                    "orderable": true
                },
                {
                    "width": "20%",
                    "targets": 2,
                    "orderable": true
                },
                {
                    "width": "8%",
                    "targets": 3,
                    "orderable": true
                },
                {
                    "width": "8%",
                    "targets": 4,
                    "orderable": true
                },
                {
                    "width": "8%",
                    "targets": 5,
                    "orderable": true
                },
                {
                    "width": "8%",
                    "targets": 6,
                    "orderable": true
                },
                {
                    "width": "8%",
                    "targets": 7,
                    "orderable": true
                },
                {
                    "width": "8%",
                    "targets": 8,
                    "orderable": false
                },
                {
                    "width": "8%",
                    "targets": 9,
                    "orderable": false
                },
                {
                    "width": "8%",
                    "targets": 10,
                    "orderable": false
                },
                {
                    "width": "8%",
                    "targets": 11,
                    "orderable": false
                },

            ],
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
                    url: SITE_URL + 'customer_service/order/updatestatus',
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
      <select name="" id="frs_id" class="col-md-4 form-control form-control-sm float-left fran_search " style="width:20%">
      <option value="">Select Franchise </option>`;
            @foreach ($Franchise as $fra)
                html +=
                    `<option value="{{ $fra->id }}" {{ Session::get('cs_frs_id') == $fra->id ? 'selected' : '' }}>{{ $fra->franchises_name }}</option>`;
            @endforeach
            html += `</select>`
            html += `
      <select name="" id="dp_id" class="col-md-4 form-control form-control-sm float-left fran_search ml-2" style="width:20%">
      <option value="">Select Delivery Person</option>`;
            @foreach ($Delivery_person as $d)
                html +=
                    `<option value="{{ $d->id }}" {{ Session::get('cs_dp_id') == $d->id ? 'selected' : '' }}>{{ $d->dp_name }}</option>`;
            @endforeach
            html += `</select>`

            html += `<select name="" id="status" class="col-md-4 form-control form-control-sm float-left ml-2 status_search" style="width:40%">
      <option value="">Select status </option>`;
            @foreach ($status_list as $status)
                html +=
                    `<option value="{{ $status->id }}" {{ Session::get('cs_status') == $status->id ? 'selected' : '' }}>{{ $status->os_name }}</option>`;
            @endforeach
            html += `</select>
      <button class="btn btn-danger btn-sm float-right btn-reset ml-2">Reset</button>
     `;
            $('#table_filter').before().append(html);
        });
        $(document).on('change', '#frs_id,#status,#dp_id', function() {
            var frs_id = $('#frs_id').val();
            var status = $('#status').val();
            var dp_id = $('#dp_id').val();
            var new_url = SITE_URL + "customer_service/order/list?frs_id=" + frs_id + '&status=' + status +
                '&dp_id=' + dp_id;
            table.ajax.url(new_url).load();
        })

        $(document).on('click', '.btn-reset', function() {
            $('#frs_id').val('');
            $('#status').val('');
            $('#session_frs_id').val('');
            $('#session_status').val('');
            $('#dp_id').val('');
            $('#session_dp_id').val('');
            table
                .search('')
                .columns().search('');
            var new_url = SITE_URL + "customer_service/order/list";
            table.ajax.url(new_url).load();
        });

        function Reassign(id) {
            $.confirm({
                title: '',
                content: 'Are you sure you want To reassign ?',
                closeIcon: true,
                buttons: {
                    confirm: {
                        text: 'Reassign',
                        btnClass: 'btn-success',
                        action: function() {
                            $.ajax({
                                url: SITE_URL + 'customer_service/order/ReassignPopup',
                                type: 'POST',
                                data: 'id=' + id + '&_token=' + $('meta[name=csrf-token]').attr(
                                    'content'),
                                success: function(obj) {

                                    $('#commonModalHtml').html(obj);
                                    $('#commonModal').modal('show');

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
        setTimeout(function() {
            // window.location.reload();
        }, 60000);
    </script>


    {{-- #form_Reassignpopup script: --}}
    <script>
        $(document).on('submit', '#form_Reassignpopup', function(e) {

            e.preventDefault();
            loader_show();
            var data = new FormData(this);

            $('#form_Reassignpopup .is-invalid').removeClass('is-invalid');
            $('#form_Reassignpopup .text-danger').remove();
            $.ajax({
                url: SITE_URL + 'customer_service/order/Reassign',
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
                        messageAlert('Success', obj.msg, 'fa-check', 'success')
                        $('#form_Reassignpopup')[0].reset();
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
