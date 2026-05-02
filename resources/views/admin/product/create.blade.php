@extends('admin.layout.layout')
@section('pageCSS')
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
@endsection
@section('header_content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">{{ empty($row) == false ? 'Update Product' : 'Add New Product' }}</h1>
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
                    <h3 class="card-title">{{ empty($row) == false ? 'Update Product' : 'Add New Product' }}</h3>
                </div>
                <form method="post" id="addProduct" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="id"
                        value="{{ empty($row) == false ? $row->id : '' }}">
                    <div class="card-body col-sm-12 col-md-6">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">*Category</label>
                                    <select name="category_name" id="category_name" class="form-control select2">
                                        <option value="">Select Category</option>
                                        {!! $html !!}
                                    </select>
                                </div>
                                <span id="category_name_error"></span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">*Name</label>
                                    <select name="name" id="name" class="form-control select2 js-example-tags"
                                        onchange="getProductDetailFromStock(this.value);">
                                        <option value="{{ empty($row) == false ? $row->product_name : '' }}">
                                            {{ empty($row) == false ? $row->product_name : 'Select Product' }}</option>
                                        {!! $htmlProduct !!}
                                    </select>
                                </div>
                                <span id="name_error"></span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">Article Number</label>
                                    <input type="text" class="form-control" id="article_number" name="article_number"
                                        value="{{ empty($row) == false ? $row->product_article_number : '' }}">
                                </div>
                                <span id="article_number_error"></span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">*Final Price</label>
                                    <input type="text" class="form-control" id="vat_price" name="vat_price"
                                        onchange="getvatprice()" value="{{ empty($row) == false ? $row->vat_price : '' }}">
                                </div>
                                <span id="vat_price_error"></span>
                            </div>
                        </div>
                        <div class="clearfix"></div>


                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">*Vats</label><br>
                                    <input type="radio" id="vat0" name="vat" value="0"
                                        {{ empty($row) == false && $row->vat == '9' ? 'checked' : 'checked' }}
                                        onchange="getvatprice()">
                                    <label class="mr-2">0%</label>
                                    <input type="radio" id="vat9" name="vat" value="9"
                                        {{ empty($row) == false && $row->vat == '9' ? 'checked' : '' }}
                                        onchange="getvatprice()">
                                    <label class="mr-2">9%</label>
                                    <input type="radio" id="vat21" name="vat" value="21"
                                        {{ empty($row) == false && $row->vat == '21' ? 'checked' : '' }}
                                        onchange="getvatprice()">
                                    <label class="mr-2">21%</label>
                                </div>

                            </div>
                        </div>
                        <div class="clearfix"></div>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">*Price</label>
                                    <input type="text" class="form-control" id="price" name="price"
                                        value="{{ empty($row) == false ? $row->product_price : '' }}" readonly>
                                </div>
                                <span id="price_error"></span>
                            </div>
                        </div>
                        <div class="clearfix"></div>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">Description</label>
                                    <textarea type="text" class="form-control" id="description" name="description">{{ empty($row) == false ? $row->description : '' }}</textarea>
                                </div>
                                <span id="description_error"></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <label for="first_name">Alcohol</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="alcohol" name="alcohol"
                                        value="{{ empty($row) == false ? $row->alcohol : '' }}">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fas fa-percent "></i></span>
                                    </div>
                                </div>
                                <span id="alcohol_error"></span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">Product From</label>
                                    <select class="form-control" id="order_from" name="order_from">
                                        @foreach ($warehouse as $value)
                                            <option value="{{ $value->id }}"
                                                {{ empty($row) == false && $row->order_from == $value->id ? 'selected' : '' }}>
                                                {{ $value->wh_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <span id="order_from_error"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="Franchise">*Allergense</label>
                                    <select class="form-control select2" id="allergense" name="allergense[]"
                                        placeholder="Select Allergense" multiple="multiple" autocomplete="off"
                                        style="width:100% !important;">
                                        <option value="" id="selectvalue">Select Allergense</option>
                                        @foreach ($allergense as $value)
                                            <option value="{{ $value->id }}"
                                                {{ empty($row) == false && in_array($value->id, $allergense_array) ? 'selected' : '' }}>
                                                {{ $value->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <span id="allergense_error"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">*Alcoholic Items</label>
                                    <input type="text" class="form-control" id="alcoholic_items"
                                        name="alcoholic_items"
                                        value="{{ empty($row) == false ? $row->alcoholic_items : '1' }}">
                                </div>
                                <span id="price_error"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="Franchise">*Product Type</label>
                                    <select class="form-control" id="product_type_id" name="product_type_id">
                                        <option value="">Select Product Type</option>
                                        @foreach ($product_type_id as $value)
                                            <option value="{{ $value->id }}"
                                                {{ empty($row) == false && $row->product_type_id == $value->id ? 'selected' : '' }}>
                                                {{ $value->product_type }}</option>
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
                                        <input class="form-check-input" type="checkbox" id="is_popular"
                                            name="is_popular"
                                            {{ empty($row) == true ? 'checked' : (empty($row) == false && $row->is_popular == '1' ? 'checked' : '') }}>
                                        <label class="form-check-label"><b>Popular</b></label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_show" name="is_show"
                                            {{ empty($row) == true ? 'checked' : (empty($row) == false && $row->is_show == '1' ? 'checked' : '') }}>
                                        <label class="form-check-label"><b>Show (Do you want to show this Product in Web?)
                                            </b></label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- <div class="row">
                  <div class="col-sm-12">
                    <div class="form-group">
                       <label for="first_name">*Minimum stock</label>
                       <input type="text" class="form-control" id="min_stock" name="min_stock" value="{{ (empty($row)==false)?$row->min_stock:''}}">
                     </div>
                     <span id="min_stock_error"></span>
                   </div>
                </div>
                <div class="clearfix"></div>
                <div class="row">
                  <div class="col-sm-12">
                    <div class="form-group">
                       <label for="first_name">*Current  stock</label>
                       <input type="text" class="form-control" id="current_stock" name="current_stock" value="{{ (empty($row)==false)?$row->current_stock:''}}">
                     </div>
                     <span id="current_stock_error"></span>
                   </div>
                </div> --}}
                        <div class="clearfix"></div>
                        {{-- <div class="row">
                  <div class="col-sm-12">
                    <div class="form-group">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_reminder_set" name="is_reminder_set" {{ (empty($row)==false && $row->is_reminder_set=='1')?'checked':''}}>
                        <label class="form-check-label">Set Reminder</label>
                      </div>
                    </div>
                   </div>
                </div> --}}
                        <div class="clearfix"></div>
                        @if (empty($row) == false)
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Product Image</label>
                                        <input type="file" name="image_file" id="image_file" class="d-none"
                                            accept=".png, .jpg, .jpeg,.svg,.PNG,.JPG,.JPEG">
                                        <input type="hidden" name="old_cat_pic" id="old_cat_pic"
                                            value="{{ $row->image }}">
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
                                                {{-- <div class="card elevation-1 mb-3 " style="width:120px;" id="img">
                      <div class="d-flex align-self-center align-items-center px-2" style="height:120px;">
                        <img src="{{$row->image}}" style="max-height:120px;margin-left: auto;margin-right: auto;" class="card-img-top cart-item-img" alt="">
                      </div>
                      <button type="button" data-toggle="tooltip" data-placement="bottom" title="View full image" style="position: absolute;left: 2%;bottom: 2%;" class="btn btn-primary btn-sm previewImage">
                        <i class="fas fa-search-plus"></i>
                        </button>
                        <button type="button" data-toggle="tooltip" data-placement="bottom" title="Remove" style="position: absolute;right: 2%;bottom: 2%;" class="btn btn-danger btn-sm deleteImage" data-id="">
                          <i class="fas fa-trash-alt"></i>
                        </button>
                    </div> --}}
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
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Product Image</label>
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

                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary"><i
                                class="fas fa-save"></i>&nbsp;&nbsp;{{ empty($row) == false ? 'Update' : 'Save' }}</button>
                        <a href="{{ url('admin/product/list') }}" class="btn btn-secondary text-white"> <i
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
        function getvatprice() {

            var price = $('#vat_price').val();
            var vat = $("input[name='vat']:checked").val();
            vat_price = price * vat / 100;
            //alert(vat_price);
            Finalvat = parseFloat(price) - parseFloat(vat_price);
            $('#price').val(parseFloat(Finalvat).toFixed(2));
        }

        function getProductDetailFromStock(_description) {
            $.ajax({
                url: SITE_URL + 'getProductDetailFromStock',
                type: 'POST',
                data: '_description=' + _description + '&_token=' + $('meta[name=csrf-token]').attr('content'),
                success: function(obj) {
                    if (obj.status == true) {
                        $('#article_number').val(obj.data._articleNumber);
                        $('#price').val(obj.data._price);
                        $('#description').val(obj.data._description);
                        $('#vat_price').val(obj.data._price);
                        $('#alcohol').val(obj.data._alcohol);
                        loader_hide();

                    } else {
                        $.alert('Something went wrong');
                    }
                }
            });
        }
        $(".js-example-tags").select2({
            tags: true
        });

        $(document).ready(function() {
            $('.select2').select2();
        })
        $(document).ready(function() {
            $(".js-example-tags").select2({
                tags: true
            });
        })
        $(document).on('submit', '#addProduct', function(e) {
            e.preventDefault();
            loader_show();
            var data = new FormData(this);
            if (images.length > 0) {
                for (key in images) {
                    data.append('image_file', images[key]);
                }
            }
            $('#addProduct .is-invalid').removeClass('is-invalid');
            $('#addProduct .text-danger').remove();
            $.ajax({
                url: SITE_URL + 'admin/product/add',
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
                        messageAlert('Success', obj.msg, 'fa-check', 'success')
                        $('#addProduct')[0].reset();
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
