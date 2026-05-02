@extends('franchise.layout.layout')
@section('pageCSS')
<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
@endsection
@section('header_content')
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">History</h1>
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
          <h3 class="card-title">Delivey Person:{{$dp['dp_name']}}</h3><br/>
          <h3 class="card-title">Date: {{$date['Date']}}</h3>
             </div>
             <div class="card-body col-sm-12 col-md-12">
              @if(count($start)>0)
              <div class="card">
                <div class="card-header" style="background-color:#f2f2f2;">
                  <h3 class="card-title">Start Time</h3>
                     </div>
                     <div class="card-body">
                        <div class="row">
                          @foreach($start as $value)
                          <div class="col-12 col-sm-12  col-lg-3 col-md-4 col-xl-2">
                            <div class="card elevation-1 mb-3">
                              <!-- <span class="add-to-fav-btn">
                                <i class="far fa-heart"></i>
                              </span>-->
                              <div class="d-flex align-self-center align-items-center p-2" style="height: 200px;">
                                <img  class="ImageHistory" src="{{asset('uploads/deliveryhistory/start/'.$value->dp_im_name)}}"   style="max-height:190px;width:150px;"  class="card-img-top cart-item-img" alt="...">
                               </div>
                            </div>
                          </div>

                          @endforeach
                        </div>

                  </div>
                </div>
                @endif
              @if(count($end)>0)
              <div class="card">
                <div class="card-header" style="background-color:#f2f2f2;">
                  <h3 class="card-title">End Time</h3>
                     </div>
                     <div class="card-body">
                       @foreach($end as $value)
                        <div class="row">
                          <div class="col-12 col-sm-12  col-lg-3 col-md-4 col-xl-2">
                            <div class="card elevation-1 mb-3">
                              <!-- <span class="add-to-fav-btn">
                                <i class="far fa-heart"></i>
                              </span>-->
                              <div class="d-flex align-self-center align-items-center p-2" style="height: 200px;">
                                <img  class="ImageHistory" src="{{asset('uploads/deliveryhistory/end/'.$value->dp_im_name)}}" style="max-height:190px;width:150px;"  class="card-img-top cart-item-img" alt="...">
                               </div>
                            </div>
                          </div>
                          @endforeach
                      </div>
                  </div>
                </div>
                @endif


             <!-- /.card-body -->

             <div class="card-footer">
                <a href="{{ url('franchise/deliveryperson/view/'.$dp['id']) }}" class="btn btn-secondary text-white"> <i class="fas fa-arrow-left"></i>&nbsp;&nbsp;Back</a>
             </div>

           </div>
         </div>
       </div>
<div id="ImageHistory" class="modal fade show" role="dialog"  aria-modal="true">
  <div class="modal-dialog" style="max-height:500px;">
     <div class="modal-content" id="commonModalHtml">
      <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title"><b>Image</b></h4>
            <button type="button" class="close" data-dismiss="modal">×</button>
        </div>
        <div class="modal-body">
          <img class="modal-content" >
        </div>
     </div>
    </div>
 </div>
</div>
@endsection
@section('pageJS')
<script>
$(".ImageHistory").click(function(){
 var attr=$(this).attr('src');
 $('#ImageHistory').modal('show');
 $('.modal-content').attr('src',attr);

});

</script>
<script src="{{ asset('plugins/select2/js/select2.min.js') }}"></script>

@endsection
