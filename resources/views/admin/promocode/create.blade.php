@extends('admin.layout.layout')
@section('pageCSS')
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bootstrap-datepicker/css/bootstrap-datepicker.css') }}">
@endsection
@section('header_content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">{{ empty($row) == false ? 'Update Promo Code' : 'Add New Promo Code' }}</h1>
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
                    <h3 class="card-title">{{ empty($row) == false ? 'Update Promo Code' : 'Add New Promo Code' }}</h3>
                </div>
                <form method="post" id="addPromoCode" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="id" value="{{ empty($row) == false ? $row->id : '' }}">
                    <div class="card-body col-sm-12 col-md-6">
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">*Code</label>
                                    <input type="text" class="form-control" id="code_text" name="code_text"
                                        value="{{ empty($row) == false ? $row->code_text : '' }}">
                                </div>
                                <span id="code_text_error"></span>
                            </div>
                        </div>
                        <div class="clearfix"></div>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">*Discount</label><br>
                                    <input type="radio" id="flat" name="discount_type" value="0"
                                        {{ empty($row) == false && $row->discount_type == '0' ? 'checked' : 'checked' }}
                                        onchange="getDiscountFeild()">
                                    <label class="mr-2"> Flat amount(€)</label>
                                    <input type="radio" id="per" name="discount_type" value="1"
                                        {{ empty($row) == false && $row->discount_type == '1' ? 'checked' : '' }}
                                        onchange="getDiscountFeild()">
                                    <label class="mr-2">Percentage(%)</label>
                                </div>

                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <input type="text" class="form-control" id="discount" name="discount"
                                        placeholder="{{ empty($row) == true || $row->discount_type == '0' ? '*Please enter Flat Amount(€)' : '*Please enter Amount in (%)' }}"
                                        value="{{ empty($row) == false ? $row->discount : '' }}">
                                </div>
                                <span id="discount_error"></span>
                            </div>
                        </div>

                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">*Limitation type</label><br>
                                    <input type="radio" id="unlimited" name="limitation_type" value="0"
                                        {{ empty($row) == false && $row->limitation_type == '0' ? 'checked' : 'checked' }}
                                        onchange="getMaxUserFeild()">
                                    <label class="mr-2">Unlimited</label>
                                    <input type="radio" id="maxuser" name="limitation_type" value="1"
                                        {{ empty($row) == false && $row->limitation_type == '1' ? 'checked' : '' }}
                                        onchange="getMaxUserFeild()">
                                    <label class="mr-2">Max. no of users</label>
                                </div>

                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-sm-12" id="maxuserdiv"
                                style="display:{{ empty($row) == true || $row->limitation_type == '0' ? 'none' : 'block' }};">
                                <div class="form-group">
                                    {{-- <label for="first_name">*Max.No of User</label> --}}
                                    <input type="text" class="form-control" id="maxusers" name="maxusers"
                                        placeholder="*Please enter Max Users"
                                        value="{{ empty($row) == false ? $row->max_users : '' }}">
                                </div>
                                <span id="maxusers_error"></span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">*Maximum use per user</label>
                                    <input type="text" class="form-control" id="maxperusers" name="maxperusers"
                                        placeholder="*Please enter Max Use Per user"
                                        value="{{ empty($row) == false ? $row->max_per_user : '' }}">
                                </div>
                                <span id="maxperusers_error"></span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">*Expiration type</label><br>
                                    <input type="radio" id="forever" name="expiration_type" value="0"
                                        {{ empty($row) == false && $row->expiration_type == '0' ? 'checked' : 'checked' }}
                                        onchange="getDate()">
                                    <label class="mr-2">No Expiry date/ Forever</label>
                                    <input type="radio" id="specifyenddate" name="expiration_type" value="1"
                                        {{ empty($row) == false && $row->expiration_type == '1' ? 'checked' : '' }}
                                        onchange="getDate()">
                                    <label class="mr-2">Specify end date</label>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">*Start Date</label>
                                    <input type="text" class="form-control datepicker" id="start_date"
                                        name="start_date" placeholder="*Please enter Start Date"
                                        value="{{ empty($row) == false ? $row->starttime : '' }}">
                                </div>
                                <span id="start_date_error"></span>
                            </div>
                        </div>
                        <div class="clearfix"></div>

                        <div class="row">
                            <div class="col-sm-12" id="enddatediv"
                                style="display:{{ empty($row) == true || $row->expiration_type == '0' ? 'none' : 'block' }};">
                                <div class="form-group">
                                    <label for="first_name">*End Date</label>
                                    <input type="text" class="form-control datepicker" id="end_date" name="end_date"
                                        placeholder ="*Please enter End Date"
                                        value="{{ empty($row) == false ? $row->endtime : '' }}">
                                </div>
                                <span id="end_date_error"></span>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary"><i
                                class="fas fa-save"></i>&nbsp;&nbsp;{{ empty($row) == false ? 'Update' : 'Save' }}</button>
                        <a href="{{ url('admin/promocode/list') }}" class="btn btn-secondary text-white"> <i
                                class="fas fa-arrow-left"></i>&nbsp;&nbsp;Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('pageJS')
    <script src="{{ asset('plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap-datepicker/js/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('js/page/promocode.js') }}"></script>
    <script>
        function getDiscountFeild() {

            var discount_type = $("input[name='discount_type']:checked").val();
            if (discount_type == 1) {
                $("#discount").attr('placeholder', "*Please enter Amount in (%)");
            } else {
                $("#discount").attr('placeholder', "*Please enter Flat Amount(€)");
            }
        }

        function getMaxUserFeild() {

            var limitation_type = $("input[name='limitation_type']:checked").val();
            if (limitation_type == 1) {
                $("#maxuserdiv").show();
            } else {
                $("#maxuserdiv").hide();
            }
        }

        function getDate() {

            var expiration_type = $("input[name='expiration_type']:checked").val();

            if (expiration_type == 1) {
                $("#enddatediv").show();
                $("#end_date").val('');
            } else {
                $("#enddatediv").hide();
            }
        }
        $(function() {
            $(".datepicker").datepicker({
                format: "dd-mm-yyyy",
                todayHighlight: true,
                autoclose: true
            });
        });

        $('#addPromoCode').on('submit', function(e) {
            e.preventDefault();
            loader_show();
            let formData = new FormData(this)
            $('#addPromoCode .is-invalid').removeClass('is-invalid');
            $('#addPromoCode .text-danger').remove();
            $.ajax({
                url: SITE_URL + 'admin/promocode/add',
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
                        $('#addPromoCode')[0].reset();
                        setTimeout(function() {
                            window.location = SITE_URL + obj.page;
                        }, 1500)
                    }
                },

            })
        })
    </script>
@endsection
