<style type="text/css">
   .bootstrap-select .dropdown-menu{
   z-index: 9999 !important;
   }
   #workflowdependent .select2-container.select2-container-disabled .select2-choice {
   background-color: #ffffff;
   border:0;
   border-radius:0;
   padding-left:0;
   }
   #workflowdependent .select2-container.select2-container-disabled .select2-choice .select2-arrow {
   background-color: #ffffff;
   }
   #workflowdependent .select2-container-multi .select2-choices{
   border: 0;
   border-bottom: 1px solid #c1c1c1;
   background-image: none;
   }
   #workflowdependent tbody tr td{
   padding: 8px 5px 8px 5px!important;
   }
   .move-handle-icon{
   cursor:grab;
   }
   /*Hide Arrows From Input Number*/
   /* Chrome, Safari, Edge, Opera */
   input::-webkit-outer-spin-button,
   input::-webkit-inner-spin-button {
   -webkit-appearance: none;
   margin: 0;
   }
   /* Firefox */
   input[type=number] {
   -moz-appearance: textfield;
   }
</style>
<div class="material-datatables" >
   <table class="table display nowrap sort_par_div" id="workflowdependent"  cellspacing="0" width="100%"  style="width:100%">
      <thead>
         <tr>
            <th style="width: 50%;">Workflow</th>
            <th style="width: 50%;" class="text-center">Milestone</th>
         </tr>
      </thead>
      <tbody>
         <?php
            foreach ($milestonematricsdetails as $key => $value) { if($value['WorkflowModuleUID']) { ?>
         <tr id='<?php echo $value["WorkflowModuleUID"];?>'>
            <td>
               <div class="form-group">
                  <?php foreach ($WorkflowDetails as $key => $workflow) {
                  if($workflow->WorkflowModuleUID == $value['WorkflowModuleUID']){  ?>
                  <span><?php echo $value['ProductCode']." - ".$workflow->SystemName;?></span>
                  <?php } } ?> 
                  <!-- <select class="select2picker workflowUID"  name="workflowUID" style="width: 100%" disabled>
                     <?php foreach ($WorkflowDetails as $key => $workflow) {
                        if($workflow->WorkflowModuleUID == $value['WorkflowModuleUID']){  ?>
                     <option value="<?php echo $workflow->WorkflowModuleUID; ?>" selected><?php echo $workflow->SystemName;?></option>
                     <?php }else{  ?>
                     <option value="<?php echo $workflow->WorkflowModuleUID; ?>"><?php echo $workflow->SystemName;?></option>
                     <?php } } ?>  
                  </select> -->
               </div>
            </td>
            <!-- Milestone -->
            <td>
               <div class="form-group">
                  <select class="select2picker MilestoneUID" name="MilestoneUID" <?php if(isset($value['MileStoneUID'])) { echo "data-oldmilestoneuid='".$value['MileStoneUID']."'"; } ?> data-live-search="true" style="width: 100%">
                     <option value="0" selected="selected">Select Milestone</option>
                     <?php foreach ($MilestoneDetaiils as $key => $milestone) {
                        if($milestone->MilestoneUID == $value['MileStoneUID']){  ?>
                     <option value="<?php echo $milestone->MilestoneUID; ?>" selected><?php echo $milestone->MilestoneName;?></option>
                     <?php }else{  ?>
                     <option value="<?php echo $milestone->MilestoneUID; ?>"><?php echo $milestone->MilestoneName;?></option>
                     <?php } } ?>  
                  </select>
               </div>
            </td>
         </tr>
         <?php } } ?>
      </tbody>
   </table>
</div>

<div class="well clearfix">
   <a class="btn btn-default pull-right add-othermilestone active" data-added="0"><i class="glyphicon glyphicon-plus"></i> Add Milestone</a>
</div>

<div class="material-datatables" >
<table class="table display nowrap sort_par_div" id="MilestoneMetricsOptional"  cellspacing="0" width="100%"  style="width:100%">
   <thead>
      <tr>
         <th style="width: 90%">Milestone</th>
         <th style="width: 10%;" class="text-left">Action</th>   
      </tr>
   </thead>
   <tbody id="MilestonMetricsOptionalbody">
      <?php 
      $rec = 1;
      foreach ($milestonematricsdetails as $key => $value) {
         if (!$value['WorkflowModuleUID']) { ?>
            <tr id='0'>
               <td>
                  <div class="form-group">
                     <select class="select2picker MilestoneUID" name="MilestoneUID" <?php echo "data-oldmilestoneuid='".$value['MileStoneUID']."'"; ?> data-live-search="true" style="width: 100%">
                        <option value="0" selected="selected">Select Milestone</option>
                        <?php foreach ($MilestoneDetaiils as $key => $milestone) {
                           if($milestone->MilestoneUID == $value['MileStoneUID']){  ?>
                        <option value="<?php echo $milestone->MilestoneUID; ?>" selected><?php echo $milestone->MilestoneName;?></option>
                        <?php }else{  ?>
                        <option value="<?php echo $milestone->MilestoneUID; ?>"><?php echo $milestone->MilestoneName;?></option>
                        <?php } } ?>  
                     </select>
                     <!-- <select class="select2picker MilestoneUID" name="MilestoneUID" data-live-search="true" style="width: 20%">
                        <option value="0" selected="selected">Select Milestone</option>
                        <?php foreach ($MilestoneDetaiils as $key => $milestone) { ?>
                        <option value="<?php echo $milestone->MilestoneUID; ?>"><?php echo $milestone->MilestoneName;?></option>
                        <?php } ?>  
                     </select> -->
                  </div>
               </td>
               <td>
                  <a class="delete-optionalmilestone" data-id="" title="Delete Optional Milestone">
                     <i class="fa fa-trash pull-center" aria-hidden="true" style="font-size: 20px;"></i>
                   </a>
               </td>
            </tr>
         <?php $rec++; }
      } ?>      
   </tbody>
</table>
</div>
<div style="display:none;">
   <table id="milestonemetricsdefaultrow">
      <tr id='0'>
         <td>
            <div class="form-group">
               <select class="select2picker MilestoneUID" name="MilestoneUID" data-live-search="true" style="width: 100%">
                  <option value="0" selected="selected">Select Milestone</option>
                  <?php foreach ($MilestoneDetaiils as $key => $milestone) { ?>
                  <option value="<?php echo $milestone->MilestoneUID; ?>"><?php echo $milestone->MilestoneName;?></option>
                  <?php } ?>  
               </select>
            </div>
         </td>
         <td>
            <a class="delete-optionalmilestone" data-id="" title="Delete Optional Milestone">
               <i class="fa fa-trash pull-center" aria-hidden="true" style="font-size: 20px;"></i>
             </a>
         </td>
      </tr>
   </table>
</div>
<div class="ml-auto text-right">
   <a href="Customer" class="btn btn-fill btn-danger btn-wd btn-back" name="UpdateCustomer"><i class="icon-arrow-left8 pr-10"></i> Back</a>
</div>
<div class="clearfix"></div>
<script src="assets/js/jquery-ui.min.js"></script>
<script type="text/javascript">
   function callselect2() {
   	$("select.select2picker").select2({
   		theme: "bootstrap",
   	});
   }
   
   $(function() {
   $("select.select2picker").select2({
   	theme: "bootstrap",
   });
   });
   
   
   $(document).ready(function() {
   
   $("div.inner.show").addClass("perfectscrollbar");
   $('.perfectscrollbar').perfectScrollbar();
   
   $("body").on("click" , ".removeproductrow" , function(e){
   	e.preventDefault();
   	$(this).closest(".WorkflowmoduleWrapper").remove();
   	return false;
   });
   
   //Customer workflow milestone update
   $(document).off('change', '.MilestoneUID').on('change', '.MilestoneUID', function (e) {
      e.preventDefault();
   	var CustomerUID = $('#CustomerUID').val();
   	var workflowUID = $(this).closest("tr").attr('id');
      var oldmilestoneuid = 0;
      oldmilestoneuid = $(this).data("oldmilestoneuid");
      
      if (workflowUID == '0') {
         workflowUID == null;
      }
   	var MilestoneUID = $(this).val();
   	$.ajax({
   		type:'POST',
   		dataType: 'JSON',
   		global: false,
   		url:'<?php echo base_url();?>Customer/insertcustomermilestone',
   		data: {'CustomerUID':CustomerUID,'workflowUID':workflowUID,'MilestoneUID':MilestoneUID,'OldMilestoneUID':oldmilestoneuid},
   		success: (json) => {
   			console.log(json);
            if (!json.error) {
               if (MilestoneUID == 0) {
                  $(this).removeAttr('data-oldmilestoneuid');
                  $(this).removeData("oldmilestoneuid");
               } else {
                  $(this).removeData("oldmilestoneuid");
                  $(this).attr('data-oldmilestoneuid', MilestoneUID);
               }
            }
   			$.notify({icon:"icon-bell-check",message:json.msg},{type:json.type,delay:3000 });
   		}
   	});
   });

   $(document).off('click', 'a.add-othermilestone').on('click', 'a.add-othermilestone', function(e) {
      e.preventDefault();
      $("select.select2picker").select2('destroy');
      var content = $('#milestonemetricsdefaultrow tr'),
          size = $('#MilestoneMetricsOptional >tbody >tr').length + 1,
          element = null,
          element = content.clone();
      element.attr('id', '0');
      element.find('.delete-optionalmilestone').attr('data-id', size);
      element.appendTo('#MilestonMetricsOptionalbody');
      element.find('.sn').html(size);
      $("select.select2picker").select2({
          theme: "bootstrap",
      });
      });

      $(document).off('click', 'a.delete-optionalmilestone').on('click', 'a.delete-optionalmilestone', function(e) {
         e.preventDefault();        
         var CustomerUID = $('#CustomerUID').val();
         var oldmilestoneuid = $(this).closest('tr').children().find('.MilestoneUID :selected').val();
         if (oldmilestoneuid != 0) {
            $.ajax({
               type:'POST',
               dataType: 'JSON',
               global: false,
               url:'<?php echo base_url();?>Customer/insertcustomermilestone',
               data: {'CustomerUID':CustomerUID,'OldMilestoneUID':oldmilestoneuid},
               success: (json) => {
                  console.log(json);
                  if (!json.error) {
                     $(this).closest('tr').remove();                   
                  }
                  $.notify({icon:"icon-bell-check",message:json.msg},{type:json.type,delay:1000 });
               }
            });
         } else {
            $(this).closest('tr').remove();
         }
      });
   
});
   
</script>