@extends('admin.layout.layout')
@section('header_content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">{{ empty($row) == false ? 'Update Category' : 'Add New Category' }}</h1>
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
                    <h3 class="card-title">{{ empty($row) == false ? 'Update Category' : 'Add New Category' }}</h3>
                </div>
                <form method="post" id="addCategory" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="category_id" id="category_id" value="{{ !empty($row) ? $row->id : '' }}">
                    <div class="card-body col-sm-12 col-md-6">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">Parent Category</label>
                                    <select name="category_parent" id="category_parent" class="form-control" autocomplete="off">
                                        <option value="">Select Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" @selected(!empty($row) && $category->id == $row->category_id)>{{ $category->category_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">*Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ empty($row) == false ? $row->category_name : '' }}">
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">*Description</label>
                                    <textarea type="text" class="form-control" id="description" name="description">{{ empty($row) == false ? $row->description : '' }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="Franchise">*Product Type</label>
                                    <select class="form-control" id="product_type_id" name="product_type_id">
                                        <option value="">Select Product Type</option>
                                        @foreach ($productTypes as $value)
                                            <option value="{{ $value->id }}" {{ empty($row) == false && $row->product_type_id == $value->id ? 'selected' : '' }}>{{ $value->product_type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <span id="product_type_error"></span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_show" name="is_show" {{ empty($row) == true ? 'checked' : (empty($row) == false && $row->is_show == '1' ? 'checked' : '') }}>
                                        <label class="form-check-label"><b>Show (Do you want to show this Category in Web?) </b></label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if (empty($row) == false)
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Category Image</label>
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

                                                {{-- <div class="card elevation-2 col-sm-6 p-1" id="img">
                      <img src="{{$row->image}}" />
                       <button type="button" data-toggle="tooltip" data-placement="bottom" title="View full image" style="position: absolute;left: 2%;bottom: 2%;" class="btn btn-primary btn-sm previewImage">
                       <i class="fas fa-search-plus"></i>
                       </button>
                       <button type="button" data-toggle="tooltip" data-placement="bottom" title="Remove" style="position: absolute;right: 2%;bottom: 2%;" class="btn btn-danger btn-sm deleteImage" data-id="">
                         <i class="fas fa-trash-alt"></i>
                       </button>
                     </div>  --}}
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
                                        <label for="exampleInputEmail1">Category Image</label>
                                        <div id="postedImages"></div>
                                        <input type="file" name="image_file" id="image_file" class="d-none" accept=".png, .jpg, .jpeg,.svg,.PNG,.JPG,.JPEG">
                                        <input type="hidden" name="old_cat_pic" id="old_cat_pic" value="">
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
                        <a href="{{ url('admin/category/list') }}" class="btn btn-secondary text-white"> <i class="fas fa-arrow-left"></i>&nbsp;&nbsp;Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('pageJS')
    <script src="{{ asset('js/page/image_upload.js') }}"></script>
    <script>
        $('#addCategory').on('submit', function(e) {
            e.preventDefault();
            loader_show();
            let formData = new FormData(this)
            if (images.length > 0) {
                for (key in images) {
                    formData.append('image_file', images[key]);
                }
            }
            $('#addCategory .is-invalid').removeClass('is-invalid');
            $('#addCategory .text-danger').remove();
            $.ajax({
                url: SITE_URL + 'admin/category/add',
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
                        $('#addCategory')[0].reset();
                        setTimeout(function() {
                            window.location = SITE_URL + obj.page;
                        }, 1500)
                    }
                },

            })
        })
        $(document).on('submit', '#editCategory', function(e) {
            e.preventDefault();
            loader_show();
            var data = new FormData(this);
            if (images.length > 0) {
                for (key in images) {
                    data.append('image', images[key]);
                }
            }
            $('#editCategory .is-invalid').removeClass('is-invalid');
            $('#editCategory .text-danger').remove();
            $.ajax({
                url: SITE_URL + 'admin/Category/updateCategory',
                type: 'POST',
                data: data,
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
                        Toast.fire({
                            type: 'success',
                            title: obj.msg
                        })
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
        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();
            let id = $(this).attr('data-id');
            $.confirm({
                title: '',
                content: 'Sure want to delete?',
                buttons: {
                    confirm: {
                        text: 'Yes',
                        btnClass: 'btn-danger',
                        action: function() {
                            $.ajax({
                                url: SITE_URL + 'admin/Category/deleteCategory',
                                type: 'GET',
                                data: 'id=' + id,
                                success: function(obj) {
                                    if (obj.status == true) {
                                        // table.draw();
                                        Toast.fire({
                                            type: 'success',
                                            title: obj.msg
                                        })
                                        setTimeout(function() {
                                            window.location = SITE_URL + obj.page;
                                        }, 1500)
                                    } else {
                                        $.alert('Something went wrong');
                                    }
                                }
                            });
                        }
                    },
                    cancel: {
                        text: 'No',
                        action: function() {}
                    },
                }
            });
        })
        $('#addMenu').on('submit', function(e) {
            e.preventDefault();
            loader_show();
            let formData = new FormData(this)
            console.log('formData', formData);
            $('#addMenu .is-invalid').removeClass('is-invalid');
            $('#addMenu .text-danger').remove();
            $.ajax({
                url: SITE_URL + 'admin/uber/syncMenu',
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
                        $('#addMenu')[0].reset();
                        setTimeout(function() {
                            window.location = SITE_URL + obj.page;
                        }, 1500)
                    }
                },

            })
        });
    </script>
@endsection
