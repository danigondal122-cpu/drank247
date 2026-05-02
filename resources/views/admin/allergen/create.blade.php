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
                    <h1 class="m-0 text-dark">{{ empty($row) == false ? 'Update Allergen' : 'Add New Allergen' }}</h1>
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
                    <h3 class="card-title">{{ empty($row) == false ? 'Update Allergen' : 'Add New Allergen' }}</h3>
                </div>
                <form method="post" id="addAllergen" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="id" value="{{ empty($row) == false ? $row->id : '' }}">
                    <div class="card-body col-sm-12 col-md-6">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">*Name</label>
                                    <input type="text" class="form-control" name="name" id="name"
                                        value="{{ empty($row) == false ? $row->name : '' }}">
                                </div>
                                <span id="name_error"></span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary"><i
                                class="fas fa-save"></i>&nbsp;&nbsp;{{ empty($row) == false ? 'Update' : 'Save' }}</button>
                        <a href="{{ url('admin/allergen/list') }}" class="btn btn-secondary text-white"> <i
                                class="fas fa-arrow-left"></i>&nbsp;&nbsp;Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('pageJS')
    <script>
        $(document).on('submit', '#addAllergen', function(e) {
            e.preventDefault();
            loader_show();
            var data = new FormData(this);
            $('#addAllergen .is-invalid').removeClass('is-invalid');
            $('#addAllergen .text-danger').remove();

            $.ajax({
                url: SITE_URL + 'admin/allergen/add',
                type: 'POST',
                data: data,
                success: function(response) {
                    loader_hide();
                    if (!response.status && response.type === 'validation') {
                        for (const key in response.errors) {
                            $('#' + key).addClass('is-invalid');
                            $('#' + key + '_error').after('<p class="text-danger">' + response.errors[
                                key] + '</p>');
                        }
                    } else if (response.status) {
                        messageAlert('Success', response.msg, 'fa-check', 'success');
                        $('#addAllergen')[0].reset();
                        setTimeout(function() {
                            window.location = SITE_URL + response.page;
                        }, 1500);
                    }
                },
                cache: false,
                contentType: false,
                processData: false
            });
        });
    </script>
@endsection
