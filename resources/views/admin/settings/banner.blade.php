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
                    <h3 class="card-title">Banner</h3>
                </div>
                <form id="form_banner" action="#" method="post">
                    @csrf
                    <div class="card-body col-12">
                        <div class="card-deck" id="postedImages">
                            {{-- <div class="row"> --}}
                            @foreach ($row as $key => $value)
                                <div class="col-4 my-3" id="{{ $value->id }}" type="main">
                                    <div class="card">
                                        <div>
                                            <img class="card-img-top" src="/uploads/banner/{{ $value->image }}"
                                                style="width:auto;height:250px;" alt="Card image cap">
                                        </div>
                                        <button type="button" data-toggle="tooltip" data-placement="bottom"
                                            title="View full image" style="position: absolute;left: 2%;bottom: 2%;"
                                            class="btn btn-primary btn-sm previewImage">
                                            <i class="fas fa-search-plus"></i>
                                        </button>
                                        <button type="button" data-toggle="tooltip" data-placement="bottom" title="Remove"
                                            style="position: absolute;right: 2%;bottom: 2%;"
                                            class="btn btn-danger btn-sm deleteImage" data-id="">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>

                                </div>
                            @endforeach
                            {{-- </div> --}}

                        </div>

                        <a onclick="$('#input_filebanner').click()" class="btn btn-success text-white my-3"
                            style=""><i class="fa fa-upload" aria-hidden="true"></i>
                            Add Image</a>
                        <input type="file" id="input_filebanner" name="input_file[]" multiple="multiple" accept="image/*"
                            style="display:none;">


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
        var images = [];
        $('#input_filebanner').on('change', function() {
            let files = $(this)[0].files;
            for (var i = 0; i < files.length; i++) {
                if (images.length >= 10) {
                    $(this).val('')
                    return false
                }
                files[i].newName = new Date().getTime() + i
                let template = getFileTemplatebanner(files[i], URL.createObjectURL(files[i]));
                $('#postedImages').append(template);
                $('.' + files[i].newName).attr('src', URL.createObjectURL(files[i]))
                images.push(files[i])
            }
        });

        function getFileTemplatebanner(fileObject, file_path, type = "doc") {
            let fileType = fileObject.type.split('/')[0]
            let fileExt = fileObject.name.split('.')
            fileExt = fileExt[fileExt.length - 1]
            let inner = ``
            if (fileType == 'image') {
                inner =
                    `<img src="" class="${fileObject.newName} card-img-top" style="width:auto;height:250px;" alt="Card image cap" style="" />`
            }
            let html = `
            <div class="col-4 my-3" id="${fileObject.newName}" type="new">
                      <div class="card"><div>
                        ${inner}`
            html += `  </div><button type="button" data-toggle="tooltip" data-placement="bottom" title="View full image" style="position: absolute;left: 2%;bottom: 2%;" class="btn btn-primary btn-sm previewImage">
                          <i class="fas fa-search-plus"></i>
                          </button>
                          <button type="button" data-toggle="tooltip" data-placement="bottom" title="Remove" style="position: absolute;right: 2%;bottom: 2%;" class="btn btn-danger btn-sm deleteImage" data-id="">
                            <i class="fas fa-trash-alt"></i>
                          </button>  
                          </div>
                     </div>
`;
            return html;
        }
        $(document).on('click', '.previewImage', function() {
            let url = $(this).parent().children().find('img').attr('src');
            window.open(url, '_blank')
        })
        $(document).on('click', '.deleteImage', function() {
            var id = $(this).parent().parent().attr('id');
            var type = $(this).parent().parent().attr('type');

            if (type == "main") {
                $.ajax({
                    url: SITE_URL + 'admin/settings/deleteBanner',
                    type: 'POST',
                    data: 'id=' + id + '&_token=' + $('meta[name=csrf-token]').attr('content'),
                    success: function(obj) {
                        if (obj.status == true) {
                            $('#' + id).remove();

                        } else {
                            $.alert('Something went wrong');
                        }
                    }
                });

            } else {
                $('#' + id).remove();
            }
        })

        $("#form_banner").on("submit", function(e) {
            e.preventDefault();
            loader_show();
            let formData = new FormData(this);
            if (images.length > 0) {
                for (key in images) {
                    formData.append("banner[]", images[key]);
                }
            }
            $("#form_banner .is-invalid").removeClass("is-invalid");
            $("#form_banner .text-danger").remove();
            $.ajax({
                url: SITE_URL + "admin/settings/updateBanner",
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
                        $("#form_banner")[0].reset();
                        setTimeout(function() {
                            window.location = SITE_URL + obj.page;
                        }, 1500);
                    }
                },
            });
        });
    </script>
@endsection
