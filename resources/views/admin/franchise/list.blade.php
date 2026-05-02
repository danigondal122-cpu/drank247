@extends('admin.layout.layout')
@section('pageCSS')
  <link rel="stylesheet" type="text/css" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css')}}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
@endsection
@section('header_content')
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">Franchise List</h1>
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
          <h3 class="card-title">Franchise List</h3>
            <a href="{{ url('admin/franchise/add') }}" class="btn btn-primary btn-sm float-right">Add Franchise</a>
        </div>
        <!-- /.card-header -->
        <div class="card-body table-responsive">
          <table id="table" class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>Name</th>
                <th>Franchise Pool</th>
                <th>Email</th>
                <th>Username</th>
                <th>Mobile No</th>
                <th>Postcode</th>
                <th>Status</th>
                <th>Action</th>
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
  <div id="commonModalDocument" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md" >
       <div class="modal-content" id="commonModalHtmlDocument"></div>
   </div>
  </div>
  <!-- /.row -->
@endsection
@section('pageJS')
  <script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
  <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
  <script>
   var table;
   table = $('#table').DataTable({
     "pageLength": 10,
     "processing": true, //Feature control the processing indicator.
     "serverSide": true, //Feature control DataTables' server-side processing mode.
     "order": [],
     "autoWidth": true,//Initial no order.
     // Load data for the table's content from an Ajax source
     "ajax": {
       "url": SITE_URL + "admin/franchise/list",
       "type": "POST",
       "data": function(d) {
        d._token = $('meta[name=csrf-token]').attr('content');
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
        { data: 'franchises_name' },

        {
          "mRender": function ( data, type, row ) {
            let html = "<div class='text-truncate' style='max-width:150px;'>"+row.poolareas+"</div>";
            return html;
          }
        },
        { data: 'franchises_email' },
        { data: 'franchises_username' },
        { data: 'mobile_no' },
        { data: 'post_code' },
        {
          "mRender": function ( data, type, row ) {
            $color=row.fs_on_off=='online' ? 'text-success' : 'text-danger';
            let html = "<div class="+$color+" style=''>"+row.fs_on_off+"</div>";
            return html;
          }
        },
        {
          "mRender": function ( data, type, row ) {
            $color=row.fs_on_off=='online' ? 'checked' : '';
            let html = "<div class='btn-group float-left' style='margin-right: -14px;'><div class='custom-control custom-switch custom-switch-off-danger custom-switch-on-success'> <input type='checkbox' class='custom-control-input scheduleonoff' id='customSwitch"+row.id+"' onchange='onoff("+row.id+");' "+$color+"><label class='custom-control-label' for='customSwitch"+row.id+"'></label></div></div>";
            html += "<div class='btn-group float-left pr-2'><a href='"+SITE_URL+"admin/franchise/edit/"+row.id+"' class='btn btn-xs btn-secondary btn-edit text-white'><i class='fa fa-pencil-alt'></i></a></div>";
            html += "<div class='btn-group float-left pr-2'><a href='javascript:;' onclick='getDocument("+row.id+")'  class='btn btn-xs btn-warning  btn-view text-white'><i class='fas fa-file'></i></a></div>";
            html += "<div class='btn-group float-left pr-2'><a href='javascript:;' onclick='deleteFranchise("+row.id+")' class='btn btn-xs btn-danger btn-delete text-white'><i class='fa fa-trash-alt'></i></a></div>";
            return html;
          }
        },
      ],
      "columnDefs": [
      { "width": "10%", "targets": 0 ,"orderable": false},
      { "width": "10%", "targets": 1 ,"orderable": true},
      { "width": "10%", "targets": 2 ,"orderable": true },
      { "width": "7%", "targets": 3 ,"orderable": true },
      { "width": "7%", "targets": 4 ,"orderable": false },
      { "width": "7%", "targets": 5 ,"orderable": false },
      { "width": "7%", "targets": 6 ,"orderable": false },
      { "width": "50%", "targets": 7 ,"orderable": false },

     ],

   }).on('init.dt', function() {
     let html = `
      <select name="" id="frs_id" class="form-control form-control-sm float-left franchise_search" style="width:40%">
      <option value="">Select Franchise Pool</option>`;
      @foreach ($pool as $frs)
        html += `<option value="{{ $frs->id }}">{{ $frs->area }}</option>`;
      @endforeach
      html +=`</select>
   <button class="btn btn-danger btn-sm float-right btn-reset ml-2">Reset</button>
     `;
     $('#table_filter').before().append(html);
   });
   $(document).on('change', '#frs_id', function() {
    var frs_id = $(this).val();
    var new_url = SITE_URL + "admin/franchise/list?frs_id="+frs_id;
    table.ajax.url(new_url).load();
  })
  $(document).on('click', '.btn-reset', function() {
    $('#frs_id').val('');
    table
      .search('')
      .columns().search('');
    var new_url = SITE_URL + "admin/franchise/list";
    table.ajax.url(new_url).load();
  });
   function deleteFranchise(id) {
      $.confirm({
        title: '',
        content: 'Are you sure to delete?',
        closeIcon: true,
        buttons: {
          confirm: {
            text: 'Delete',
            btnClass: 'btn-danger',
            action: function () {
              $.ajax({
                url: SITE_URL + 'admin/franchise/delete',
                type: 'POST',
                data: 'id=' + id+'&_token='+$('meta[name=csrf-token]').attr('content'),
                success: function (obj) {
                  if (obj.status == true) {
                    table.draw();
                    Toast.fire({
                      type: obj.msgType,
                      title: obj.msg
                    })
                  } else {
                    $.alert('Something went wrong');
                  }
                }
              });
            }
          },
          Reject: {
            text: 'Cancel',
            btnClass: 'btn-default',
            action: function () {}
          },
        }
      });
   }
  function getDocument(id){
  $.ajax({
    url: SITE_URL + 'admin/franchise/getDocument',
              type: 'POST',
            data: 'id=' + id+'&_token='+$('meta[name=csrf-token]').attr('content'),
            success: function (obj) {
            $('#commonModalHtmlDocument').html(obj);
            $('#commonModalDocument').modal('show');
            }
        });

 }
 $('.view-text').click(function(){
   alert('sfasfas');

});

function onoff(id) {
  loader_show();
  var checkBox = document.getElementById("customSwitch"+id);
  var value=(checkBox.checked == true) ? 'online' : 'offline'

  $.ajax({
    url: SITE_URL + 'admin/franchise/updateonoff',
    type : 'POST',
    data: 'value='+ value + '&id='+id+'&_token='+$('meta[name=csrf-token]').attr('content'),
    success : function(obj){
      loader_hide();
      location.reload();

    },

  })
}

 </script>
@endsection
