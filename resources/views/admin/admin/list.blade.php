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
                    <h1 class="m-0 text-dark">Admin</h1>
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
                    <h3 class="card-title">Admin</h3>
                    <a href="{{ url('admin/admin/add') }}" class="btn btn-primary btn-sm float-right">Add Admin</a>
                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive">
                    <table id="table" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Contact no</th>
                                <th>Accountant</th>
                                <th>Assign Module</th>
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
    <div id="commonModalDocument" class="modal fade" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content" id="commonModalHtmlDocument"></div>
        </div>
    </div>

    <div id="assignmodule" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-header" style="padding: 20px;">
                Assign module
            </div>
            <div class="modal-content" id="assignmodulemodal" style="padding: 20px;">
                <form method="post" id="form-assignmodule">
                    @csrf
                    <input type="hidden" name="id" id="id" value="">
                    <div class="row">
                        <div class="form-group">
                            <label for="Module">*Assign Module</label>
                        </div>
                        <div class="col-sm-12">
                            <div class="row module_div">

                            </div>
                            <span id="module_error"></span>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" style="float: right;"><i
                            class="fas fa-save"></i>&nbsp;&nbsp;Save</button>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('pageJS')
    <script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/x-editable/bootstrap-editable.min.js') }}" type="text/javascript"></script>
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
                "url": SITE_URL + "admin/admin/list",
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
                    data: 'name'
                },
                {
                    data: 'email'
                },
                {
                    data: 'admin_mobile_no'
                },
                {
                    "mRender": function(data, type, row) {
                        let color = '';
                        let status = '';
                        if (row.is_accountant == '1') {
                            row.is_accountant = 'YES';
                            color = '#2c8c1b';
                        } else {
                            row.is_accountant = 'NO';
                            color = '#dc3545';

                        }
                        let html = '<div style="background:' + color +
                            '; text-align:center;border:unset;" data-id=' + row.id + ' data-value=' +
                            row.is_accountant + ' class="btn btn-primary changeaccountant">' + row
                            .is_accountant + '</div>';

                        return html;
                    }
                },
                {
                    "mRender": function(data, type, row) {
                        html = '';
                        $.each(row.modules, function(key, val) {
                            html += "<a href='#'  id='module" + val.module_id + "' name=" + val
                                .module_name + " onclick='openassignmodule(" + row.id +
                                ")' data-id='" + row.id +
                                "'  class='assignmodule' data-original-title='' title=''>" + val
                                .module_name + "</a><br>";
                        });
                        return html;
                    }
                },
                {
                    "mRender": function(data, type, row) {
                        let html = "<div class='btn-group float-left pr-2'><a href='" + SITE_URL +
                            "admin/admin/edit/" + row.id +
                            "' class='btn btn-xs btn-secondary btn-edit text-white'><i class='fa fa-pencil-alt'></i></a></div>";
                        html +=
                            "<div class='btn-group float-left pr-2'><a href='javascript:;' onclick='deleteAdmin(" +
                            row.id +
                            ")' class='btn btn-xs btn-danger btn-delete text-white'><i class='fa fa-trash-alt'></i></a></div>";
                        return html;
                    }
                },
            ],
            "columnDefs": [{
                "targets": [3], //first column / numbering column
                "orderable": false, //set not orderable
            }, ],

        });

        $(document).on('click', '.changeaccountant', function() {
            var id = $(this).data('id');
            var value = $(this).data('value');
            $.ajax({
                url: SITE_URL + 'admin/admin/changeaccountant',
                type: 'POST',
                data: {
                    'id': id,
                    '_token': $('meta[name=csrf-token]').attr('content'),
                    'value': value
                },
                success: function(obj) {
                    window.location.reload();
                    console.log(obj.data.is_accountant);
                    // if(obj.data.is_accountant == 0)
                    // {
                    //   $(this).css({'background': '#dc3545'}).attr("data-value","NO").text('NO');

                    // }else{
                    //   $(this).css({'background': '#2c8c1b'}).attr("data-value", "YES").text('YES');
                    // }
                    // 
                }
            });
        })

        function deleteAdmin(id) {
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
                                url: SITE_URL + 'admin/admin/delete',
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

        function openassignmodule(id) {
            $.ajax({
                url: SITE_URL + 'admin/admin/showassignmodule',
                type: 'get',
                data: 'id=' + id,
                success: function(obj) {
                    $('#id').val(id);
                    $('.module_div').html(obj.html);
                    $('#assignmodule').modal('show');
                }
            });

        }

        $(document).on('submit', '#form-assignmodule', function(e) {
            e.preventDefault();
            loader_show();
            var data = new FormData(this);
            $('#form-assignmodule .is-invalid').removeClass('is-invalid');
            $('#form-assignmodule .text-danger').remove();
            $.ajax({
                url: SITE_URL + 'admin/admin/updateAssignmodule',
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
                        $('#form-assignmodule')[0].reset();
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
