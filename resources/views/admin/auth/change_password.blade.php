@extends('admin.layout.layout')
@section('header_content')
    <div class="content-header">
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Change Password</h3>
                </div>
                <form name="change_password_frm" id="change_password_frm" class="col-md-6">
                    @csrf
                    <div class="card-body">
                        <div class="edit-field-profile">
                            <label for="current_password">Current Password</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" placeholder="Enter current password">
                            <p class="error" id="current_password_error"></p>
                        </div>
                        <div class="edit-field-profile">
                            <label for="new_password">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Enter new password">
                            <p class="error" id="new_password_error"></p>
                        </div>
                        <div class="edit-field-profile">
                            <label for="re_password">Confirm Password</label>
                            <input type="password" class="form-control" id="re_password" name="re_password" placeholder="Enter confirm password">
                            <p class="error" id="re_password_error"></p>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
                <!-- /.card-body -->
            </div>
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
@endsection
@section('pageJS')
    <script>
        // Change password
        $("#change_password_frm").submit(function(e) {
            $('#change_password_frm .is-invalid').removeClass('is-invalid');
            $('#change_password_frm .text-danger').remove();
            let fromData = $("#change_password_frm").serialize();
            loader_show();
            $.ajax({
                url: SITE_URL + 'admin/change-password',
                type: 'put',
                data: fromData,
                success: function(obj) {
                    if (obj.status == false && obj.type == 'VALIDATION') {
                        loader_hide();
                        $('.error').text('');
                        $('.form-control').removeClass('is-invalid');
                        for (key in obj.errors) {
                            $('#' + key).addClass('is-invalid');
                            $('#' + key).after('<p class="text-danger">' + obj.errors[key] + '</p>');
                        }
                    } else if (obj.status == false && obj.type == 'SYSTEM') {
                        $.alert(obj.msg)
                    } else {
                        messageAlert('Success', obj.msg, 'fa-check', 'success');
                        setTimeout(function() {
                            window.location = SITE_URL + obj.page;
                        }, 1000)
                    }
                }
            })
            return false;
        });
    </script>
@endsection
