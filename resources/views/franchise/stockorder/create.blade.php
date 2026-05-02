@extends('franchise.layout.layout')
@section('pageCSS')
<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/jquery-ui/jquery-ui.css')}}">
@endsection
@section('header_content')
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">{{ (empty($row)==false)?'Stock Order':'Stock Order'}}</h1>l
        </div>
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
@endsection
@section('content')
  <div class="row">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">{{ (empty($row)==false)?'Stock Order':'Stock Order'}}</h3>
             </div>
             <form method="post" id="addstockorder">
             @csrf
            
             <div class="card-body col-sm-12 col-md-6">
               <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="first_name">*Product</label>
                  <select name="Product" id="Product"  class="getStockList form-control" autocomplete="off">
                    <option value="">Select Product</option>
                    @foreach ($products as $Product)
                    <option value="{{$Product->product_id}}">{{$Product->product_name}}</option>   
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="first_name">get available stocks</label>
                  <select name="Product" id="Product"  class="getStockList form-control" autocomplete="off">
                    <option value="">Select Product</option>
                    @foreach ($products as $Product)
                    <option value="{{$Product->product_id}}">{{$Product->product_name}}</option>   
                    @endforeach
                  </select>
                </div>
              </div>
            </div>

             </div>
                          
             <!-- /.card-body -->

             <div class="card-footer">
                <button type="button" id="submit" onclick="submitForm();"  class="btn btn-primary"><i class="fas fa-save"></i>&nbsp;&nbsp;{{ (empty($row)==false)?'Update':'Save'}}</button>
                <a href="{{ url('franchise/stockorder/list') }}" class="btn btn-secondary text-white"> <i class="fas fa-arrow-left"></i>&nbsp;&nbsp;Back</a>
             </div>
             </form>
           </div>
         </div>
       </div>
@endsection
@section('pageJS')
<script>


$(document).on('change','.getStockList',function(){
  var product_id=$(this).val();

        $.ajax({
        data: 'product_id='+product_id+'&_token='+$('meta[name=csrf-token]').attr('content'),
        type: 'POST',
        url: SITE_URL + "franchise/stockorder/getProductStock",
        success: function(response) {
          loader_hide();
        }
      });
})
</script>
<script src="{{ asset('plugins/select2/js/select2.min.js') }}"></script>
<script src="{{ asset('plugins/jquery-ui/jquery-ui.min.js') }}"></script>
<script src="{{ asset('js/page/image_upload.js') }}"></script>
<script src="{{ asset('js/page/franchise/deliveryperson.js') }}"></script>

@endsection
