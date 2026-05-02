@extends('admin.layout.layout')
@section('pageCSS')
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/x-editable/bootstrap-editable.css') }}">
@endsection
@section('header_content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Category List</h1>
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
                    <h3 class="card-title">Category List</h3>
                    <a href="{{ url('admin/category/add') }}" class="btn btn-primary btn-sm float-right">Add Category</a>
                    {{-- <a  href="javascript:;" onclick="syncProduct();" class="btn btn-primary btn-sm float-right" style="margin-right:5px;">Sync Category</a> --}}
                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive">
                    <table id="table" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Category Name</th>
                                <th>Description</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="tablecontents">

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
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "rowId": 'category_rowid',
            "paging": false,
            "autoWidth": true,

            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": SITE_URL + "admin/category/list",
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
                    data: 'category_name'
                },
                {
                    data: 'description'
                },
                {
                    "mRender": function(data, type, row) {
                        let html = "<div class='btn-group pr-2'><a href='" + SITE_URL + "admin/category/edit/" + row.id + "' class='btn btn-xs btn-secondary btn-edit text-white'><i class='fa fa-pencil-alt'></i></a></div>";
                        html += "<div class='btn-group pr-2' style=';'><a href='" + SITE_URL + "admin/category/subcategorylist/" + row.id + "' class='btn btn-xs btn-success  btn-view text-white'><i class='fa fa-list'></i></a></div>";
                        html += "<div class='btn-group pr-2' style=''><a href='javascript:;' onclick='deleteCategory(" + row.id + ")' class='btn btn-xs btn-danger btn-delete text-white'><i class='fa fa-trash-alt'></i></a></div>";
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
                    "width": "55%",
                    "targets": 1,
                    "orderable": false
                },
                {
                    "width": "15%",
                    "targets": 2,
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
                                data: serialized + '&_token=' + $('meta[name=csrf-token]').attr('content'),
                                type: 'POST',
                                url: SITE_URL + "admin/category/updateCategoryOrder",
                                success: function(response) {
                                    loader_hide();
                                }
                            });
                        }
                    });
                }
            }
        });

        function deleteCategory(id) {
            $.confirm({
                title: '',
                content: 'Are you sure you want to delete ? All the products related to this category will be deleted',
                closeIcon: true,
                buttons: {
                    confirm: {
                        text: 'Delete',
                        btnClass: 'btn-danger',
                        action: function() {
                            $.ajax({
                                url: SITE_URL + 'admin/category/' + id,
                                type: 'POST',
                                data: '_method=DELETE&_token=' + $('meta[name=csrf-token]').attr('content'),
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
