@extends('admin.layout.layout')
@section('pageCSS')
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/jquery-ui/jquery-ui.css') }}">
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
@section('header_content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0 text-dark">{{ $row->name }}
                        <a href="{{ url('admin/uber/storelist') }}" class="btn btn-secondary text-white float-right"> <i
                                class="fas fa-arrow-left "></i>&nbsp;&nbsp;Back</a>
                        <a href="javascript:;" data-id={{ $row->store_id }}
                            class="getStoreMenu btn btn-primary text-white float-right mr-2">Get Store Menu From Uber</a>
                    </h1>
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
                <div class="card-header" style="background-color:#f2f2f2;">
                    <ul class="nav nav-pills">
                        <li class="nav-item"><a class="nav-link tabselection" href="#storeDetail" id="storedetail_tab"
                                data-toggle="tab">Store Detail</a></li>
                        <li class="nav-item"><a class="nav-link tabselection" href="#storeMenu" id="storemenu_tab"
                                data-toggle="tab">Store Menu</a></li>
                        <li class="nav-item"><a class="nav-link tabselection" href="#storeItem" id="storeitem_tab"
                                data-toggle="tab">Store Item</a></li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane " id="storeDetail">
                            <div class="row">
                                <div class="col-md-2">
                                    <strong>Store Name : </strong>
                                </div>
                                <div class="col-md-4">
                                    <p class="text-muted">{{ $row->name }}</p>
                                </div>
                                <div class="col-md-2">
                                    <strong>Store Id </strong>
                                </div>
                                <div class="col-md-4">
                                    <p class="text-muted">{{ $row->store_id }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <strong>Address : </strong>
                                </div>
                                <div class="col-md-4">
                                    <p class="text-muted">
                                        {{ isset($row->location->address_2) ? $row->location->address_2 : '' }} ,
                                        {{ $row->location->address }}</p>
                                </div>
                                <div class="col-md-2">
                                    <strong>City : </strong>
                                </div>
                                <div class="col-md-4">
                                    <p class="text-muted">{{ $row->location->city }} </p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <strong>State : </strong>
                                </div>
                                <div class="col-md-4">
                                    <p class="text-muted">{{ $row->location->state }}</p>
                                </div>
                                <div class="col-md-2">
                                    <strong>Postal Code : </strong>
                                </div>
                                <div class="col-md-4">
                                    <p class="text-muted">{{ $row->location->country }}- {{ $row->location->postal_code }}
                                    </p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <strong>Status : </strong>
                                </div>
                                <div class="col-md-4">
                                    <p class="text-muted">{{ $row->status }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane" id="storeMenu">
                            <form method="post" id="addMenu" enctype="multipart/form-data">
                                @csrf

                                <table id="table" class="table table-bordered table-hover"
                                    style="border-top: 2px solid #f2f2f2">
                                    <thead>
                                        <tr>
                                            <th><b>#</b></th>

                                            <th>Monday</th>
                                            <th>Tuesday</th>
                                            <th>Wednesday</th>
                                            <th>Thursday</th>
                                            <th>Friday</th>
                                            <th>Saturday</th>
                                            <th>Sunday </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><b>From Time</b></td>
                                            @for ($m = 1; $m < 8; $m++)
                                                <td>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <dt style="text-align:center;">HR</dt>
                                                            <select name="starttime0_{{ $m }}"
                                                                id="starttime0_{{ $m }}"
                                                                class="form-control select2"
                                                                style="width:115%;padding:5px !important;">

                                                                @for ($i = 0; $i < 24; $i++)
                                                                    <option value="{{ $i < 10 ? '0' . $i : $i }}"
                                                                        {{ $time && $time[$m - 1]['start_time0'] == $i ? 'selected' : '' }}>
                                                                        {{ $i < 10 ? '0' . $i : $i }}</option>
                                                                @endfor

                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <dt style="text-align:center;">MIN</dt>
                                                            <select name="starttime1_{{ $m }}"
                                                                id="starttime1_{{ $m }}"
                                                                class="form-control select2"
                                                                style="width:115%;padding:5px !important;">

                                                                @for ($j = 0; $j < 60; $j++)
                                                                    <option value="{{ $j < 10 ? '0' . $j : $j }}"
                                                                        {{ $time && $time[$m - 1]['start_time1'] == $j ? 'selected' : '' }}>
                                                                        {{ $j < 10 ? '0' . $j : $j }}</option>
                                                                @endfor

                                                            </select>
                                                        </div>
                                                    </div>
                                                </td>
                                            @endfor

                                        </tr>
                                        <tr>
                                            <td><b>Until This Time</b></td>
                                            @for ($m = 1; $m < 8; $m++)
                                                <td>

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <dt style="text-align:center;">HR</dt>
                                                            <select name="endtime0_{{ $m }}"
                                                                id="endtime0_{{ $m }}"
                                                                class="form-control select2"
                                                                style="width:115%;padding:5px !important;">

                                                                @for ($k = 0; $k < 24; $k++)
                                                                    <option value="{{ $k < 10 ? '0' . $k : $k }}"
                                                                        {{ $time && $time[$m - 1]['end_time0'] == $k ? 'selected' : '' }}>
                                                                        {{ $k < 10 ? '0' . $k : $k }}</option>
                                                                @endfor

                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <dt style="text-align:center;">MIN</dt>
                                                            <select name="endtime1_{{ $m }}"
                                                                id="endtime1_{{ $m }}"
                                                                class="form-control select2"
                                                                style="width:115%;padding:5px !important;">

                                                                @for ($l = 0; $l < 60; $l++)
                                                                    <option value="{{ $l < 10 ? '0' . $l : $l }}"
                                                                        {{ $time && $time[$m - 1]['end_time1'] == $l ? 'selected' : '' }}>
                                                                        {{ $l < 10 ? '0' . $l : $l }}</option>
                                                                @endfor

                                                            </select>
                                                        </div>
                                                    </div>
                                                </td>
                                            @endfor

                                        </tr>
                                    </tbody>
                                </table>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="first_name">Category</label>
                                            <select name="category[]" id="category" class="form-control select2"
                                                autocomplete="off" multiple onchange="getProductList();">
                                                <option class="d-none" value="">Select Category</option>
                                                @foreach ($category as $category)
                                                    <option value="{{ $category->id }}" <?php echo in_array($category->category_name, $uber_categoryids) ? 'selected' : ''; ?>>
                                                        {{ $category->category_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="first_name">Product</label>
                                            <select name="product[]" id="product" class="form-control select2"
                                                autocomplete="off" multiple>
                                                @foreach ($product as $product)
                                                    <option value="{{ $product->id }}" <?php echo in_array($product->id, $uber_productids) ? 'selected' : ''; ?>>
                                                        {{ $product->product_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <input type="hidden" name="store_id" id="store_id" value="{{ $row->store_id }}">
                                    <input type="hidden" name="id" id="id" value="{{ $row->id }}">
                                    <input type="hidden" name="uber_productids" id="uber_productids"
                                        value="{{ implode(',', $uber_productids) }}">
                                    <button type="submit" class="btn btn-primary"><i
                                            class="fas fa-save"></i>&nbsp;&nbsp;Sync Menu</button>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane" id="storeItem">
                            <div class="row">
                                <table id="table" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th width="5%">Id</th>
                                            <th width="5%">Image</th>
                                            <th width="20%">Name</th>
                                            <th width="10%">Price</th>
                                            <th width="10%">Price</th>
                                            <th width="10%">#</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($uber_item as $key => $value)
                                            <tr>
                                            <tr>
                                                <td>{{ $value['id'] }}</td>
                                                <td><img height="50px" width="50px"
                                                        src="{{ isset($value['image_url']) ? $value['image_url'] : '' }}">
                                                </td>
                                                <td>{{ $value['name'] }}</td>
                                                <td>{{ $value['price_info'] }}</td>
                                                <td><input type="number" class="price_update"
                                                        name="price_{{ $value['id'] }}" id="price_{{ $value['id'] }}"
                                                        value="{{ $value['price_info'] }}"></td>
                                                <td><a onclick="updateItemPrice('{{ $value['id'] }}')"
                                                        href="javascript:;"
                                                        class="btn btn-primary btn-sm text-center">Update Item </a></td>
                                            </tr>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>


                </div>
                <!-- /.card-body -->
                {{-- <div class="card-footer">
        <a href="{{ url('admin/uber/storelist') }}" class="btn btn-secondary text-white"> <i class="fas fa-arrow-left"></i>&nbsp;&nbsp;Back</a>
      </div> --}}
            </div>
        </div>

    @endsection
    @section('pageJS')
        <script src="{{ asset('plugins/select2/js/select2.min.js') }}"></script>
        <script>
            $('#addMenu').on('submit', function(e) {
                e.preventDefault();
                loader_show();
                let formData = new FormData(this)
                $('#addMenu .is-invalid').removeClass('is-invalid');
                $('#addMenu .text-danger').remove();
                $.ajax({
                    url: SITE_URL + 'admin/uber/syncUberMenu',
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
                            messageAlert('Success', obj.message, 'fa-check', 'success')
                            $('#addMenu')[0].reset();
                            setTimeout(function() {
                                // window.location = SITE_URL + 'admin/uber/storemenudetail/'+$('#id').val();
                                location.reload();
                            }, 1500)
                        }
                    },

                })
            })
        </script>
        <script>
            $(document).ready(function() {
                getProductList();
                // $('#category,#product option[value="selectvalue"]').attr("disabled", true);
                // $('#category').select2();
                // $('#product').select2();
                $("#product, #category").select2({
                    closeOnSelect: false,
                    allowHtml: true,
                    allowClear: true,
                    tags: true
                });
            })

            function getProductList() {
                var category_ids = $('#category').val();
                var uber_productids = $('#uber_productids').val();
                $.ajax({
                    url: SITE_URL + 'admin/uber/getProductList',
                    type: 'POST',
                    data: 'category_ids=' + category_ids + '&uber_productids=' + uber_productids + '&_token=' + $(
                        'meta[name=csrf-token]').attr('content'),
                    success: function(obj) {
                        if (obj.status == true) {
                            $('#product').html(obj.html);
                            loader_hide();
                        } else {
                            $.alert('Something went wrong');
                        }
                    }
                });
            }

            function updateItemPrice(item_id) {
                loader_show();

                var store_id = $('#store_id').val();
                var price = $('#price_' + item_id).val();

                $.ajax({
                    url: SITE_URL + 'admin/uber/update_store_item',
                    type: 'POST',
                    data: 'store_id=' + store_id + '&item_id=' + item_id + '&price=' + price + '&_token=' + $(
                        'meta[name=csrf-token]').attr('content'),
                    success: function(obj) {
                        if (obj.status == true) {
                            loader_hide();
                            messageAlert('Success', obj.message, 'fa-check', 'success');
                            // location.reload();
                        } else {

                            loader_hide();
                            $.alert('Something went wrong');
                            // location.reload();

                        }
                    }
                });
            }

            $(window).on("load", function() {
                var seltab = (sessionStorage.getItem('selectedtab') != "undefined" && sessionStorage.getItem(
                    'selectedtab') != null ? sessionStorage.getItem('selectedtab') : 'storedetail_tab')

                $('#' + seltab).trigger('click');
                $(".tabselection").click(function(e) {
                    e.preventDefault();
                    var tabno = $(this).attr('id');
                    sessionStorage.setItem('selectedtab', tabno);
                    $(this).tab('show');
                });
            });
            $(document).on('click', '.getStoreMenu', function() {
                store_id = $(this).attr('data-id');
                loader_show();
                $.ajax({
                    url: SITE_URL + 'admin/uber/get_uber_stores_menu',
                    type: 'POST',
                    data: 'store_id=' + store_id + '&_token=' + $('meta[name=csrf-token]').attr('content'),
                    success: function(obj) {
                        if (obj.status == true) {
                            loader_hide();
                            messageAlert('Success', obj.message, 'fa-check', 'success');
                            location.reload();
                        } else {
                            loader_hide();
                            $.alert('Something went wrong');
                            location.reload();

                        }
                    }
                });
            });
        </script>
    @endsection
