@extends('customerservice.layout.layout')
@section('pageCSS')
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
@endsection
@section('header_content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">{{ empty($row) == false ? 'Update Delivery Person' : 'Add New Delivery Person' }}
                    </h1>
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
                    <h3 class="card-title">{{ empty($row) == false ? 'Update Delivery Person' : 'Add New Delivery Person' }}
                    </h3>
                </div>
                <form method="post" id="addDelivery">
                    @csrf
                    <input type="hidden" name="dp_id" id="dp_id" value="{{ empty($row) == false ? $row->id : '' }}">
                    <div class="card-body col-sm-12 col-md-6">
                        {{-- <div class="row">
                <div class="col-sm-12">
                  <div class="form-group">
                     <label for="first_name">*franchise</label>
                     <select class="form-control" id="franchise" name="franchise">
                       <option>Select franchise</option>
                     @foreach ($franchise as $value)
                     <option value="{{$value->franchise_id}}"  {{ (empty($row)==false && ($value->franchise_id==$row->dp_franchisesid))?'selected':''}}>{{$value->franchises_name}}</option>
                     @endforeach
                     </select>
                   </div>
                   <span id="franchise_error"></span>
                 </div>
              </div> --}}

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">*Name</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ empty($row) == false ? $row->dp_name : '' }}">
                                </div>
                                <span id="name_error"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">*Email</label>
                                    <input type="text" class="form-control" id="email" name="email"
                                        value="{{ empty($row) == false ? $row->dp_email : '' }}">
                                </div>
                                <span id="email_error"></span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">*Contact No</label>
                                    <input type="text" class="form-control" id="contact_no" name="contact_no"
                                        value="{{ empty($row) == false ? $row->dp_contact_no : '' }}">
                                </div>
                                <span id="contact_no_error"></span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">*Street</label>
                                    <input type="text" class="form-control" id="street" name="street"
                                        value="{{ empty($row) == false ? $row->dp_street : '' }}">
                                </div>
                                <span id="street_error"></span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">*City</label>
                                    <input type="text" class="form-control" id="city" name="city"
                                        value="{{ empty($row) == false ? $row->dp_city : '' }}">
                                </div>
                                <span id="city_error"></span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">*State</label>
                                    <input type="text" class="form-control" id="state" name="state"
                                        value="{{ empty($row) == false ? $row->dp_state : '' }}">
                                </div>
                                <span id="state_error"></span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">*Post Code</label>
                                    <input type="text" class="form-control" id="postcode" name="postcode"
                                        value="{{ empty($row) == false ? $row->dp_postcode : '' }}">
                                </div>
                                <span id="postcode_error"></span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row">
                            {{--
                  <div class="col-sm-12">
                    <div class="form-group">
                       <label for="first_name">*Pool</label>
                       <select class="form-control select2" id="pool" name="pool[]" multiple="multiple">
                         <option value="" id="selectvalue">Select Pool</option>
                       @foreach ($pool as $value)
                       <option value="{{$value->pool_id}}"  {{ (empty($row)==false && (in_array($value->pool_id,$poolarray)))?'selected':''}}>{{$value->area}}</option>
                       @endforeach
                       </select>
                     </div>
                     <span id="pool_error"></span>
                   </div> --}}
                        </div>
                        <div class="clearfix"></div>
                        @if (empty($row) == false)
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Profile</label>
                                        <input type="file" name="image_file" id="image_file" class="d-none"
                                            accept="image/*">
                                        <input type="hidden" name="old_cat_pic" id="old_cat_pic"
                                            value="{{ $row->dp_image }}">
                                        <div id="postedImages">
                                            @if ($row->dp_image != '')
                                                <div class="card elevation-1 mb-3 " style="width:120px;" id="img">
                                                    <div class="d-flex align-self-center align-items-center px-2"
                                                        style="height:120px;">
                                                        <img src="{{ $row->dp_image }}"
                                                            style="max-height:120px;margin-left: auto;margin-right: auto;"
                                                            class="card-img-top cart-item-img" alt="">
                                                    </div>
                                                    <button type="button" data-toggle="tooltip" data-placement="bottom"
                                                        title="View full image"
                                                        style="position: absolute;left: 2%;bottom: 2%;"
                                                        class="btn btn-primary btn-sm previewImage">
                                                        <i class="fas fa-search-plus"></i>
                                                    </button>
                                                    <button type="button" data-toggle="tooltip" data-placement="bottom"
                                                        title="Remove" style="position: absolute;right: 2%;bottom: 2%;"
                                                        class="btn btn-danger btn-sm deleteImage" data-id="">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                @else
                                                    <input type="file" name="image_file" id="image_file"
                                                        class="d-none" accept="image/*">
                                                    <div class="dropHere float-left">
                                                        <button class="btn btn-outline-primary" type="button"
                                                            onclick="$('#image_file').click()"
                                                            title="click here to add images">
                                                            <i class="fas fa-plus fa-5x"></i>
                                                        </button>
                                                    </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <span id="image_file_error"></span>
                        @else
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Profile</label>
                                        <div id="postedImages"></div>
                                        <input type="file" name="image_file" id="image_file" class="d-none"
                                            accept="image/*">
                                        <input type="hidden" name="old_cat_pic" id="old_cat_pic"
                                            value="{{ empty($row) == false ? $row->dp_image : '' }}">
                                        <div class="dropHere float-left">
                                            <button class="btn btn-outline-primary" type="button"
                                                onclick="$('#image_file').click()" title="click here to add images">
                                                <i class="fas fa-plus fa-5x"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <span id="image_file_error"></span>
                        @endif
                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary"><i
                                class="fas fa-save"></i>&nbsp;&nbsp;{{ empty($row) == false ? 'Update' : 'Save' }}</button>
                        <a href="{{ url('customer_service/deliveryperson/list') }}" class="btn btn-secondary text-white">
                            <i class="fas fa-arrow-left"></i>&nbsp;&nbsp;Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('pageJS')
    <script>
        // $('#selectvalue').select2({
        //     disabled: true
        // });
    </script>
    <script src="{{ asset('plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('js/page/image_upload.js') }}"></script>
    <script>
        $(document).ready(function(){
            $('.select2').select2();
        })
        $(document).on('submit', '#addDelivery', function (e) {
            e.preventDefault();
            loader_show();
            var data = new FormData(this);
            if(images.length >0){
                for(key in images){
                    data.append('image_file',images[key]);
                }
            }
            $('#addDelivery .is-invalid').removeClass('is-invalid');
            $('#addDelivery .text-danger').remove();
            $.ajax({
                url: SITE_URL + 'customer_service/deliveryperson/add',
                type: 'POST',
                data: data,
                success: function (obj) {
                    if (!obj.status && obj.type == 'validation') {
                        loader_hide();
                        for (key in obj.errors) {
                            $('#' + key).addClass('is-invalid');
                            $('#' + key+'_error').after('<p class="text-danger">' + obj.errors[key] + '</p>');
                        }
                    }
                    if (obj.status) {
                        loader_hide();
                        messageAlert('Success',obj.msg,'fa-check','success')
                        $('#addDelivery')[0].reset();
                        setTimeout(function () {
                            window.location = SITE_URL + obj.page;
                        }, 1500)
                    }
                },
                cache: false,
                contentType: false,
                processData: false
            })
        })


    </script>
@endsection
