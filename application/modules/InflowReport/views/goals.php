
<style>
  th, td { text-align: center; }
  .bold{ font-weight: bold;  }
</style>
<link rel="stylesheet" type="text/css" href="assets/lib/fselect/fSelect.css">
<div class="card mt-40" id="ProcessInflow">
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
             <!--  <div class="col-md-4 ">
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
              <!-- <div class="col-md-3 ">
                <div class="form-group bmd-form-group">
                  <label for="adv_period" class="bmd-label-floating">Period</label>
                  <select class="select2picker form-control" id="adv_period"  name="period">   
                    <option></option>
                    <option  value="today">Today</option>
                    <option selected value="week">This Week</option>                
                    <option value="month">This Month</option>             
                  </select>
                </div>
              </div>
 -->
               <!-- taeget -->
              <div class="col-md-4">
                <div class="form-group bmd-form-group">
                  <label for="adv_Goals" class="bmd-label-floating">Projected Goals<span style="color: red">*</span></label>
                  <input type="text" id="adv_Goals" name="Goals" class="form-control" value="<?php echo !empty($MTDGoals) ? $MTDGoals : '';  ?>">
                </div> 
              </div>

              <!-- From Date filter with from date  -->
             <!--  <div class="col-md-4">
                <div class="form-group bmd-form-group">
                  <label for="adv_fromDate" class="bmd-label-floating">Date  <span style="color: red">*</span></label>
                  <input type="text" id="adv_fromDate" name="fromDate" class="form-control datepicker" value="<?php echo $date['firstday'] ?>">
                </div> 
              </div> -->

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
<script type="text/javascript">
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
   var Goals = $('#adv_Goals').val();

   if((FromDate == '') || (Goals == '') )
   {
     $.notify(
     {
      icon:"icon-bell-check",
      message:'Please Choose Goals'
    },
    {
      type:'danger',
      delay:1000 
    });
   }
   else 
   {
     var formData = ({'FromDate': FromDate ,'Goals' : Goals});
     ProcessReport(formData);
   }

   return false;
 });

  $(document).off('click','.reset').on('click','.reset',function(){
    var period = 'week';
    $("#adv_period").val(period);
    // getDate(period);
     $("#adv_period").select2();
      $('.goalstable').hide();
  });

  function ProcessReport(formData)
  {    
    $.ajax({
      type: "POST",
      url: '<?php echo base_url();?>InflowReport/getGoalsTable',
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
  var Goals = $('#adv_Goals').val();
  var formData = ({'FromDate': FromDate ,'Goals' : Goals});
    var filename = 'GoalsReport.xlsx';
    $.ajax({
      type: "POST",
      url:'<?php echo base_url();?>InflowReport/WriteGoalsExcel',
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


  // $('#adv_period').change(function()
  // {
  //   var period = $(this).val();
  //   getDate(period);
  // });

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


  $('#adv_Goals').focusout(function()
  {
    var MTDGoals = $(this).val();
    $.ajax({
      type: "POST",
      url: '<?php echo base_url();?>InflowReport/ChangeMTDGoals',
      data: {'MTDGoals':MTDGoals},
      dataType: 'JSON',
      success: function(data)
      {
        if(data == 1)
        {
         $.notify(
         {
          icon:"icon-bell-check",
          message:'MTDGoals Updated'
        },
        {
          type:'success',
          delay:1000 
        });
       }
     }
   }); 
  });

  // $('#adv_toDate').focusout(function()
  // {
  //   $('#adv_period').val('<option></option>');
  //   $("#adv_period").select2();
  // });

</script>



