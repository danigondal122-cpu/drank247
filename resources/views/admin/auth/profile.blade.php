@extends('admin.layout.layout')
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
                    @method('PUT')
                    <input type="hidden" name="category_id" id="category_id" value="{{ $row->admin_id }}">
                    <div class="card-body col-sm-12 col-md-12">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="exampleInputEmail1">*Profile Image</label>
                                    <input type="file" name="image_file" id="image_file" class="d-none" accept="image/*">
                                    <input type="hidden" name="old_cat_pic" id="old_cat_pic" value="{{ $row->image }}">
                                    <div id="postedImages">
                                        @if ($row->image != '')
                                            <div class="card elevation-2 col-sm-6 p-1" id="img">
                                                <img src="{{ $row->image }}" />
                                                <button type="button" data-toggle="tooltip" data-placement="bottom"
                                                    title="View full image" style="position: absolute;left: 2%;bottom: 2%;"
                                                    class="btn btn-primary btn-sm previewImage">
                                                    <i class="fas fa-search-plus"></i>
                                                </button>
                                                <button type="button" data-toggle="tooltip" data-placement="bottom"
                                                    title="Remove" style="position: absolute;right: 2%;bottom: 2%;"
                                                    class="btn btn-danger btn-sm deleteImage" data-id="">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        @else
                                            <input type="file" name="image_file" id="image_file" class="d-none"
                                                accept="image/*">
                                            <div class="dropHere float-left">
                                                <button class="btn btn-outline-primary" type="button"
                                                    onclick="$('#image_file').click()" title="click here to add images">
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
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="first_name">*Name</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ $row->name }}">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="first_name">*Email</label>
                                    <input type="text" class="form-control" id="email" name="email"
                                        value="{{ $row->email }}">
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="mobile_no">Mobile No.</label>
                                    <input type="text" class="form-control" id="mobile_no" name="mobile_no"
                                        value="{{ empty($row) == false ? $row->admin_mobileno : '' }}">
                                </div>
                                <span id="mobile_no_error"></span>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="street">Street</label>
                                    <input type="text" class="form-control" id="street" name="street"
                                        value="{{ empty($row) == false ? $row->admin_street : '' }}">
                                </div>
                                <span id="street_error"></span>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="city">City</label>
                                    <input type="text" class="form-control" id="city" name="city"
                                        value="{{ empty($row) == false ? $row->admin_city : '' }}">
                                </div>
                                <span id="city_error"></span>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="state">State</label>
                                    <input type="text" class="form-control" id="state" name="state"
                                        value="{{ empty($row) == false ? $row->admin_state : '' }}">
                                </div>
                                <span id="state_error"></span>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="postcode">PostCode</label>
                                    <input type="text" class="form-control" id="postcode" name="postcode"
                                        value="{{ empty($row) == false ? $row->admin_postcode : '' }}">
                                </div>
                                <span id="postcode_error"></span>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="postcode">Company name</label>
                                    <input type="text" class="form-control" id="company" name="company"
                                        value="{{ empty($row) == false ? $row->admin_company : '' }}">
                                </div>
                                <span id="company_error"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="postcode">Vat Number</label>
                                    <input type="text" class="form-control" id="vat" name="vat"
                                        value="{{ empty($row) == false ? $row->admin_vat : '' }}">
                                </div>
                                <span id="vat_error"></span>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="postcode">Chamber of Commerce number</label>
                                    <input type="text" class="form-control" id="commerce_number"
                                        name="commerce_number"
                                        value="{{ empty($row) == false ? $row->admin_commerce_number : '' }}">
                                </div>
                                <span id="commerce_number_error"></span>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
                        <a href="{{ url('admin/dashboard') }}" class="btn btn-secondary text-white"> <i
                                class="fas fa-arrow-left"></i>&nbsp;&nbsp;Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('pageJS')
    <script src="{{ asset('js/page/image_upload.js') }}"></script>
    <script>
        $('#updateProfile').on('submit', function(e) {

            e.preventDefault();
            loader_show();
            let formData = new FormData(this)

            if (images.length > 0) {

                for (key in images) {
                    formData.append('image_file', images[key]);
                }
            }
            $('#updateProfile .is-invalid').removeClass('is-invalid');
            $('#updateProfile .text-danger').remove();
            $.ajax({
                url: SITE_URL + 'admin/profile-update',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(obj) {
                    if (!obj.status && obj.type == 'validation') {
                        loader_hide();
                        for (key in obj.errors) {
                            $('#' + key).addClass('is-invalid');
                            $('#' + key).after('<p class="text-danger">' + obj.errors[key] + '</p>');
                        }
                    }
                    if (obj.status) {
                        loader_hide();
                        messageAlert('Success', obj.msg, 'fa-check', 'success')
                        $('#updateProfile')[0].reset();
                        setTimeout(function() {
                            window.location = SITE_URL + obj.page;
                        }, 1500)
                    }
                },

            })
        })
    </script>
@endsection
