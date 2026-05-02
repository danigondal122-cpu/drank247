@extends('admin.layout.layout')
@section('pageCSS')
@endsection
@section('header_content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark"></h1>
                </div>
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </div>
@endsection
@section('content')
    <style>
        #map {
            height: 100%;
        }

        .dataTables_paginate {
            float: right !important;
        }

        .table thead {
            display: none !important;
        }
    </style>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Delivery Person</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-5">
                            <table id="table" class="table table-bordered table-hover">
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-7">
                            <div id="map" style="width:100%;height:500px;"></div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <input type="hidden" name="" id="hiddeenMapVal" value="{{ $delivery }}">
                    <a href="{{ url('admin/deliveryperson/list') }}" class="btn btn-secondary text-white"> <i
                            class="fas fa-arrow-left"></i>&nbsp;&nbsp;Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('pageJS')
    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBE1s-LuOOBxhJEaXiVYBrWgd0TwGJv3so&callback=initMap&libraries=geometry&v=weekly"
        async></script>
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script>
        // Initialize and add the map
        function initMap() {
            var locations = JSON.parse($('#hiddeenMapVal').val());
            // The location of city
            const city = {
                lat: 27.733601,
                lng: 81.158401
            };
            // The map, centered at city
            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 4,
                center: city,
                mapTypeId: 'terrain',
                mapTypeControl: false
            });
            // The marker, positioned at city
            var i;
            var infowindow = new google.maps.InfoWindow();
            for (i = 0; i < locations.length; i++) {
                let newMarker = new google.maps.Marker({
                    position: new google.maps.LatLng(locations[i].dp_lat, locations[i].dp_lng),
                    map: map,
                    title: locations[i].dp_name,
                    animation: google.maps.Animation.DROP,
                    label: {
                        color: 'pink',
                        fontWeight: 'bold',
                        text: locations[i].dp_name,
                    },
                });
                marker.push({
                    marker: newMarker,
                    latLng: locations[i].dp_lat + "" + locations[i].dp_lng
                })
                google.maps.event.addListener(marker, 'click', toggleBounce);
            }

        }

        var marker = []
        var table;
        table = $('#table').DataTable({
            "pageLength": 10,
            "processing": true, //Feature control the processing indicator.
            "serverSide": true,
            //  "bFilter": false, 
            "lengthChange": false,
            "language": {
                "paginate": {
                    'next': '&#8594;', // or '→'
                    'previous': '&#8592;' // or '←' 
                }
            },
            "autoWidth": false,
            "bInfo": false, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": SITE_URL + "admin/deliveryperson/list",
                "type": "POST",
                "data": function(d) {
                    d._token = $('meta[name=csrf-token]').attr('content');
                    // etc
                },
                dataFilter: function(data) {
                    var json = jQuery.parseJSON(data);
                    json.recordsTotal = json.total;
                    json.recordsFiltered = json.total;
                    json.data = json.data;
                    return JSON.stringify(json); // return JSON string
                }
            },
            columns: [

                {
                    "mRender": function(data, type, row) {
                        let html = "<div class='getlocation' data-lat=" + row.dp_lat + "  data-lng=" + row
                            .dp_lng + "><img src=" + row.dp_image + " style=height:50px;></div>";
                        return html;
                    }
                },
                {
                    data: 'dp_name'
                },

            ],
            "columnDefs": [{
                    "width": "40%",
                    "targets": 0,
                    "orderable": false
                },
                {
                    "width": "60%",
                    "targets": 1,
                    "orderable": false
                },

            ],

        }).on('init.dt', function() {
            $("#table").on("click", ".getlocation", function() {

                var lat = $(this).attr('data-lat');
                var lng = $(this).attr('data-lng');
                var ltng = lat + "" + lng;
                const findMarker = marker.filter((mark) => {
                    return mark.latLng == ltng
                })

                if (findMarker.length == 1) {
                    // console.log(findMarker[0].marker)
                    findMarker[0].marker.setAnimation(google.maps.Animation.BOUNCE);
                    setTimeout(function() {
                        findMarker[0].marker.setAnimation(null);
                        $(findMarker[0].marker).dequeue();
                    }, 1400);
                }
            });
        });


        function toggleBounce() {
            if (this.getAnimation() != null) {
                this.setAnimation(null);
            } else {
                this.setAnimation(google.maps.Animation.BOUNCE);
                setTimeout(this.setAnimation(null), 1520);
            }
        }
    </script>
@endsection
