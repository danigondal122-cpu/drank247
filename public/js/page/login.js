$(function () {
    $('#loginForm').on('submit', function (e) {
        // alert(SITE_URL);
        e.preventDefault();
        // loader_show();
        let formData = $(this).serialize();
        $('.is-invalid').removeClass('is-invalid');
        $('.text-danger').html('');
        $.ajax({
            url: SITE_URL + 'admin/login',
            type: 'POST',
            data: formData,
            success: function (obj) {
                if (!obj.status && obj.type == 'VALIDATION') {
                    // loader_hide();
                    for (key in obj.errors) {
                        $('#' + key).addClass('is-invalid');
                        $('#' + key + '_error').html(obj.errors[key]);
                    }
                }
                if (obj.status) {
                    console.log('Looged in');
                    window.location = SITE_URL + 'admin/dashboard';
                }
            },
            error: function () {

            }
        });
    })
    $('#forgotForm').on('submit', function (e) {
        e.preventDefault();
        loader_show();
        let formData = $(this).serialize();
        $('.is-invalid').removeClass('is-invalid');
        $('.text-danger').html('');
        $.ajax({
            url: SITE_URL + 'admin/forgotPassword',
            type: 'POST',
            data: formData,
            success: function (obj) {
                if (!obj.status && obj.type == 'VALIDATION') {
                    loader_hide();
                    for (key in obj.errors) {
                        $('#' + key).addClass('is-invalid');
                        $('#' + key + '_error').html(obj.errors[key]);
                    }
                }
                if (obj.status) {
                    loader_hide();
                    console.log('Looged in');
                    successMessage(obj.message);
                    setTimeout(function () {
                        window.location = SITE_URL + 'admin/login';
                    }, 3000);
                }
            },
            error: function () {

            }
        });
    })
    $('#resetForm').on('submit', function (e) {

        e.preventDefault();
        loader_show();
        let formData = $(this).serialize();
        $('.is-invalid').removeClass('is-invalid');
        $('.text-danger').html('');
        $.ajax({
            url: SITE_URL + 'admin/resetpassword',
            type: 'POST',
            data: formData,
            success: function (obj) {
                if (!obj.status && obj.type == 'VALIDATION') {
                    loader_hide();
                    for (key in obj.errors) {
                        $('#' + key).addClass('is-invalid');
                        $('#' + key + '_error').html(obj.errors[key]);
                    }
                }
                if (!obj.status && obj.type == 'EXPIRED') {
                    successMessage(obj.message);
                }
                if (obj.status) {
                    console.log('Looged in');
                    successMessage(obj.message);
                    setTimeout(function () {
                        window.location = SITE_URL + 'admin/login';
                    }, 3000);
                }
            },
            error: function () {

            }
        });
    })
});
function successMessage(msg) {
    jQuery("#message_div").html("<div class='alert  alert-dismissable' style='background-color:#ecf0f5;'><i class='fa fa-check'></i><button aria-hidden='true' data-dismiss='alert' class='close' type='button'>×</button>" + msg + "</div>");
    $target = jQuery(".success-message:first");
    if ($target.length) {
        jQuery('html, body').stop().animate({
            'scrollTop': (parseInt($target.offset().top) - 125)
        }, 900, 'swing', function () {
        });
    }
    $(".alert-dismissable").fadeOut(3000);

}
