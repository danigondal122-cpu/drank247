@extends('admin.layout.layout')
@section('pageCSS')
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/jquery-ui/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/timepicker/bootstrap-timepicker.min.css') }}">

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
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">{{ empty($row) == false ? 'Update Franchise' : 'Add New Franchise' }}</h1>
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
                    <h3 class="card-title">{{ empty($row) == false ? 'Update Franchise' : 'Add New Franchise' }}</h3>
                </div>
                <form method="post" id="addFranchise">
                    @csrf
                    <input type="hidden" name="id" id="id" value="{{ empty($row) == false ? $row->id : '' }}">
                    <div class="card-body col-sm-12 col-md-12">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="first_name">*Franchise Name</label>
                                    <input type="text" class="form-control" id="franchise_name" name="franchise_name"
                                        value="{{ empty($row) == false ? $row->franchises_name : '' }}">
                                </div>
                                <span id="franchise_name_error"></span>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="first_name">*Franchise Email</label>
                                    <input type="text" class="form-control" id="franchise_email" name="franchise_email"
                                        value="{{ empty($row) == false ? $row->franchises_email : '' }}">
                                </div>
                                <span id="franchise_email_error"></span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="first_name">*Username</label>
                                    <input type="text" class="form-control" id="franchise_username"
                                        name="franchise_username"
                                        value="{{ empty($row) == false ? $row->franchises_username : '' }}">
                                </div>
                                <span id="franchise_username_error"></span>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="franchise_number">Franchise number</label>
                                    <input type="text" class="form-control" id="franchise_number" name="franchise_number"
                                        value="{{ empty($row) == false ? $row->franchise_number : '' }}">
                                </div>
                                <span id="franchise_number_error"></span>
                            </div>
                        </div>
                        <div class="clearfix"></div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="first_name">*Firstname</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name"
                                        value="{{ empty($row) == false ? $row->first_name : '' }}">
                                </div>
                                <span id="first_name_error"></span>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="first_name">*Lastname</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name"
                                        value="{{ empty($row) == false ? $row->last_name : '' }}">
                                </div>
                                <span id="last_name_error"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="first_name">Mobile no</label>
                                    <input type="text" class="form-control" id="mobile_no" name="mobile_no"
                                        value="{{ empty($row) == false ? $row->mobile_no : '' }}">
                                </div>
                                <span id="mobile_no_error"></span>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="first_name">Date of Birth</label>
                                    <input type="text" class="form-control datepicker" id="date_of_birth"
                                        name="date_of_birth"
                                        value="{{ empty($row) == false ? date('d-m-Y', strtotime($row->date_of_birth)) : '' }}">
                                </div>
                                <span id="date_of_birth_error"></span>
                            </div>
                        </div>
                        <hr class="mb-1">
                        <h5>Company Details</h5>
                        <hr class="mt-1">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="first_name">Company Name</label>
                                    <input type="text" class="form-control" id="company_name" name="company_name"
                                        value="{{ empty($row) == false ? $row->company_name : '' }}">
                                </div>
                                <span id="company_name_error"></span>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="first_name">*House No/Street No</label>
                                    <input type="text" class="form-control" id="house_no_street"
                                        name="house_no_street"
                                        value="{{ empty($row) == false ? $row->house_no_street : '' }}">
                                </div>
                                <span id="house_no_street_error"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="first_name">Block No</label>
                                    <input type="text" class="form-control" id="block_no" name="block_no"
                                        value="{{ empty($row) == false ? $row->block_no : '' }}">
                                </div>
                                <span id="block_no_error"></span>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="first_name">Residence</label>
                                    <input type="text" class="form-control" id="residence" name="residence"
                                        value="{{ empty($row) == false ? $row->residence : '' }}">
                                </div>
                                <span id="residence_error"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="first_name">Landmark</label>
                                    <input type="text" class="form-control" id="landmark" name="landmark"
                                        value="{{ empty($row) == false ? $row->landmark : '' }}">
                                </div>
                                <span id="landmark_error"></span>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="first_name">*Postcode</label>
                                    <input type="text" class="form-control" id="post_code" name="post_code"
                                        value="{{ empty($row) == false ? $row->post_code : '' }}">
                                </div>
                                <span id="post_code_error"></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="city">City</label>
                                    <input type="text" class="form-control" id="city" name="city"
                                        value="{{ empty($row) == false ? $row->city : '' }}">
                                </div>
                                <span id="city_error"></span>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="country">Country</label>
                                    <input type="text" class="form-control" id="country" name="country"
                                        value="{{ empty($row) == false ? $row->country : '' }}">
                                </div>
                                <span id="country_error"></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="first_name">IBAN No.</label>
                                    <input type="text" class="form-control" id="bank_account" name="bank_account"
                                        value="{{ empty($row) == false ? $row->bank_account : '' }}">
                                </div>
                                <span id="bank_account_error"></span>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="first_name">Start From Date</label>
                                    <input type="text" class="form-control datepicker" id="start_from_date"
                                        name="start_from_date"
                                        value="{{ empty($row) == false ? date('d-m-Y', strtotime($row->start_from_date)) : '' }}">
                                </div>
                                <span id="start_from_date_error"></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="first_name">*Per day charges(€)</label>
                                    <input type="text" class="form-control" id="per_day_charges"
                                        name="per_day_charges"
                                        value="{{ empty($row) == false ? $row->per_day_charges : '' }}">
                                </div>
                                <span id="per_day_charges_error"></span>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="first_name">*Royalty(%)</label>
                                    <input type="text" class="form-control" id="royalty" name="royalty"
                                        value="{{ empty($row) == false ? $row->royalty : '' }}">
                                </div>
                                <span id="royalty_error"></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="Franchise">*Franchise Pool</label>
                                    <select class="form-control select2" id="franchise_pool" name="franchise_pool[]"
                                        multiple="multiple" autocomplete="off" style="width:100%">
                                        <option value="selectvalue" id="selectvalue">Select Pool</option>
                                        @foreach ($pool as $value)
                                            <option value="{{ $value->id }}"
                                                {{ empty($row) == false && in_array($value->id, $poolarray) ? 'selected' : '' }}>
                                                {{ '(' . $value->from_postcode . '-' . $value->to_postcode . ')  ' . $value->area }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <span id="franchise_pool_error"></span>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>&nbsp;&nbsp;{{ empty($row) == false ? 'Update' : 'Save' }}
                        </button>
                        <a href="{{ url('admin/franchise/list') }}" class="btn btn-secondary text-white"> <i
                                class="fas fa-arrow-left"></i>&nbsp;&nbsp;Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('pageJS')
    <script src="{{ asset('plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('plugins/daterangepicker/moment.min.js') }}"></script>
    <script src="{{ asset('plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#franchise_pool option[value="selectvalue"]').attr("disabled", true);
            $(".select2").select2({
                closeOnSelect: false,
                allowHtml: true,
                allowClear: true,
                tags: true // создает новые опции на лету
            });
        });
        $(".datepicker").datepicker({
            dateFormat: 'yy-mm-dd', //check change
            todayHighlight: true,
            autoclose: true
        });
        $(document).ready(function() {
            $('.select2').select2();
        })
        $(document).on('submit', '#addFranchise', function(e) {
            e.preventDefault();
            loader_show();
            var data = new FormData(this);
            $('#addFranchise .is-invalid').removeClass('is-invalid');
            $('#addFranchise .text-danger').remove();
            $.ajax({
                url: SITE_URL + 'admin/franchise/add',
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
                        $('#addFranchise')[0].reset();
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
