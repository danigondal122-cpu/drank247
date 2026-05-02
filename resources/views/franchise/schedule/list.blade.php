@extends('franchise.layout.layout')
@section('pageCSS')
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker-bs3.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
@endsection
@section('header_content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Schedule List</h1>
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
                    <h3 class="card-title">Schedule List</h3>
                    <a href="{{ url('franchise/schedule/add') }}" class="btn btn-primary btn-sm float-right">Add Schedule</a>
                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive">
                    <table id="table" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Delivery Person</th>
                                <th>Date & Time</th>
                                <th>Pool</th>
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
    <script src="{{ asset('plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('plugins/daterangepicker/moment.min.js') }}"></script>
    <script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script>
        var table;
        table = $('#table').DataTable({
            "pageLength": 10,
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [],
            "language": {
                search: 'Search',
                searchPlaceholder: "Search..."
            },
            "dom": "<'row'<'col-sm-6 col-md-2'l><'search_table col-sm-12 col-md-12'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>", //Initial no order.
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": SITE_URL + "franchise/schedule/list",
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
                    data: 'dp_name'
                },
                {
                    data: 'time'
                },
                {
                    data: 'area'
                },
                {
                    "mRender": function(data, type, row) {
                        html = "<a href='#' style='color:" + row.os_color + "''  class='schedulestatus' id='status" + row.id + "' status=" + row.status + "  data-type='select'  data-pk='" + row.id + "'  class='editable editable-click' data-original-title='' title='Select Status'>" + row.os_name + "</a>";
                        return html;
                    }
                },
                {
                    "mRender": function(data, type, row) {
                        let html = "<div class='btn-group float-left pr-2'><a href='" + SITE_URL + "franchise/schedule/edit/" + row.id + "' class='btn btn-xs btn-secondary btn-edit text-white'><i class='fa fa-pencil-alt'></i></a></div>";
                        html += "<div class='btn-group float-left pr-2'><a href='javascript:;' onclick='deleteScedule(" + row.id + ")' class='btn btn-xs btn-danger btn-delete text-white'><i class='fa fa-trash-alt'></i></a></div>";
                        return html;
                    }
                },
            ],
            "columnDefs": [{
                "targets": [4], //first column / numbering column
                "orderable": false, //set not orderable
            }, ],
            "initComplete": function(settings, json) {

            }
        }).on('init.dt', function() {
            let html = `
     <select name="status" id="status"  class="form-control col-md-2 float-left mr-2 dev_search" style="width:40%;">
      <option value="">Select status</option>`;
            @foreach ($status_list as $status)
                html += `<option value="{{ $status->id }}">{{ $status->os_name }}</option>`;
            @endforeach
            html += `</select>
      <select name="delivery_id" id="delivery_id"  class="form-control col-md-2 float-left dev_search " style="width:40%;">
      <option value="">Select Delivery Person</option>`;
            @foreach ($delivery as $dev)
                html += `<option value="{{ $dev->id }}">{{ $dev->dp_name }}</option>`;
            @endforeach
            html += `</select>
     `;
            html += `
    <div class="input-group col-md-4 float-left date_div" style="">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="far fa-clock"></i></span>
                    </div>
                    <input type="text" class="form-control float-right" placeholder="Select Date range " name="date" id="date" autocomplete="off" style="width:40%">
                  </div>`;
            html += `

      <button class="btn btn-danger btn-sm float-right btn-reset ml-2">Reset</button>`;
            $('#table_filter').before().append(html);
            setTimeout(() => {
                $('input[name="date"]').daterangepicker({

                    autoUpdateInput: false,
                    timePicker24Hour: true,
                    locale: {
                        format: 'DD/MM/YYYY'
                    }

                });
                $('input[name="date"]').on('apply.daterangepicker', function(ev, picker) {
                    $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
                    var delivery_id = $('#delivery_id').val();
                    var date = $('#date').val();
                    var status = $('#status').val();
                    var new_url = SITE_URL + "franchise/schedule/list?delivery_id=" + delivery_id + '&date=' + date + '&status=' + status;
                    table.ajax.url(new_url).load();
                });

                $('input[name="date"]').on('cancel.daterangepicker', function(ev, picker) {
                    $(this).val('');
                });

            }, 1000)
        });
        $('#table').on('draw.dt', function() {
            $('.schedulestatus').editable({
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
                url: SITE_URL + 'franchise/schedule/updateStatus',
                params: {
                    'updateStatus': 'AjaxEditableCall',
                    '_token': $('meta[name=csrf-token]').attr('content')
                },
                success: function(data) {
                    $('#status' + data.id).css({
                        'color': data.color
                    });
                }

            });
        });
        $(document).on('change', '#delivery_id,#date,#status', function() {
            var delivery_id = $('#delivery_id').val();
            var date = $('#date').val();
            var status = $('#status').val();
            var new_url = SITE_URL + "franchise/schedule/list?delivery_id=" + delivery_id + '&date=' + date + '&status=' + status;
            table.ajax.url(new_url).load();
        })
        $(document).on('click', '.btn-reset', function() {
            $('#delivery_id').val('');
            $('#date').val('');
            $('#status').val('');
            table
                .search('')
                .columns().search('');
            var new_url = SITE_URL + "franchise/schedule/list";
            table.ajax.url(new_url).load();
        });

        function deleteScedule(id) {
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
                                url: SITE_URL + 'franchise/schedule/delete',
                                type: 'POST',
                                data: 'id=' + id + '&_token=' + $('meta[name=csrf-token]').attr('content'),
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
