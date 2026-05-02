@extends('customerservice.layout.layout')
@section('pageCSS')
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
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
                    <input type="hidden" name="franchise_id" id="franchise_id"
                        value="{{ empty($row) == false ? $row->id : '' }}">
                    <div class="card-body col-sm-12 col-md-12">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="first_name">*Franchise Name</label>
                                    <input type="text" class="form-control" id="franchises_name" name="franchises_name"
                                        value="{{ empty($row) == false ? $row->franchises_name : '' }}">
                                </div>
                                <span id="franchises_name_error"></span>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="first_name">*Franchise Email</label>
                                    <input type="text" class="form-control" id="franchises_email" name="franchises_email"
                                        value="{{ empty($row) == false ? $row->franchises_email : '' }}">
                                </div>
                                <span id="franchises_email_error"></span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="first_name">*Username</label>
                                    <input type="text" class="form-control" id="franchises_username"
                                        name="franchises_username"
                                        value="{{ empty($row) == false ? $row->franchises_username : '' }}">
                                </div>
                                <span id="franchises_username_error"></span>
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
                                    <input type="text" class="form-control" id="date_of_birth" name="date_of_birth"
                                        value="{{ empty($row) == false ? $row->date_of_birth : '' }}">
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
                                    <label for="first_name">IBAN No.</label>
                                    <input type="text" class="form-control" id="bank_account" name="bank_account"
                                        value="{{ empty($row) == false ? $row->bank_account : '' }}">
                                </div>
                                <span id="bank_account_error"></span>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="Franchise">Franchise Pool</label>
                                    <br>
                                    <select class="form-control select2" id="franchise_pool" name="franchise_pool[]"
                                        multiple="multiple" autocomplete="off" style="width:100%">
                                        <option value="" id="selectvalue">Select Pool</option>
                                        @foreach ($pool as $value)
                                            <option value="{{ $value->id }}"
                                                {{ empty($row) == false && in_array($value->id, $poolarray) ? 'selected' : '' }}>
                                                {{ $value->area }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <span id="franchise_pool_error"></span>
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
                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>&nbsp;&nbsp;{{ empty($row) == false ? 'Update' : 'Save' }}
                        </button>
                        <a href="{{ url('customer_service/franchise/list') }}" class="btn btn-secondary text-white"> <i
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
        $(document).ready(function(){
            $('.select2').select2();
        })
        $(document).on('submit', '#addFranchise', function (e) {
            e.preventDefault();
            loader_show();
            var data = new FormData(this);
            $('#addFranchise .is-invalid').removeClass('is-invalid');
            $('#addFranchise .text-danger').remove();
            $.ajax({
                url: SITE_URL + 'customer_service/franchise/add',
                type: 'POST',
                data: data,
                success: function (obj) {
                if (!obj.status && obj.type == 'validation') {
                    loader_hide();
                    for (key in obj.errors) {
                    $('#' + key).addClass('is-invalid');
                    $('#' + key+'_error').after('<p class="text-danger">' + obj.errors[key] + '</p>');
                    }
                }
                if (obj.status) {
                    loader_hide();
                    messageAlert('Success',obj.msg,'fa-check','success')
                    $('#addFranchise')[0].reset();
                    setTimeout(function () {
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
