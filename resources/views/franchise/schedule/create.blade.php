@extends('franchise.layout.layout')
@section('pageCSS')
<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/timepicker/bootstrap-timepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker-bs3.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/jquery-ui/jquery-ui.css')}}">
@endsection
@section('header_content')
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">{{ (empty($row)==false)?'Update Schedule':'Add Schedule'}}</h1>
        </div>
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
@endsection
@section('content')
  <div class="row">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">{{ (empty($row)==false)?'Update Schedule':'Add Schedule'}}</h3>
             </div>
             <form method="post" id="addSchedule">
             @csrf

             <input type="hidden" name="s_id" id="s_id" value="{{ (empty($row)==false)? $row->id:''}}">
             <div class="card-body col-sm-12 col-md-6">
              <div class="row">
                <div class="col-sm-12">
                  <div class="form-group">
                     <label for="first_name">*Date and Time</label>
                     <input type="text" class="form-control" id="date" name="date"  value="{{ (empty($row)==false)?$row->time:''}}" autocomplete="off">
                   </div>
                   <span id="date_error"></span>
                 </div>
              </div>

              <div class="row">
                <div class="col-sm-12">
                  <div class="form-group" id="deliverypersondiv">
                     <label for="first_name">*Delivery Person</label>
                     <select class="form-control select2" id="deliveryperson" name="deliveryperson">
                      <option value="" >Select Delivery person</option>
                      @foreach ($delivery as $value)
                    <option value="{{$value->id}}" {{ (empty($row)==false && ($value->id==$row->delivery_person_id))?'selected':''}}>{{$value->dp_name}}</option>
                    @endforeach
                    </select>
                     <span id="deliveryperson_error"></span>
                 </div>
              </div>
            </div>

                <div class="row">
                  <div class="col-sm-12">
                    <div class="form-group">
                       <label for="first_name">*Pool</label>
                       <select class="form-control select2" id="pool" name="pool">
                        <option value="" >Select Pool area</option>
                        @foreach ($pool as $value)
                      <option value="{{$value->id}}" {{ (empty($row)==false && ($value->id==$row->pool_id))?'selected':''}}>{{'('.$value->from_postcode .'-'.$value->to_postcode.')  '.$value->area}}</option>
                      @endforeach
                      </select>
                     </div>
                     <span id="pool_error"></span>
                   </div>
                </div>

             </div>
             <!-- /.card-body -->

             <div class="card-footer">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i>&nbsp;&nbsp;{{ (empty($row)==false)?'Update':'Save'}}</button>
                <a href="{{ url('franchise/schedule/list') }}" class="btn btn-secondary text-white"> <i class="fas fa-arrow-left"></i>&nbsp;&nbsp;Back</a>
             </div>
             </form>
           </div>
         </div>
       </div>
@endsection
@section('pageJS')
<script src="{{ asset('plugins/jquery-ui/jquery-ui.min.js') }}"></script>
<script src="{{ asset('plugins/daterangepicker/moment.min.js') }}"></script>
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
<script>
  $(function() {
var date=new Date();

    $('input[name="date"]').daterangepicker({
      timePicker: true,
      autoUpdateInput: false,
      timePicker24Hour: true,
      minDate: date,
      maxDate: moment(date,'DD-MM-YYYY').add(30, 'days'),
      locale: {
        format: 'DD/MM/YYYY HH:mm A'
      }
    });
    $('input[name="date"]').on('apply.daterangepicker', function(ev, picker) {
      $(this).val(picker.startDate.format('DD/MM/YYYY HH:mm') + ' - ' + picker.endDate.format('DD/MM/YYYY HH:mm'));
      getdeliverypersonlist(picker.startDate.format('DD/MM/YYYY HH:mm') + ' - ' + picker.endDate.format('DD/MM/YYYY HH:mm'));
     });

    $('input[name="date"]').on('cancel.daterangepicker', function(ev, picker) {
      $(this).val('');
     });

  });
  function getdeliverypersonlist(date){

    $.ajax({
        url: SITE_URL + 'franchise/schedule/getDeliveryPersonList',
        type: 'POST',
        data: 'date=' + date+'&_token='+$('meta[name=csrf-token]').attr('content'),
        success: function (obj) {
            if (obj.status == true) {
            $('#deliverypersondiv').html(obj.html);

            } else {

            }
        }
    });

  }

  </script>

<script src="{{ asset('plugins/select2/js/select2.min.js') }}"></script>
<script src="{{ asset('js/page/image_upload.js') }}"></script>
<script>
    $(document).ready(function(){
        $('.select2').select2();
    })
    $(document).on('submit', '#addSchedule', function (e) {
        e.preventDefault();
        loader_show();
        var data = new FormData(this);
        if(images.length >0){
            for(key in images){
            data.append('image_file',images[key]);
            }
        }
        $('#addSchedule .is-invalid').removeClass('is-invalid');
        $('#addSchedule .text-danger').remove();
        $.ajax({
            url: SITE_URL + 'franchise/schedule/add',
            type: 'POST',
            data: data,
            success: function (obj) {
                if (!obj.status && obj.type == 'validation') {
                    loader_hide();
                    for (key in obj.errors) {
                    $('#' + key).addClass('is-invalid');
                    $('#' + key+'_error').after('<p class="text-danger">' + obj.errors[key] + '</p>');
                    }
                }
                if (obj.status) {
                    loader_hide();
                    messageAlert('Success',obj.msg,'fa-check','success')
                    $('#addSchedule')[0].reset();
                    setTimeout(function () {
                      window.location = SITE_URL + obj.page;
                    }, 1500)
                }
            },
            cache: false,
            contentType: false,
            processData: false
        });
    });
</script>

@endsection
