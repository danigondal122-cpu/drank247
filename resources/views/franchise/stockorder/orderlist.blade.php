@extends('franchise.layout.layout')
@section('pageCSS')
<link rel="stylesheet" type="text/css" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css')}}">
@endsection
@section('header_content')
<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0 text-dark">Stock Order List</h1>
         </div>
      </div>
      <!-- /.row -->
   </div>
   <!-- /.container-fluid -->
</div>
@endsection
@section('content')
<div class="row">
   <div class="col-12">
      <div class="card">
         <div class="card-header">
            <h3 class="card-title">Stock Order List</h3>
            <a href="{{ url('franchise/stockproduct/list') }}" class="btn btn-secondary text-white float-right"> <i class="fas fa-arrow-left"></i>&nbsp;&nbsp;Back</a>
         </div>
         <!-- /.card-header -->
         <div class="card-body table-responsive">
            <table id="table" class="table table-bordered table-hover">
               <thead>
                  <tr>
                     <th>Order no</th>
                     <th>Order Reference No</th>
                     <th>Order Date</th>
                     <th>Order Pickup Delivery Date</th>
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
       "url": SITE_URL + "franchise/stockorder/list",
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
        { data: 'id' },
        { data: 'order_reference' },
        { data: 'order_date' },
        { data: 'pickup_date' },

        {
          "mRender": function ( data, type, row ) {
            var color= (row.order_status=="PENDING") ? 'btn-info' : 'btn-success';
            var display=(row.order_status=="PENDING") ? '' : 'disabled';
            var cursor=(row.order_status=="PENDING") ? 'pointer' : '';
            let html = "<div class='btn-group float-left pr-2'><a href='"+SITE_URL+"franchise/stockorder/view/"+row.id+"' class='btn btn-xs btn-primary btn-view text-white'><i class='fas fa-eye'></i></a></div>";
            html += "<button type='button' "+display+"  onclick='changeOrderStatus("+row.id+");' class='btn btn-xs "+color+" btn-view text-white' style='cursor:"+cursor+";'>"+row.order_status+"</button>";
            return html;
          }
        },

      ],
     "columnDefs": [{
       "targets": [4], //first column / numbering column
       "orderable": false, //set not orderable
     }, ],

   }).on('init.dt', function() {
     let html = `
      <select name="order_to" id="order_to" class="col-md-4 form-control form-control-sm float-left ml-2 order_to" style="width:40%">
      <option value="">Select</option>
      <option value="0">247AUTOSTOCK</option>
      <option value="1">247WINEHOUSE</option>
      <option value="2">247WAREHOUSE</option> `;

      html +=`</select>
      <button class="btn btn-danger btn-sm float-right btn-reset ml-2">Reset</button>
     `;
     $('#table_filter').before().append(html);
   });
   $(document).on('change', '#order_to', function() {
    var order_to = $('#order_to').val();
    var new_url = SITE_URL + "franchise/stockorder/list?order_to="+order_to;
    table.ajax.url(new_url).load();
   });
   function changeOrderStatus(oid){
        loader_show();
        $.ajax({
            url: SITE_URL + 'franchise/stockorder/changeorderstatus',
            type: 'POST',
            data: 'id=' + oid+'&_token='+$('meta[name=csrf-token]').attr('content'),
            success: function (obj) {
                if (obj.status == true) {
                loader_hide();
                messageAlert('Success',obj.message,'fa-check','success')
                setTimeout(function () {
                window.location = SITE_URL + obj.page;
                }, 1500)
                } else {
                $.alert('Something went wrong');
                }
            }
      });
   }
</script>
@endsection
