$(function () {
  $('#loginForm').on('submit', function (e) {
    e.preventDefault();
    loader_show();
    let formData = $(this).serialize();
    $('.is-invalid').removeClass('is-invalid');
    $('.text-danger').html('');
    $.ajax({
      url: SITE_URL + 'customer/login',
      type: 'POST',
      data: formData,
      success: function (obj) {
        if (!obj.status && obj.type == 'VALIDATION') {
          loader_hide();
          for (key in obj.errors) {
            $('#loginForm #' + key).addClass('is-invalid');
            $('#loginForm #' + key+'_error').html(obj.errors[key]);
          }
        }
        if (obj.status) {
          console.log('Looged in');
          location.reload();
          // window.location = SITE_URL + 'admin/dashboard';
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
      url: SITE_URL + 'customer/forgotPassword',
      type: 'POST',
      data: formData,
      success: function (obj) {
        if (!obj.status && obj.type == 'VALIDATION') {
          loader_hide();
          for (key in obj.errors) {
            $('#forgotForm #' + key).addClass('is-invalid');
            $('#forgotForm #' + key+'_error').html(obj.errors[key]);
          }
        }
        if (obj.status) {
        loader_hide();
        messageAlert('Success',obj.message,'fa-check','success')
        $('#forgotForm')[0].reset();
        setTimeout(function () {
          window.location = SITE_URL + obj.page;
        }, 1500)
        }
      },
      error: function () {

      }
    });
  })
  $('#registerForm').on('submit', function (e) {
    e.preventDefault();
    loader_show();
    let formData = $(this).serialize();
    $('.is-invalid').removeClass('is-invalid');
    $('.text-danger').html('');
    $.ajax({
      url: SITE_URL + 'customer/register',
      type: 'POST',
      data: formData,
      success: function (obj) {
        if (!obj.status && obj.type == 'VALIDATION') {
          loader_hide();
          for (key in obj.errors) {
            $('#registerForm #' + key).addClass('is-invalid');
            $('#registerForm #' + key+'_error').html(obj.errors[key]);
          }
        }
        if (obj.status) {
          messageAlert('Success',obj.message,'fa-check','success')
          $('#registerForm')[0].reset();
           setTimeout(function () {
          window.location = SITE_URL + obj.page;
        }, 1500)
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
      url: SITE_URL + 'customer/resetpassword',
      type: 'POST',
      data: formData,
      success: function (obj) {
        if (!obj.status && obj.type == 'VALIDATION') {
          loader_hide();
          for (key in obj.errors) {
            $('#' + key).addClass('is-invalid');
            $('#' + key+'_error').html(obj.errors[key]);
          }
        }
        if (obj.status) {
          loader_hide();
          console.log('Looged in');
          successMessage(obj.message);
          setTimeout(function () {
          window.location = SITE_URL + obj.page;
        }, 3000);
        }
      },
      error: function () {

      }
    });
  })
  function successMessage(msg){
    jQuery("#message_div").html("<div class='alert  alert-dismissable' style='background-color:#ecf0f5;'><i class='fa fa-check'></i><button aria-hidden='true' data-dismiss='alert' class='close' type='button'>×</button>"+msg+"</div>");
        $target = jQuery(".success-message:first");
            if($target.length)
            {
                jQuery('html, body').stop().animate({
                    'scrollTop': (parseInt($target.offset().top)-125)
                }, 900, 'swing', function () {
                });
            }
     $(".alert-dismissable").fadeOut(3000);	
            
    }

});
