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
				.scrool {
				overflow-x: auto !important;
				}
			</style>

			<div class="material-datatables scrool" >
				<table class="table display sort_par_div" id="workflowdependent"  cellspacing="0">
				<thead>
					<tr>
						<th style="width: 5%;">Workflow</th>
						<th style="width: 30%;">Dependent Workflows</th>							

						<th style="width: 10%">State</th>
						<th style="width: 10%">Loan Type</th>
						<th style="width: 10%">Property Type</th>
						<th style="width: 10%">Milestone</th>

						<th style="width: 10%">Highlight Duration(Hrs)</th>

						<th style="width: 1%;">Optional</th>
						<th style="width: 1%;">Document Chase</th>
						<th style="width: 1%;">Escalation</th>
						<th style="width: 1%;" title="Enable parking">Parking</th>
						<th style="width: 10;" title="Select parking either auto or manual">Parking Type</th>
						<th style="width: 5%;" title="Parking duration in Hrs">Parking Duration(Hrs)</th>
						<th style="width: 5%;" title="Loans will be moved to respective queues when parking duration crossed">Parking - Other Queues</th>
						<th style="width: 10%;">Category</th>
						<th style="width: 10%;">SLA(Hrs)</th>
						<!-- <th style="width: 5%;">CheckList sequence</th> -->
						<th style="width: 10%;">Status</th>									
						<th style="width: 5%;">Drag</th>									
					</tr>
				</thead>
				 <tbody>
				 	<?php
				 	foreach ($Customer_Workflow as $key => $value) { ?>
                    <tr id='<?php echo $value["WorkflowModuleUID"];?>'>
                      <td>
                      	<div class="form-group">
                      		                   
                      		<?php foreach ($WorkflowDetails as $key => $workflow) {
                      			if($workflow->WorkflowModuleUID == $value['WorkflowModuleUID']){  ?>
                      				
                      				<div id="colorpicker" >
                      					<input id="SectionColor<?php echo $value['WorkflowModuleUID']; ?>" class="SectionColor" name="SectionColor" type="hidden" data-workflow='<?php echo $value['WorkflowModuleUID']; ?>' value="<?php echo $value['ColorCode'];?>">
                      					<div>
                      						<div >
                      							<button id="color" class="jscolor {hash:true,valueElement:'SectionColor<?php echo $value['WorkflowModuleUID']; ?>'}" style="width: 100%;text-align: center;">
                      								<?php echo $value['ProductCode']." - ".$workflow->SystemName;?>
                      							</button>
                      						</div>
                      					</div> 
                      				</div>

                      				<!-- <button id="color1" class="jscolor {valueElement:'ForeColor', onFineChange:'setTextColor(this)'}" style="width: 100%;text-align: center; background: <?php echo $value['BgColor']; ?>; color: <?php echo $value['ForColor']; ?>">
                      					<?php echo $workflow->SystemName;?>
                      				</button>   -->
                      		<?php } } ?>  

                      			
                      	</div>
                      </td>
                      <td>
                      	<select class="select2picker DependentWorkflow" autocomplete="off" name="DependentWorkflow[]" multiple="multiple" style="width: 145px !important;">  
                      		
		        				<?php 		        				
		        				$DependentWorkflow_arr = explode (",", $value['DependentWorkflow']);
		        				foreach ($WorkflowDetails as $key => $workflow) {
		              				if($workflow->WorkflowModuleUID == $value['WorkflowModuleUID']){  ?>
		              				<?php }
		              				else if (in_array($workflow->WorkflowModuleUID, $DependentWorkflow_arr)) {   ?>
		              					<option value="<?php echo $workflow->WorkflowModuleUID; ?>" selected><?php echo $workflow->SystemName;?></option>
		              				<?php } else {?>

		              					<option value="<?php echo $workflow->WorkflowModuleUID; ?>"><?php echo $workflow->SystemName;?></option>

		              				<?php }
		              				 } ?>  
                      	</select>
                      </td>

                      <!--State, Loan Type and Property Type-->
                      <td>
                      	<select class="select2picker state" autocomplete="off" name="state[]" multiple="multiple" style="width: 100px !important;">  
                      		
		        				<?php 		        				
		        				$state_arr = explode (",", $value['State']);
		        				foreach ($GetStateDetails as $key => $state) { 
	              					if (in_array($state->StateCode, $state_arr)) {   ?>
		              					<option value="<?php echo $state->StateCode; ?>" selected><?php echo $state->StateCode;?></option>
		              				<?php } else {?>

		              					<option value="<?php echo $state->StateCode; ?>"><?php echo $state->StateCode;?></option>

		              				<?php }
	              				 } ?>  
                      	</select>
                      </td>
                      <td>
                      	<select class="select2picker LoanTypeName" autocomplete="off" name="LoanTypeName[]" multiple="multiple" style="width: 120px !important;">  
                      		
		        				<?php 		        				
		        				$LoanTypeName_arr = explode (",", $value['LoanTypeName']);
		        				foreach ($GetLoanTypeDetails as $key => $LoanType) { 
	              					if (in_array($LoanType->LoanTypeName, $LoanTypeName_arr)) {   ?>
		              					<option value="<?php echo $LoanType->LoanTypeName; ?>" selected><?php echo $LoanType->LoanTypeName;?></option>
		              				<?php } else {?>

		              					<option value="<?php echo $LoanType->LoanTypeName; ?>"><?php echo $LoanType->LoanTypeName;?></option>

		              				<?php }
	              				 } ?>  
                      	</select>
                      </td>

                      <!-- Customer workflow PropertyType -->
                      <td>
                      	<div class="form-group">
	                      <div>
	                      	<?php if (!empty($value['PropertyType'])) { ?>
	                      		<input type="text" style="width: 90px;" name="PropertyType" id="PropertyType" data-PropertyType="<?php echo $value['PropertyType']; ?>" value="<?php echo $value['PropertyType']; ?>" class="form-control input-xs">	
	                      	<?php } else { ?>
	                      		<input type="text" style="width: 90px;" name="PropertyType" id="PropertyType" data-PropertyType="" value="" class="form-control input-xs">
	                      	<?php } ?>
	                      	<input type="hidden" class="PropertyType_hidden" name="PropertyType_hidden" value="<?php echo $value['PropertyType']; ?>">
	                        
	                      </div>
	                    </div>
                      </td>
                      <!--State, Loan Type and Property Type-->

                      <td>
                      	<select class="select2picker MilestoneUID" autocomplete="off" name="MilestoneUID[]" multiple="multiple" style="width: 120px !important;">
	        				<?php 		        				
	        				$MilestoneUID_arr = explode (",", $value['MilestoneUID']);
	        				foreach ($GetMilestoneDetails as $key => $LoanType) { 
              					if (in_array($LoanType->MilestoneUID, $MilestoneUID_arr)) {   ?>
	              					<option value="<?php echo $LoanType->MilestoneUID; ?>" selected><?php echo $LoanType->MilestoneName;?></option>
	              				<?php } else {?>

	              					<option value="<?php echo $LoanType->MilestoneUID; ?>"><?php echo $LoanType->MilestoneName;?></option>

	              				<?php }
              				 } ?>  
                      	</select>
                      </td>

                      <!-- Customer workflow Order highlights hours -->
                      <td>
                      	<div class="form-group">
	                      <div>
	                      	<?php if (!empty($value['OrderHighlightDuration'])) { ?>
	                      		<input type="text" style="width: 90px;" name="OrderHighlightDuration" id="OrderHighlightDuration" data-OrderHighlightDuration="<?php echo $value['OrderHighlightDuration']; ?>" value="<?php echo $value['OrderHighlightDuration']; ?>" class="form-control input-xs">	
	                      	<?php } else { ?>
	                      		<input type="text" style="width: 90px;" name="OrderHighlightDuration" id="OrderHighlightDuration" data-OrderHighlightDuration="" value="" class="form-control input-xs">
	                      	<?php } ?>
	                      	<input type="hidden" class="OrderHighlightDuration_hidden" name="OrderHighlightDuration_hidden" value="<?php echo $value['OrderHighlightDuration']; ?>">
	                        
	                      </div>
	                    </div>
                      </td>
                      <!--Customer workflow Order highlights hours End-->

                      <td class="text-center">
                          <div class="form-check">
                            <label class="form-check-label " style="color: teal">
                              <input class="form-check-input" id="Optional" type="checkbox" value="<?php echo $value['WorkflowModuleUID'] ; ?>" name="Optional" <?php if($value['Optional']){echo 'checked';} ?>>
                              <span class="form-check-sign">
                                <span class="check"></span>
                              </span>
                            </label>
                          </div>
                      </td>

                      <!-- Is Document Chase Required -->
                      <td class="text-center">
                          <div class="form-check">
                            <label class="form-check-label " style="color: teal">
                              <input class="form-check-input" id="IsDocChaseRequire" type="checkbox" value="<?php echo $value['IsDocChaseRequire'] ; ?>" name="IsDocChaseRequire" <?php if($value['IsDocChaseRequire']){echo 'checked';} ?>>
                              <span class="form-check-sign">
                                <span class="check"></span>
                              </span>
                            </label>
                          </div>
                      </td>

                      <!-- Is Escalation Require -->
                      <td class="text-center">
                          <div class="form-check">
                            <label class="form-check-label " style="color: teal">
                              <input class="form-check-input" id="IsEscalationRequire" type="checkbox" value="<?php echo $value['IsEscalationRequire'] ; ?>" name="IsEscalationRequire" <?php if($value['IsEscalationRequire']){echo 'checked';} ?>>
                              <span class="form-check-sign">
                                <span class="check"></span>
                              </span>
                            </label>
                          </div>
                      </td>

       

                      <!-- Is Parking Reqire -->
                      <td class="text-center">
                          <div class="form-check">
                            <label class="form-check-label " style="color: teal">
                              <input class="form-check-input" id="IsParkingRequire" type="checkbox" value="<?php echo $value['IsParkingRequire'] ; ?>" name="IsParkingRequire" <?php if($value['IsParkingRequire']){echo 'checked';} ?>>
                              <span class="form-check-sign">
                                <span class="check"></span>
                              </span>
                            </label>
                          </div>
                      </td>

                      <!-- Parking Type -->
                      <td class="text-center">
                          <div class="form-group">
								<select class="select2picker ParkingType"  id="ParkingType" name="ParkingType" data-live-search="true" style="width: 100%" <?php if(empty($value['IsParkingRequire'])){echo 'disabled';} ?>>                   									
									<option value="Manual" <?php if ($value['ParkingType'] == "Manual") { echo "selected"; } ?>>Manual</option>
									<option value="Auto" <?php if ($value['ParkingType'] == "Auto") { echo "selected"; } ?>>Auto</option>
								</select>
							</div>
                      </td>

                      <!-- Parking Duration -->
                      <td class="text-center">
                      	<div class="form-group">
	                      <div>
	                      	<input type="number" name="ParkingDuration" id="ParkingDuration" value="<?php echo $value['ParkingDuration']; ?>" class="form-control input-xs ParkingDuration"  <?php if(empty($value['IsParkingRequire'])){echo 'disabled';} ?>>
	                      	<input type="hidden" class="ParkingDuration_hidden" name="ParkingDuration_hidden" value="<?php echo $value['ParkingDuration']; ?>">
	                        
	                      </div>
	                    </div>
                      </td>

                      <td class="text-center">
                      	<div class="form-check">
                      		<label class="form-check-label " style="color: teal">
                      			<input class="form-check-input IsParkingCron" id="IsParkingCron" type="checkbox" value="<?php echo $value['IsParkingCron'] ; ?>" name="IsParkingCron" <?php if($value['IsParkingCron']){echo 'checked';} ?> <?php if(empty($value['IsParkingRequire'])){echo 'disabled';} ?>>
                      			<span class="form-check-sign">
                      				<span class="check"></span>
                      			</span>
                      		</label>
                      	</div>
                      </td>

                      <!-- Category -->
                      <td>
                          <div class="form-group">
								<select class="select2picker CategoryUID"  id="CategoryUID<?php echo $i; ?>" name="CategoryUID[<?php echo $i; ?>]" data-live-search="true" style="width: 100%">                   
									<option value="0" selected="selected">Select Category</option>
									<?php foreach ($CategoryDetaiils as $key => $category) {
									if($category->CategoryUID == $value['CategoryUID']){  ?>

										<option value="<?php echo $category->CategoryUID; ?>" selected><?php echo $category->CategoryName;?></option>

									<?php }else{  ?>

										<option value="<?php echo $category->CategoryUID; ?>"><?php echo $category->CategoryName;?></option>

									<?php } } ?>  
								</select>
							</div>
                      </td>

                      <!-- Milestone -->
                      <!-- <td>
                          <div class="form-group">
								<select class="select2picker MilestoneUID" name="MilestoneUID" data-live-search="true" style="width: 100%">                   
									<option value="0" selected="selected">Select Milestone</option>
									<?php foreach ($MilestoneDetaiils as $key => $milestone) {
									if($milestone->MilestoneUID == $value['MilestoneUID']){  ?>

										<option value="<?php echo $milestone->MilestoneUID; ?>" selected><?php echo $milestone->MilestoneName;?></option>

									<?php }else{  ?>

										<option value="<?php echo $milestone->MilestoneUID; ?>"><?php echo $milestone->MilestoneName;?></option>

									<?php } } ?>  
								</select>
							</div>
                      </td> -->

                      <!-- Customer workflow SLA -->
                      <td>
                      	<div class="form-group">
	                      <div>
	                      	<?php if (!empty($value['SLA'])) { ?>
	                      		<input type="number" name="SLA" id="SLA" data-sla="<?php echo $value['SLA']; ?>" value="<?php echo $value['SLA']; ?>" class="form-control input-xs">	
	                      	<?php } else { ?>
	                      		<input type="number" name="SLA" id="SLA" data-sla="" value="" class="form-control input-xs">
	                      	<?php } ?>
	                      	<input type="hidden" class="SLA_hidden" name="SLA_hidden" value="<?php echo $value['SLA']; ?>">
	                        
	                      </div>
	                    </div>
                      </td>

                       <!-- Customer workflow ChecklistSequence -->
                <!--       <td>
                      	<div class="form-group">
	                      <div>
	                      	<?php if (!empty($value['ChecklistSequence'])) { ?>
	                      		<input type="text" name="ChecklistSequence" id="ChecklistSequence" data-ChecklistSequence="<?php echo $value['ChecklistSequence']; ?>" value="<?php echo $value['ChecklistSequence']; ?>" class="form-control input-xs">	
	                      	<?php } else { ?>
	                      		<input type="text" name="ChecklistSequence" id="ChecklistSequence" data-ChecklistSequence="" value="" class="form-control input-xs">
	                      	<?php } ?>
	                      	<input type="hidden" class="ChecklistSequence_hidden" name="ChecklistSequence_hidden" value="<?php echo $value['ChecklistSequence']; ?>">
	                        
	                      </div>
	                    </div>
                      </td> -->


                      <!-- Customer workflow status -->
                      <td>
                      	<div class="form-group">
                      		<select class="select2picker StatusUID"  name="StatusUID" style="width: 100%"> 
                      			<?php foreach ($mStatusDetails as $key => $mStatus) { ?>
                      				<?php if($mStatus->StatusUID == $value['StatusUID']){  ?>

                      					<option value="<?php echo $mStatus->StatusUID; ?>" selected><?php echo $mStatus->StatusName;?></option>

                      				<?php }else{  ?>

                      					<option value="<?php echo $mStatus->StatusUID; ?>"><?php echo $mStatus->StatusName;?></option>

                      				<?php } } ?>  
                      			</select>
                      		</div>
                      	</td>

                      <td class="text-center" style="width: 5%">
                        <span title="Move" class="icon_action move-handle-icon" style="color: #000;"><i class="fa fa-arrows" aria-hidden="true"></i></span>
                      </td>
                    </tr>
                <?php }?>
                  </tbody>
			</table>
		</div>

		<div class="ml-auto text-right">
			<a href="Customer" class="btn btn-fill btn-danger btn-wd btn-back" name="UpdateCustomer"><i class="icon-arrow-left8 pr-10"></i> Back</a>
			<!-- <button type="submit" class="btn btn-fill btn-update btn-wd " name="Update_Productsetup" id="Update_Productsetup"><i class="icon-floppy-disk pr-10"></i>Update</button> -->
		</div>
		<div class="clearfix"></div>

 <script src="assets/js/jquery-ui.min.js"></script>
 
<script  src="<?php echo base_url();?>assets/lib/colorpicker/js/jscolor.js" type="text/javascript" ></script>

  
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
			jscolor.installByClassName("jscolor");

			$("div.inner.show").addClass("perfectscrollbar");
			$('.perfectscrollbar').perfectScrollbar();

			$("body").on("click" , ".removeproductrow" , function(e){
				e.preventDefault();
				$(this).closest(".WorkflowmoduleWrapper").remove();
				return false;
			});

			$(document).off('change', '.DependentWorkflow').on('change', '.DependentWorkflow', function (e) {
				var CustomerUID = $('#CustomerUID').val();
				var workflowUID = $(this).closest("tr").attr('id');
				var DependentWorkflow = $(this).val();
					UpdateDependentWorkflow = $.ajax({
						type:'POST',
						dataType: 'JSON',
						global: false,
						url:'<?php echo base_url();?>Customer/UpdateDependentWorkflow',
						data: {'CustomerUID':CustomerUID,'workflowUID':workflowUID,'DependentWorkflow':DependentWorkflow},
						success: function(data)
						{
							console.log(data);
							$.notify({icon:"icon-bell-check",message:data.msg},{type:data.type,delay:1000 });
						}
					});
			});

			//Customer workflow status update
			$(document).off('change', '.StatusUID').on('change', '.StatusUID', function (e) {
				var CustomerUID = $('#CustomerUID').val();
				var workflowUID = $(this).closest("tr").attr('id');
				var StatusUID = $(this).val();
				$.ajax({
					type:'POST',
					dataType: 'JSON',
					global: false,
					url:'<?php echo base_url();?>Customer/UpdateCustomerWorkflowstatus',
					data: {'CustomerUID':CustomerUID,'workflowUID':workflowUID,'StatusUID':StatusUID},
					success: function(data)
					{
						console.log(data);
						$.notify({icon:"icon-bell-check",message:data.msg},{type:data.type,delay:1000 });
					}
				});
			});

			//Customer workflow category update
			$(document).off('change', '.CategoryUID').on('change', '.CategoryUID', function (e) {
				var CustomerUID = $('#CustomerUID').val();
				var workflowUID = $(this).closest("tr").attr('id');
				var CategoryUID = $(this).val();
				$.ajax({
					type:'POST',
					dataType: 'JSON',
					global: false,
					url:'<?php echo base_url();?>Customer/UpdateCustomerWorkflowCategory',
					data: {'CustomerUID':CustomerUID,'workflowUID':workflowUID,'CategoryUID':CategoryUID},
					success: function(data)
					{
						console.log(data);
						$.notify({icon:"icon-bell-check",message:data.msg},{type:data.type,delay:1000 });
					}
				});
			});

			//Customer workflow milestone update
			$(document).off('change', '.MilestoneUID').on('change', '.MilestoneUID', function (e) {
				var CustomerUID = $('#CustomerUID').val();
				var workflowUID = $(this).closest("tr").attr('id');
				var MilestoneUID = $(this).val();
				$.ajax({
					type:'POST',
					dataType: 'JSON',
					global: false,
					url:'<?php echo base_url();?>Customer/UpdateCustomerWorkflowMilestone',
					data: {'CustomerUID':CustomerUID,'workflowUID':workflowUID,'MilestoneUID':MilestoneUID},
					success: function(data)
					{
						console.log(data);
						$.notify({icon:"icon-bell-check",message:data.msg},{type:data.type,delay:1000 });
					}
				});
			});


			//Customer workflowcolor code update update
			$(document).off('change paste keyup', '.SectionColor').on('change paste keyup', '.SectionColor', function (e) {
				var ColorCode = $(this).val();
				var workflowUID = $(this).attr('data-workflow');
				var CustomerUID = $('#CustomerUID').val();
				$.ajax({
					type:'POST',
					dataType: 'JSON',
					global: false,
					url:'<?php echo base_url();?>Customer/UpdateCustomerWorkflowcOLORcODE',
					data: {'CustomerUID':CustomerUID,'workflowUID':workflowUID,'ColorCode':ColorCode},
					success: function(data)
					{
						console.log(data);
						$.notify({icon:"icon-bell-check",message:data.msg},{type:data.type,delay:1000 });
					}
				});
			});


			//Customer workflow Optional update
			$(document).off('click', '#Optional').on('click', '#Optional', function (e) {
				var CustomerUID = $('#CustomerUID').val();
				var workflowUID = $(this).closest("tr").attr('id');
				var Optional;
				if($(this).prop("checked") == true){
	                Optional = 1;
	            }
	            else if($(this).prop("checked") == false){
	                Optional = 0;
	            }
				$.ajax({
					type:'POST',
					dataType: 'JSON',
					global: false,
					url:'<?php echo base_url();?>Customer/UpdateCustomerWorkflowoptional',
					data: {'CustomerUID':CustomerUID,'workflowUID':workflowUID,'Optional':Optional},
					success: function(data)
					{
						console.log(data);
						$.notify({icon:"icon-bell-check",message:data.msg},{type:data.type,delay:1000 });
					}
				});
			});

			//Customer workflow IsDocChaseRequire update
			$(document).off('click', '#IsDocChaseRequire').on('click', '#IsDocChaseRequire', function (e) {
				var CustomerUID = $('#CustomerUID').val();
				var workflowUID = $(this).closest("tr").attr('id');
				var IsDocChaseRequire;
				if($(this).prop("checked") == true){
	                IsDocChaseRequire = 1;
	            }
	            else if($(this).prop("checked") == false){
	                IsDocChaseRequire = 0;
	            }
				$.ajax({
					type:'POST',
					dataType: 'JSON',
					global: false,
					url:'<?php echo base_url();?>Customer/UpdateCustomerWorkflowIsDocChaseRequire',
					data: {'CustomerUID':CustomerUID,'workflowUID':workflowUID,'IsDocChaseRequire':IsDocChaseRequire},
					success: function(data)
					{
						console.log(data);
						$.notify({icon:"icon-bell-check",message:data.msg},{type:data.type,delay:1000 });
					}
				});
			});

			//Customer workflow IsEscalationRequire update
			$(document).off('click', '#IsEscalationRequire').on('click', '#IsEscalationRequire', function (e) {
				var CustomerUID = $('#CustomerUID').val();
				var workflowUID = $(this).closest("tr").attr('id');
				var IsEscalationRequire;
				if($(this).prop("checked") == true){
	                IsEscalationRequire = 1;
	            }
	            else if($(this).prop("checked") == false){
	                IsEscalationRequire = 0;
	            }
				$.ajax({
					type:'POST',
					dataType: 'JSON',
					global: false,
					url:'<?php echo base_url();?>Customer/UpdateCustomerWorkflowIsEscalationRequire',
					data: {'CustomerUID':CustomerUID,'workflowUID':workflowUID,'IsEscalationRequire':IsEscalationRequire},
					success: function(data)
					{
						console.log(data);
						$.notify({icon:"icon-bell-check",message:data.msg},{type:data.type,delay:1000 });
					}
				});
			});



			//Customer workflow IsParkingRequire update
			var IsParkingRequire;
			var ParkingType;
			var ParkingDuration;
			var IsParkingCron;
			$(document).off('click', '#IsParkingRequire').on('click', '#IsParkingRequire', function (e) {
				var CustomerUID = $('#CustomerUID').val();
				var workflowUID = $(this).closest("tr").attr('id');				
				if($(this).prop("checked") == true){
					IsParkingRequire = 1;
					$(this).closest("tr").find('.ParkingDuration').val('').prop('disabled', false);
					$(this).closest("tr").find('.ParkingType').prop('disabled', false);
					$(this).closest("tr").find('.IsParkingCron').prop('disabled', false);
					ParkingType = $(this).closest("tr").find('.ParkingType :selected').val();
					ParkingDuration = $(this).closest("tr").find('.ParkingDuration').val();
				}
				else if($(this).prop("checked") == false){
					IsParkingRequire = 0;
					$(this).closest("tr").find('.ParkingDuration').val('').prop('disabled', true);
					$(this).closest("tr").find('.ParkingType').prop('disabled', true);
					$(this).closest("tr").find('.IsParkingCron').prop('disabled', true);

				}	
				$(this).closest("tr").find('.ParkingType').val('Manual').trigger('change.select2');
				IsParkingCron = $(this).closest("tr").find('.IsParkingCron').val();
				IsParkingRequire_func(CustomerUID, workflowUID, IsParkingRequire, ParkingType, ParkingDuration,IsParkingCron);			
			});

			$(document).off('change', '#ParkingType').on('change', '#ParkingType', function (e) {
				var CustomerUID = $('#CustomerUID').val();
				var workflowUID = $(this).closest("tr").attr('id');				
				if($(this).closest("tr").find('#IsParkingRequire').prop("checked") == true){
					IsParkingRequire = 1;
					ParkingType = $(this).closest("tr").find('.ParkingType :selected').val();
					ParkingDuration = $(this).closest("tr").find('.ParkingDuration').val();
				}
				else if($(this).closest("tr").find('#IsParkingRequire').prop("checked") == false){
					IsParkingRequire = 0;
					$(this).closest("tr").find('.ParkingDuration').val('');
				}	
				IsParkingCron = $(this).closest("tr").find('.IsParkingCron').val();
				IsParkingRequire_func(CustomerUID, workflowUID, IsParkingRequire, ParkingType, ParkingDuration,IsParkingCron);			
			});

			$(document).off('blur', '#ParkingDuration').on('blur', '#ParkingDuration', function (e) {
				var CustomerUID = $('#CustomerUID').val();
				var workflowUID = $(this).closest("tr").attr('id');	
				var ParkingDuration_hidden = $(this).closest("tr").find('.ParkingDuration_hidden').val();		
				if($(this).closest("tr").find('#IsParkingRequire').prop("checked") == true){
					IsParkingRequire = 1;
					ParkingType = $(this).closest("tr").find('.ParkingType :selected').val();
					ParkingDuration = $(this).closest("tr").find('.ParkingDuration').val();
					if (ParkingDuration == '') {
						return false;
					} else if(ParkingDuration == ParkingDuration_hidden) {
						return false;
					}
				}
				else if($(this).closest("tr").find('#IsParkingRequire').prop("checked") == false){
					IsParkingRequire = 0;
					$(this).closest("tr").find('.ParkingDuration').val('');
				}	
				$(this).closest("tr").find('.ParkingDuration_hidden').val(ParkingDuration);
				IsParkingCron = $(this).closest("tr").find('.IsParkingCron').val();
				IsParkingRequire_func(CustomerUID, workflowUID, IsParkingRequire, ParkingType, ParkingDuration,IsParkingCron);			
			});

			$(document).off('click', '#IsParkingCron').on('click', '#IsParkingCron', function (e) {
				var CustomerUID = $('#CustomerUID').val();
				var workflowUID = $(this).closest("tr").attr('id');	
				ParkingType = $(this).closest("tr").find('.ParkingType :selected').val();
				ParkingDuration = $(this).closest("tr").find('.ParkingDuration').val();
				IsParkingCron = $(this).closest("tr").find('.IsParkingCron').is( ':checked' ) ? 1: 0;
				IsParkingRequire = $(this).closest("tr").find('#IsParkingRequire').is( ':checked' ) ? 1: 0;
				IsParkingRequire_func(CustomerUID, workflowUID, IsParkingRequire, ParkingType, ParkingDuration,IsParkingCron);
			});

			function IsParkingRequire_func(CustomerUID, workflowUID, IsParkingRequire, ParkingType, ParkingDuration,IsParkingCron) {
				$.ajax({
					type:'POST',
					dataType: 'JSON',
					global: false,
					url:'<?php echo base_url();?>Customer/UpdateCustomerWorkflowIsParkingRequire',
					data: {'CustomerUID':CustomerUID,'workflowUID':workflowUID,'IsParkingRequire':IsParkingRequire,'ParkingType':ParkingType,'ParkingDuration':ParkingDuration,'IsParkingCron':IsParkingCron},
					success: function(data)
					{
						console.log(data);
						$.notify({icon:"icon-bell-check",message:data.msg},{type:data.type,delay:1000 });						
					}
				});
			}

			$(".sort_par_div tbody").sortable({
				axis: "y",
				cursor: "grabbing",
				handle: ".move-handle-icon",
				opacity: 1
			});


			sortRequest = null; 
			$(".sort_par_div tbody").sortable({
				axis: "y",
				cursor: "grabbing",
				handle: ".move-handle-icon",
				opacity: 1,
				stop: function(event, ui)
				{
					var current = ui.item.attr('id');
					var wrkprz = ui.item.find('td:nth-child(1)').text();
					var sortData = new Array();
					var CustomerUID = $('#CustomerUID').val();
					$('#workflowdependent tbody tr').each(function() {
						sortData.push({'Type':'Parent','ID':$(this).attr('id')});
					});

					if(sortRequest != null) 
					{
						sortRequest.abort(); 
						sortRequest = null;
					}

					sortRequest = $.ajax({
						type:'POST',
						dataType: 'JSON',
						global: false,
						url:'<?php echo base_url();?>Customer/sortWorkflowDependence',
						data: {sortData,'current': current,'wPzt': current,'CustomerUID':CustomerUID},
						success: function(data)
						{
							console.log(data);
							$.notify({icon:"icon-bell-check",message:data.msg},{type:data.type,delay:1000 });
						}
					});

				}
			});

			// Update Client Workflow SLA
			$(document).off('blur', '#SLA').on('blur', '#SLA', function (e) {
				var CustomerUID = $('#CustomerUID').val();
				var workflowUID = $(this).closest("tr").attr('id');
				var SLA = $(this).val();
				var SLA_hidden = $(this).closest("tr").find('.SLA_hidden').val();
				if (SLA_hidden != SLA) {
					$(this).closest("tr").find('.SLA_hidden').val(SLA);
					$.ajax({
						type:'POST',
						dataType: 'JSON',
						global: false,
						url:'<?php echo base_url();?>Customer/UpdateCustomerWorkflowSLA',
						data: {'CustomerUID':CustomerUID,'workflowUID':workflowUID,'SLA':SLA},
						success: function(data)
						{
							console.log(data);
							$.notify({icon:"icon-bell-check",message:data.msg},{type:data.type,delay:1000 });
						}
					});
				}
			});

			//Update State
			$(document).off('change', '.state').on('change', '.state', function (e) {
				var CustomerUID = $('#CustomerUID').val();
				var workflowUID = $(this).closest("tr").attr('id');
				var state = $(this).val();
					Updatestate = $.ajax({
						type:'POST',
						dataType: 'JSON',
						global: false,
						url:'<?php echo base_url();?>Customer/UpdateCustomerWorkflowState',
						data: {'CustomerUID':CustomerUID,'workflowUID':workflowUID,'state':state},
						success: function(data)
						{
							console.log(data);
							$.notify({icon:"icon-bell-check",message:data.msg},{type:data.type,delay:1000 });
						}
					});
			});

			//Update Loan Type Name
			$(document).off('change', '.LoanTypeName').on('change', '.LoanTypeName', function (e) {
				var CustomerUID = $('#CustomerUID').val();
				var workflowUID = $(this).closest("tr").attr('id');
				var LoanTypeName = $(this).val();
					UpdateLoanTypeName = $.ajax({
						type:'POST',
						dataType: 'JSON',
						global: false,
						url:'<?php echo base_url();?>Customer/UpdateCustomerWorkflowLoanType',
						data: {'CustomerUID':CustomerUID,'workflowUID':workflowUID,'LoanTypeName':LoanTypeName},
						success: function(data)
						{
							console.log(data);
							$.notify({icon:"icon-bell-check",message:data.msg},{type:data.type,delay:1000 });
						}
					});
			});

			//Update Loan Type Name
			$(document).off('change', '.MilestoneUID').on('change', '.MilestoneUID', function (e) {
				var CustomerUID = $('#CustomerUID').val();
				var workflowUID = $(this).closest("tr").attr('id');
				var MilestoneUID = $(this).val();
					UpdateMilestoneUID = $.ajax({
						type:'POST',
						dataType: 'JSON',
						global: false,
						url:'<?php echo base_url();?>Customer/UpdateCustomerWorkflowMilestoneUID',
						data: {'CustomerUID':CustomerUID,'workflowUID':workflowUID,'MilestoneUID':MilestoneUID},
						success: function(data)
						{
							console.log(data);
							$.notify({icon:"icon-bell-check",message:data.msg},{type:data.type,delay:1000 });
						}
					});
			});

			// Update Client Workflow PropertyType
			$(document).off('blur', '#PropertyType').on('blur', '#PropertyType', function (e) {
				var CustomerUID = $('#CustomerUID').val();
				var workflowUID = $(this).closest("tr").attr('id');
				var PropertyType = $(this).val();
				var PropertyType_hidden = $(this).closest("tr").find('.PropertyType_hidden').val();		
				if (PropertyType_hidden != PropertyType) {
					$(this).closest("tr").find('.PropertyType_hidden').val(PropertyType);
					$.ajax({
						type:'POST',
						dataType: 'JSON',
						global: false,
						url:'<?php echo base_url();?>Customer/UpdateCustomerWorkflowPropertyType',
						data: {'CustomerUID':CustomerUID,'workflowUID':workflowUID,'PropertyType':PropertyType},
						success: function(data)
						{
							console.log(data);
							$.notify({icon:"icon-bell-check",message:data.msg},{type:data.type,delay:1000 });
						}
					});
				}
			});

				// Update Client Workflow //checklistsequence 
			$(document).off('blur', '#ChecklistSequence').on('blur', '#ChecklistSequence', function (e) {
				var CustomerUID = $('#CustomerUID').val();
				var workflowUID = $(this).closest("tr").attr('id');
				var ChecklistSequence = $(this).val();
				var ChecklistSequence_hidden = $(this).closest("tr").find('.ChecklistSequence_hidden').val();		
				if (ChecklistSequence_hidden != ChecklistSequence) {
					$(this).closest("tr").find('.ChecklistSequence_hidden').val(ChecklistSequence);
					$.ajax({
						type:'POST',
						dataType: 'JSON',
						global: false,
						url:'<?php echo base_url();?>Customer/UpdateCustomerWorkflowChecklistSequence',
						data: {'CustomerUID':CustomerUID,'workflowUID':workflowUID,'ChecklistSequence':ChecklistSequence},
						success: function(data)
						{
							console.log(data);
							$.notify({icon:"icon-bell-check",message:data.msg},{type:data.type,delay:1000 });
						}
					});
				}
			});

			// Update Workflow Order Highlight Duration in hours
			$(document).off('blur', '#OrderHighlightDuration').on('blur', '#OrderHighlightDuration', function (e) {
				var CustomerUID = $('#CustomerUID').val();
				var workflowUID = $(this).closest("tr").attr('id');
				var OrderHighlightDuration = $(this).val();
				var OrderHighlightDuration_hidden = $(this).closest("tr").find('.OrderHighlightDuration_hidden').val();		
				if (OrderHighlightDuration_hidden != OrderHighlightDuration) {
					$(this).closest("tr").find('.OrderHighlightDuration_hidden').val(OrderHighlightDuration);
					$.ajax({
						type:'POST',
						dataType: 'JSON',
						global: false,
						url:'<?php echo base_url();?>Customer/UpdateCustomerWorkflowOrderHighlightDuration',
						data: {'CustomerUID':CustomerUID,'workflowUID':workflowUID,'OrderHighlightDuration':OrderHighlightDuration},
						success: function(data)
						{
							console.log(data);
							$.notify({icon:"icon-bell-check",message:data.msg},{type:data.type,delay:1000 });
						}
					});
				}
			});

		});

	</script>