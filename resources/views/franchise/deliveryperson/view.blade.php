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
          <h1 class="m-0 text-dark">Detail</h1>
        </div>
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
@endsection
@section('content')
<input type="hidden" name="dp_id" id="dp_id" value="{{$row['id']}}"  >
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Delivery Person: {{$row['dp_name']}}</h3>
          <a href="{{ url('franchise/deliveryperson/list') }}" class="btn btn-secondary text-white btn-sm float-right"> <i class="fas fa-arrow-left"></i>&nbsp;&nbsp;Back</a>
          <a onclick="ExportExcel('{{auth('franchise')->id()}}')"  class="btn btn-primary btn-sm float-right" style="color:#fff;margin-right:10px;">Export</a>
        </div>
        <!-- /.card-header -->
        <div class="card-body table-responsive">
          <table id="table" class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>Date</th>
                <th>Time</th>
                <th>Odo Meter No.</th>
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
<script src="{{asset('plugins/x-editable/bootstrap-editable.min.js')}}" type="text/javascript"></script>
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
     "order": [], //Initial no order.
     "language": { search: '', searchPlaceholder: "Search..." },
     "dom": "<'row'<'col-sm-6 col-md-2'l><'col-sm-12 col-md-12'f>>" +
       "<'row'<'col-sm-12'tr>>" +
       "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>", //Initial no order.
     // Load data for the table's content from an Ajax source
     "ajax": {
       "url": SITE_URL + "franchise/deliveryperson/dateList",
       "type": "POST",
       "data": function(d) {
        d._token = $('meta[name=csrf-token]').attr('content');
        d.dp_id= $('#dp_id').val();

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
     columns: [
        { data: 'Date' },
        { data: 'TotalOrderTime' },
        { data: 'OdoMeter' },
        {
          "mRender": function ( data, type, row ) {
            let html = "<div class='btn-group float-left pr-2'><a href='"+SITE_URL+"franchise/deliveryperson/historydetail/"+row.history_id+"' class='btn btn-xs btn-primary btn-view text-white'><i class='fas fa-eye'></i></a></div>";
            return html;
          }
        },
      ],
     "columnDefs": [{
       "targets": [1,2,3], //first column / numbering column
       "orderable": false, //set not orderable
     }, ],

   }).on('init.dt', function() {
     let html = `
    <div class="input-group col-md-4 float-left">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="far fa-clock"></i></span>
                    </div>
                    <input type="text" class="form-control float-right" placeholder="Select Date range " name="date" id="date" autocomplete="off">
                  </div>`;
    html += `<button class="btn btn-danger btn-sm float-right btn-reset ml-2">Reset</button>`;
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
      var date = $('#date').val();
      var new_url = SITE_URL + "franchise/deliveryperson/dateList?date="+date;
      table.ajax.url(new_url).load();
     });

    $('input[name="date"]').on('cancel.daterangepicker', function(ev, picker) {
      $(this).val('');
     });

    },1000)
   });
   $(document).on('click', '.btn-reset', function() {
    $('#date').val('');
    table
      .search('')
      .columns().search('');
    var new_url = SITE_URL + "franchise/deliveryperson/dateList";
    table.ajax.url(new_url).load();
  });
function ExportExcel(f_id){
var delivery_id = $('#dp_id').val();
 var date = $('#date').val();
 $.ajax({
 url : SITE_URL+'franchise/historyhours/export',
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
