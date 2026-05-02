@extends('customerservice.layout.layout')
@section('pageCSS')
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
@endsection
@section('header_content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Help</h1>
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
                    <h3 class="card-title">Help</h3>
                    <input type="hidden" name="id" id="id" value="{{ auth('customer_service')->user()->id }}">
                    {{-- <a href="{{ url('admin/product/add') }}" class="btn btn-primary btn-sm float-right">Add Product</a> --}}
                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive">
                    <table id="table" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Delivery Person</th>
                                {{-- <th>From Help</th> --}}
                                <th>Message</th>
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
                "url": SITE_URL + "customer_service/help/list",
                "type": "POST",
                "data": function(d) {
                    d._type = "0",
                        d._id = $("#id").val();
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
                    data: 'dp_name'
                },
                // {
                //     data: '_to_help'
                // },
                {
                    data: 'message'
                },
                {
                    "mRender": function(data, type, row) {
                        html = "<a href='#' style='color:" + row.os_color +
                            "''  class='orderstatus' id='status" + row.id + "' status=" + row.order_status +
                            "  data-type='select'  data-pk='" + row.id +
                            "'  class='editable editable-click' data-original-title='' title=''>" + row
                            .os_name + "</a>";
                        return html;
                    }
                },

                {
                    "mRender": function(data, type, row) {

                        let html =
                            "<div class='btn-group float-left pr-2'><a href='javascript:;' onclick='deleteHelp(" +
                            row.id +
                            ")' class='btn btn-xs btn-danger btn-delete text-white'><i class='fa fa-trash-alt'></i></a></div>";
                        return html;
                    }
                },

            ],
            "columnDefs": [{
                "targets": [0, 1, 2, 3], //first column / numbering column
                "orderable": false, //set not orderable
            }, ],
            "initComplete": function(settings, json) {
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
                    url: SITE_URL + 'customer_service/help/updatestatus',
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
      <select name="" id="dp_id" class="col-md-6 form-control form-control-sm float-left fran_search" style="width:40%">
      <option value="">Select Delivery Person </option>`;
            @foreach ($deliverylist as $del)
                html += `<option value="{{ $del->dp_id }}">{{ $del->dp_name }}</option>`;
            @endforeach
            html += `</select>
      <button class="btn btn-danger btn-sm float-right btn-reset ml-2">Reset</button>
     `;
            $('#table_filter').before().append(html);
        });
        $(document).on('change', '#dp_id', function() {
            var dp_id = $('#dp_id').val();
            var new_url = SITE_URL + "customer_service/help/list?dp_id=" + dp_id;
            table.ajax.url(new_url).load();
        })
        $(document).on('click', '.btn-reset', function() {
            $('#dp_id').val('');
            table
                .search('')
                .columns().search('');
            var new_url = SITE_URL + "customer_service/help/list";
            table.ajax.url(new_url).load();
        });

        function deleteHelp(id) {
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
                                url: SITE_URL + 'customer_service/help/delete',
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
    </script>
@endsection
