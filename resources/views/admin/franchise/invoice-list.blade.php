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
                    <h1 class="m-0 text-dark">All Invoices</h1>
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
                    <h3 class="card-title">Order Invoice</h3>

                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive">
                    <table id="table" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th></th>
                                <th>#</th>
                                <th>Franchise</th>
                                <th>Date</th>
                                <th>From</th>
                                <th>Through</th>
                                <th>Amount</th>
                                <th>Paid</th>
                                <th>Paid on</th>
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

    <input type='hidden' name='session_f_id' id='session_f_id'
        value="{{ Session::get('fs_f_id') != '' ? Session::get('fs_f_id') : '' }}">
    <input type='hidden' name='session_week' id='session_f_week'
        value="{{ Session::get('fs_f_week') != '' ? Session::get('fs_f_week') : '' }}">
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
            "autoWidth": false,
            // Load data for the table's content from an Ajax source
            "dom": "<'row'<'col-sm-6 col-md-2'l><'col-sm-6 col-md-10'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            "ajax": {
                "url": SITE_URL + "admin/order/invoicelist",
                "type": "POST",
                "data": function(d) {
                    d._token = $('meta[name=csrf-token]').attr('content');
                    d.franchise_id = $('#session_f_id').val();
                    d.week = $('#session_f_week').val();
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
                    "width": '5%',
                    "mRender": function(data, type, row) {
                        row.pdf_name = row.pdf_name ? row.pdf_name : 'drank.png';
                        let html = "<a href=" + SITE_URL + 'uploads/generatepdf/' + row.pdf_name +
                            " target='_blank'><img src=" + SITE_URL + 'img/pdf-circle.png' +
                            " width='40'></a>";

                        return html;
                    }
                },
                {
                    "width": '8%',
                    data: 'order_id'
                },
                {
                    "width": '18%',
                    data: 'franchises_name'
                },
                {
                    "width": '10%',
                    data: 'date'
                },
                {
                    "width": '10%',
                    data: 'from_date'
                },
                {
                    "width": '9%',
                    data: 'to_date'
                },
                {
                    "width": '4%',
                    data: 'amount'
                },
                {
                    "width": '8%',
                    data: 'paid_amount'
                },
                {
                    "width": '10%',
                    data: 'date'
                }

            ],
            "columnDefs": [{
                "targets": [0, 2, 3, 4, 5], //first column / numbering column
                "orderable": false, //set not orderable
            }, ]
        }).on('init.dt', function() {

            let html =
                `<select name="" id="franchise" class="form-control form-control-sm float-left franchise_search" style="width:15%;margin-right:5px;"><option value="">Select Franchise</option>`;
            @foreach ($Franchise as $key => $f)
                html +=
                    `<option value="{{ $f['franchise_id'] }}" {{ Session::get('fs_f_id') == $f['franchise_id'] ? 'selected' : '' }}>{{ $f['franchises_name'] }}</option>`;
            @endforeach
            html += `</select>`
            html +=
                `<select name="" id="week" class="form-control form-control-sm float-left franchise_search" style="width:15%;margin-right:5px;"><option value="">Select Week</option>`;
            @foreach ($weeklist as $key => $week)
                html +=
                    `<option value="{{ $key }}" {{ Session::get('fs_f_week') == $key ? 'selected' : '' }}>{{ $week }}</option>`;
            @endforeach
            html += `</select>
      <button class="btn btn-danger btn-sm float-right btn-reset ml-2">Reset</button>`;
            $('#table_filter').before().append(html);
        });


        $(document).on('change', '#franchise', function() {
            var f_id = $(this).val();
            $('#session_f_id').val(f_id);
            var new_url = SITE_URL + "admin/order/invoicelist?f_id=" + f_id;
            table.ajax.url(new_url).load();
        })
        $(document).on('change', '#week', function() {
            var week = $(this).val();
            $('#session_f_week').val(week);
            var new_url = SITE_URL + "admin/order/invoicelist?week=" + week;
            table.ajax.url(new_url).load();

        })


        $(document).on('click', '.btn-reset', function() {
            $('#session_f_week').val('');
            $('#session_f_id').val('');
            $('#franchise').val('');
            $('#week').val('');
            table
                .search('')
                .columns().search('');
            var new_url = SITE_URL + "admin/order/invoicelist";
            table.ajax.url(new_url).load();
        });
    </script>
@endsection
