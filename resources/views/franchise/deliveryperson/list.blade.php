@extends('franchise.layout.layout')
@section('pageCSS')
  <link rel="stylesheet" type="text/css" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css')}}">

  <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
@endsection
@section('header_content')
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">Delivery Person List</h1>
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
          <h3 class="card-title">Delivery Person List</h3>
            <a href="{{ url('franchise/deliveryperson/add') }}" class="btn btn-primary btn-sm float-right">Add Delivery Person</a>
        </div>
        <!-- /.card-header -->
        <div class="card-body table-responsive">
          <table id="table" class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Mobile no.</th>
                <th>City</th>
                {{-- <th>Pool</th> --}}
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
       "url": SITE_URL + "franchise/deliveryperson/list",
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
        { data: 'dp_name' },
        { data: 'dp_email' },
        { data: 'dp_contact_no' },
        { data: 'dp_city' },
        // { data: 'area' },
        {
          "mRender": function ( data, type, row ) {
            $color=row.dp_onoff=='online' ? 'checked' : '';
            let html = "<div class='btn-group float-left' style='margin-right: -14px;'><div class='custom-control custom-switch custom-switch-off-danger custom-switch-on-success'> <input type='checkbox' class='custom-control-input scheduleonoff' id='customSwitch"+row.id+"' onchange='onoff("+row.id+");' "+$color+"><label class='custom-control-label' for='customSwitch"+row.id+"'></label></div></div>";
            html += "<div class='btn-group float-left pr-2'><a href='"+SITE_URL+"franchise/deliveryperson/edit/"+row.id+"' class='btn btn-xs btn-secondary btn-edit text-white'><i class='fa fa-pencil-alt'></i></a></div>";
            html += "<div class='btn-group float-left pr-2'><a href='"+SITE_URL+"franchise/deliveryperson/view/"+row.id+"' class='btn btn-xs btn-primary btn-view text-white'><i class='fas fa-eye'></i></a></div>";
            html += "<div class='btn-group float-left pr-2'><a href='javascript:;' onclick='deleteDelivery("+row.id+")' class='btn btn-xs btn-danger btn-delete text-white'><i class='fa fa-trash-alt'></i></a></div>";
            return html;
          }
        },
      ],
     "columnDefs": [{
       "targets": [3,4], //first column / numbering column
       "orderable": false, //set not orderable
     }, ],

   });
   function deleteDelivery(id) {
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
                url: SITE_URL + 'franchise/deliveryperson/delete',
                type: 'POST',
                data: 'id=' + id+'&_token='+$('meta[name=csrf-token]').attr('content'),
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

   function onoff(id) {
    loader_show();
    var checkBox = document.getElementById("customSwitch"+id);
    var value=(checkBox.checked == true) ? 'online' : 'offline'

    $.ajax({
      url: SITE_URL + 'franchise/deliveryperson/updateonoff',
      type : 'POST',
      data: 'value='+ value + '&id='+id+'&_token='+$('meta[name=csrf-token]').attr('content'),
      success : function(obj){
        loader_hide();
        // location.reload();
      },

    })
}
 </script>
@endsection
