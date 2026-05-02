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
          <h3 class="card-title">Order Invoice</h3>

        </div>
        <!-- /.card-header -->
        <div class="card-body table-responsive">
          <table id="table" class="table table-bordered table-hover">
            <thead>
              <tr>
                <th></th>
                <!-- <th>Order No.</th> -->
                <th>#</th>
                <th>Date</th>
                <!-- <th>Franchisee</th> -->
                <!-- <th>Delivery Person</th> -->
                <th>From</th>
                <th>Through</th>
                <th>Amount</th>
                <th>Paid</th>
                <th>Paid on</th>
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
     // Load data for the table's content from an Ajax source
     "ajax": {
       "url": SITE_URL + "franchise/order/invoicelist",
       "type": "POST",
       "data": function(d) {
        d._token = $('meta[name=csrf-token]').attr('content');
        d.month = $('#session_month').val();
        d.year = $('#session_year').val();
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
            row.pdf_name=row.pdf_name? row.pdf_name : 'drank.png';
           let html = "<a href="+SITE_URL+'uploads/generatepdf/'+row.pdf_name+" target='_blank'><img src="+SITE_URL+'img/pdf-circle.png'+" width='40'></a>";

            return html;
          }
        },
        { data: 'order_id' },
        { data: 'date' },
        { data: 'from_date' },
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
        { data: 'to_date' },
        // { data: 'order_finalamount' },
        {
          data : 'amount'
        },
        {
          data : 'paid_amount'
        },
        {
          data : 'date'
        }

      ],
     "columnDefs": [{
       "targets": [0,1,2,3,4], //first column / numbering column
       "orderable": false, //set not orderable
     }, ],
     "drawCallback": function( settings, json ) {
      $('.orderstatus').editable({
          type:"select",
          mode:'inline',
          source: [


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
      html +=`<select name="" id="month" class="form-control form-control-sm float-left franchise_search" style="width:30%"><option value="">Select Month</option>`;
      @foreach ($monthlist as $key=>$month)
       html += `<option value="{{ $key }}" @selected(Session::get('fs_month')==$key || date('m') == $key)>{{ $month }}</option>`;
      @endforeach
      html += `</select>
      <button class="btn btn-danger btn-sm float-right btn-reset ml-2">Reset</button>
     `;
     $('#table_filter').before().append(html);
     $( "#month" ).trigger( "change" );
   });


  $(document).on('change', '#year', function() {
    var year = $(this).val();
    $('#session_year').val(year);
    var new_url = SITE_URL + "franchise/order/invoicelist?year="+year;
    table.ajax.url(new_url).load();
  })
  $(document).on('change', '#month', function() {
    var month = $(this).val();
    var year = $('#year').val();
    $('#session_month').val(month);
    var new_url = SITE_URL + "franchise/order/invoicelist?month="+month;
    table.ajax.url(new_url).load();

  })



  $(document).on('click', '.btn-reset', function() {
    $('#month').val('');
    $('#year').val('');
    $('#session_month').val('');
    $('#session_year').val('');
    table
      .search('')
      .columns().search('');
    var new_url = SITE_URL + "franchise/order/invoicelist";
    table.ajax.url(new_url).load();
  });
  // setTimeout(function() {
  //   window.location.reload();
  //  }, 60000);
 </script>

@endsection
