@extends('franchise.layout.layout')
@section('pageCSS')
  <link rel="stylesheet" type="text/css" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css')}}">
  <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker-bs3.css') }}">
  <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
@endsection
@section('header_content')
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">Hours </h1>
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
          <h3 class="card-title">Hours</h3>
            {{-- <a href="{{ url('file-export') }}"  onclick="ExportExcel('{{auth('franchise')->user()->franchises_id}}'')"  class="btn btn-primary btn-sm float-right">Export</a> --}}
            <a onclick="ExportExcel('{{auth('franchise')->id()}}')"  class="btn btn-primary btn-sm float-right" style="color:#fff;">Export</a>

        </div>
        <!-- /.card-header -->
        <div class="card-body table-responsive">
          <table id="table" class="table table-bordered table-hover" style="width:100%">
            <thead>
              <tr>
                <th>Order No</th>
                <th>Delivery Person</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Total Time</th>
              </tr>
            </thead>

            <tbody>

            </tbody>
            <tfoot>
              <tr style="background-color:#f2f2f2;">
                <th colspan="4">Total Hours</th>
                <th id="TotalHours">10.00</th>
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
<script src="{{asset('plugins/x-editable/bootstrap-editable.min.js')}}" type="text/javascript"></script>
<script src="{{ asset('plugins/jquery-ui/jquery-ui.min.js') }}"></script>
<script src="{{ asset('plugins/daterangepicker/moment.min.js') }}"></script>
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
  <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
  <script>
   var table;
   table = $('#table').DataTable({
     "pageLength": 50,
     "processing": true, //Feature control the processing indicator.
     "serverSide": true, //Feature control DataTables' server-side processing mode.
     "order": [],
     "language": { search: '', searchPlaceholder: "Search..." },
     "dom": "<'row'<'col-sm-6 col-md-2'l>>" +"<'row'<'col-sm-12 col-md-12'f>>"+
       "<'row'<'col-sm-12'tr>>" +
       "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>", //Initial no order.
     // Load data for the table's content from an Ajax source
     "ajax": {
       "url": SITE_URL + "franchise/hours/list",
       "type": "POST",
       "data": function(d) {
        d._token = $('meta[name=csrf-token]').attr('content');
         // etc
       },
       dataFilter: function(data){
          var json = jQuery.parseJSON(data);
          json.recordsTotal = json.total;
          json.recordsFiltered = json.total;
          json.data = json.data;
          return JSON.stringify(json); // return JSON string
      }
     },
     "drawCallback": function (settings) {

        var response = settings.json;
        console.log(response.TotalHours);
        $('#TotalHours').html(response.TotalHours);
    },
     columns: [
        { data: 'id' },
        { data: 'dp_name' },
        { data: 'start_date' },
        { data: 'end_date' },
        { data: 'total_order_time' },

      ],
     "columnDefs": [{
       "targets": [2,3,4], //first column / numbering column
       "orderable": false, //set not orderable
     }, ],
     "initComplete": function( settings, json ) {

     }
    }).on('init.dt', function() {
     let html = `

      <select name="delivery_id" id="delivery_id"  class="form-control col-md-2 float-left hour_search" style="width:40%;">
      <option value="">Select Delivery Person</option>`;
      @foreach ($delivery as $dev)
        html += `<option value="{{ $dev->id }}">{{ $dev->dp_name }}</option>`;
      @endforeach
      html +=`</select>
     `; html += `
    <div class="input-group col-md-4 float-left hour_search" style="width:40%">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="far fa-clock"></i></span>
                    </div>
                    <input type="text" class="form-control float-right " placeholder="Select Date range " name="date" id="date" autocomplete="off">
                  </div>`;
                  html +=` <button class="btn btn-danger btn-sm float-right btn-reset ml-2">Reset</button>`;
    $('#table_filter').before().append(html);
    setTimeout(()=>{
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
      var new_url = SITE_URL + "franchise/hours/list?delivery_id="+delivery_id+'&date='+date;
      table.ajax.url(new_url).load();
     });

    $('input[name="date"]').on('cancel.daterangepicker', function(ev, picker) {
      $(this).val('');
     });

    },1000)
   });

   $(document).on('change', '#delivery_id,#date', function() {
    var delivery_id = $('#delivery_id').val();
    var date = $('#date').val();

    var new_url = SITE_URL + "franchise/hours/list?delivery_id="+delivery_id+'&date='+date;
    table.ajax.url(new_url).load();
  })
  $(document).on('click', '.btn-reset', function() {
    $('#delivery_id').val('');
    $('#date').val('');
    table
      .search('')
      .columns().search('');
    var new_url = SITE_URL + "franchise/hours/list";
    table.ajax.url(new_url).load();
  });
function ExportExcel(f_id){

     var delivery_id = $('#delivery_id').val();
      var date = $('#date').val();
      $.ajax({
      url : SITE_URL+'franchise/hours/export',
      type: 'POST',
      data: 'f_id=' + f_id+'&delivery_id='+delivery_id+'&date='+date+'&_token='+$('meta[name=csrf-token]').attr('content'),
      success: function (obj) {
        if(obj.status){
          console.log(obj);
          location.href = obj.url;
        }
      }
      });

}

 </script>
@endsection
