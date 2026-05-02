@extends('customerservice.layout.layout')
@section('pageCSS')
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/jquery-ui/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/timepicker/bootstrap-timepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker-bs3.css') }}">
@endsection
@section('header_content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">{{ empty($row) == false ? 'Update Hours' : 'Add Hours' }}</h1>
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
                    <h3 class="card-title">{{ empty($row) == false ? 'Update Hours' : 'Add Hours' }}</h3>
                </div>
                <form method="post" id="addHours">
                    @csrf

                    <input type="hidden" name="id" id="id" value="{{ empty($row) == false ? $row->id : '' }}">
                    <div class="card-body col-sm-12 col-md-6">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">*Start Date</label>
                                    <input type="text" class="form-control" id="start_date" name="start_date"
                                        value="{{ empty($row) == false ? $row->start_date : '' }}" autocomplete="off">
                                </div>
                                <span id="start_date_error"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">*End Date</label>
                                    <input type="text" class="form-control" id="end_date" name="end_date"
                                        value="{{ empty($row) == false ? $row->end_date : '' }}" autocomplete="off">
                                </div>
                                <span id="end_date_error"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="bootstrap-timepicker">
                                    <div class="form-group">
                                        <label for="first_name">*Start Time</label>
                                        <div class="input-group input-group-lg">
                                            <input type="text" id="start_time" name="start_time"
                                                value="{{ empty($row) == false ? $row->start_time : '' }}"
                                                class="form-control timepicker">
                                            <div class="input-group-addon">
                                                <span class="glyphicon glyphicon-time"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <span id="start_time_error"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="bootstrap-timepicker">
                                    <div class="form-group">
                                        <label for="first_name">*End Time</label>
                                        <div class="input-group input-group-lg">
                                            <input type="text" id="end_time" name="end_time"
                                                value="{{ empty($row) == false ? $row->end_time : '' }}"
                                                class="form-control timepicker">

                                            <div class="input-group-addon">
                                                <span class="glyphicon glyphicon-time"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <span id="end_time_error"></span>
                                </div>
                            </div>
                        </div>


                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary"><i
                                class="fas fa-save"></i>&nbsp;&nbsp;{{ empty($row) == false ? 'Update' : 'Save' }}</button>
                        <a href="{{ url('customer_service/hours/list') }}" class="btn btn-secondary text-white"> <i
                                class="fas fa-arrow-left"></i>&nbsp;&nbsp;Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('pageJS')
    <script src="{{ asset('plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('plugins/daterangepicker/moment.min.js') }}"></script>
    <script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>

    <script>
        $(function() {
            $("#start_date,#end_date").datepicker({
                dateFormat: 'dd-mm-yy', //check change
                todayHighlight: true,
                autoclose: true
            });
        });
        $(function() {
            $('.timepicker').timepicker({
                showInputs: false,
                showMeridian: false
            })
        });
    </script>

    <script src="{{ asset('plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('js/page/image_upload.js') }}"></script>
    <script>
        $(document).on('submit', '#addHours', function(e) {

            e.preventDefault();
            loader_show();
            var data = new FormData(this);

            $('#addHours .is-invalid').removeClass('is-invalid');
            $('#addHours .text-danger').remove();
            $.ajax({
                url: SITE_URL + 'customer_service/hours/add',
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
                        $('#addHours')[0].reset();
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
