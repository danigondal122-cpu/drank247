@extends('admin.layout.layout')
@section('pageCSS')
  <link rel="stylesheet" type="text/css" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css')}}">
  <link rel="stylesheet" type="text/css" href="{{asset('plugins/x-editable/bootstrap-editable.css')}}">
  <link rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-touchspin/4.3.0/jquery.bootstrap-touchspin.min.css"
  integrity="sha512-0GlDFjxPsBIRh0ZGa2IMkNT54XGNaGqeJQLtMAw6EMEDQJ0WqpnU6COVA91cUS0CeVA5HtfBfzS9rlJR3bPMyw=="
  crossorigin="anonymous" />
@endsection
<style>

</style>
@section('header_content')
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">Ware House  List</h1>
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
          <div class="row">
          <div class="col-md-6 col-xs-5 col-sm-5" style="display:inline-block;"><h3 class="card-title">Ware House List</h3></div>
          <div class="col-md-6 col-xs-7 col-sm-7" style="display:inline-block;">
            <a href="{{ url('admin/warehouse/add') }}" class="btn btn-primary btn-sm float-right">Add Ware House</a>
            </div>
        </div>

        </div>
        <!-- /.card-header -->
        <div class="card-body table-responsive">
          <table id="table" class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>Logo</th>
                <th>Ware House Name</th>
                <th>Min Stock Order Amount</th>
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

<script src="{{ asset('plugins/jquery-ui/jquery-ui.min.js') }}"></script>
<script src="{{asset('plugins/x-editable/bootstrap-editable.min.js')}}" type="text/javascript"></script>
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
  <script>
   var table;
   table = $('#table').DataTable({
     "pageLength": 10,
     'bJQueryUI':true,

     "processing": true, //Feature control the processing indicator.
     "serverSide": true, //Feature control DataTables' server-side processing mode.
     "order": [], //Initial no order.
     "dom": "<'row'<'col-sm-6 col-md-2'l>>" +
        "<'row'<'col-sm-12 col-md-12'f>>" +
       "<'row'<'col-sm-12'tr>>" +
       "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>", //Initial no order.
     // Load data for the table's content from an Ajax source
     "ajax": {
       "url": SITE_URL + "admin/warehouse/list",
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
     columns: [
      {
          "mRender": function ( data, type, row ) {
           let html = "<img class='image' style='width:35px' src="+row.image+">";

            return html;
          }
        },
        { data: 'wh_name' },

        { data:'wh_minprice'},
        {
          "mRender": function ( data, type, row ) {
            let html = "<div class='btn-group float-left pr-2'><a href='"+SITE_URL+"admin/warehouse/edit/"+row.id+"' class='btn btn-xs btn-secondary btn-edit text-white'><i class='fa fa-pencil-alt'></i></a></div>";
            html += "<div class='btn-group float-left pr-2'><a href='javascript:;' onclick='deleteWarehouse("+row.id+")' class='btn btn-xs btn-danger btn-delete text-white'><i class='fa fa-trash-alt'></i></a></div>";
            return html;
          }
        },
      ],
      "columnDefs": [
      { "width": "30%", "targets": 0 ,"orderable": false},
      { "width": "10%", "targets": 1 ,"orderable": true},
      { "width": "20%", "targets": 2 ,"orderable": false},
      { "width": "10%", "targets": 3 ,"orderable": false},

     ],
   });
   function deleteWarehouse(id) {
      $.confirm({
        title: '',
        content: 'Are you sure to delete?',
        closeIcon: true,
        buttons: {
          confirm: {
            text: 'Delete',
            btnClass: 'btn-danger',
            action: function () {
              $.ajax({
                url: SITE_URL + 'admin/warehouse/'+id,
                type: 'POST',
                data: '_method=delete&_token='+$('meta[name=csrf-token]').attr('content'),
                success: function (obj) {
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
            action: function () {}
          },
        }
      });
   }
 </script>
@endsection
