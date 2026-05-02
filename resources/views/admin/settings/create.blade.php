@extends('admin.layout.layout')
@section('header_content')
    <div class="content-header">
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Settings</h3>
                </div>
                <form method="post" id="updateSettings" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="category_id" id="category_id" value="">
                    <div class="card-body col-sm-12 col-md-6">
                        <div class="row">
                            <div class="col-11">
                                <div class="form-group">
                                    <label for="first_name">*Email</label>
                                    <input type="text" class="form-control" id="email" name="email"
                                        value="{{ $row->email }}">
                                </div>
                            </div>
                            <div class="col-1" style="margin-top:28px">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" style="vertical-align:center" type="checkbox"
                                            id="email_show" name="email_show"
                                            {{ empty($row) || $row->email_show == '1' ? 'checked' : '' }}>

                                        <label class="form-check-label"><b></b></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">*Address</label>
                                    <input type="text" class="form-control" id="address" name="address"
                                        value="{{ $row->address }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="first_name">*Contact No</label>
                                    <input type="text" class="form-control" id="contact_no" name="contact_no"
                                        value="{{ $row->contact_no }}">
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>


                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
                        {{-- <a href="{{ url('admin/category/list') }}" class="btn btn-secondary text-white"> <i class="fas fa-arrow-left"></i>&nbsp;&nbsp;Back</a> --}}
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('pageJS')
    <script>
        $("#updateSettings").on("submit", function(e) {
            e.preventDefault();
            loader_show();
            let formData = new FormData(this);
            $("#updateSettings .is-invalid").removeClass("is-invalid");
            $("#updateSettings .text-danger").remove();
            $.ajax({
                url: SITE_URL + "admin/settings",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(obj) {
                    if (!obj.status && obj.type == "validation") {
                        loader_hide();
                        for (key in obj.errors) {
                            $("#" + key).addClass("is-invalid");
                            $("#" + key).after(
                                '<p class="text-danger">' + obj.errors[key] + "</p>"
                            );
                        }
                    }
                    if (obj.status) {
                        loader_hide();
                        messageAlert("Success", obj.msg, "fa-check", "success");
                        $("#updateSettings")[0].reset();
                        setTimeout(function() {
                            window.location = SITE_URL + obj.page;
                        }, 1500);
                    }
                },
            });
        });
    </script>
@endsection
