@extends('admin.layout.layout')
@section('header_content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">{{ empty($row) == false ? 'Assign Extra Product' : 'Assign Extra Product' }}
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
                    <h3 class="card-title">{{ empty($row) == false ? 'Assign Extra Product' : 'Assign Extra Product' }}</h3>
                </div>
                <form method="post" id="assignProduct" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="category_id" id="category_id" value="{{ $main_category->id }}">
                    <div class="card-body col-sm-12 col-md-12 table-responsive ">
                        <table class="table table-striped table-valign-middle ">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Image</th>
                                    <th>Product</th>
                                    <th>Article Number</th>

                                </tr>
                            </thead>
                            <tbody>
                                @if (count($product) > 0)
                                    @foreach ($product as $key => $value)
                                        <tr>
                                            <td class="text-center" style="vertical-align: baseline;">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        data-id="{{ $value['id'] }}" value="{{ $value['id'] }}"
                                                        name="extra_product"
                                                        {{ in_array($value['id'], $assigned_category) ? 'checked' : '' }}>
                                                    {{-- <label class="form-check-label"><b>{{$value['product_name']}} </b></label> --}}
                                                </div>
                                            </td>
                                            <td>
                                                <img src="{{ $value['image'] }}" alt=""
                                                    class="img-circle img-size-32 mr-2">
                                            </td>
                                            <td>{{ $value['product_name'] }}</td>
                                            <td>{{ $value['product_article_number'] }}</td>

                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4">- No Record Found</td>
                                    </tr>
                                @endif

                            </tbody>
                        </table>

                        {{-- @foreach ($product as $key => $value)

                <div class="row">
                  <div class="col-sm-12">
                    <div class="form-group">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" data-id="{{$value['product_id']}}"  value="{{$value['product_id']}}" name="extra_product" {{in_array($value['product_id'],$assigned_category) ? 'checked': '' }}>
                        <label class="form-check-label"><b>{{$value['product_name']}} </b></label>
                      </div>
                    </div>
                  </div>
                  </div>
              @endforeach --}}
                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary"
                            style="display:{{ count($product) > 0 ? '' : 'none' }}"><i
                                class="fas fa-save"></i>&nbsp;&nbsp;{{ empty($row) == false ? 'Update' : 'Save' }}</button>
                        <a href="{{ $main_category->category_id ? url('admin/category/subcategorylist/' . $main_category->category_id) : url('admin/category/subcategorylist/' . $main_category->id) }}"
                            class="btn btn-secondary text-white"> <i class="fas fa-arrow-left"></i>&nbsp;&nbsp;Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('pageJS')
    <script>
        $('#assignProduct').on('submit', function(e) {
            e.preventDefault();
            loader_show();
            let formData = new FormData(this);
            var extra_product = [];
            $("input:checkbox[name=extra_product]:checked").each(function() {
                extra_product.push($(this).val());
            });
            formData.append('extra_productarray', extra_product);
            $('#assignProduct .is-invalid').removeClass('is-invalid');
            $('#assignProduct .text-danger').remove();
            $.ajax({
                url: SITE_URL + 'admin/category/assignProductSave',
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
                        $('#assignProduct')[0].reset();
                        setTimeout(function() {
                            window.location = SITE_URL + obj.page;
                        }, 1500)
                    }
                },

            })
        });
    </script>
@endsection
