@extends('franchise.layout.layout')
@section('pageCSS')
  <link rel="stylesheet" type="text/css" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css')}}">

  <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">

@endsection
@section('header_content')
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">Reporting</h1>
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
          <h3 class="card-title">Order Archive</h3>

        </div>
        <!-- /.card-header -->
        <div class="card-body table-responsive">
          <table id="table" class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>Order From</th>
                <!-- <th>Order No.</th> -->
                <th>Date</th>
                <th>Customer</th>
                <!-- <th>Franchisee</th> -->
                <!-- <th>Delivery Person</th> -->
                <th>Order Id</th>
                <th>Price</th>
                <th>Order Payment</th>
                <!-- <th>Status</th>
                <th>Action</th> -->
              </tr>
            </thead>
            <tbody>

            </tbody>
          </table>
        </div>
        <!-- /.card-body -->
      </div>
    </div>
    <!-- /.col -->
  </div>

  <input type='hidden' name='session_month' id='session_month' value="{{Session::get('fs_month') != '' ? Session::get('fs_month') : date('m')}}" >
  <input type='hidden' name='session_year' id='session_year' value="{{Session::get('fs_year') != ''?Session::get('fs_year'):date('Y')}}" >
  <input type='hidden' name='session_week' id='session_week' value="{{Session::get('fs_week') != ''?Session::get('fs_week'):''}}" >
  <!-- /.row -->
@endsection
@section('pageJS')
<script src="{{asset('plugins/x-editable/bootstrap-editable.min.js')}}" type="text/javascript"></script>
  <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
  <script>
   var table;
   table = $('#table').DataTable({
     "pageLength": 10,
     "processing": true, //Feature control the processing indicator.
     "serverSide": true, //Feature control DataTables' server-side processing mode.
     "order": [], //Initial no order.
     "dom": "<'row'<'col-sm-6 col-md-2'l><'col-sm-6 col-md-10'f>>" +
       "<'row'<'col-sm-12'tr>>" +
       "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
     // Load data for the table's content from an Ajax source
     "ajax": {
       "url": SITE_URL + "franchise/order/list",
       "type": "POST",
       "data": function(d) {
        d._token = $('meta[name=csrf-token]').attr('content');
        d.month = $('#session_month').val();
        d.year = $('#session_year').val();
        d.week = $('#session_week').val();
         // etc
       },
       dataFilter: function(data){
          var json = jQuery.parseJSON(data);
          json.recordsTotal = json.total;
          json.recordsFiltered = json.total;
          json.data = json.data;
          return JSON.stringify(json); // return JSON string
      }
     },
     columns: [
      {
          "mRender": function ( data, type, row ) {
            row.channel_image=row.channel_image? row.channel_image : 'drank.png';
           let html = "<img class='channel_image' src="+SITE_URL+'images/channel/'+row.channel_image+">";

            return html;
          }
        },
        // { data: 'order_id' },
        { data: 'new_order_date' },
        { data: 'customer_name' },
        // { data: 'franchises_name' },
        // { data: 'dp_name' },
        // {  "mRender": function ( data, type, row ) {
        //   let order='';
        //   if(row.order_channel_id!=""){
        //     order = 'Deliverect';
        //   }else{
        //     order = "-";
        //   }
        //   return order;
        //   }
        //   },
        { data: 'order_channel_order_id' },
        // { data: 'order_finalamount' },
        {
          "mRender": function ( data, type, row ) {
           let html = "€ "+row.order_final_with_discount;

            return html;
          }
        },
        {
          "mRender": function ( data, type, row ) {
            let color='';
            let status='';
            if(row.order_payment_status){
               color='#2c8c1b';
            }else{
               color='#cbce23';
            }
           let html = '<div style="color:'+color+'; text-align:center">'+row.order_payment_status_text+'</div>';

            return html;
          }
        },
        // {
        //   "mRender": function ( data, type, row ) {
        //       html = "<a href='#' style='color:"+row.os_color +"''  class='orderstatus' id='status"+row.order_id+"' status="+row.order_status+" title='"+row.order_cancelled_reason+"' data-type='select'  data-pk='"+row.order_id +"'  class='editable editable-click' data-original-title='' title=''>"+row.os_name +"</a>";
        //     return html;
        //   }
        // },
        // {
        //   "mRender": function ( data, type, row ) {
        //    let html = "<div class='btn-group float-left pr-2'><a href='"+SITE_URL+"franchise/order/view/"+row.order_id+"'  class='btn btn-xs btn-primary  btn-view text-white'><i class='fas fa-eye'></i></a></div>";

        //     return html;
        //   }
        // },
      ],
     "columnDefs": [{
       "targets": [0,1,2,3,4,5], //first column / numbering column
       "orderable": false, //set not orderable
     }, ],
     "drawCallback": function( settings, json ) {
      $('.orderstatus').editable({
          type:"select",
          mode:'inline',
          source: [

                @foreach($status_list as $status)
                    { value: '{{ $status->id }}', text: '{{ $status->os_name }}' },
                @endforeach
            ],
            mode: 'popup',
            url: SITE_URL + 'franchise/order/updatestatus',
            params: {'updatestatus': 'AjaxEditableCall','_token':$('meta[name=csrf-token]').attr('content')},
            success: function (data) {
             $('#status'+data.id).css({'color': data.color});
               }

          });
          $(".orderstatus").mouseover(function(){
           var reason= $(this).attr('title');
           var status= $(this).attr('status');
           if(status=='7'){
            if(reason!=""){
              $('.orderstatus').tooltip();
            }
          }
          });


      }

   }).on('init.dt', function() {
      let html =`<select name="" id="year" class="form-control form-control-sm float-left franchise_search" style="width:20%;margin-right:5px;">
        <option value="">Select Year</option>
        <option value="2020" @selected(Session::get('fs_year') == '2020' || date('Y') == 2020)>2020</option>
        <option value="2021" @selected(Session::get('fs_year') == '2021' || date('Y') == 2021)>2021</option>
        <option value="2022" @selected(Session::get('fs_year') == '2022' || date('Y') == 2022)>2022</option>
        <option value="2023" @selected(Session::get('fs_year') == '2023' || date('Y') == 2023)>2023</option>
        <option value="2024" @selected(Session::get('fs_year') == '2024' || date('Y') == 2024)>2024</option>
        </select>`;
      html +=`<select name="" id="month" class="form-control form-control-sm float-left franchise_search" style="width:30%;margin-right:5px;"><option value="">Select Month</option>`;
      @foreach ($monthlist as $key=>$month)
       html += `<option value="{{ $key }}" @selected(Session::get('fs_month')==$key || date('m') == $key)>{{ $month }}</option>`;
      @endforeach
      html += `</select><select name="" id="week" class="form-control form-control-sm float-left franchise_search" style="width:20%;margin-right:5px;"><option value="">Select Week</option>`;
      @foreach ($weeklist as $key=>$week)
      html += `<option value="{{ $key }}" @selected(Session::get('fs_week')==$key)>{{ $week }}</option>`;
      @endforeach
      html += `</select><a class="btn btn-danger float-right btn-sm ml-2 generate_pdf" target="_blank" href="{{ url('franchise/order/generatereportPdf') }}">PDF</a>
      <a class="btn btn-danger float-right btn-sm ml-2 generate_pdf" target="_blank" href="{{ url('franchise/order/generateinvoicePdf') }}">InvoicePDF</a>
      <button class="btn btn-danger btn-sm float-right btn-reset ml-2">Reset</button>
     `;
     $('#table_filter').before().append(html);
    //  $( "#year" ).trigger( "change" );
     $( "#month" ).trigger( "change" );
   });


  $(document).on('change', '#year', function() {
    var year = $(this).val();
    $('#session_year').val(year);
    var new_url = SITE_URL + "franchise/order/list?year="+year;
    table.ajax.url(new_url).load();
  })
  $(document).on('change', '#month', function() {
    var month = $(this).val();
    var year = $('#year').val();
    $('#session_month').val(month);
    var new_url = SITE_URL + "franchise/order/list?month="+month;
    table.ajax.url(new_url).load();
    // if(month != ''){
    // $.ajax({
    //   url: SITE_URL + 'franchise/order/getdate',
    //   "type": "GET",
    //   data : { month : month , year : year },
    //   success: function(result){
    //     console.log(result);

    //   $("#week").html(result);
    // }});}
  })

  $(document).on('change', '#week', function() {
    var week = $(this).val();
    var new_url = SITE_URL + "franchise/order/list?week="+week;
    table.ajax.url(new_url).load();
  })

  $(document).on('click', '.btn-reset', function() {
    $('#year').val('');
    $('#month').val('');
    $('#week').val('');
    $('#session_month').val('');
    $('#session_week').val('');
    $('#session_year').val('');
    table
      .search('')
      .columns().search('');
    var new_url = SITE_URL + "franchise/order/list";
    table.ajax.url(new_url).load();
  });
  // setTimeout(function() {
  //   window.location.reload();
  //  }, 60000);
 </script>

@endsection
