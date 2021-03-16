
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
.padding
{
  padding: 8px;
}
</style>
<link rel="stylesheet" type="text/css" href="assets/lib/fselect/fSelect.css">
<div class="card mt-20" id="FollowUpSpin">
	<div class="card-header card-header-danger card-header-icon">
    <div class="card-icon">
      <i class="icon-file-check"></i>
    </div>
    <div class="row">
      <div class="col-md-6">
        <h4 class="card-title"> Production Summary </h4>
      </div>
    </div>
  </div>
  <div class="" id="filter-bar">
    <div id="advancedFilterForReport"  style="display: none;">
      <fieldset class="advancedsearchdiv">
        
        <form id="advancedsearchdata">
          <legend>Advanced Search</legend>
          <div class="col-md-12 pd-0">
            <div class="row " >
             
              <!-- time peroid -->
              <div style="width: 10%" class="padding ">
                <div class="form-group bmd-form-group">
                  <label for="adv_period" class="bmd-label-floating">Period</label>
                  <select class="select2picker form-control" id="adv_period"  name="period">   
                    <option></option>
                    <option  value="today">Today</option>
                    <option selected value="week">This Week</option>                
                    <option value="month">This Month</option>             
                    <option value="year">This Year</option>             
                  </select>
                </div>
              </div>

             <!-- user name filter with multi selecet -->
              <div style="width: 20%" class="padding agent">
                <div class="form-group bmd-form-group">
                  <label for="adv_Process" class="bmd-label-floating"> Users </label>
                  <select class="processUser form-control mdb-select" id="adv_Process"  name="Process" multiple="true" placeholder="Select User(s)">   
                    <?php foreach ($ActivityUsers as $key => $value) { ?>
                      <option value="<?php echo $value->UserUID; ?>" ><?php echo $value->UserName; ?></option>
                    <?php } ?>                      
                  </select>
                </div>
              </div>
              <?php 
                // $Workflow = $this->config->item('Workflows');
              ?>
              <!-- user name filter with multi selecet -->
              <!-- <div style="width: 20%" class="padding workflow">
                <div class="form-group bmd-form-group">
                  <label for="adv_workflow" class="bmd-label-floating"> Workflow </label>
                  <select class="form-control select2picker" id="adv_workflow"  name="workflow" placeholder="Select Workflow(s)">   
                    <?php foreach ($Workflow as $key => $value) { ?>
                      <option value="<?php echo $value; ?>" ><?php echo $key; ?></option>
                    <?php } ?>                      
                  </select>
                </div>
              </div> -->

              <!-- From Date filter with from date  -->
              <div style="width: 10%" class="padding">
                <div class="form-group bmd-form-group">
                  <label for="adv_fromDate" class="bmd-label-floating">From Date  <span style="color: red">*</span></label>
                  <input type="text" id="adv_fromDate" name="fromDate" class="form-control datepicker" value="<?php echo $date['firstday'] ?>">
                </div> 
              </div>

               <!-- To Date filter with from date  -->
              <div style="width: 10%" class="padding">
                <div class="form-group bmd-form-group">
                  <label for="adv_toDate" class="bmd-label-floating">To Date <span style="color: red">*</span></label>
                  <input type="text" id="adv_toDate" name="toDate" class="form-control datepicker" value="<?php echo $date['lastday'] ?>">
                </div> 
              </div>

              <!-- Include Weekends  -->
              <div style="width: 30%" class="padding">
                <div class="form-check">                  
                  <label class="form-check-label" style="color: teal;">
                    Include Weekends
                    <input class="form-check-input" id="IncludeWeekends" type="checkbox" value="<?php echo $Customers->IncludeWeekends; ?>" name="IncludeWeekends">
                    <span class="form-check-sign">
                      <span class="check"></span>
                    </span>
                  </label>
                </div>
              </div>
             
                </div>
              </div>
            
              <div class="col-md-12  text-right pd-0 mt-10">
                <button type="button" class="btn btn-fill btn-facebook  filterreport" >Submit</button>
                <button type="button" class="btn btn-fill btn-tumblr  reset">Reset</button>
              </div>

            </form>
          </fieldset>
        </div>
         <!--  <ul class="nav nav-pills nav-pills-rose customtab" role="tablist">
            <li class="nav-item">
              <a class="nav-link active followup-day" data-toggle="tab" href="#followup-day" role="tablist">
                  Followup Count - Same day - Title
              </a>
            </li>
              <li class="nav-item">
                <a class="nav-link followup-agent" data-toggle="tab" href="#followup-agent" role="tablist">
                  Followup Count - Title
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link followup-tat" data-toggle="tab" href="#followup-tat" role="tablist">
                  Followup Count - Title - Missed TAT
                </a>
              </li>    
          </ul> -->
            <div class="col-md-12 TableDiv scrool tab-content">
              <!-- <div class="tab-pane switchtab active" id="followup-day">
        	
        			</div>
              <div class="tab-pane switchtab" id="followup-agent">
              
              </div>
              <div class="tab-pane switchtab " id="followup-tat">
                
              </div> -->
        		</div>
            
            </div>

        </div>
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
  // $(document).on('click', '.nav-link', function(){
  //   $('.reset').trigger('click');
  //   if($(this).hasClass('followup-day')){
  //     $('.agent').addClass('hide');
  //     $('.workflow').removeClass('hide');
  //   }else if($(this).hasClass('followup-agent')){
  //     $('.agent').removeClass('hide');
  //     $('.workflow').addClass('hide');
  //   }else{
  //     $('.agent').addClass('hide');
  //     $('.workflow').removeClass('hide');
  //   }
  // });

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
  
  $(document).off('click','.filterreport').on('click','.filterreport',function()
  {
   var Process = $('#adv_Process').val();
   var workflow = $('#adv_workflow').val();
   var FromDate = $('#adv_fromDate').val();
   var ToDate = $('#adv_toDate').val();
   var Status = $('#adv_Status').val();
   var IncludeWeekends = $('#IncludeWeekends');
   if(IncludeWeekends.prop("checked") == true){
      IncludeWeekends = 1;
   }
   else if(IncludeWeekends.prop("checked") == false){
      IncludeWeekends = 0;
   }
   
   if((FromDate == '') || (ToDate == ''))
   {
     $.notify(
     {
      icon:"icon-bell-check",
      message:'Please select from date and to date'
    },
    {
      type:'danger',
      delay:1000 
    });
   }
   else 
   {
     var formData = ({'workflow': workflow, 'Process':Process,'FromDate': FromDate ,'ToDate' : ToDate,'Status':Status, 'IncludeWeekends': IncludeWeekends});
     ProductionSummary(formData);
   }

   return false;
 });

   $(document).off('click','.reset').on('click','.reset',function(){
    var period = 'today';
    $("#adv_period").val(period);
    $("#adv_Status").val('Pending');
    getDate(period);
    $("#adv_period").select2();
    $("#adv_Status").select2();
    $('.processinflow').hide();
  });

  function ProductionSummary(formData)
  { 	
  	$.ajax({
  		type: "POST",
  		url: '<?php echo base_url();?>ProductionSummary/getActiveLogs',
  		data: formData,
  		dataType: 'JSON',
  		beforeSend: function()
  		{
        addcardspinner($('#FollowUpSpin'));
  		},
  		success: function(data)
  		{
  			$('.TableDiv').html(data.table);
        removecardspinner($('#FollowUpSpin'));
  		}
  	});
  }

  // Trigger period 
  getDate('today');
  $("#adv_period").val('today');

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
        url: '<?php echo base_url();?>ProductionSummary/getFromToDate',
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



