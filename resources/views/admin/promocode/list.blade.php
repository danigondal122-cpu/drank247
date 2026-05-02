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
                    <h1 class="m-0 text-dark">Promo Code List</h1>
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
                    <h3 class="card-title">Promo Code List</h3>
                    <a href="{{ url('admin/promocode/add') }}" class="btn btn-primary btn-sm float-right">Add Promo Code</a>
                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive">
                    <table id="table" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Discount</th>
                                <th>Max Users</th>
                                <th>Max Per User</th>
                                <th>Start Date</th>
                                <th>End Date</th>
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
    <div id="commonModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content modal-md" id="commonModalHtml" style="width:max-content;"></div>
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
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": SITE_URL + "admin/promocode/list",
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
                    data: 'code_text'
                },
                {
                    "mRender": function(data, type, row) {
                        $type = row.discount_type == "0" ? '(€)' : '(%)';
                        let html = row.discount + '' + $type;

                        return html;
                    }
                },
                {
                    "mRender": function(data, type, row) {
                        $users = (row.limitation_type == "0" || row.limitation_type == "") ? 'Unlimited' :
                            (row.max_users != null ? row.max_users : "-");
                        let html = $users;
                        return html;
                    }
                },
                {
                    data: 'max_per_user'
                },

                {
                    data: 'start_date'
                },
                {
                    "mRender": function(data, type, row) {
                        $date = row.expiration_type == "0" ? '-' : row.end_date;
                        let html = $date;

                        return html;
                    }
                },

                {
                    "mRender": function(data, type, row) {
                        let html = "<div class='btn-group float-left pr-2'><a href='" + SITE_URL +
                            "admin/promocode/edit/" + row.id +
                            "' class='btn btn-xs btn-secondary btn-edit text-white'><i class='fa fa-pencil-alt'></i></a></div>";
                        html +=
                            "<div class='btn-group float-left pr-2'><a href='javascript:;'  onclick='viewPromoCodeOrder(" +
                            row.id +
                            ")' class='btn btn-xs btn-primary  btn-view text-white'><i class='fas fa-eye'></i></a></div>";
                        html +=
                            "<div class='btn-group float-left pr-2'><a href='javascript:;' onclick='deletePromoCode(" +
                            row.id +
                            ")' class='btn btn-xs btn-danger btn-delete text-white'><i class='fa fa-trash-alt'></i></a></div>";
                        if (row.code_status == "0") {
                            html +=
                                "<a class='btn btn-danger btn-xs activation  text-white' style='margin-left:10px;' title='Activate' data-code_id='" +
                                row.id + "' data-is-active='1'>Deactive</a>";
                        } else {
                            html +=
                                "<a class='btn btn-primary btn-xs activation  text-white' style='margin-left:10px;' title='Activate' data-code_id='" +
                                row.id + "' data-is-active='0'>Active</a>";
                        }


                        return html;
                    }
                },
            ],
            "columnDefs": [{
                "targets": [1, 2, 4, 5, 6], //first column / numbering column  
                "orderable": false, //set not orderable
            }, ],
            "initComplete": function(settings, json) {

            }
        }).on('init.dt', function() {

        });

        function deletePromoCode(id) {
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
                                url: SITE_URL + 'admin/promocode/delete',
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
        $(document).on('click', '.activation', function() {

            var code_id = this.getAttribute("data-code_id");
            var code_status = this.getAttribute("data-is-active");
            if (code_status == "1") {
                var confirm = "Are you sure you want to Active this Code?"
            } else {
                var confirm = "Are you sure you want to Deactive this Code?"
            }
            $.confirm({
                title: '',
                content: confirm,
                closeIcon: true,
                buttons: {
                    confirm: {
                        text: 'Yes',
                        btnClass: 'btn-primary',
                        action: function() {
                            $.ajax({
                                url: SITE_URL + 'admin/promocode/ActivateCode',
                                type: 'POST',
                                data: 'id=' + code_id + '&code_status=' + code_status +
                                    '&_token=' + $('meta[name=csrf-token]').attr('content'),
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

        });

        function viewPromoCodeOrder(id) {
            $.ajax({
                url: SITE_URL + 'admin/promocode/viewPromoCodeOrder',
                type: 'POST',
                data: 'id=' + id + '&_token=' + $('meta[name=csrf-token]').attr('content'),
                success: function(obj) {
                    $('#commonModalHtml').html(obj);
                    $('#commonModal').modal('show');

                }
            });
        }
    </script>
@endsection
