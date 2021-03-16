
<style>
  th, td { text-align: center; }
  .bold{ font-weight: bold;  }
</style>
<link rel="stylesheet" type="text/css" href="assets/lib/fselect/fSelect.css">
<div class="card mt-40 inflowdiv" id="ProcessInflow">
  <div class="card-header card-header-danger card-header-icon">
    <div class="card-icon">
      <i class="icon-file-check"></i>
    </div>
    <div class="row">
      <div class="col-md-6">
        <h4 class="card-title">Inflow Report</h4>
      </div>
    </div>
  </div>
    <div class="text-right"> 
      <i class="fa fa-filter" title="Advanced Search" aria-hidden="true" style="font-size: 13px;color: #900C3F;cursor: pointer;"></i>&nbsp;&nbsp;
      <i class="fa fa-file-excel-o excelorderlist" title="Export Excel" aria-hidden="true" style="font-size: 13px;color:#0B781C;cursor: pointer;"></i>
    </div>


    <div id="advancedFilterForReport"  style="display: none;">
      <fieldset class="advancedsearchdiv">
        <legend>Advanced Search</legend>
        <form id="advancedsearchdata">
          <div class="col-md-12 pd-0">
            <div class="row " >
             

             <!-- user name filter with multi selecet -->
             <!--  <div class="col-md-3 ">
                <div class="form-group bmd-form-group">
                  <label for="adv_Process" class="bmd-label-floating">Process Users </label>
                  <select class="processUser form-control mdb-select" id="adv_Process"  name="Process" multiple="true" placeholder="Select User(s)">   
                    <?php foreach ($ProcessUsers as $key => $value) { ?>
                      <option value="<?php echo $value->UserUID; ?>" ><?php echo $value->UserName; ?></option>
                    <?php } ?>                      
                  </select>
                </div>
              </div> -->

               <!-- time peroid -->
              <div class="col-md-3 ">
                <div class="form-group bmd-form-group">
                  <label for="adv_period" class="bmd-label-floating">Period</label>
                  <select class="select2picker form-control" id="adv_period"  name="period">   
                    <option></option>
                    <option  value="today">Today</option>
                    <option  value="week">This Week</option>                
                    <option selected value="month">This Month</option>             
                  </select>
                </div>
              </div>

              <!-- order completed status -->
               <div class="col-md-3 ">
                <div class="form-group bmd-form-group">
                  <label for="adv_Status" class="bmd-label-floating">Status</label>
                  <select class="select2picker form-control" id="adv_Status"  name="Status">   
                    <option value="All">All</option>
                    <option value="Pending">Pending</option>
                    <option value="Completed">Completed</option>                           
                  </select>
                </div>
              </div>

              <!-- From Date filter with from date  -->
              <div class="col-md-3">
                <div class="form-group bmd-form-group">
                  <label for="adv_fromDate" class="bmd-label-floating">From Date  <span style="color: red">*</span></label>
                  <input type="text" id="adv_fromDate" name="fromDate" class="form-control datepicker" value="<?php echo $date['firstday'] ?>">
                </div> 
              </div>

               <!-- To Date filter with from date  -->
              <div class="col-md-3">
                <div class="form-group bmd-form-group">
                  <label for="adv_toDate" class="bmd-label-floating">To Date <span style="color: red">*</span></label>
                  <input type="text" id="adv_toDate" name="toDate" class="form-control datepicker" value="<?php echo $date['lastday'] ?>">
                </div> 
              </div>


                </div>
              </div>
            
              <div class="col-md-12  text-right pd-0 mt-10">
                <button type="button" class="btn btn-fill btn-facebook  filterreport" >Submit</button>
                <!-- <button type="button" class="btn btn-fill btn-tumblr  reset">Reset</button> -->
              </div>

            </form>
          </fieldset>
        </div>
  
<ul class="nav nav-pills nav-pills-danger customtab prioritytab" role="tablist">
  <li class="nav-item">
    <a data-reporttype="" class="nav-link ajaxload <?php if ($this->uri->segment(1) == 'InflowReport' && $this->uri->segment(2) == '') {echo "active";} ?>" href="<?php echo base_url(); ?>InflowReport" role="tablist">InflowReport
    </a>
  </li>
  <li class="nav-item">
    <a data-reporttype="agingbucket" class="nav-link ajaxload <?php if ($this->uri->segment(1) == 'InflowReport' && $this->uri->segment(2) == 'Goals') {echo "active";} ?>" href="<?php echo base_url(); ?>InflowReport/Goals" role="tablist">Projected Goals</a>
  </li>
</ul>


        <div class="col-md-12 TableDiv">

          
              </div>

            </div>
                        <!-- Orders Table -->
            <?php $this->load->view('InflowReport/CountListView'); ?>
            <!-- Orders Table -->
            <script src="assets/js/app/inflowReport.js?reload=1.0.1"></script>
<script type="text/javascript">

 $('table tr:last').css({"font-weight":"bold",'color':'#000'}); 

   $(function() {
    $(".select2picker").select2({
      theme: "bootstrap",
    });
  });

   $("#advancedFilterForReport").show();
  $('.fa-filter').click(function(){
    $("#advancedFilterForReport").slideToggle();
  });
 
  $(document).off('click','.filterreport').on('click','.filterreport',function()
  {
   var FromDate = $('#adv_fromDate').val();
   var ToDate = $('#adv_toDate').val();
   var Status = $('#adv_Status').val();

   if((FromDate == '') || (ToDate == ''))
   {
     $.notify(
     {
      icon:"icon-bell-check",
      message:'Please Choose Date'
    },
    {
      type:'danger',
      delay:1000 
    });
   }

   else 
   {
     var formData = ({'FromDate': FromDate ,'ToDate' : ToDate,'Status':Status});
     ProcessReport(formData);
   }

   return false;
 });

  $(document).off('click','.reset').on('click','.reset',function(){
    var period = 'month';
    $("#adv_period").val(period);
    getDate(period);
    $("#adv_Status").val('Pending');
     $("#adv_period").select2();
      $("#adv_Status").select2();
      $('.inflowtable').hide();
  });

  function ProcessReport(formData)
  {    
    $.ajax({
      type: "POST",
      url: '<?php echo base_url();?>InflowReport/getProcessTable',
      data: formData,
      dataType: 'JSON',
      beforeSend: function()
      {
        addcardspinner($('#ProcessInflow'));
      },
      success: function(data)
      {
        $('.TableDiv').html(data);
        removecardspinner($('#ProcessInflow'));
      }
    });
  }


  $(document).off('click','.excelorderlist').on('click','.excelorderlist',function(){
  var FromDate = $('#adv_fromDate').val();
  var ToDate = $('#adv_toDate').val();
  var Status = $('#adv_Status').val();
  var formData = ({'FromDate': FromDate ,'ToDate' : ToDate,'Status':Status});
    var filename = 'InflowReport.xlsx';
    $.ajax({
      type: "POST",
      url:'<?php echo base_url();?>InflowReport/WriteOrdersExcel',
      xhrFields: {
        responseType: 'blob',
      },
      data: formData,
      beforeSend: function(){
         addcardspinner($('#ProcessInflow'));
      },
      success: function(data)
      {
        if (typeof window.chrome !== 'undefined') {
          //Chrome version
          var link = document.createElement('a');
          link.href = window.URL.createObjectURL(data);
          link.download = filename;
          link.click();
        } else if (typeof window.navigator.msSaveBlob !== 'undefined') {
          //IE version
          var blob = new Blob([data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
          window.navigator.msSaveBlob(blob, filename);
        } else {
          //Firefox version
          var file = new File([data], filename, { type: 'application/octet-stream' });
          window.open(URL.createObjectURL(file));
        }
        removecardspinner($('#ProcessInflow'));
      },
      error: function (jqXHR, textStatus, errorThrown) {

        console.log(jqXHR);


      },
      failure: function (jqXHR, textStatus, errorThrown) {

        console.log(errorThrown);

      },
    });

  });


  $('#adv_period').change(function()
  {
    var period = $(this).val();
    getDate(period);
  });

    function getDate(period)
    {      
      if(period == 'today')
      {
        $('#adv_fromDate').val("<?php echo date('m/d/Y') ?>");
        $('#adv_toDate').val("<?php echo date('m/d/Y') ?>");
      }
      else
      {
        $.ajax({
          type: "POST",
          url: '<?php echo base_url();?>InflowReport/getFromToDate',
          data: {'period':period},
          dataType: 'JSON',
          success: function(data)
          {
            $('#adv_fromDate').val(data.fromDate);
            $('#adv_toDate').val(data.toDate);
          }
        });
      }
    }


  $('#adv_fromDate').focusout(function()
  {
    $('#adv_period').val('<option></option>');
    $("#adv_period").select2();
  });

  $('#adv_toDate').focusout(function()
  {
    $('#adv_period').val('<option></option>');
    $("#adv_period").select2();
  });

</script>



