@extends('admin.layout.layout')
@section('pageCSS')
  <link rel="stylesheet" type="text/css" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css')}}">

  <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">

  <link rel="stylesheet" href="{{ asset('plugins/timepicker/bootstrap-timepicker.min.css') }}">
@endsection
@section('header_content')
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">Delivery Schedule</h1>
        </div>
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
@endsection
@section('content')

  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
          <input type="checkbox" class="custom-control-input scheduleonoff" id="customSwitch3" {{$time_schedule['time_schedule']=="1" ? 'checked': ''}}>
            <label class="custom-control-label" for="customSwitch3">Delivery Schedule:   ON/OFF</label>
          </div>

        </div>
        <!-- /.card-header -->
        <div class="card-body table-responsive">
          <table id="table" class="table table-bordered table-hover" style="border-top: 2px solid #f2f2f2">
            <thead>
              <tr>
                <th><b>#</b></th>
                <th><div class="icheck-primary d-inline">
                  <input type="checkbox" class="checkbox" value="1" name="1" id="1" {{$time[0]['is_checked']=="1"? 'checked':''}}  >
                  <label for="todoCheck2"></label>
                </div>Sunday
               </th>
                <th><div class="icheck-primary d-inline">
                        <input type="checkbox" class="checkbox" value="1" name="2" id="2" {{$time[1]['is_checked']=="1"? 'checked':''}} >
                        <label for="todoCheck2"></label>
                    </div>Monday
                </th>
                <th><div class="icheck-primary d-inline">
                    <input type="checkbox" class="checkbox" value="1" name="3" id="3" {{$time[2]['is_checked']=="1"? 'checked':''}} >
                    <label for="todoCheck2"></label>
                  </div>Tuesday
                </th>
                <th><div class="icheck-primary d-inline">
                  <input type="checkbox" class="checkbox" value="1" name="4" id="4" {{$time[3]['is_checked']=="1"? 'checked':''}}  >
                  <label for="todoCheck2"></label>
                    </div>Wednesday
                </th>
                <th><div class="icheck-primary d-inline">
                    <input type="checkbox" class="checkbox" value="1" name="5" id="5" {{$time[4]['is_checked']=="1"? 'checked':''}} >
                    <label for="todoCheck2"></label>
                   </div>Thursday
                </th>
                <th><div class="icheck-primary d-inline">
                  <input type="checkbox" class="checkbox" value="1" name="6" id="6" {{$time[5]['is_checked']=="1"? 'checked':''}} >
                  <label for="todoCheck2"></label>
                  </div>Friday
                 </th>
                <th><div class="icheck-primary d-inline">
                  <input type="checkbox" class="checkbox" value="1" name="7" id="7" {{$time[6]['is_checked']=="1"? 'checked':''}}  >
                  <label for="todoCheck2"></label>
                  </div>Saturday
                </th>

              </tr>
            </thead>

              <tr>

                  <td><b>From Time</b></td>
                  @for($m=1; $m<8; $m++)
                  <td>

                    <div class="row">
                      <div class="col-md-6"><dt style="text-align:center;">HR</dt>
                      <select name="starttime0_{{$m}}"  id="starttime0_{{$m}}" class="form-control select2" style="width:115%;padding:5px !important;"  onchange="getStartTime({{$m}},'start0');">

                            @for($i=0; $i<24; $i++)
                               <option value="{{$i<10 ? '0'.$i: $i}}" {{$time[$m-1]['start_time_0']==$i ? "selected": "" }}>{{$i<10 ? '0'.$i: $i}}</option>
                            @endfor

                        </select>
                      </div>
                      <div class="col-md-6"><dt style="text-align:center;">MIN</dt>
                      <select name="starttime1_{{$m}}" id="starttime1_{{$m}}" class="form-control select2"  style="width:115%;padding:5px !important;"  onchange="getStartTime({{$m}},'start1');">

                            @for($j=0; $j<60; $j++)
                        <option value="{{$j<10 ? '0'.$j: $j}}" {{$time[$m-1]['start_time_1']==$j ? "selected": "" }}>{{$j<10 ? '0'.$j: $j}}</option>
                            @endfor

                        </select>
                      </div>
                    </div>
                  </td>
                  @endfor


              </tr>
              <tr>
                <td><b>Until This Time</b></td>
                @for($m=1; $m<8; $m++)
                <td>

                  <div class="row">
                    <div class="col-md-6"><dt style="text-align:center;">HR</dt>
                    <select name="endtime0_{{$m}}"  id="endtime0_{{$m}}" class="form-control select2" style="width:115%;padding:5px !important;"  value="{{$time[0]['start_time_0']}}" onchange="getStartTime({{$m}},'end0');">

                          @for($k=0; $k<24; $k++)
                           <option value="{{$k<10 ? '0'.$k: $k}}" {{$time[$m-1]['end_time_0']==$k ? "selected": "" }}>{{$k<10 ? '0'.$k: $k}}</option>
                          @endfor

                      </select>
                    </div>
                    <div class="col-md-6"><dt style="text-align:center;">MIN</dt>
                    <select name="endtime1_{{$m}}" id="endtime1_{{$m}}" class="form-control select2" style="width:115%;padding:5px !important;"  onchange="getStartTime({{$m}},'end1');">

                          @for($l=0; $l<60; $l++)
                           <option value="{{$l<10 ? '0'.$l: $l}}" {{$time[$m-1]['end_time_1']==$l ? "selected": "" }}>{{$l<10 ? '0'.$l: $l}}</option>
                          @endfor

                      </select>
                    </div>
                  </div>
                </td>
                @endfor

              </tr>
            </tbody>
          </table>
        </div>
        <!-- /.card-body -->
      </div>
    </div>
    <!-- /.col -->
  </div>
  <!-- /.row -->
@endsection
@section('pageJS')
  <script src="{{ asset('plugins/timepicker/bootstrap-timepicker.min.js') }}" type="text/javascript"></script>

  <script type="text/javascript">
    $(function () {
        $('.txt_time').timepicker({
            showMeridian: false,
            defaultTime: false
        })
    });
    $(".checkbox").click(function(){
     var id=$(this).attr('id');
      if($(this).prop("checked") == true){
        var value='1';
      }
      else if($(this).prop("checked") == false){
        var value='0';
      }
      $.ajax({
            url: SITE_URL + 'admin/deliveryperson/getChecked',
            type: 'POST',
            data: 'id='+ id +'&value='+value+'&_token='+$('meta[name=csrf-token]').attr('content'),
            success: function (obj) {

            }
          });

    });

    function getStartTime(id,type){
      if(type=="start0"){
        var time=$( "#starttime0_"+id+ " option:selected").text();
      }else if(type=="start1"){
        var time=$( "#starttime1_"+id+ " option:selected").text();
      }else if(type=="end0"){
        var time=$( "#endtime0_"+id+ " option:selected").text();
      }else{
        var time=$( "#endtime1_"+id+ " option:selected").text();
      }

      $.ajax({
            url: SITE_URL + 'admin/deliveryperson/getStartTime',
            type: 'POST',
            data: 'id='+ id +'&type='+type+'&time=' + time +'&_token='+$('meta[name=csrf-token]').attr('content'),
            success: function (obj) {

            }
          });


    }
    $(".scheduleonoff").click(function(){
      if($(this).prop("checked") == true){
        var value='1';
      }
      else if($(this).prop("checked") == false){
        var value='0';
      }
      $.ajax({
            url: SITE_URL + 'admin/deliveryperson/scheduleOnOff',
            type: 'POST',
            data: 'value='+ value +'&_token='+$('meta[name=csrf-token]').attr('content'),
            success: function (obj) {

            }
          });

    });
</script>
  @endsection
