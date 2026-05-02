@extends('franchise.layout.layout')
@section('pageCSS')
<link rel="stylesheet" href="{{ asset('plugins/jquery-ui/jquery-ui.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('plugins/x-editable/bootstrap-editable.css')}}">
<link rel="stylesheet" href="{{ asset('plugins/bootstrap-datepicker/css/bootstrap-datepicker.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css') }}">
@endsection
@section('header_content')
<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0 text-dark">Product List</h1>
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
            <h3 class="card-title">Product List</h3>
            <a href="{{url('/franchise/stockorder/list')}}" class="btn btn-primary btn-sm float-right text-white" style="margin-right:5px;">Order List</a>
            <a onclick="selectStock();" class="btn btn-primary btn-sm float-right text-white " style="margin-right:5px;">Add Stock Order</a>
            <a onclick="updateStock();" class="btn btn-primary btn-sm float-right btn-white text-white" style="margin-right:5px;">Update Available Stock</a>
         </div>
         <!-- /.card-header -->
         <div class="card-body table-responsive">
            <table id="table" class="table table-bordered table-hover" style="width:100%">
               <thead>
                  <tr>
                     <th class="text-center">#</th>
                     <th>Product</th>
                     <th>Article Number</th>
                     <th>Available Stock</th>
                     <th>Quantity</th>
                  </tr>
               </thead>
               <tbody id="tablecontents">
               </tbody>
            </table>
         </div>
      </div>
      <!-- /.card-body -->
   </div>
</div>
<!-- /.col -->
<!-- /.row -->
<div id="commonModal" class="modal fade" role="dialog">
   <div class="modal-dialog" style="width:430px;">
      <div class="modal-content" id="commonModalHtml">
         <div class="modal-header">
            <h5 class="modal-title">Detail</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <div class="row">
               <div class="col-sm-12">
                  <div class="form-group">
                     <label for="first_name">*Order type</label><br>
                     <input type="radio" id="pickup" name="order_type" value="P">
                     <label class="mr-2">Pickup</label>
                     <input type="radio" id="delivery" name="order_type" value="D">
                     <label class="mr-2">Delivery</label>
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-sm-12">
                  <div class="form-group">
                     <label for="first_name">*Select Date</label><br>
                     <div class="form-group pmd-textfield pmd-textfield-floating-label">
                        {{-- <label class="control-label" for="datepicker"></label> --}}
                        <input type="text" class="form-control" id="datepicker">
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="modal-footer">
            <input type="hidden" name="order_id" id="order_id" value="" >
            <button type="button" onclick="sendStockOrder();" class="btn btn-primary">Order</button>
            <button type="button" onclick="removeStockOrder();" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
         </div>
      </div>
   </div>
</div>
@endsection
@section('pageJS')
<script src="{{ asset('plugins/jquery-ui/jquery-ui.min.js') }}"></script>
<script src="{{asset('plugins/x-editable/bootstrap-editable.min.js')}}" type="text/javascript"></script>
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/daterangepicker/moment.min.js') }}"></script>
<script src="{{ asset('plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js') }}"></script>
<script>
   var table;
   table = $('#table').DataTable({
     "pageLength": 10,
     "processing": true, //Feature control the processing indicator.
     "serverSide": true, //Feature control DataTables' server-side processing mode.
     "order": [], //Initial no order.
      "paging":   true,
     "autoWidth": true,

     // Load data for the table's content from an Ajax source
     "ajax": {
       "url": SITE_URL + "franchise/stockproduct/list",
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
            let html = "<input type='checkbox' class='simple checksingle' id="+row.id+" name='checksingle[]' value="+row.id+">";
            return html;
          }
        },
        { data: 'product_name' },
        { data: 'product_article_number' },
        {
          "mRender": function ( data, type, row ) {
            let html = "<div name=stock"+row.id+" id=stock"+row.id+" value="+row.api_available_stock+">"+row.api_available_stock+"</div>";
            return html;
          }
        },
        {
          "mRender": function ( data, type, row ) {
            let html = "<input type='number' name=qty"+row.id+" id=qty"+row.id+" value='1' min='1'>";
            return html;
          }
        },
      ],
     "columnDefs": [
      { "width": "15%", "targets": 0 ,"orderable": false, "className": "text-center",},
      { "width": "40%", "targets": 1 ,"orderable": false},
      { "width": "30%", "targets": 2 ,"orderable": false,"className": "text-center",},
      { "width": "20%", "targets": 3 ,"orderable": false,"className": "text-center",},
      { "width": "10%", "targets": 4 ,"orderable": false,"className": "text-center",},
     ],
     "initComplete": function( settings, json ) {

     }
   }).on('init.dt', function() {
     let html = `
      <select name="order_to" id="order_to" class="col-md-4 form-control form-control-sm float-left ml-2 order_to" style="width:40%">
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
    var new_url = SITE_URL + "franchise/stockproduct/list?order_to="+order_to;
    table.ajax.url(new_url).load();
   })
   $(document).on('click', '.checksingle', function() {


   })
   function updateStock(id) {
        loader_show();
        $.ajax({
            url: SITE_URL + 'franchise/stockorder/updateStock',
            type: 'POST',
            data: 'id=' + id + '&_token=' + $('meta[name=csrf-token]').attr('content'),
            success: function(obj) {
                if (obj.status == true) {
                    loader_hide();
                    messageAlert('Success', obj.message, 'fa-check', 'success');
                    location.reload();
                } else {
                    $.alert('Something went wrong');
                }
            }
        });

    }

   function selectStock() {
     var order_to=$('#order_to').val();
    var total=$('input[name="checksingle[]"]:checked').length;
      if(total=='0'){
      $.alert('Please Select Product');
      }else{
      $.confirm({
        title: '',
        content: 'Are you sure you want to Order all selected Product?',
        closeIcon: true,
        buttons: {
          confirm: {
            text: 'Yes',
            btnClass: 'btn-primary',
            action: function () {
              var product_stock= [];
              $('.checksingle:checked').each(function(i){
                    $count=$(this).val();
                    product_stock.push({product_id: $(this).val(), stock: $('#stock'+$count).html(), qty: $('#qty'+$count).val()});
              });
              $('.is-invalid').removeClass('is-invalid');
              $('.text-danger').remove();
                 loader_show();
              $.ajax({
                url: SITE_URL + 'franchise/stockorder/selectStock',
                type: 'POST',
                data:{product_stock:product_stock,order_to:order_to},
                success: function (obj) {

                if (!obj.status && obj.type == 'validation') {
                  loader_hide();
                for (key in obj.errors) {
                  $('#' + key).addClass('is-invalid');
                  $('#' + key).after('<p class="text-danger">' + obj.errors[key] + '</p>');
                }
                }
                if (obj.status) {
                    loader_hide();
                    if(order_to==0){
                        var order_id=obj.order_id;
                        $('#commonModal').modal('show');
                        $('#order_id').val(order_id);
                    } else {
                        messageAlert('Success',obj.message,'fa-check','success')
                        setTimeout(function () {
                        window.location = SITE_URL + obj.page;
                        }, 1500)
                    }
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
   }
   function sendStockOrder(){
        loader_show();
        var order_id=$('#order_id').val();
        var order_type=$('input[name="order_type"]:checked').val();
        var date=$('#datepicker').val();
        $.ajax({
                        url: SITE_URL + 'franchise/stockorder/sendStockOrder',
                    type: 'POST',
                    data: 'id=' + order_id+'&order_type=' + order_type+'&date=' + date+'&_token='+$('meta[name=csrf-token]').attr('content'),
                    success: function (obj) {
                    if (obj.status == true) {
                        loader_hide();
                        messageAlert('Success',obj.message,'fa-check','success');
                        setTimeout(function () {
                        window.location = SITE_URL + obj.page;
                        }, 1500)

                    } else {
                        $.alert('Something went wrong');
                    }
                    }
                });

    }
   function removeStockOrder(){
    var order_id=$('#order_id').val();
    $.ajax({
                url: SITE_URL + 'franchise/stockorder/removeStockOrder',
                type: 'POST',
                data: 'id='+order_id+'&_token='+$('meta[name=csrf-token]').attr('content'),
                success: function (obj) {
                  if (obj.status == true) {
                    setTimeout(function () {
                    window.location = SITE_URL + obj.page;
                     }, 1500)
                  } else {
                    $.alert('Something went wrong');
                  }
                }
              });
   }
   $('#datepicker').datetimepicker({
    format: 'yyyy-mm-ddTh:i:s',
    language:'en',
    use24hours: true,

   });
   $(document).on('click', '.btn-reset', function() {
    $('#order_to').val('0');
    table
      .search('')
      .columns().search('');
    var new_url = SITE_URL + "franchise/stockproduct/list";
    table.ajax.url(new_url).load();
  });
</script>
@endsection
