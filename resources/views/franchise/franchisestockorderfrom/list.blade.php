@extends('franchise.layout.layout')
@section('pageCSS')
<link rel="stylesheet" type="text/css" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css')}}">
@endsection
@section('header_content')
<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0 text-dark">Stock Product</h1>
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
            <h3 class="card-title">Stock Product</h3>
            <a href="javascript:;" class="btn btn-primary btn-sm float-right" onclick="placeOrderforStock()" >Order</a>
            <a href="{{ url('franchise/franchisestockorderfrom/FrStockOrderList') }}" class="btn btn-primary btn-sm float-right mr-2">OrderList</a>

         </div>
         <!-- /.card-header -->
         <div class="card-body table-responsive">
            <table id="table" class="table table-bordered table-hover">
               <thead>
                  <tr>
                     <th>Product</th>
                     <th>Article No</th>
                     <th>Price</th>
                     <th>Max Stock Order</th>
                     <th>Current Stock </th>
                     <th>Minimum Stock </th>
                     <th>Total Price </th>
                     {{-- <th>Action</th> --}}
                  </tr>
               </thead>
               <tbody>
               </tbody>
               <tfoot>
                  <tr style="background-color:#f2f2f2;">
                    <th colspan="6">Total Amount</th>
                    <th colspan="1" id="TotalAmount"></th>
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
       "url": SITE_URL + "franchise/franchisestockorderfrom/list",
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
        { data: 'product_name' },
        { data: 'product_article_number' },
        { data: 'price' },
        { data: 'max_stock_order' },
        { data: 'stock_current' },
        { data: 'stock_minimum' },
        { data: 'total' },

      //   {
      //     "mRender": function ( data, type, row ) {
      //       let html = "<div class='btn-group float-left pr-2'><a href='"+SITE_URL+"franchise/stockorder/view/"+row.id+"' class='btn btn-xs btn-primary btn-view text-white'><i class='fas fa-eye'></i></a></div>";
      //       return html;
      //     }
      //   },

      ],
      "columnDefs": [
      { "width": "10%", "targets": 0 ,"orderable": false},
      { "width": "10%", "targets": 1 ,"orderable": false},
      { "width": "10%", "targets": 2 ,"orderable": false },
      { "width": "10%", "targets": 3 ,"orderable": false },
      { "width": "10%", "targets": 4 ,"orderable": false },
      { "width": "10%", "targets": 5 ,"orderable": false },
      { "width": "10%", "targets": 6 ,"orderable": false },
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
    var new_url = SITE_URL + "franchise/franchisestockorderfrom/list";
    table.ajax.url(new_url).load();
  });
   $(document).on('change', '#order_from', function() {
    var order_from = $('#order_from').val();
    var new_url = SITE_URL + "franchise/franchisestockorderfrom/list?order_from="+order_from;
    table.ajax.url(new_url).load();
   });
   function placeOrderforStock(){
    loader_show();
    var order_from=$('#order_from').val();
    if(order_from==""){
       $.alert('Please select Ware House');
       loader_hide();
    }else{
      $.confirm({
        title: '',
        content: 'Are you sure you want to Order All Product?',
        closeIcon: true,
        buttons: {
          confirm: {
            text: 'Yes',
            btnClass: 'btn-primary',
            action: function () {
               $.ajax({
                    url: SITE_URL + 'franchise/franchisestockorderfrom/placeOrderforStock',
                    type: 'POST',
                    data: 'order_from=' + order_from+'&_token='+$('meta[name=csrf-token]').attr('content'),
                    success: function (obj) {
                        if (obj.status == true) {
                            loader_hide();
                            messageAlert('Success',obj.message,'fa-check','success')
                            setTimeout(function () {
                                window.location = SITE_URL + obj.page;
                            }, 1500)
                        } else {
                            $.alert(obj.message);
                        }
                        loader_hide();
                    }
               });
            }
          },
          Reject: {
            text: 'Cancel',
            btnClass: 'btn-default',
            action: function () {
                loader_hide();
            }
          },
        }
      });
    }

   }
</script>
@endsection
