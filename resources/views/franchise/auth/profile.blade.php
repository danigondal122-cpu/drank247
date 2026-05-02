@extends('franchise.layout.layout')
@section('pageCSS')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
@endsection
@section('header_content')
  <div class="content-header">
  </div>
@endsection
@section('content')
  <div class="row">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Profile</h3>
             </div>
             <form method="post" id="updateProfile" enctype="multipart/form-data">
             @csrf
             <input type="hidden" name="frs_id" id="frs_id" value="{{$row->cs_id}}">
             <div class="card-body col-sm-12 col-md-6">
              <div class="row">
                 {{-- <div class="col-sm-12 mt-3">
                <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                  <input type="checkbox" class="custom-control-input scheduleonoff" id="customSwitch3" name="customSwitch3" {{$row->fs_on_off=="online" ? 'checked': ''}}>
                    <label class="custom-control-label" for="customSwitch3"> Online/Offline</label>
                  </div>
                </div> --}}
                <div class="col-sm-12 mt-3">
                 <div class="form-group">
                   <label for="exampleInputEmail1">*Profile Image</label>
                   <input type="file" name="image_file" id="image_file" class="d-none" accept="image/*">
                   <input type="hidden" name="old_cat_pic" id="old_cat_pic" value="{{$row->image}}">

                   <div id="postedImages">
                  @if($row->image!="")
                     <div class="card elevation-2 col-sm-6 p-1" id="img">
                      <img src="{{$row->image}}" />
                       <button type="button" data-toggle="tooltip" data-placement="bottom" title="View full image" style="position: absolute;left: 2%;bottom: 2%;" class="btn btn-primary btn-sm previewImage">
                       <i class="fas fa-search-plus"></i>
                       </button>
                       <button type="button" data-toggle="tooltip" data-placement="bottom" title="Remove" style="position: absolute;right: 2%;bottom: 2%;" class="btn btn-danger btn-sm deleteImage" data-id="">
                         <i class="fas fa-trash-alt"></i>
                       </button>
                     </div>
                 @else
                   <input type="file" name="image_file" id="image_file" class="d-none" accept="image/*">
                   <div class="dropHere float-left">
                     <button class="btn btn-outline-primary" type="button" onclick="$('#image_file').click()" title="click here to add images">
                       <i class="fas fa-plus fa-5x"></i>
                     </button>
                   </div>
                  @endif
                   </div>
                 </div>
               </div>
             </div>
              <div class="clearfix"></div>
               <div class="row">
                 <div class="col-sm-12">
                   <div class="form-group">
                     <label for="first_name">*Name</label>
                     <input type="text" class="form-control" id="name" name="name" value="{{$row->franchises_name}}">
                   </div>
                 </div>
               </div>
              <div class="clearfix"></div>
              <div class="row">
                <div class="col-sm-12">
                  <div class="form-group">
                    <label for="first_name">*Email</label>
                    <input type="text" class="form-control" id="email" name="email" value="{{$row->franchises_email}}">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-12">
                  <div class="form-group">
                    <label for="first_name">*Contact No</label>
                    <input type="text" class="form-control" id="contact_no" name="contact_no" value="{{$row->mobile_no}}">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-12">
                  <div class="form-group">
                    <label for="franchise_number">Franchise Number</label>
                    <input type="text" class="form-control" id="franchise_number" name="franchise_number" value="{{$row->franchise_number}}">
                  </div>
                </div>
              </div>
              <div class="clearfix"></div>

              <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                      <label for="first_name">Bank Account No.</label>
                      <div class="d-flex">
                      <div class="custom-file">
                        <input type="file" class="custom-file-input" id="bank_pass_no" name="bank_pass_no">
                        <label class="custom-file-label" for="customFile">{{$row->bank_pass_no}}</label>
                      </div>
                      <div> <a data-fancybox="gallery" target="_blank" href="{{asset('uploads/franchiseDocument/'.$row->franchise_id.'/'.$row->bank_pass_no)}}"><img src="{{ asset('images/icon/download.png') }}"  class="nav-icon nav-svg" style="width: 1.9rem;margin-left: .5rem;" alt="Chat"></a></div>
                    </div>
                    </div>
                </div>
              </div>
              <div class="clearfix"></div>
              <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                      <label for="first_name">Bank Account Passbook front</label>
                      <div class="d-flex" >
                      <div class="custom-file">
                        <input type="file" class="custom-file-input" id="bank_pass_front" name="bank_pass_front">
                        <label class="custom-file-label" for="customFile">{{$row->bank_pass_front}}</label>
                      </div>
                      <div> <a data-fancybox="gallery" target="_blank" href="{{asset('uploads/franchiseDocument/'.$row->franchise_id.'/'.$row->bank_pass_front)}}"><img src="{{ asset('images/icon/download.png') }}"  class="nav-icon nav-svg" style="width: 1.9rem;margin-left: .5rem;" alt="Chat"></a></div>
                    </div>
                  </div>
                    </div>
                </div>

              <div class="clearfix"></div>
              <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                      <label for="first_name">Bank Account Passbook back</label>
                      <div class="d-flex" >
                      <div class="custom-file">
                        <input type="file" class="custom-file-input" id="bank_pass_back" name="bank_pass_back">
                        <label class="custom-file-label" for="customFile">{{$row->bank_pass_back}}</label>
                      </div>
                      <div> <a data-fancybox="gallery" target="_blank" href="{{asset('uploads/franchiseDocument/'.$row->franchise_id.'/'.$row->bank_pass_back)}}"><img src="{{ asset('images/icon/download.png') }}"  class="nav-icon nav-svg" style="width: 1.9rem;margin-left: .5rem;" alt="Chat"></a></div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="clearfix"></div>
              <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                      <label for="first_name">Statement of Conduct</label>
                      <div class="d-flex" >
                      <div class="custom-file">
                        <input type="file" class="custom-file-input" id="statement_conduct" name="statement_conduct">
                        <label class="custom-file-label" for="customFile">{{$row->statement_conduct}}</label>
                      </div>
                      <div> <a data-fancybox="gallery" target="_blank" href="{{asset('uploads/franchiseDocument/'.$row->franchise_id.'/'.$row->statement_conduct)}}"><img src="{{ asset('images/icon/download.png') }}"  class="nav-icon nav-svg" style="width: 1.9rem;margin-left: .5rem;" alt="Chat"></a></div>
                    </div>
                </div>
              </div>
            </div>
              <div class="clearfix"></div>
              <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                      <label for="first_name">Driving licence front</label>
                      <div class="d-flex" >
                      <div class="custom-file">
                        <input type="file" class="custom-file-input" id="licence_front" name="licence_front">
                        <label class="custom-file-label" for="customFile">{{$row->licence_front}}</label>
                      </div>
                      <div> <a data-fancybox="gallery" target="_blank" href="{{asset('uploads/franchiseDocument/'.$row->franchise_id.'/'.$row->licence_front)}}"><img src="{{ asset('images/icon/download.png') }}"  class="nav-icon nav-svg" style="width: 1.9rem;margin-left: .5rem;" alt="Chat"></a></div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="clearfix"></div>
              <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                      <label for="first_name">Driving licence back</label>
                      <div class="d-flex" >
                      <div class="custom-file">
                        <input type="file" class="custom-file-input" id="licence_back" name="licence_back">
                        <label class="custom-file-label" for="customFile">{{$row->licence_back}}</label>
                      </div>
                      <div> <a data-fancybox="gallery" target="_blank" href="{{asset('uploads/franchiseDocument/'.$row->franchise_id.'/'.$row->licence_back)}}"><img src="{{ asset('images/icon/download.png') }}"  class="nav-icon nav-svg" style="width: 1.9rem;margin-left: .5rem;" alt="Chat"></a></div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="clearfix"></div>
              <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                      <label for="first_name">Franchise Contract</label>
                      <div class="d-flex" >
                      <div class="custom-file">
                        <input type="file" class="custom-file-input" id="franchise_contract" name="franchise_contract">
                        <label class="custom-file-label" for="customFile">{{$row->franchise_contract}}</label>
                      </div>
                      <div> <a data-fancybox="gallery" target="_blank" href="{{asset('uploads/franchiseDocument/'.$row->franchise_id.'/'.$row->franchise_contract)}}"><img src="{{ asset('images/icon/download.png') }}"  class="nav-icon nav-svg" style="width: 1.9rem;margin-left: .5rem;" alt="Chat"></a></div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="clearfix"></div>
              <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                      <label for="first_name">Payroll Contract</label>
                      <div class="d-flex" >
                      <div class="custom-file">
                        <input type="file" class="custom-file-input" id="payroll_contract" name="payroll_contract">
                        <label class="custom-file-label" for="customFile">{{$row->payroll_contract}}</label>
                      </div>
                      <div> <a data-fancybox="gallery" target="_blank" href="{{asset('uploads/franchiseDocument/'.$row->franchise_id.'/'.$row->payroll_contract)}}"><img src="{{ asset('images/icon/download.png') }}"  class="nav-icon nav-svg" style="width: 1.9rem;margin-left: .5rem;" alt="Chat"></a></div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="clearfix"></div>
              <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                      <label for="first_name">Extra Option</label>
                      <div class="d-flex" >
                      <div class="custom-file">
                        <input type="file" class="custom-file-input" id="extra_option" name="extra_option">
                        <label class="custom-file-label" for="customFile">{{$row->extra_option}}</label>
                      </div>
                      <div> <a data-fancybox="gallery" target="_blank" href="{{asset('uploads/franchiseDocument/'.$row->franchise_id.'/'.$row->extra_option)}}"><img src="{{ asset('images/icon/download.png') }}"  class="nav-icon nav-svg" style="width: 1.9rem;margin-left: .5rem;" alt="Chat"></a></div>
                    </div>
                  </div>
                </div>
             </div>
             <!-- /.card-body -->

             <div class="card-footer">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
                <a href="{{ url('franchise/stock/list') }}" class="btn btn-secondary text-white"> <i class="fas fa-arrow-left"></i>&nbsp;&nbsp;Back</a>
             </div>
             </form>
           </div>
         </div>
       </div>
@endsection
@section('pageJS')
<script>
  $(function () {
    bsCustomFileInput.init();
  });
  </script>
<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
<script src="{{ asset('js/page/image_upload.js') }}"></script>
<script>
     $('#updateProfile').on('submit',function (e){

        e.preventDefault();
        loader_show();
        let formData = new FormData(this)

        if(images.length >0){

        for(key in images){
            formData.append('image_file',images[key]);
        }
        }
        $('#updateProfile .is-invalid').removeClass('is-invalid');
        $('#updateProfile .text-danger').remove();
        $.ajax({
        url: SITE_URL + 'franchise/profileupdate',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
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
            messageAlert('Success',obj.msg,'fa-check','success')
            $('#updateProfile')[0].reset();
            setTimeout(function () {
                window.location = SITE_URL + obj.page;
            }, 1500)
            }
        },

        })
    });
</script>
@endsection
