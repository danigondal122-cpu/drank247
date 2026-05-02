@extends('franchise.layout.layout')
@section('pageCSS')
<link rel="stylesheet" type="text/css" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css')}}">
@endsection
@section('header_content')
<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0 text-dark">Stock Order</h1>
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
            <h3 class="card-title">Stock Order</h3>
            <a href="{{ url('franchise/franchisestockorderfrom/list') }}" class="btn btn-secondary  btn-sm text-white float-right"> <i class="fas fa-arrow-left"></i>&nbsp;&nbsp;Back</a>
            {{-- <a href="javascript:;" class="btn btn-primary btn-sm float-right" onclick="placeOrderforStock()" >Order</a>
            <a href="{{ url('franchise/franchisestockorderfrom/orderlist') }}" class="btn btn-primary btn-sm float-right mr-2">OrderList</a>
          --}}
         </div>
         <!-- /.card-header -->
         <div class="card-body table-responsive">
            <table id="table" class="table table-bordered table-hover">
               <thead>
                  <tr>
                     <th>Order No</th>
                     <th>Warehouse</th>
                     <th>No of Product</th>
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
       "url": SITE_URL + "franchise/franchisestockorderfrom/FrStockOrderList",
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
      $('#TotalAmount').html(response.amount);
     },
     columns: [
        { data: 'order_id' },
        { data: 'wh_name' },
        { data: 'total' },
        {
          "mRender": function ( data, type, row ) {
            var color= (row.final_order_status=="PENDING") ? 'btn-info' : 'btn-success';
            var display=(row.final_order_status=="PENDING") ? '' : 'disabled';
            var cursor=(row.final_order_status=="PENDING") ? 'pointer' : '';
            let html = "<div class='btn-group float-left pr-2'><a href='"+SITE_URL+"franchise/franchisestockorderfrom/view/"+row.order_id+"' class='btn btn-xs btn-primary btn-view text-white'><i class='fas fa-eye'></i></a></div>";
            html += "<button type='button' "+display+"  onclick='changeOrderStatus("+row.order_id+");' class='btn btn-xs "+color+" btn-view text-white' style='cursor:"+cursor+";'>"+row.final_order_status+"</button>";
            return html;

          }
        },

      ],
      "columnDefs": [
      { "width": "10%", "targets": 0 ,"orderable": true},
      { "width": "10%", "targets": 1 ,"orderable": true},
      { "width": "10%", "targets": 2 ,"orderable": false },
      { "width": "10%", "targets": 3,"orderable": false },
      ],
   }).on('init.dt', function() {
     let html = `
      <select name="" id="order_from" class="col-md-4 form-control form-control-sm float-left ml-2 order_from" style="width:40%">
      <option value="">Order From</option>`;
      @foreach ($warehouse as $wh)
        html += `<option value="{{ $wh->id }}">{{ $wh->wh_name }}</option>`;
      @endforeach
      html +=`</select>
      <button class="btn btn-danger btn-sm float-right btn-reset ml-2">Reset</button>
     `;
     $('#table_filter').before().append(html);
   });
   $(document).on('click', '.btn-reset', function() {
    $('#order_from').val('');
    table.search('').columns().search('');
    var new_url = SITE_URL + "franchise/franchisestockorderfrom/FrStockOrderList";
    table.ajax.url(new_url).load();
  });
   $(document).on('change', '#order_from', function() {
    var order_from = $('#order_from').val();
    var new_url = SITE_URL + "franchise/franchisestockorderfrom/FrStockOrderList?order_from="+order_from;
    table.ajax.url(new_url).load();
   });
   function changeOrderStatus(oid){
        loader_show();
        $.ajax({
                url: SITE_URL + 'franchise/franchisestockorderfrom/changeorderstatus',
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
