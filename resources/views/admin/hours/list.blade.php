@extends('admin.layout.layout')
@section('pageCSS')
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
@endsection
@section('header_content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Hours List</h1>
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
                    <h3 class="card-title">Customer Service: {{ $name }}</h3>
                    <input type="hidden" name="id" id="id" value="{{ $id }}">
                    {{-- <a href="{{ url('customer_service/hours/add') }}" class="btn btn-primary btn-sm float-right">Add Hours</a> --}}
                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive">
                    <table id="table" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Total Hours</th>
                                {{-- <th>Action</th> --}}
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        <tfoot>
                            <tr style="background-color:#f2f2f2;">
                                <th colspan="4">Total Hours</th>
                                <th id="TotalHours">0.00</th>
                            </tr>
                        </tfoot>
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
                "url": SITE_URL + "admin/customerservice/hours/lists",
                "type": "POST",
                "data": function(d) {
                    d.id = $('#id').val();
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
            "drawCallback": function(settings) {

                var response = settings.json;
                $('#TotalHours').html(response.TotalHours);
            },
            columns: [{
                    data: 'start_date'
                },
                {
                    data: 'end_date'
                },
                {
                    data: 'start_time'
                },
                {
                    data: 'end_time'
                },
                {
                    data: 'total_hours_inH'
                },

                // {
                //   "mRender": function ( data, type, row ) {
                //     let html = "<div class='btn-group float-left pr-2'><a href='"+SITE_URL+"customerservice/hours/edit/"+row.id+"' class='btn btn-xs btn-secondary btn-edit text-white'><i class='fa fa-pencil-alt'></i></a></div>";
                //     html += "<div class='btn-group float-left pr-2'><a href='javascript:;' onclick='deleteHours("+row.id+")' class='btn btn-xs btn-danger btn-delete text-white'><i class='fa fa-trash-alt'></i></a></div>";
                //     return html;
                //   }
                // },

            ],
            "columnDefs": [{
                "targets": [4], //first column / numbering column
                "orderable": false, //set not orderable
            }, ],

        }).on('init.dt', function() {

        });
    </script>
@endsection
