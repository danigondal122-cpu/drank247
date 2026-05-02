@extends('admin.layout.layout')
@section('pageCSS')
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
@endsection
@section('header_content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">{{ empty($row) == false ? 'Update Stock' : 'Add New Stock' }}</h1>
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
                    <h3 class="card-title">{{ empty($row) == false ? 'Update Stock' : 'Add New Stock' }}</h3>
                </div>
                <form method="post" id="addStock">
                    @csrf
                    <input type="hidden" name="id" id="id" value="{{ empty($row) == false ? $row->id : '' }}">
                    <div class="card-body col-sm-12 col-md-6">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">*Product</label>
                                    <select name="product_name" id="product_name" class="form-control select2"
                                        onchange="getCategory(this.value);">
                                        <option value="">Select Product</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}"
                                                {{ empty($row) == false && $product->id == $row->product_id ? 'selected' : '' }}>
                                                {{ $product->product_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <span id="product_name_error"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">*Franchise</label>
                                    <select name="franchise_name" id="franchise_name" class="form-control select2">
                                        <option value="">Select Franchise</option>
                                        @foreach ($franchisee as $franchisees)
                                            <option value="{{ $franchisees->id }}"
                                                {{ empty($row) == false && $franchisees->id == $row->franchise_id ? 'selected' : '' }}>
                                                {{ $franchisees->franchises_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <span id="franchise_name_error"></span>
                            </div>
                        </div>
                        {{-- <div class="row">
                 <div class="col-sm-12">
                   <div class="form-group">
                      <label for="first_name">*Category</label>
                      <select name="category_name" id="category_name" class="form-control select2">
                        <option value="">Select Category</option>
                        @foreach ($categories as $category)
                        <option value="{{$category->category_id}}" {{ (empty($row)==false && ($category->category_id==$row->stock_category))?'selected':''}}>{{$category->category_name}}</option>   
                        @endforeach
                      </select>
                    </div>
                    <span id="category_name_error"></span>
                  </div>
                </div> --}}
                        <div class="clearfix"></div>
                        {{-- <div class="row">
                  <div class="col-sm-12">
                    <div class="form-group">
                       <label for="first_name">*Price</label>
                       <input type="text" class="form-control" id="price" name="price" value="{{ (empty($row)==false)?$row->stock_price:''}}">
                     </div>
                     <span id="price_error"></span>
                   </div>
                </div> --}}
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">*Current Stock</label>
                                    <input type="text" class="form-control" id="current_stock" name="current_stock"
                                        value="{{ empty($row) == false ? $row->stock_current : '' }}">
                                </div>
                                <span id="current_stock_error"></span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">*Minimum stock</label>
                                    <input type="text" class="form-control" id="min_stock" name="min_stock"
                                        value="{{ empty($row) == false ? $row->stock_minimum : '' }}">
                                </div>
                                <span id="min_stock_error"></span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_reminder_set"
                                            name="is_reminder_set"
                                            {{ empty($row) == false && $row->is_reminder_set == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label">Set Reminder</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary"><i
                                class="fas fa-save"></i>&nbsp;&nbsp;{{ empty($row) == false ? 'Update' : 'Save' }}</button>
                        <a href="{{ url('admin/stock/list') }}" class="btn btn-secondary text-white"> <i
                                class="fas fa-arrow-left"></i>&nbsp;&nbsp;Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('pageJS')
    <script src="{{ asset('plugins/select2/js/select2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2();
        })
        $(document).on('submit', '#addStock', function(e) {
            e.preventDefault();
            loader_show();
            var data = new FormData(this);
            $('#addStock .is-invalid').removeClass('is-invalid');
            $('#addStock .text-danger').remove();
            $.ajax({
                url: SITE_URL + 'admin/stock/add',
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
                    }
                    if (obj.status) {
                        loader_hide();
                        messageAlert('Success', obj.msg, 'fa-check', 'success')
                        $('#addStock')[0].reset();
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

        function getCategory(id) {
            $.ajax({
                url: SITE_URL + 'admin/stock/getCategory',
                type: 'POST',
                data: 'id=' + id + '&_token=' + $('meta[name=csrf-token]').attr('content'),
                success: function(obj) {
                    $("#category_name").val(obj.category_id).trigger('change');
                }
            });

        }
    </script>
@endsection
