@extends('admin.layout.layout')
@section('pageCSS')
<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
@endsection
@section('header_content')
<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0 text-dark">View Message</h1>
         </div>
      </div>
      <!-- /.row -->
   </div>
   <!-- /.container-fluid -->
</div>
@endsection
@section('content')
<div class="row">
<div class="col-sm-12">
   <div class="card">
      <div class="card-header">
         <h3 class="card-title">Message Detail</h3>
      </div>
      <div class="card-body col-sm-12 col-md-12">
         <div class="card">
            <div class="card-header" style="background-color:#f2f2f2;">
               <h3 class="card-title">Message</h3>
            </div>
            <div class="card-body">
               <div class="row">
                  <div class="col-md-1 card-title">Message:</div>
                  <div class="col-md-11">{{$row->message_text}}</div>
               </div>
               @if($row->message_to =="customer" && $row->image!="")
               <div class="row mt-3">
                  <div class="col-md-1 card-title">Image:</div>
                  <div class="col-md-11">
                     <div class="card elevation-1 mb-3 " style="width:120px;" id="img">  
                        <div class="d-flex align-self-center align-items-center px-2" style="height:120px;">
                          <img src="{{$row->image}}" style="max-height:120px;margin-left: auto;margin-right: auto;" class="card-img-top cart-item-img" alt="">
                        </div>
                        <button type="button" data-toggle="tooltip" data-placement="bottom" title="View full image" style="position: absolute;left: 2%;bottom: 2%;" class="btn btn-primary btn-sm previewImage">
                          <i class="fas fa-search-plus"></i>
                          </button>
                      </div>
                  </div>
               </div>
               @endif
             
               <div class="row mt-3">
                  <div class="col-md-1 card-title"></div>
                  <div class="col-md-12">
                     <div class="table-responsive">
                        <table class="table table-bordered table-hover dataTable no-footer">
                          <thead>
                           <tr style="background-color:#f2f2f2;">
                              <th style="width:20px;">
                                 No.
                              </th>
                              <th>
                                 User
                              </th>
                              <th>
                                 Email
                              </th>
                           </tr>
                          </thead>
                          <tbody>
                           @foreach($userdata as $key=>$value)
                           <tr>
                              <td>{{$key}}</td>
                              <td>{{$value->name}}</td>
                              <td>{{$value->email}}</td>
                           </tr>
                           @endforeach
                          </tbody>
                        </table>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="card-footer">
            <a href="{{ url('admin/message/list') }}" class="btn btn-secondary text-white"> <i class="fas fa-arrow-left"></i>&nbsp;&nbsp;Back</a>
         </div>
      </div>
   </div>
</div>
@endsection
@section('pageJS')
<script src="{{ asset('plugins/select2/js/select2.min.js') }}"></script>
<script src="{{ asset('js/page/image_upload.js') }}"></script>
@endsection