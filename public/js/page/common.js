$(document).ready(function () {
  var timeZone=Intl.DateTimeFormat().resolvedOptions().timeZone;
   $.ajax({
    url: SITE_URL + 'timezone',
    type: 'POST',
    data: 'timeZone=' + timeZone+'&_token='+$('meta[name=csrf-token]').attr('content'),
    success: function (obj) {

    }
  });
  //Sidebar active class
  var url = window.location.href;
  $('ul.nav-sidebar a').filter(function () {
    let relations = $(this).attr('data-relation');
    if (relations != undefined) {
      relations = relations.split(',');
      relations = relations.map(el => SITE_URL + el);
      for (var i = 0, l = relations.length; i < l; i++) {
        if (url.includes(relations[i])) {
          return true;
        }
      }
    } else {
      return this.href == url;
    }
  }).addClass('active');
});
function loader_show() {
  $.blockUI({
    baseZ: 99999,
    message: '<div class="loading-message loading-message-boxed"><i class="fa fa-circle-notch fa-spin fa-fw"></i><span>&nbsp;&nbsp;LOADING...</span></div>',
  });
}
function loader_hide() {
  $.unblockUI();
}

function messageAlert(title, message, icon = 'fa-check', type = 'white', time = 1000,redirect) {
   var href=SITE_URL + 'cart';
   var body=(redirect=="true") ? '<div><a href='+href+'  style="color:#fff;" >'+message+'</a></div>' :'<div>'+message+'</div>';
  $(document).Toasts('create', {
    class: 'bg-' + type,
    title: title,
    autohide: true,
    delay: time,
    body: body,
    icon: 'fa ' + icon + ' fa-lg',
  })
}
function readURL(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();

    reader.onload = function (e) {
      $('#userProfileImage').attr('src', e.target.result);
    }
    reader.readAsDataURL(input.files[0]); // convert to base64 string
  }
}
$("#image").change(function () {
  readURL(this);
});

$(document).on('click', '.deleteImage', function () {

  $("#image").val('');
  $('#userProfileImage').attr('src', SITE_URL + 'img/plus.png');
});
$(document).on('click', '.userProfileImage', function () {
  let url = $(this).attr('src');
  window.open(url, '_blank')
})

$(document).ready(function () {
  var ua = navigator.userAgent.toLowerCase();
  if (ua.indexOf('safari') != -1) {
    if (ua.indexOf('chrome') > -1) {
    } else {
      $('body').addClass('safari');
    }
  }
});
jQuery('img.svg').each(function(){
  var $img = jQuery(this);
  var imgID = $img.attr('id');
  var imgClass = $img.attr('class');
  var imgURL = $img.attr('src');

  jQuery.get(imgURL, function(data) {
      // Get the SVG tag, ignore the rest
      var $svg = jQuery(data).find('svg');

      // Add replaced image's ID to the new SVG
      if(typeof imgID !== 'undefined') {
          $svg = $svg.attr('id', imgID);
      }
      // Add replaced image's classes to the new SVG
      if(typeof imgClass !== 'undefined') {
          $svg = $svg.attr('class', imgClass+' replaced-svg');
      }

      // Remove any invalid XML tags as per http://validator.w3.org
      $svg = $svg.removeAttr('xmlns:a');

      // Replace image with new SVG
      $img.replaceWith($svg);

  }, 'xml');


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
