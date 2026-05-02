@extends('franchise.layout.layout')
@section('pageCSS')
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/jquery-ui/jquery-ui.css') }}">
@endsection
@section('header_content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">{{ empty($row) == false ? 'Update Delivery' : 'Add New Delivery Person' }}</h1>
                </div>
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <style>
        .select2-container {
            min-width: 400px;
        }

        .select2-results__option {
            padding-right: 20px;
            vertical-align: middle;
        }

        .select2-results__option:before {
            content: "";
            display: inline-block;
            position: relative;
            height: 20px;
            width: 20px;
            border: 2px solid #e9e9e9;
            border-radius: 4px;
            background-color: #fff;
            margin-right: 20px;
            vertical-align: middle;
        }

        .select2-results__option[aria-selected=true]:before {
            font-family: fontAwesome;
            /* content: "\f00c"; */
            color: #fff;
            background-color: #e91362;
            border: 0;
            display: inline-block;
            padding-left: 3px;
        }

        .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: #fff;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #eaeaeb;
            color: #272727;
        }

        .select2-container--default .select2-selection--multiple {
            margin-bottom: 10px;
        }

        .select2-container--default.select2-container--open.select2-container--below .select2-selection--multiple {
            border-radius: 4px;
        }

        .select2-container--default.select2-container--focus .select2-selection--multiple {
            border-color: #e91362;
            border-width: 2px;
        }

        .select2-container--default .select2-selection--multiple {
            border-width: 2px;
        }

        .select2-container--open .select2-dropdown--below {

            border-radius: 6px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);

        }

        .select2-selection .select2-selection--multiple:after {
            content: 'hhghgh';
        }

        /* select with icons badges single*/
        .select-icon .select2-selection__placeholder .badge {
            display: none;
        }

        .select-icon .placeholder {
            display: none;
        }

        .select-icon .select2-results__option:before,
        .select-icon .select2-results__option[aria-selected=true]:before {
            display: none !important;
            /* content: "" !important; */
        }

        .select-icon .select2-search--dropdown {
            display: none;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ empty($row) == false ? 'Update Delivery' : 'Add New Delivery Person' }}</h3>
                </div>
                <form method="post" id="addDelivery">
                    @csrf
                    <input type="hidden" name="dp_id" id="dp_id" value="{{ empty($row) == false ? $row->id : '' }}">
                    <div class="card-body col-sm-12 col-md-6">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">*Email</label>
                                    <input type="text" id="email" name="email" class="form-control validateitemfield valid typeahead" autocomplete="off" placeholder="Select Email " value="{{ empty($row) == false ? $row->dp_email : '' }}">
                                </div>
                                <span id="email_error"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">*Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ empty($row) == false ? $row->dp_name : '' }}">
                                </div>
                                <span id="name_error"></span>
                            </div>
                        </div>

                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">*Contact No</label>
                                    <input type="text" class="form-control" id="contact_no" name="contact_no" value="{{ empty($row) == false ? $row->dp_contact_no : '' }}">
                                </div>
                                <span id="contact_no_error"></span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">*Street</label>
                                    <input type="text" class="form-control" id="street" name="street" value="{{ empty($row) == false ? $row->dp_street : '' }}">
                                </div>
                                <span id="street_error"></span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">*City</label>
                                    <input type="text" class="form-control" id="city" name="city" value="{{ empty($row) == false ? $row->dp_city : '' }}">
                                </div>
                                <span id="city_error"></span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">*State</label>
                                    <input type="text" class="form-control" id="state" name="state" value="{{ empty($row) == false ? $row->dp_state : '' }}">
                                </div>
                                <span id="state_error"></span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">*Post Code</label>
                                    <input type="text" class="form-control" id="postcode" name="postcode" value="{{ empty($row) == false ? $row->dp_postcode : '' }}">
                                </div>
                                <span id="postcode_error"></span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">*Pool</label>
                                    <select class="form-control select2" id="pool" name="pool[]" multiple="multiple">
                                        <option value="selectvalue" id="selectvalue">Select Pool</option>
                                        @foreach ($pool as $value)
                                            <option value="{{ $value->id }}" {{ empty($row) == false && in_array($value->id, $poolarray) ? 'selected' : '' }}>{{ '(' . $value->from_postcode . '- ' . $value->to_postcode . ') ' }}{{ $value->area }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <span id="pool_error"></span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        @if (empty($row) == false)
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Profile</label>
                                        <input type="file" name="image_file" id="image_file" class="d-none" accept="image/*">
                                        <input type="hidden" name="old_cat_pic" id="old_cat_pic" value="{{ $row->dp_image }}">
                                        <div id="postedImages">
                                            @if ($row->dp_image != '')
                                                <div class="card elevation-1 mb-3 " style="width:120px;" id="img">
                                                    <div class="d-flex align-self-center align-items-center px-2" style="height:120px;">
                                                        <img src="{{ $row->dp_image }}" style="max-height:120px;margin-left: auto;margin-right: auto;" class="card-img-top cart-item-img" alt="">
                                                    </div>
                                                    <button type="button" data-toggle="tooltip" data-placement="bottom" title="View full image" style="position: absolute;left: 2%;bottom: 2%;" class="btn btn-primary btn-sm previewImage">
                                                        <i class="fas fa-search-plus"></i>
                                                    </button>
                                                    <button type="button" data-toggle="tooltip" data-placement="bottom" title="Remove" style="position: absolute;right: 2%;bottom: 2%;" class="btn btn-danger btn-sm deleteImage" data-id="">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
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
                        @else
                            <div class="row" id="imagesource">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Profile</label>
                                        <div id="postedImages"></div>
                                        <input type="file" name="image_file" id="image_file" class="d-none" accept="image/*">
                                        <input type="hidden" name="old_cat_pic" id="old_cat_pic" value="{{ empty($row) == false ? $row->dp_image : '' }}">
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
                        <button type="button" id="submit" onclick="submitForm();" class="btn btn-primary"><i class="fas fa-save"></i>&nbsp;&nbsp;{{ empty($row) == false ? 'Update' : 'Save' }}</button>
                        <a href="{{ url('franchise/deliveryperson/list') }}" class="btn btn-secondary text-white"> <i class="fas fa-arrow-left"></i>&nbsp;&nbsp;Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('pageJS')
    <script>
        var codelist = [];

        function getDeliveryPersonList() {
            $.ajax({
                url: SITE_URL + 'franchise/getDeliveryPersonList',
                type: 'POST',
                data: '&_token=' + $('meta[name=csrf-token]').attr('content'),
                success: function(obj) {
                    result = jQuery.parseJSON(obj);
                    codelist = result;
                }
            })

            $(".typeahead").autocomplete({
                source: codelist,
                minLength: 0,
                select: function(event, ui) {
                    email = ui.item.label;
                    fillitemdetails(email);
                }
            }).focus(function() {
                $(this).autocomplete("search");
            });
            $("#email").blur();
            $("#email").focus();

        }

        function fillitemdetails(email) {
            loader_show();
            $.ajax({
                url: SITE_URL + 'franchise/getDeliveryPersonDetail',
                type: 'POST',
                data: 'email=' + email + '&_token=' + $('meta[name=csrf-token]').attr('content'),
                success: function(obj) {
                    loader_hide();
                    if (obj.status == true) {
                        $(".dropHere").remove();
                        $("#name").val(obj.name);
                        $("#contact_no").val(obj.contact_no);
                        $("#street").val(obj.street);
                        $("#city").val(obj.city);
                        $("#state").val(obj.state);
                        $("#postcode").val(obj.postcode);
                        $("#postedImages").html(obj.dp_image);
                    }
                }
            })
        }

        $(document).on('keyup', '#email', function() {
            getDeliveryPersonList();

        })
    </script>
    <script src="{{ asset('plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('js/page/image_upload.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2();
        })

        function submitForm() {
            $('#addDelivery').submit();
        }
        $(document).on('submit', '#addDelivery', function(e) {

            e.preventDefault();
            // TODO!: uncomment ini jika sudah berfungsi
            // loader_show();
            var data = new FormData(this);
            if (images.length > 0) {
                for (key in images) {
                    data.append('image_file', images[key]);
                }
            }
            $('#addDelivery .is-invalid').removeClass('is-invalid');
            $('#addDelivery .text-danger').remove();
            $.ajax({
                url: SITE_URL + 'franchise/deliveryperson/add',
                type: 'POST',
                data: data,
                success: function(obj) {

                    if (!obj.status && obj.type == 'validation') {
                        loader_hide();
                        for (key in obj.errors) {
                            $('#' + key).addClass('is-invalid');
                            $('#' + key + '_error').after('<p class="text-danger">' + obj.errors[key] + '</p>');
                        }
                    }
                    if (obj.status) {
                        loader_hide();
                        messageAlert('Success', obj.msg, 'fa-check', 'success')
                        $('#addDelivery')[0].reset();
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
    <script>
        $(document).ready(function() {
            $('#pool option[value="selectvalue"]').attr("disabled", true);
            $(".select2").select2({
                closeOnSelect: false,
                allowHtml: true,
                allowClear: true,
                tags: true // создает новые опции на лету
            });
        });
    </script>
@endsection
