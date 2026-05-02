@extends('admin.layout.layout')
@section('pageCSS')
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
@endsection
@section('header_content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">{{ empty($row) == false ? 'Update Pool' : 'Add New Pool' }}</h1>
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
                    <h3 class="card-title">{{ empty($row) == false ? 'Update Pool' : 'Add New Pool' }}</h3>
                </div>
                <form method="post" id="addPool">
                    @csrf
                    <input type="hidden" name="id" id="id" value="{{ empty($row) == false ? $row->id : '' }}">
                    <div class="card-body col-sm-12 col-md-6">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">*From PostCode</label>
                                    <input type="text" class="form-control" id="from_postcode" name="from_postcode"
                                        value="{{ empty($row) == false ? $row->from_postcode : '' }}">
                                </div>
                                <span id="from_postcode_error"></span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">*To PostCode</label>
                                    <input type="text" class="form-control" id="to_postcode" name="to_postcode"
                                        value="{{ empty($row) == false ? $row->to_postcode : '' }}">
                                </div>
                                <span id="to_postcode_error"></span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">*Area</label>
                                    <input type="text" class="form-control" id="area" name="area"
                                        value="{{ empty($row) == false ? $row->area : '' }}">
                                </div>
                                <span id="area_error"></span>
                            </div>
                        </div>

                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">*Delivery Charge (€)</label>
                                    <input type="text" class="form-control" id="delivery_charge" name="delivery_charge"
                                        value="{{ empty($row) == false ? $row->delivery_charge : '2.50' }}">
                                </div>
                                <span id="delivery_charge_error"></span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">*Delivery start from (€)</label>
                                    <input type="text" class="form-control" id="delivery_start_from"
                                        name="delivery_start_from"
                                        value="{{ empty($row) == false ? $row->delivery_start_from : '22.50' }}">
                                </div>
                                <span id="delivery_start_from_error"></span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">*Free from (€)</label>
                                    <input type="text" class="form-control" id="delivery_free_from"
                                        name="delivery_free_from"
                                        value="{{ empty($row) == false ? $row->delivery_free_from : '75' }}">
                                </div>
                                <span id="delivery_free_from_error"></span>
                            </div>
                        </div>

                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary"><i
                                class="fas fa-save"></i>&nbsp;&nbsp;{{ empty($row) == false ? 'Update' : 'Save' }}</button>
                        <a href="{{ url('admin/pool/list') }}" class="btn btn-secondary text-white"> <i
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
        $(document).on('submit', '#addPool', function(e) {
            e.preventDefault();
            loader_show();
            var data = new FormData(this);
            $('#addPool .is-invalid').removeClass('is-invalid');
            $('#addPool .text-danger').remove();
            $.ajax({
                url: SITE_URL + 'admin/pool/add',
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
                        $('#addPool')[0].reset();
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
