$('#addCategory').on('submit',function (e){
  e.preventDefault();
  loader_show();
  let formData = new FormData(this)
  if(images.length >0){
    for(key in images){
      formData.append('image_file',images[key]);             
    }      
  }
  $('#addCategory .is-invalid').removeClass('is-invalid');
  $('#addCategory .text-danger').remove();
  $.ajax({
    url: SITE_URL + 'admin/category/add',
    type: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    success: function (obj) {
      if (!obj.status && obj.type == 'validation') {
        loader_hide();
        for (key in obj.errors) {
          $('#' + key).addClass('is-invalid');
          $('#' + key).after('<p class="text-danger">' + obj.errors[key] + '</p>');
        }
      }
      if (obj.status) {
        loader_hide();
        messageAlert('Success',obj.msg,'fa-check','success')
        $('#addCategory')[0].reset();
        setTimeout(function () {
          window.location = SITE_URL + obj.page;
        }, 1500)
      }
    },
   
  })
})
$(document).on('submit', '#editCategory', function (e) {
  e.preventDefault();
  loader_show();
  var data = new FormData(this);
  if(images.length >0){
    for(key in images){
      data.append('image',images[key]);             
    }      
  }
  $('#editCategory .is-invalid').removeClass('is-invalid');
  $('#editCategory .text-danger').remove();
  $.ajax({
    url: SITE_URL + 'admin/Category/updateCategory',
    type: 'POST',
    data: data,
    success: function (obj) {
      if (!obj.status && obj.type == 'validation') {
        loader_hide();
        for (key in obj.errors) {
          $('#' + key).addClass('is-invalid');
          $('#' + key).after('<p class="text-danger">' + obj.errors[key] + '</p>');
        }
      }
      if (obj.status) {
        loader_hide();
        Toast.fire({
          type: 'success',
          title: obj.msg
        })
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
$(document).on('click', '.btn-delete', function (e) {
  e.preventDefault();
  let id = $(this).attr('data-id');
  $.confirm({
    title: '',
    content: 'Sure want to delete?',
    buttons: {
      confirm: {
        text: 'Yes',
        btnClass: 'btn-danger',
        action: function () {
          $.ajax({
            url: SITE_URL + 'admin/Category/deleteCategory',
            type: 'GET',
            data: 'id=' + id,
            success: function (obj) {
              if (obj.status == true) {
                // table.draw();
                Toast.fire({
                  type: 'success',
                  title: obj.msg
                })
                setTimeout(function () {
                  window.location = SITE_URL + obj.page;
                }, 1500)
              } else {
                $.alert('Something went wrong');
              }
            }
          });
        }
      },
      cancel: {
        text: 'No',
        action: function () { }
      },
    }
  });
})