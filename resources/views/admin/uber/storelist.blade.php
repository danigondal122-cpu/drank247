@extends('admin.layout.layout')
@section('pageCSS')
<link rel="stylesheet" type="text/css" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css')}}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
@endsection
@section('header_content')
<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0 text-dark">Uber Store</h1>
            
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
            <h3 class="card-title">Uber Store</h3>
            <a onclick="getUberStorelist();"  href="javascript:;" class="btn btn-primary btn-sm float-right">Sync store from Uber</a>
         </div>
         <!-- /.card-header -->
         <div class="card-body table-responsive">
            <table id="table" class="table table-bordered table-hover">
               <thead>
                  <tr>
                     <th>Store Name </th>
                     <th>Store Id</th>
                     <th>Status</th>
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
<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{asset('plugins/x-editable/bootstrap-editable.min.js')}}" type="text/javascript"></script>
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
       "url": SITE_URL + "admin/uber/storelist",
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
        { data: 'name' },
        { data: 'store_id' },
        { data: 'status' },
        {
          "mRender": function ( data, type, row ) {
           let html = "<div class='btn-group float-left pr-2'><a href='"+SITE_URL+"admin/uber/storeview/"+row.id+"'  class='btn btn-xs btn-primary  btn-view text-white'><i class='fas fa-eye'></i></a></div>";
         //   html  +="<div class='btn-group float-left pr-2'><a href='"+SITE_URL+"admin/uber/storemenu/"+row.id+"'  class='btn btn-xs btn-primary  btn-view text-white'><i class='fas fa-list'></i></a></div>";
         //   html  +="<div class='btn-group float-left pr-2'><a data-id='"+row.store_id+"'  class='getStoreMenu btn btn-xs btn-primary  btn-view text-white'><i class='fas fa-list'></i></a></div>";
         //   html  +="<div class='btn-group float-left pr-2'><a href='"+SITE_URL+"admin/uber/storemenudetail/"+row.id+"'  class='btn btn-xs btn-primary  btn-view text-white'><i class='fas fa-list'></i></a></div>";
           return html;
          }
        },
      ],
     "columnDefs": [{
       "targets": [3], //first column / numbering column
       "orderable": false, //set not orderable
     }, ],
   
   });

   function getUberStorelist(){
   loader_show();
    $.ajax({  
        url: SITE_URL + 'admin/uber/get_uber_store_list',
        type: 'POST',
        data: '_token='+$('meta[name=csrf-token]').attr('content'),
        success: function (obj) {
          if (obj.status == true) {
            loader_hide();
            messageAlert('Success',obj.message,'fa-check','success');
            location.reload();
          } else {
             
            loader_hide();
            $.alert('Something went wrong');
            location.reload();

          }
        }
      });
   }
   $(document).on('click', '.getStoreMenu', function() {
      store_id=$(this).attr('data-id');
      loader_show();
      $.ajax({  
        url: SITE_URL + 'admin/uber/get_uber_stores_menu',
        type: 'POST',
        data: 'store_id='+store_id+'&_token='+$('meta[name=csrf-token]').attr('content'),
        success: function (obj) {
          if (obj.status == true) {
            loader_hide();
            messageAlert('Success',obj.message,'fa-check','success');
            location.reload();
          } else {
            loader_hide();
            $.alert('Something went wrong');
            location.reload();

          }
        }
      });
  });

   
</script>
@endsection