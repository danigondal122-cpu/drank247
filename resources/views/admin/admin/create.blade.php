@extends('admin.layout.layout')
@section('pageCSS')
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
@endsection
@section('header_content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">{{ empty($row) == false ? 'Update Admin' : 'Add New Admin' }}</h1>
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
                    <h3 class="card-title">{{ empty($row) == false ? 'Update Admin' : 'Add New Admin' }}</h3>
                </div>
                <form method="post" id="addAdmin">
                    @csrf
                    <input type="hidden" name="id" id="id" value="{{ empty($row) == false ? $row->id : '' }}">
                    <div class="card-body col-sm-12 col-md-12">
                        <div class="clearfix"></div>
                        @if (empty($row) == false)
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Image</label>
                                        <input type="file" name="image_file" id="image_file" class="d-none"
                                            accept=".png, .jpg, .jpeg,.svg,.PNG,.JPG,.JPEG">
                                        <input type="hidden" name="old_cat_pic" id="old_cat_pic"
                                            value="{{ $row->image }}">
                                        <div id="postedImages">
                                            @if ($row->image != '')
                                                <div class="card elevation-1 mb-3 " style="width:120px;" id="img">
                                                    <div class="d-flex align-self-center align-items-center px-2"
                                                        style="height:120px;">
                                                        <img src="/uploads/adminprofile/{{ $row->image }}"
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
                                                </div>
                                            @else
                                                <input type="file" name="image_file" id="image_file" class="d-none"
                                                    accept=".png, .jpg, .jpeg,.svg,.PNG,.JPG,.JPEG">
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
                        @else
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Image</label>
                                        <div id="postedImages"></div>
                                        <input type="file" name="image_file" id="image_file" class="d-none"
                                            accept=".png, .jpg, .jpeg,.svg,.PNG,.JPG,.JPEG">
                                        <input type="hidden" name="old_cat_pic" id="old_cat_pic"
                                            value="{{ empty($row) == false ? $row->image : '' }}">
                                        <div class="dropHere float-left">
                                            <button class="btn btn-outline-primary" type="button"
                                                onclick="$('#image_file').click()" title="click here to add images">
                                                <i class="fas fa-plus fa-5x"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="row mt-2">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="name">*Name</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ empty($row) == false ? $row->name : '' }}">
                                </div>
                                <span id="name_error"></span>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="email">*Email</label>
                                    <input type="text" class="form-control" id="email" name="email"
                                        value="{{ empty($row) == false ? $row->email : '' }}">
                                </div>
                                <span id="email_error"></span>
                            </div>
                        </div>
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

                        <div class="row">

                            <div class="col-sm-6">
                                <div class="row">

                                    <div class="col-md-4" style="display:inline-block">
                                        <label>Accountant ? <input type="checkbox" name="is_accountant" value="1"
                                                {{ isset($row->is_accountant) && $row->is_accountant == 1 ? 'checked' : '' }}>
                                        </label>
                                    </div>

                                </div>

                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group">
                                <label for="Module">*Assign Module</label>
                            </div>
                            <div class="col-sm-12">
                                <div class="row">
                                    @foreach ($modules as $key => $value)
                                        <div class="col-md-4" style="display:inline-block">
                                            <label><input type="checkbox" name="module[]" value="{{ $value->id }}"
                                                    {{ in_array($value->id, $assign_module) ? 'checked' : '' }}>
                                                {{ $value->module_name }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <span id="module_error"></span>
                            </div>
                        </div>

                    </div>
            </div>
            <!-- /.card-body -->

            <div class="card-footer">
                <button type="submit" class="btn btn-primary"><i
                        class="fas fa-save"></i>&nbsp;&nbsp;{{ empty($row) == false ? 'Update' : 'Save' }}</button>
                <a href="{{ url('admin/admin/list') }}" class="btn btn-secondary text-white"> <i
                        class="fas fa-arrow-left"></i>&nbsp;&nbsp;Back</a>
            </div>
            </form>
        </div>
    </div>
    </div>
@endsection
@section('pageJS')
    <script src="{{ asset('plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('js/page/image_upload.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2();
        })
        $(document).on('submit', '#addAdmin', function(e) {
            e.preventDefault();
            loader_show();
            var data = new FormData(this);
            if (images.length > 0) {
                for (key in images) {
                    data.append('image_file', images[key]);
                }
            }
            $('#addAdmin .is-invalid').removeClass('is-invalid');
            $('#addAdmin .text-danger').remove();
            $.ajax({
                url: SITE_URL + 'admin/admin/add',
                type: 'POST',
                data: data,
                success: function(obj) {
                    if (!obj.status && obj.type == 'validation') {
                        loader_hide();
                        for (key in obj.errors) {
                            $('#' + key).addClass('is-invalid');
                            $('#' + key + '_error').after('<p class="text-danger">' + obj.errors[key] +
                                '</p>');
                        }
                        $target = jQuery(".is-invalid:first");
                        if ($target.length) {
                            jQuery("html, body")
                                .stop()
                                .animate({
                                        scrollTop: parseInt($target.offset().top) - 125,
                                    },
                                    900,
                                    "swing",
                                    function() {}
                                );
                        }
                    }
                    if (obj.status) {
                        loader_hide();
                        messageAlert('Success', obj.msg, 'fa-check', 'success')
                        $('#addAdmin')[0].reset();
                        setTimeout(function() {
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
