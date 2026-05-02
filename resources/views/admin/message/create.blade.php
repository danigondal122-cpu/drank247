@extends('admin.layout.layout')
@section('pageCSS')
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/fselect/fSelect.css') }}">
@endsection
@section('header_content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">{{ empty($row) == false ? 'Update Message' : 'Add New Message' }}</h1>
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
                    <h3 class="card-title">{{ empty($row) == false ? 'Update Message' : 'Add New Message' }}</h3>
                </div>
                <form method="post" id="addMessage" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="id" value="{{ empty($row) == false ? $row->id : '' }}">
                    <div class="card-body col-sm-12 col-md-6">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">*Message to</label>
                                    <select name="message_to" id="message_to" class="form-control select2"
                                        onchange="getUserList();">

                                        <option value="">Please Select</option>
                                        <option value="franchise"
                                            {{ empty($row) == false && $row->message_to == 'franchise' ? 'selected=selected' : '' }}>
                                            Franchise</option>
                                        <option value="deliveryperson"
                                            {{ empty($row) == false && $row->message_to == 'deliveryperson' ? 'selected=selected' : '' }}>
                                            Delivery Person</option>
                                        <option value="customer"
                                            {{ empty($row) == false && $row->message_to == 'customer' ? 'selected=selected' : '' }}>
                                            Customer</option>
                                    </select>
                                </div>
                                <span id="message_to_error"></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12" id="Userslist">
                                <div class="form-group">
                                    @if (empty($row) == false && $row->message_to == 'deliveryperson')
                                        <div class="form-group">
                                            <label for="first_name">*User</label>
                                            <select name="message_user[]" id="message_user" class="form-control select2"
                                                multiple="">
                                                <option value="">Select Delivery Person</option>`;
                                                @foreach ($deliverylist as $frs)
                                                    <option value="{{ $frs->id }}"
                                                        {{ empty($row) == false && in_array($frs->id, $user) ? 'selected=selected' : '' }}>
                                                        {{ $frs->dp_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <span id="message_user_error"></span>
                                    @endif
                                    @if (empty($row) == false && $row->message_to == 'customer')
                                        <div class="form-group">
                                            <label for="first_name">*User</label>
                                            <select name="message_user[]" id="message_user" class="form-control select2"
                                                multiple="">
                                                <option value="">Select Customer</option>`;
                                                @foreach ($customerlist as $frs)
                                                    <option value="{{ $frs->id }}"
                                                        {{ empty($row) == false && in_array($frs->id, $user) ? 'selected=selected' : '' }}>
                                                        {{ $frs->customer_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <span id="message_user_error"></span>
                                    @endif
                                    @if (empty($row) == false && $row->message_to == 'franchise')
                                        <div class="form-group">
                                            <label for="first_name">*User</label>
                                            <select name="message_user[]" id="message_user" class="form-control select2"
                                                multiple="">
                                                <option value="">Select Franchise</option>`;
                                                @foreach ($franchiselist as $frs)
                                                    <option value="{{ $frs->id }}"
                                                        {{ empty($row) == false && in_array($frs->id, $user) ? 'selected=selected' : '' }}>
                                                        {{ $frs->franchises_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <span id="message_user_error"></span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">*Message</label>
                                    <textarea class="form-control" id="message_text" name="message_text">{{ empty($row) == false ? $row->message_text : '' }}</textarea>
                                </div>
                                <span id="message_text_error"></span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        @if (empty($row) == false && $row->message_to == 'customer')
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Upload Image</label>
                                        <input type="file" name="image_file" id="image_file" class="d-none"
                                            accept=".png, .jpg, .jpeg,.svg,.PNG,.JPG,.JPEG">
                                        {{-- <input type="hidden" name="old_cat_pic" id="old_cat_pic" value="{{$row->image}}"> --}}
                                        <div id="postedImages">
                                            @if ($row->image != '')
                                                <div class="card elevation-1 mb-3 " style="width:120px;" id="img">
                                                    <div class="d-flex align-self-center align-items-center px-2"
                                                        style="height:120px;">
                                                        <img src="{{ $row->image }}"
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
                        @else
                            <div class="row">
                                <div class="col-sm-12" id="ImageDiv" style="Display:none;">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Upload Image</label>
                                        <div id="postedImages"></div>
                                        <input type="file" name="image_file" id="image_file" class="d-none"
                                            accept=".png, .jpg, .jpeg,.svg,.PNG,.JPG,.JPEG">
                                        <input type="hidden" name="old_cat_pic" id="old_cat_pic" value="">
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
                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary"
                            style="display:{{ empty($row) == false ? 'none;' : '' }}"><i
                                class="fas fa-save"></i>&nbsp;&nbsp;{{ empty($row) == false ? 'Update' : 'Save' }}</button>
                        <a href="{{ url('admin/message/list') }}" class="btn btn-secondary text-white"> <i
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
        function getUserList() {
            var type = $('#message_to').val();

            if (type == 'deliveryperson') {
                let html = `
     <div class="form-group">
      <label for="first_name">*User</label>
     <select name="message_user[]" id="message_user" class="form-control select2" multiple="">
     `;
                @foreach ($deliverylist as $frs)
                    html += `<option value="{{ $frs->id }}">{{ $frs->dp_name }}</option>`;
                @endforeach
                html += `</select>
    
      <button type="button" class="chosen-toggle btn btn-primary btn-sm select mt-2">Select all</button>
      <button type="button" class="chosen-toggle btn btn-danger  btn-sm deselect mt-2">Deselect all</button>
     
      </div>
      <span id="message_user_error"></span>`;

                $('#Userslist').html(html);
                $('#ImageDiv').hide();
                $(".select2").select2();
                $('#DisabledDiv').select2({
                    disabled: true
                });
            }
            if (type == 'customer') {
                let html = `
     <div class="form-group">
      <label for="first_name">*User</label>
     <select name="message_user[]" id="message_user" class="form-control select2" multiple="">
     `;
                @foreach ($customerlist as $frs)
                    html += `<option value="{{ $frs->id }}">{{ $frs->customer_name }}</option>`;
                @endforeach
                html += `</select>
     
      <button type="button" class="chosen-toggle btn btn-primary btn-sm select mt-2">Select all</button>
      <button type="button" class="chosen-toggle btn btn-danger  btn-sm deselect mt-2">Deselect all</button>
      </div>
      
      <span id="message_user_error"></span>`;
                $('#Userslist').html(html);
                $('#ImageDiv').show();
                $(".select2").select2();
                $('#DisabledDiv').select2({
                    disabled: true
                });
            }
            if (type == 'franchise') {

                let html = `
     <div class="form-group">
      <label for="first_name">*User</label>
     <select name="message_user[]" id="message_user" class="form-control select2" multiple="">
     `;
                @foreach ($franchiselist as $frs)
                    html += `<option value="{{ $frs->id }}">{{ $frs->franchises_name }}</option>`;
                @endforeach
                html += `</select>
     
      <button type="button" class="chosen-toggle btn btn-primary btn-sm select mt-2">Select all</button>
      <button type="button" class="chosen-toggle btn btn-danger  btn-sm deselect mt-2">Deselect all</button>
      </div>
      
      <span id="message_user_error"></span>`;
                $('#Userslist').html(html);
                $('#ImageDiv').hide();
                $(".select2").select2();
                $('#DisabledDiv').select2({
                    disabled: true
                });
            }

            $('.chosen-toggle').each(function(index) {
                $(this).on('click', function() {
                    $(this).parent().find('option').prop('selected', $(this).hasClass('select')).parent()
                        .trigger('chosen:updated');
                    $("#message_user").trigger("change");
                });
            });
        }

        $(document).ready(function() {
            $('.select2').select2();
        })

        $(document).on('submit', '#addMessage', function(e) {
            e.preventDefault();
            loader_show();
            let formData = new FormData(this)
            if (images.length > 0) {
                for (key in images) {
                    formData.append('image_file', images[key]);
                }
            }
            $('#addMessage .is-invalid').removeClass('is-invalid');
            $('#addMessage .text-danger').remove();
            $.ajax({
                url: SITE_URL + 'admin/message/add',
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
                        $('#addMessage')[0].reset();
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
    <script src="{{ asset('plugins/fselect/fSelect.js') }}"></script>
@endsection
