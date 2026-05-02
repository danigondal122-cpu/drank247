let images = [];
function getFileTemplate(fileObject)
{
  let html = `
  <div class="card elevation-1 mb-3 " style="width:120px;">  
  <div class="d-flex align-self-center align-items-center px-2" style="height:120px;">
    <img src="" style="max-height:120px;margin-left: auto;margin-right: auto;" class="${fileObject.newName} card-img-top cart-item-img">
  </div>
  <button type="button" data-toggle="tooltip" data-placement="bottom" title="View full image" style="position: absolute;left: 2%;bottom: 2%;" class="btn btn-primary btn-sm previewImage">
    <i class="fas fa-search-plus"></i>
    </button>
    <button type="button" data-toggle="tooltip" data-placement="bottom" title="Remove" style="position: absolute;right: 2%;bottom: 2%;" class="btn btn-danger btn-sm deleteImage" data-id="">
      <i class="fas fa-trash-alt"></i>
    </button>  
</div>
  `;
  return html;
}
$('#image_file').on('change',function(){
  $(".dropHere").remove();
  let files = $(this)[0].files;
  files[0].newName = new Date().getTime();
  let template = getFileTemplate(files[0]);
  $('#postedImages').append(template);
  $('img.'+files[0].newName).attr('src',URL.createObjectURL(files[0]))
  images.push(files[0]) 
  $(this).val('')
  $('[data-toggle="tooltip"]').tooltip()
})
$(document).on('click','.previewImage',function(){
  let url = $(this).parent().children('.d-flex').find('img').attr('src');
  window.open(url,'_blank')
})
$(document).on('click','.deleteImage',function(){
  $("#old_cat_pic").val('');
  $("#old_pic").val('');
  let id = $(this).attr('data-id');
  $('#img'+id).remove()
  let addImageTemplate = `
  <div class="dropHere float-left">
    <button class="btn btn-outline-primary" type="button" onclick="$('#image_file').click()" title="click here to add images">
      <i class="fas fa-plus fa-5x"></i>
    </button>                    
  </div>`;
  $("#postedImages").html(addImageTemplate);
  images = images.filter(function(file){
    return file.newName != id;
  })
})