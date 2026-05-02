$(document).ready(function() {
  if (location.hash) {
    $("a[href='" + location.hash + "']").tab("show");
  }
});
$(window).on("popstate", function() {
  var anchor = location.hash || $("a[data-toggle='tab']").first().attr("href");
  $("a[href='" + anchor + "']").tab("show");
});
$(function () {
  $('#profileForm').on('submit', function (e) {
    e.preventDefault();
    loader_show();
    var data = new FormData(this);
    if(images.length >0){
      for(key in images){
        data.append('image_file',images[key]);             
      }      
    }
    $('.is-invalid').removeClass('is-invalid');
    $('.text-danger').html('');
    $.ajax({
      url: SITE_URL + 'customer/update',
      type: 'POST',
      data: data,
      cache: false,
      contentType: false,
      processData: false,
      success: function (obj) {
        if (!obj.status && obj.type == 'VALIDATION') {
          loader_hide();
          for (key in obj.errors) {
            $('#profileForm #' + key).addClass('is-invalid');
            $('#profileForm #' + key+'_error').html(obj.errors[key]);
          }
        }
        if (obj.status) {
          loader_hide();
          messageAlert('Success',obj.msg,'fa-check','success');
          location.reload();
          // window.location = SITE_URL + 'admin/dashboard';
        }
      },
      error: function () {

      }
    });
  })
})
// Change password
$( "#change_password_frm" ).submit(function( e ) {
  $('#change_password_frm .is-invalid').removeClass('is-invalid');
  $('#change_password_frm .text-danger').remove();
  let fromData = $( "#change_password_frm" ).serialize();
  loader_show();
  $.ajax({
    url : SITE_URL+'customer/changepassword',
    type : 'POST',
    data : fromData,
    success : function(obj){
      loader_hide();
      if(obj.status == false && obj.type=='VALIDATION') {
          $('.error').text('');
          $('.form-control').removeClass('is-invalid');
          for (key in obj.errors) {
            $('#change_password_frm #' + key).addClass('is-invalid');
            $('#change_password_frm #' + key).after('<p class="text-danger mb-0">' + obj.errors[key] + '</p>');
          }
      } else if(obj.status == false && obj.type=='SYSTEM') {
        $.alert(obj.msg)
      } else {
        messageAlert('Success',obj.msg,'fa-check','success');
        $('#change_password_frm')[0].reset()
      }
    }
  })
  return false;
});
// Change password
$( "#addUpdateAddressForm" ).submit(function( e ) {
  $('#addUpdateAddressForm .is-invalid').removeClass('is-invalid');
  $('#addUpdateAddressForm .text-danger').remove();
  let fromData = $( "#addUpdateAddressForm" ).serialize();
  loader_show();
  $.ajax({
    url : SITE_URL+'customer/addupdateaddress',
    type : 'POST',
    data : fromData,
    success : function(obj){
      loader_hide();
      // console.log(obj)
      // return 
      if(obj.status == false && obj.type=='VALIDATION') {
          $('.error').text('');
          $('.form-control').removeClass('is-invalid');
          for (key in obj.errors) {
            $('#addUpdateAddressForm #' + key).addClass('is-invalid');
            $('#addUpdateAddressForm #' + key).after('<p class="text-danger mb-0">' + obj.errors[key] + '</p>');
          }
      } else if(obj.status == false && obj.type=='SYSTEM') {
        $.alert(obj.msg)
      }
      else if(obj.status == false && obj.type=='InvalidAddress') {
    
        $.confirm({
          title: '',
          content: obj.msg,
          closeIcon: true,
          buttons: {
            confirm: {
              text: 'ok',
              btnClass: 'btn-primary',
              action: function () {
                $('#addUpdateAddress').modal('hide');
                 $('#addManualAddress').modal('show');
                 $('#addManualAddress #houseno').val(obj.house_no);
                 $('#addManualAddress #postcode').val(obj.postcode);
              }
            },
            Reject: {
              text: 'Cancel',
              btnClass: 'btn-secondary',
              action: function () {}
            },
          }
        });

      }
      else if(obj.status == false && (obj.type=='NotValid')) {
         $.alert(obj.msg)
      }else {
        getAddressList();
        $('#addUpdateAddress').modal('hide');
        $('#addUpdateAddress').modal('hide');
        messageAlert('Success',obj.msg,'fa-check','success');
        $('#addUpdateAddressForm')[0].reset()
      }
    }
  })
  return false;
});
// $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
//   // e.target // newly activated tab
//   // e.relatedTarget // previous active tab
//   location.hash = this.getAttribute("href");
//   if(e.target.id=='addresslist_tab'){
//     getAddressList();
//   } else if(e.target.id=='orderhistory_tab'){

//   }
// })
$( "#addManualAddressForm" ).submit(function( e ) {
  $('#addManualAddressForm .is-invalid').removeClass('is-invalid');
  $('#addManualAddressForm .text-danger').remove();
  let fromData = $( "#addManualAddressForm" ).serialize();
  loader_show();
  $.ajax({
    url : SITE_URL+'customer/addmanualaddress',
    type : 'POST',
    data : fromData,
    success : function(obj){
      loader_hide();
      // console.log(obj)
      // return 
      if(obj.status == false && obj.type=='VALIDATION') {
          $('.error').text('');
          $('.form-control').removeClass('is-invalid');
          for (key in obj.errors) {
            $('#addManualAddressForm #' + key).addClass('is-invalid');
            $('#addManualAddressForm #' + key).after('<p class="text-danger mb-0">' + obj.errors[key] + '</p>');
          }
      }else {
        getAddressList();
        $('#addManualAddress').modal('hide');
        messageAlert('Success',obj.msg,'fa-check','success');
      }
    }
  })
  return false;
});

$('#addUpdateAddress, #addManualAddress').on('hide.bs.modal', function (e) {
  $(this).find('form')[0].reset();
  $(this).find('#addUpdateAddressForm #address_id').val('');
  $(this).find('#addManualAddressForm input[name="id"]').remove();
})
function getAddressList() {
  $.ajax({
    url : SITE_URL+'customer/addresses',
    type : 'GET',
    success : function(obj){
      $('#addressList').html('');
      $('#addressList').html(obj);
    }
  })
}
function getAddressDetails(address_id) {
  let data = {};
  $.ajax({
    url : SITE_URL+'customer/address/'+address_id,
    type : 'GET',
    async:true,
    success : function(obj){
      if(obj.status){
        return data = obj.details;
      }
    }
  }).then((res)=>{
    data = res.details;
  })
  return data;
}
function setDefaultAddress(address_id){
  loader_show();
  $.ajax({
    url : SITE_URL+'customer/setdefaultaddress',
    type : 'POST',
    data : { 
      address_id:address_id,
      _token: $('meta[name=csrf-token]').attr('content'),
    },
    success : function(obj){
      loader_hide();
      if(obj.status == false && obj.type=='VALIDATION') {
          $('.error').text('');
          $('.form-control').removeClass('is-invalid');
          for (key in obj.errors) {
            $.alert(obj.errors[key]);
          }
      } else if(obj.status == false && obj.type=='SYSTEM') {
        $.alert(obj.msg)
      } else {
        messageAlert('Success',obj.msg,'fa-check','success');
      
        sessionStorage.setItem('selectedtab', 'addresslist_tab');
        window.location.href = SITE_URL+'profile';
        getAddressList()
      }
    }
  })
}
function editAddress(address_id){
  if(address_id){
  loader_show();
  $.ajax({
      url : SITE_URL+'customer/address/'+address_id,
      type : 'GET',
      success : function(obj){
        loader_hide();
        if(obj.status)
        {
          let address_details = obj.details;

          if (address_details.manual == '0')
          {
            $('#addUpdateAddress').modal('show');
            $('#addUpdateAddress #houseno').val(address_details.house_no);
            $('#addUpdateAddress #postcode').val(address_details.post_code);
            $('#addUpdateAddress #address_id').val(address_details.id);
          }
          else
          {
            var address = address_details.address.split(', ');
            $('#addManualAddress').modal('show');
            $('#addManualAddressForm #houseno').val(address_details.house_no);
            $('#addManualAddressForm #street').val(address[1] ?? '');
            $('#addManualAddressForm #city').val(address[2] ?? '');
            $('#addManualAddressForm #state').val(address[3] ?? '');
            $('#addManualAddressForm #postcode').val(address_details.post_code);
            $('#addManualAddressForm').append(
              $('<input>', { 
                type: 'hidden', 
                name: 'id', 
                value: address_details.id
              })
            );
          }
        }
      }
    })
  }
}
function deleteAddress(address_id){
  $.confirm({
    title: 'Confirm!',
    content: 'Are you sure you want to delete?',
    buttons: {
        confirm: function () {
          loader_show();
          $.ajax({
            url : SITE_URL+'customer/address/delete',
            type : 'POST',
            data : { 
              address_id:address_id,
              _token: $('meta[name=csrf-token]').attr('content'),
            },
            success : function(obj){
              loader_hide();
              if(obj.status == false && obj.type=='VALIDATION') {
                  $('.error').text('');
                  $('.form-control').removeClass('is-invalid');
                  for (key in obj.errors) {
                    $.alert(obj.errors[key]);
                  }
              } else if(obj.status == false && obj.type=='SYSTEM') {
                $.alert(obj.msg)
              } else {
                messageAlert('Success',obj.msg,'fa-check','success');
                getAddressList()
                // setTimeout(function () {
                //   window.location = SITE_URL + 'profile';
                //   }, 1000);
              
              }
            }
          })
        },
        cancel: function () {            
        }
        
    }
});
$('#orderReview').on('submit', function (e) {
alert('sdfsd');
  e.preventDefault();
  loader_show();
  var data = new FormData(this);
  $('.is-invalid').removeClass('is-invalid');
  $('.text-danger').html('');
  $.ajax({
    url: SITE_URL + 'customer/addreview',
    type: 'POST',
    data: data,
    cache: false,
    contentType: false,
    processData: false,
    success: function (obj) {
      if (!obj.status && obj.type == 'VALIDATION') {
        loader_hide();
        for (key in obj.errors) {
          $('#orderReview #' + key).addClass('is-invalid');
          $('#orderReview #' + key+'_error').html(obj.errors[key]);
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
}
