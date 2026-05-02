@extends('admin.layout.layout')
@section('pageCSS')
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/x-editable/bootstrap-editable.css') }}">
@endsection
@section('header_content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">{{ $row->page_name }}</h1>
                </div>
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@endsection
@section('content')
    <script src="{{ asset('plugins/ckeditor/ckeditor.js') }}"></script>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form id="form_cms" action="#" method="post">
                    @csrf
                    <input type="hidden" name="id" id="id" value="{{ $row->id }}">

                    <div class="card-body table-responsive">
                        <div class="col-sm-10 pl-3 pb-2">
                            <div class="col-sm-2" style="display:inline-block;">
                                <input type="radio" id="languange" name="languange" value="0" checked="">
                                English
                            </div>
                            <div class="col-sm-2" style="display:inline-block;">
                                <input type="radio" id="languange" name="languange" value="1">
                                Dutch
                            </div>
                        </div>
                        <textarea name="description" id="description" rows="1" class="form-control" autocomplete="off">{{ $row->page_content_eng }}</textarea>
                        <span class="text-danger" id="description_error"></span>
                    </div>
                    <div class="card-footer">
                        <div class="form-group">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary btn-save btn-flat submit"
                                    style="margin-right:5px;"><i class="fas fa-save"></i> Update</button>
                            </div>
                        </div>
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
        $('#form_cms').on('submit',function (e){
            $('.submit').trigger("click");
            loader_show();
            e.preventDefault();
            let formData = new FormData(this)
            $.ajax({
            url:SITE_URL+'admin/cms/saveCms',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (obj) {
                if (!obj.status && obj.type == 'validation') {
                    loader_hide();
                    for (key in obj.errors) {
                        $('#' + key).addClass('is-invalid');
                        $('#' + key+'_error').html(obj.errors[key][0]);
                    }
                }
                if (obj.status) {
                    loader_hide();
                    console.log('Looged in');
                    successMessage(obj.message);
                    location.reload();
                }
            },
            error: function () {}
            });
        });
    </script>
    <script type="text/javascript">
        //resize CKEditor with customised height and width
        var editor = CKEDITOR.replace('description', {
            enterMode: CKEDITOR.ENTER_BR
        });


        //   CKEDITOR.replace('description',{
        //     width: "1000px",
        //       height: "300px"
        //       // toolbar :
        //       // [
        //       //     ['Source'],
        //       //     ['Bold','Italic','Underline','Strike'],
        //       // ],

        //    }
        // );
        CKEDITOR.instances['description'].on('change', function() {
            CKEDITOR.instances['description'].updateElement()
        });

        $('input[type=radio][name=languange]').change(function() {
            var id = $('#id').val();
            $.ajax({
                url: SITE_URL + 'admin/cms/getCmsDetail',
                type: 'POST',
                data: 'id=' + id + '&language=' + this.value + '&_token=' + $('meta[name=csrf-token]').attr(
                    'content'),
                success: function(obj) {
                    if (obj.status == true) {
                        CKEDITOR.instances.description.setData(obj.content);
                    }
                }
            });
        });
    </script>
@endsection
