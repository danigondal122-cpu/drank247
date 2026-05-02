@extends('franchise.layout.layout')
@section('header_content')
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">Dashboard</h1>
        </div>
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
@endsection
<style>
.scrollable-content {
    height: 415px;
    overflow-y: scroll;
}
</style>@section('content')
  <div class="row">
    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box">
        <span class="info-box-icon bg-info elevation-1"><i class="fas fa-cart-plus"></i></span>

        <div class="info-box-content">
          <span class="info-box-text"> Afronden Bestelling</span>
          <span class="info-box-number">
            Totale {{ $total_ordercount }}
          </span>
          <span class="info-box-number">
            Vandaag {{ $todays_ordercount }}
          </span>
        </div>
        <!-- /.info-box-content -->
      </div>
      <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box mb-3">
        <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-users"></i></span>

        <div class="info-box-content">
          <span class="info-box-text">Unieke Klanten</span>
          <span class="info-box-number">Totale {{ $total_customercount }}</span>
          <span class="info-box-number">Vandaag {{ $todays_customercount }}</span>
        </div>
        <!-- /.info-box-content -->
      </div>
      <!-- /.info-box -->
    </div>
    <!-- /.col -->

    <!-- fix for small devices only -->
    <div class="clearfix hidden-md-up"></div>

    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box mb-3">
        <span class="info-box-icon bg-success elevation-1"><i class="fas fa-shopping-cart"></i></span>

        <div class="info-box-content">
          <span class="info-box-text">Klantbeoordelingen</span>
          <span class="info-box-number">Totale 10</span>
          <span class="info-box-number">Vandaag 760</span>
        </div>
        <!-- /.info-box-content -->
      </div>
      <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-12 col-sm-6 col-md-3">
      <div class="info-box mb-3">
        <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-users"></i></span>

        <div class="info-box-content">
          <span class="info-box-text">Totale omzet</span>
          <span class="info-box-number">Totale € {{ $total_revenue }}</span>
          <span class="info-box-number">Vandaag € {{ $todays_revenue }}</span>
        </div>
        <!-- /.info-box-content -->
      </div>
      <!-- /.info-box -->
    </div>
    <!-- /.col -->
  </div>
  <div class="row">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title" style="margin-top:0px;"><i class="fas fa-cart-plus"></i><b> Afronden Bestelling</b></h4><br>
          <span class="info-box-number" id="result-period">Resultaten over periode {{ date('Y') }}</span>
          <h4 class="card-title" style="margin-top:0px;float:right;">
            <select name="year" id="year_filter" onchange="getOrderDataByYears(this.value);" class="form-control">
            {!! $years !!}
            </select>
          </h4>
        </div>
        <div class="card-body">
          <div class="ct-chart" id="chartHours">
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title" style="margin-top:0px;"><b> Populaire producten</b></h4><br>
          <span class="info-box-number">Een overzicht van de meest populaire producten</span>
        </div>
        <div class="card-body">
          <div class="ct-chart scrollable-content">
            @if(empty($popular_products)==false)
              @foreach ($popular_products as $popular_row)
                <div class="info-box">
                  <span class="info-box-icon"><img src="{{ $popular_row->image }}" class="card-img-top product_img" alt=""></span>
                  <div class="info-box-content">
                    <span class="info-box-number">{{ $popular_row->product_name }}</span>
                    <span class="info-box-number">Order Count: {{ $popular_row->order_count }}</span>
                  </div>
                </div>
              @endforeach
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="https://code.highcharts.com/highcharts.js"></script>
  <script type="text/javascript">

    $(document).ready(function () {
      var selected_year = $("#year_filter").val();
      getOrderDataByYears(selected_year);
    });

    function getOrderDataByYears(selected_year)
    {
      var chart1 = $('#chartHours').highcharts();
      $.ajax({
        url: SITE_URL + 'franchise/getOrderdataOfyears',
        type: 'POST',
        data: 'selected_year='+selected_year+'&_token='+$('meta[name=csrf-token]').attr('content'),
        success: function (obj) {
          if (obj.status == true)
          {

            var userData =obj.data;
            var last_year = obj.last_year;
            var currentYear = selected_year;
            var resultText = 'Resultaten over periode '+currentYear;

            $('#result-period').text(resultText)

            Highcharts.chart('chartHours', {
                chart: {
                    type: 'spline'
                },
                title: {
                    text: resultText
                },
                subtitle: {
                    text: ''
                },
                xAxis: {
                    categories: ['jan.', 'feb.', 'mrt.', 'apr.', 'mei.', 'jun.', 'jul.', 'aug.', 'sep.',
                            'okt.', 'nov.', 'dec.'
                    ]
                },
                yAxis: {
                    title: {
                        text: 'Number of Bestelling'
                    }
                },
                plotOptions: {
                        line: {
                            dataLabels: {
                                enabled: true
                            },
                            enableMouseTracking: true
                        }
                    },
                series: [{
                    name: 'Dit jaar',
                    data: userData,
                    color: "#FF5733"
                },
                {
                    name: 'Vorig jaar',
                    data: last_year,
                    color: "#EFE7E6"
                }],

            });
            loader_hide();

          } else {
            $.alert('Something went wrong');
          }
        }
      });
    }
</script>
@endsection
