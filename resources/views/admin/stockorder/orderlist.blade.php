@extends('admin.layout.layout')
@section('pageCSS')
<link rel="stylesheet" type="text/css" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css')}}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
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
            {{-- <a  href="javascript:;" onclick="syncOrder();" class="btn btn-primary btn-sm float-right" style="margin-right:5px;">Sync Order</a> --}}
         </div>
         <!-- /.card-header -->
         <div class="card-body table-responsive">
            <table id="table" class="table table-bordered table-hover">
               <thead>
                  <tr>
                     <th>Order no</th>
                     <th>Franchise</th>
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
<script src="{{asset('plugins/x-editable/bootstrap-editable.min.js')}}" type="text/javascript"></script>
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script>
   var table;
   table = $('#table').DataTable({
     "pageLength": 10,
     "processing": true, //Feature control the processing indicator.
     "serverSide": true, //Feature control DataTables' server-side processing mode.
     "order": [], //Initial no order.
     "dom": "<'row'<'col-sm-6 col-md-2'l>>" +
        "<'row'<'col-sm-12 col-md-12'f>>" +
       "<'row'<'col-sm-12'tr>>" +
       "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>", //Initial no order.
     // Load data for the table's content from an Ajax source
     "ajax": {
       "url": SITE_URL + "admin/stockorder/list",
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
        { data: 'franchises_name' },
        {
          "mRender": function ( data, type, row ) {
             let html='';
             if(row.order_to==0){
                html= '<div>'+row.order_reference+'</div>';
             }else{
               html= '<div>-</div>';
             }

            return html;
          }
        },

        { data: 'order_date' },

        {
          "mRender": function ( data, type, row ) {
             let html='';
             if(row.order_to==0){
                html= '<div>'+row.pickup_date+'</div>';
             }else{
               html= '<div>-</div>';
             }

            return html;
          }
        },
        {
          "mRender": function ( data, type, row ) {

            let html = "<div class='btn-group float-left pr-2'><a href='"+SITE_URL+"admin/stockorder/view/"+row.id+"' class='btn btn-xs btn-primary btn-view text-white'><i class='fas fa-eye'></i></a></div>";

            return html;
          }
        },
      ],
     "columnDefs": [{
       "targets": [3,4,5], //first column / numbering column
       "orderable": false, //set not orderable
     }, ],
     "initComplete": function( settings, json ) {

      }
   }).on('init.dt', function() {
     let html = `
      <select name="" id="frs_id" class="col-md-4 form-control form-control-sm float-left fran_search" style="width:40%">
      <option value="">Select Franchise </option>`;
      @foreach ($Franchise as $fra)
        html += `<option value="{{ $fra->id }}">{{ $fra->franchises_name }}</option>`;
      @endforeach
      html +=`</select>
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
   $(document).on('change', '#frs_id ,#order_to', function() {
    var frs_id = $('#frs_id').val();
    var order_to = $('#order_to').val();
    var new_url = SITE_URL + "admin/stockorder/list?frs_id="+frs_id+'&order_to='+order_to;
    table.ajax.url(new_url).load();
   })
   $(document).on('click', '.btn-reset', function() {
    $('#frs_id').val('');
    table
      .search('')
      .columns().search('');
    var new_url = SITE_URL + "admin/stockorder/list";
    table.ajax.url(new_url).load();
   });

</script>
@endsection
