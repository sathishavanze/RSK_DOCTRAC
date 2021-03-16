<style type="text/css">
  .top-header .card-footer .fa{
    position: relative;
    top: 4px;
    font-size: 14px;
    margin-right:5px;
  }
  .top-header .card-header.card-header-icon i.fa {
    font-size: 30px!important;
    line-height: 56px!important;
    width: 56px!important;
    height: 56px!important;
    text-align: center!important;
  }
  #container {
    height: 400px; 
  }

  .highcharts-figure, .highcharts-data-table table {
    min-width: 310px; 
    max-width: 800px;
    margin: 1em auto;
  }

  .highcharts-data-table table {
    font-family: Verdana, sans-serif;
    border-collapse: collapse;
    border: 1px solid #EBEBEB;
    margin: 10px auto;
    text-align: center;
    width: 100%;
    max-width: 500px;
  }
  .highcharts-data-table caption {
    padding: 1em 0;
    font-size: 1.2em;
    color: #555;
  }
  .highcharts-data-table th {
    font-weight: 600;
    padding: 0.5em;
  }
  .highcharts-data-table td, .highcharts-data-table th, .highcharts-data-table caption {
    padding: 0.5em;
  }
  .highcharts-data-table thead tr, .highcharts-data-table tr:nth-child(even) {
    background: #f8f8f8;
  }
  .highcharts-data-table tr:hover {
    background: #f1f7ff;
  }
</style>
<div class="col-md-12">
  <div class="row top-header">
    <div class="col-lg-3 col-md-6 col-sm-6">
      <div class="card card-stats">
        <div class="card-header card-header-warning card-header-icon">
          <div class="card-icon">
            <i class="fa fa-home"></i>
          </div>
          <p class="card-category">MY ORDERS</p>
          <h3 class="card-title">18425</h3>
        </div>
        <div class="card-footer">
          <div class="stats">
            <i class="fa fa-undo"></i>
            <a href="#pablo">Last 24 Hours</a>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6">
      <div class="card card-stats">
        <div class="card-header card-header-rose card-header-icon">
          <div class="card-icon">
            <i class="fa fa-file-text"></i>
          </div>
          <p class="card-category">FUNDED ORDERS</p>
          <h3 class="card-title">5.521</h3>
        </div>
        <div class="card-footer">
          <div class="stats">
            <i class="fa fa-undo"></i> Updated
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6">
      <div class="card card-stats">
        <div class="card-header card-header-success card-header-icon">
          <div class="card-icon">
            <i class="fa fa-thumbs-up"></i>
          </div>
          <p class="card-category">COMPLETED ORDERS</p>
          <h3 class="card-title">4,245</h3>
        </div>
        <div class="card-footer">
          <div class="stats">
            <i class="fa fa-undo"></i> Last 24 Hours
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6">
      <div class="card card-stats">
        <div class="card-header card-header-info card-header-icon">
          <div class="card-icon">
            <i class="fa fa-undo"></i>
          </div>
          <p class="card-category">CANCELLED ORDERS</p>
          <h3 class="card-title">245</h3>
        </div>
        <div class="card-footer">
          <div class="stats">
            <i class="fa fa-undo"></i> Just Updated
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-12">
      <div class="card card-stats" style="margin-top:0;">
        <div class="col-lg-12">

          <script src="https://code.highcharts.com/highcharts.js"></script>
          <script src="https://code.highcharts.com/modules/exporting.js"></script>
          <script src="https://code.highcharts.com/modules/export-data.js"></script>
          <script src="https://code.highcharts.com/modules/accessibility.js"></script>

          <figure class="highcharts-figure" style="min-width: auto;
          max-width: 100%;width:100%;"> 
          <div id="chart1" style="height:auto;"></div>
        </figure>
      </div>
    </div>
  </div>
</div>
</div>

<script type="text/javascript">
  Highcharts.chart('chart1', {
    chart: {
      type: 'areaspline'
    },
    title: {
      text: 'Order Statistics',
      align: 'left',
    },
    legend: {
      layout: 'vertical',
      align: 'left',
      verticalAlign: 'top',
      x: 150,
      y: 100,
      floating: true,
      borderWidth: 1,
      backgroundColor:
      Highcharts.defaultOptions.legend.backgroundColor || '#FFFFFF'
    },
    xAxis: {
      categories: [
      'Monday',
      'Tuesday',
      'Wednesday',
      'Thursday',
      'Friday',
      'Saturday',
      'Sunday'
      ],
    plotBands: [{ // visualize the weekend
      from: 4.5,
      to: 6.5,
      color: 'rgba(68, 170, 213, .2)'
    }]
  },
  yAxis: {
    title: {
      text: 'Fruit units'
    }
  },
  tooltip: {
    shared: true,
    valueSuffix: ' units'
  },
  credits: {
    enabled: false
  },
  plotOptions: {
    areaspline: {
      fillOpacity: 0.5
    }
  },
  series: [{
    name: 'John',
    data: [3, 4, 3, 5, 4, 10, 12]
  }, {
    name: 'Jane',
    data: [1, 3, 4, 3, 3, 5, 4]
  }]
});
</script>