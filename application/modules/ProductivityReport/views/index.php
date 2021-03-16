
<style>
	th, td { text-align: center; }
	.bold{ font-weight: bold;  }
	.scrool {
    overflow-x: auto !important;
}
.mdb-select {
  padding: 0;
}
 .fs-wrap.multiple .fs-option.selected .fs-checkbox i {
    background-color:#e91e63 !important;
  }
  .fs-option:hover {
    background-color: #e91e63 !important;
    color: #fff !important;
}
</style>
<link rel="stylesheet" type="text/css" href="assets/lib/fselect/fSelect.css">
<div class="card mt-40 inflowdiv" id="ProcessInflow">
	<div class="card-header card-header-danger card-header-icon">
    <div class="card-icon">
      <i class="icon-file-check"></i>
    </div>
    <div class="row">
      <div class="col-md-6">
        <h4 class="card-title">Productivity Report</h4>
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
              <div class="col-md-3 ">
                <div class="form-group bmd-form-group">
                  <label for="adv_Process" class="bmd-label-floating">Process Users </label>
                  <select class="processUser form-control mdb-select" id="adv_Process"  name="Process" multiple="true" placeholder="Select User(s)">   
                    <?php foreach ($ProcessUsers as $key => $value) {
                  if($value->UserUID == $this->loggedid){ ?>
                    <option selected value="<?php echo $value->UserUID; ?>" ><?php echo $value->UserName; ?></option>
                  <?php } else {?>
                    <option value="<?php echo $value->UserUID; ?>" ><?php echo $value->UserName; ?></option>
                  <?php } } ?>                      
                  </select>
                </div>
              </div>


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

               <!-- Goal Filter -->
              <div class="col-md-3">
                <div class="form-group bmd-form-group">
                  <label for="adv_Target" class="bmd-label-floating">Target <span style="color: red">*</span></label>
                  <input type="text" id="adv_Target" name="Target" class="form-control" value="">
                </div> 
              </div>

              <!-- workflow filter -->
              <div class="col-md-3 ">
                <div class="form-group bmd-form-group">
                  <label for="adv_WorkflowModuleUID" class="bmd-label-floating">Workflow </label>
                  <select class="select2picker form-control" id="adv_WorkflowModuleUID"  name="WorkflowModuleUID">   
                    <?php foreach ($Modules as $key => $value) { ?>
                      <option value="<?php echo $value->WorkflowModuleUID; ?>" ><?php echo $value->SystemName; ?></option>
                    <?php } ?>                      
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
  

        <div class="col-md-12 TableDiv scrool">

        	
        			</div>

        		</div>

            <!-- Orders Table -->
            <?php $this->load->view('InflowReport/CountListView'); ?>
            <!-- Orders Table -->
            <script src="assets/js/app/inflowReport.js?reload=1.0.1"></script>
<script type="text/javascript" src="assets/lib/fselect/fSelect.js"></script>

<script type="text/javascript">
  create_fselect();
    function create_fselect(){
    $(".mdb-select").each(function(){
      var placeholder = $(this).attr('placeholder');
      $(this).fSelect({
        placeholder: placeholder,
        numDisplayed: 2,
        overflowText: '{n} selected', 
        showSearch: true
      }); 
    });   
  }

    function destroy_fselect(){
    $(".mdb-select").fSelect('destroy');
  }

	 $(function() {
    $(".select2picker").select2({
      theme: "bootstrap",
    });
  });

   $("#advancedFilterForReport").show();
  $('.fa-filter').click(function(){
    $("#advancedFilterForReport").slideToggle();
  });
  // var Process = $('#adv_Process').val();
  // var FromDate = $('#adv_fromDate').val();
  // var ToDate = $('#adv_toDate').val();
  // var Status = $('#adv_Status').val();
  // var formData = ({'Process':Process,'FromDate': FromDate ,'ToDate' : ToDate,'Status':Status}); 
  // ProcessReport(formData);
  $(document).off('click','.filterreport').on('click','.filterreport',function()
  {
   var Process = $('#adv_Process').val();
   var FromDate = $('#adv_fromDate').val();
   var ToDate = $('#adv_toDate').val();
   var Status = $('#adv_Status').val();
   var Target = $('#adv_Target').val();
   var WorkflowModuleUID = $('#adv_WorkflowModuleUID').val();

   if(Process == '')
   {
     $.notify(
     {
      icon:"icon-bell-check",
      message:'Please Choose User'
    },
    {
      type:'danger',
      delay:1000 
    });
   }
   else if((FromDate == '') || (ToDate == ''))
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
   else if(Target == '')
   {
     $.notify(
     {
      icon:"icon-bell-check",
      message:'Please Choose Target'
    },
    {
      type:'danger',
      delay:1000 
    });

   }
   else 
   {
     var formData = ({'Process':Process,'FromDate': FromDate ,'ToDate' : ToDate,'Status':Status,'Target':Target,'WorkflowModuleUID':WorkflowModuleUID});
     ProcessReport(formData);
   }

   return false;
 });

   $(document).off('click','.reset').on('click','.reset',function(){
    var period = 'month';
    $("#adv_period").val(period);
    $('#adv_Target').val('');
    $('#adv_WorkflowModuleUID').val('<option></optiojn>');
    // $("#adv_Status").val('Pending');
    getDate(period);
    $("#adv_period").select2();
    $("#adv_Status").select2();
    $("#adv_WorkflowModuleUID").select2();
    $('.productivityreport').hide();
  });

  function ProcessReport(formData)
  { 	
  	$.ajax({
  		type: "POST",
  		url: '<?php echo base_url();?>ProductivityReport/getProcessTable',
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
    var Process = $('#adv_Process').val();
  var FromDate = $('#adv_fromDate').val();
  var ToDate = $('#adv_toDate').val();
  var Status = $('#adv_Status').val();
  var Target =$('#adv_Target').val();
   var WorkflowModuleUID = $('#adv_WorkflowModuleUID').val();
  var formData = ({'Process':Process,'FromDate': FromDate ,'ToDate' : ToDate,'Status':Status,'Target':Target,'WorkflowModuleUID':WorkflowModuleUID});
    var filename = 'ProductivityReport.xlsx';
    $.ajax({
      type: "POST",
      url:'<?php echo base_url();?>ProductivityReport/WriteOrdersExcel',
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
        url: '<?php echo base_url();?>ProcessInflowReport/getFromToDate',
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
</script>



