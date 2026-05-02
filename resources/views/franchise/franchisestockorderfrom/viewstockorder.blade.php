@extends('franchise.layout.layout')
@section('pageCSS')
<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
@endsection
@section('header_content')
{{-- <div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0 text-dark">View Stock Order</h1>
         </div>
      </div>
      <!-- /.row -->
   </div>
   <!-- /.container-fluid -->
</div> --}}
@endsection
@section('content')
<div class="row">
   <div class="col-sm-12">
      <div class="card">
         {{--
         <div class="card-header">
            <h3 class="card-title">Order Detail</h3>
         </div>
         --}}
         <div class="card-body col-sm-12 col-md-12">
            <div class="card">
               <div class="card-body">

                     <div class="card">
                        <div class="card-header" style="background-color:#f2f2f2;">
                           <h3 class="card-title">Stock Order Detail</h3>
                        </div>
                        <div class="card-body col-sm-12 col-md-12">
                           <div class="row">
                            Order No : {{$order_no}}
                           </div>
                           <div class="row">
                              Ware House : {{$warehouse['wh_name']}}
                           </div>

                        </div>
                  </div>

                  <div class="row">
                     <table class="table m-0">
                        <thead>
                           <tr style="background-color:#f2f2f2;">
                              <th>Product Name</th>
                              <th>Article Number</th>
                              <th>Quantity</th>
                              <th>Price</th>
                              <th>Order Status</th>
                           </tr>
                        </thead>
                        <tbody>
                           @foreach($list as $value)
                           <tr>
                              <td>{{$value->product_name}}</td>
                              <td>{{$value->product_article_number}}</td>
                              <td>{{$value->fs_qty}}</td>
                              <td>{{$value->price}}</td>
                              <td><button type="button" {{$value->order_status=='PENDING' ? '' : 'disabled'}} onclick="changeProductStatus({{$value->id}})"  class="btn btn-xs {{$value->order_status=='PENDING' ? 'btn-info' : 'btn-success'}} text-white" style="cursor:{{$value->order_status=='PENDING' ? 'pointer' : ''}}">{{$value->order_status}}</button>
                              </td>
                           </tr>
                           @endforeach
                           <tr>
                              <td colspan="3">Total</td>
                              <td>{{$amount}}</td>
                           </tr>
                     </table>
                  </div>
               </div>
               <div class="card-footer">
                  <a href="{{ url('franchise/franchisestockorderfrom/FrStockOrderList') }}" class="btn btn-secondary text-white"> <i class="fas fa-arrow-left"></i>&nbsp;&nbsp;Back</a>
               </div>
            </div>
            <!-- /.card-body -->
         </div>
      </div>
   </div>
</div>
@endsection
@section('pageJS')
<script src="{{ asset('plugins/select2/js/select2.min.js') }}"></script>
<script>
function changeProductStatus(id){
   loader_show();
    $.ajax({
                url: SITE_URL + 'franchise/franchisestockorderfrom/changeproductstatus',
                type: 'POST',
                data: 'id=' + id+'&_token='+$('meta[name=csrf-token]').attr('content'),
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
