@extends('admin.layout.layout')
@section('pageCSS')
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
@endsection
@section('header_content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">{{ empty($row) == false ? 'Update Product' : 'Add New Ware House' }}</h1>
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
                    <h3 class="card-title">{{ empty($row) == false ? 'Update Ware House' : 'Add New Ware House' }}</h3>
                </div>
                <form method="post" id="addWareHouse" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="wh_id" id="wh_id" value="{{ empty($row) == false ? $row->id : '' }}">
                    <div class="card-body col-sm-12 col-md-6">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">*Name</label>
                                    <input type="text" class="form-control" id="wh_name" name="wh_name" value="{{ empty($row) == false ? $row->wh_name : '' }}">
                                </div>
                                <span id="wh_name_error"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">*Min price</label>
                                    <input type="text" class="form-control" id="wh_minprice" name="wh_minprice" value="{{ empty($row) == false ? $row->wh_minprice : '' }}">
                                </div>
                                <span id="wh_minprice_error"></span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        @if (empty($row) == false)
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Logo</label>
                                        <input type="file" name="image_file" id="image_file" class="d-none" accept=".png, .jpg, .jpeg,.svg,.PNG,.JPG,.JPEG">
                                        <input type="hidden" name="old_cat_pic" id="old_cat_pic" value="{{ $row->image }}">
                                        <div id="postedImages">
                                            @if ($row->image != '')
                                                <div class="card elevation-1 mb-3 " style="width:120px;" id="img">
                                                    <div class="d-flex align-self-center align-items-center px-2" style="height:120px;">
                                                        <img src="{{ $row->image }}" style="max-height:120px;margin-left: auto;margin-right: auto;" class="card-img-top cart-item-img" alt="">
                                                    </div>
                                                    <button type="button" data-toggle="tooltip" data-placement="bottom" title="View full image" style="position: absolute;left: 2%;bottom: 2%;" class="btn btn-primary btn-sm previewImage">
                                                        <i class="fas fa-search-plus"></i>
                                                    </button>
                                                    <button type="button" data-toggle="tooltip" data-placement="bottom" title="Remove" style="position: absolute;right: 2%;bottom: 2%;" class="btn btn-danger btn-sm deleteImage" data-id="">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            @else
                                                <input type="file" name="image_file" id="image_file" class="d-none" accept=".png, .jpg, .jpeg,.svg,.PNG,.JPG,.JPEG">
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
                        @else
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Logo</label>
                                        <div id="postedImages"></div>
                                        <input type="file" name="image_file" id="image_file" class="d-none" accept=".png, .jpg, .jpeg,.svg,.PNG,.JPG,.JPEG">
                                        <input type="hidden" name="old_cat_pic" id="old_cat_pic" value="{{ empty($row) == false ? $row->image : '' }}">
                                        <div class="dropHere float-left">
                                            <button class="btn btn-outline-primary" type="button" onclick="$('#image_file').click()" title="click here to add images">
                                                <i class="fas fa-plus fa-5x"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i>&nbsp;&nbsp;{{ empty($row) == false ? 'Update' : 'Save' }}</button>
                        <a href="{{ url('admin/warehouse/list') }}" class="btn btn-secondary text-white"> <i class="fas fa-arrow-left"></i>&nbsp;&nbsp;Back</a>
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
        $('#addWareHouse').on('submit', function(e) {
            e.preventDefault();
            loader_show();
            let formData = new FormData(this)
            if (images.length > 0) {
                for (key in images) {
                    formData.append('image_file', images[key]);
                }
            }
            $('#addWareHouse .is-invalid').removeClass('is-invalid');
            $('#addWareHouse .text-danger').remove();
            $.ajax({
                url: SITE_URL + 'admin/warehouse/add',
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
                        $('#addWareHouse')[0].reset();
                        setTimeout(function() {
                            window.location = SITE_URL + obj.page;
                        }, 1500)
                    }
                },

            })
        })
    </script>
@endsection
