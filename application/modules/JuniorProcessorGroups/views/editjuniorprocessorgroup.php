<div class="card" id="Orderentrycard">
	<div class="card-header card-header-danger card-header-icon">
		<div class="card-icon">Edit Junior Processor Group
		</div>

	</div>
	<div class="card-body">
		<div class="col-md-12">
			<form action="#"  name="JuniorProcessor_Form" id="JuniorProcessor_Form" class="JuniorProcessor_Form mb-0">
				<div class="form-group bmd-form-group">
					<label for="message-text" class="form-label">Junior Processor <span class="mandatory"></span></label>
					<select class="select2picker form-control"  id="JuniorProcessorUserUID" name="JuniorProcessorUserUID">
						<option value=""></option>
						<?php foreach ( $Processors as $key => $value) { ?>							
							<option value="<?php echo $value->UserUID; ?>" <?php if ($value->UserUID == $JuniorProcessorGroupDetails->JuniorProcessorUserUID) { echo 'selected'; } ?>><?php echo $value->UserName; ?></option>
						<?php } ?>                
					</select>
				</div>
				<div class="form-group bmd-form-group">
					<label for="message-text" class="form-label">Processor <span class="mandatory"></span></label>
					<select class="select2picker form-control" id="ProcessorUserUID" name="ProcessorUserUID[]" multiple="multiple" style="height: 38px !important;">
						<option value=""></option>
						<?php 
						$ProcessorsArr = explode(',', $JuniorProcessorGroupDetails->ProcessorUserUIDs);
						foreach ( $Processors as $key => $value) { ?>
							<option value="<?php echo $value->UserUID; ?>" <?php if (in_array($value->UserUID, $ProcessorsArr)) { echo 'selected'; } ?>><?php echo $value->UserName; ?></option>
						<?php } ?>                
					</select>
				</div>

				<div class="form-group bmd-form-group">

					<div class="well clearfix">
					   <a class="btn btn-default pull-right add-record active" data-added="0"><i class="glyphicon glyphicon-plus"></i> Add Workflow</a>
					</div>

					<div class="material-datatables" >
						<table class="table display nowrap sort_par_div" id="WorkflowTable"  cellspacing="0" width="100%"  style="width:100%">
							<thead>
								<tr>
									<th style="width: 15%">Workflow</th>
									<th style="width: 65%">Sub Queues</th>	
									<th style="width: 5%">KickBack</th>	
									<th style="width: 5%;" class="text-center">Action</th>							
								</tr>
							</thead>
							 <tbody id="tbl_posts_body">
							 	<?php
							 	$WorkflowModuleUIDs = explode(',', $JuniorProcessorGroupDetails->WorkflowModuleUIDs);
							 	if (!empty($WorkflowModuleUIDs)) {
							 		$count=1;
							 		foreach ($WorkflowModuleUIDs as $Workflowkey => $WorkflowModuleUID) 
							 		{
							 			$QueueDetails = $this->JuniorProcessorGroups_Model->GetJuniorWorkflowQueueDetails($JuniorProcessorGroupDetails->GroupUID, $WorkflowModuleUID);
							 			$WorkflowQueueDetails = $this->JuniorProcessorGroups_Model->getCustomerWorkflowQueues($WorkflowModuleUID);
							 			if (!empty($QueueDetails)) { ?>
							 				<tr id='rec-<?php echo $count; ?>'>
								        	<td>
								        		<div class="form-group">
								              		<select class="select2picker WorkflowModuleUID" name="WorkflowModuleUID[<?php echo $count; ?>]" style="width: 100%">     
								              			<option value="">Select Workflow</option>
								          				<?php foreach ($Customer_Workflow as $key => $workflow) { ?>

								      					<option value="<?php echo $workflow['WorkflowModuleUID']; ?>" <?php if ($workflow['WorkflowModuleUID'] == $WorkflowModuleUID) 
								      					{
								      						echo 'selected';
								      					} ?>><?php echo $workflow['SystemName'];?></option>

								          				<?php } ?>  
								          			</select>
								          		</div>
								        	</td>
								        	<td>
								        		<div class="form-group bmd-form-group">
													<select class="select2picker form-control" id="QueueUID" name="QueueUID[<?php echo $WorkflowModuleUID; ?>][]" multiple="multiple" style="height: 38px !important;">
														<?php foreach ($WorkflowQueueDetails as $queuekey => $Queue) { ?>

								      					<option value="<?php echo $Queue->QueueUID; ?>" <?php if (in_array($Queue->QueueUID, array_column($QueueDetails, 'QueueUID'))) 
								      					{
								      						echo 'selected';
								      					} ?>><?php echo $Queue->QueueName;?></option>

								          				<?php } ?>
													</select>
												</div>
								        	</td>
								        	<td class="text-center">
								        		<div class="form-check">
								        			<label class="form-check-label " style="color: teal">
								        				<input class="form-check-input" id="IsKickBack" class="IsKickBack" type="checkbox" value="" name="IsKickBack[<?php echo $WorkflowModuleUID; ?>][]" <?php if (in_array(1, array_column($QueueDetails, 'IsKickBack'))) 
								      					{
								      						echo 'checked';
								      					} ?>>
								        				<span class="form-check-sign">
								        					<span class="check"></span>
								        				</span>
								        			</label>
								        		</div>
								        	</td>
								        	<td style="text-align: center !important;">
								              	<a class="delete-record" data-id="<?php echo $count; ?>" title="Delete Workflow">
								                	<i class="fa fa-trash" aria-hidden="true" style="font-size: 20px;"></i>
								              	</a>
								          	</td>
								        </tr>
							 			<?php }
							 			$count++;
							 		}
							 	}
							 	?>									 	
					          </tbody>
						</table>
					</div>

				</div>

				<div class="form-group bmd-form-group is-filled">
					<div class="togglebutton">
						<label class="label-color" style="margin-top: 18pt"> Active
							<input type="checkbox" id="Active" name="Active" class="Active"  <?php if($JuniorProcessorGroupDetails->Active == 1){ echo "checked"; } ?>>
							<span class="toggle"></span>
						</label>
					</div>
				</div>
				<div class="ml-auto mt-10 mb-10 text-right">
					<a href="<?php echo base_url() ?>JuniorProcessorGroups" class="btn btn-fill btn-back btn-wd ajaxload" role="button" aria-pressed="true"><i class="icon-arrow-left15 pr-10 Back"></i> Back<div class="ripple-container"></div></a>
					<button type="submit" class="btn btn-fill btn-save btn-wd Update_JuniorProcessorGroup" id="Update_JuniorProcessorGroup"><i class="icon-floppy-disk pr-10"></i>Update Junior Processor Group</button>
				</div>
			</form>

		</div>
	</div>
</div>

<div style="display:none;">
   <table id="AppendBody">
        <tr id="">
        	<td>
        		<div class="form-group">
              		<select class="select2picker WorkflowModuleUID" name="WorkflowModuleUID" style="width: 100%">     
              			<option value="">Select Workflow</option>
          				<?php foreach ($Customer_Workflow as $key => $workflow) { ?>

      					<option value="<?php echo $workflow['WorkflowModuleUID']; ?>"><?php echo $workflow['SystemName'];?></option>

          				<?php } ?>  
          			</select>
          		</div>
        	</td>
        	<td>
        		<div class="form-group bmd-form-group">
					<select class="select2picker form-control" id="QueueUID" name="QueueUID[]" multiple="multiple" style="height: 38px !important;">
						<option value=""></option>
					</select>
				</div>
        	</td>
        	<td class="text-center">
        		<div class="form-check">
        			<label class="form-check-label " style="color: teal">
        				<input class="form-check-input" id="IsKickBack" type="checkbox" value="" name="IsKickBack">
        				<span class="form-check-sign">
        					<span class="check"></span>
        				</span>
        			</label>
        		</div>
        	</td>
        	<td style="text-align: center !important;">
              	<a class="delete-record" data-id="" title="Delete Workflow">
                	<i class="fa fa-trash" aria-hidden="true" style="font-size: 20px;"></i>
              	</a>
          	</td>
        </tr>
   </table>
</div>

<script type="text/javascript">
	$('.card-icon').show();
	$(document).ready(function(){

		var GroupUID = '<?php echo $JuniorProcessorGroupDetails->GroupUID; ?>';

		$(document).off('click','.Update_JuniorProcessorGroup').on('click','.Update_JuniorProcessorGroup', function(e) {
			e.preventDefault();
			var formdata = $('#JuniorProcessor_Form').serialize() + "&GroupUID=" + GroupUID;
			button = $(this);
			button_val = $(this).val();
			button_text = $(this).html();
			$.ajax({
				type: "POST",
				url: "<?php echo base_url('JuniorProcessorGroups/SaveJuniorProcessorGroup'); ?>",
				data: formdata,
				dataType:'json',
				beforeSend: function () {
					button.prop("disabled", true);
					button.html('Loading ...');
					button.val('<i class="fa fa-spin fa-spinner"></i> Updating...');
				},

				success: function (response) {
					if(response.Status == 0)
					{
						$.notify(
						{
							icon:"icon-bell-check",
							message:response.message
						},
						{
							type:"success",
							delay:2000 
						}); 

						window.setTimeout(function(){
							triggerpage('<?php echo base_url();?>JuniorProcessorGroups');
						},3000);

					}
					else
					{
						$.notify(
						{
							icon:"icon-bell-check",
							message:response.message
						},
						{
							type:"danger",
							delay:2000 
						});
						$.each(response, function(k, v) {
							console.log($('#'+ k +'.select2picker'));
							$('#'+k).addClass("is-invalid").closest('.form-group').removeClass('has-success').addClass('has-danger');
							$('#'+ k +'.select2picker').next().find('span.select2-selection').addClass('errordisplay');
						});

						button.html(button_text);
						button.val(button_val);
						button.prop('disabled',false);
					}

				},
				error : function() {
					button.html(button_text);
					button.val(button_val);
					button.prop('disabled',false);
				}
			});

		});

		$(document).off('click', 'a.add-record').on('click', 'a.add-record', function(e) {
            e.preventDefault();
            $("select.select2picker").select2('destroy');
            var content = $('#AppendBody tr'),
                size = $('#WorkflowTable >tbody >tr').length + 1,
                element = null,
                element = content.clone();
            element.attr('id', 'rec-' + size);
            element.find('.delete-record').attr('data-id', size);
            element.find('.WorkflowModuleUID').attr('name', 'WorkflowModuleUID['+size+']');
            element.appendTo('#tbl_posts_body');
            $("select.select2picker").select2({
                theme: "bootstrap",
            });
        });

        $(document).off('click', 'a.delete-record').on('click', 'a.delete-record', function(e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
            $('#rec-' + id).remove();     

        });

        $(document).off('change', '.WorkflowModuleUID').on('change', '.WorkflowModuleUID', function(e) {

            var WorkflowModuleUID = $(this).val();
            $QueueUID = $(this).closest('tr').find('#QueueUID');
            $QueueUID.attr('name','QueueUID['+WorkflowModuleUID+'][]');
            $IsKickBack = $(this).closest('tr').find('#IsKickBack');
            $IsKickBack.attr('name','IsKickBack['+WorkflowModuleUID+'][]');

            $.ajax({
            	url: 'JuniorProcessorGroups/FetchWorkflowSubQueues',
            	type: 'POST',
            	dataType: 'JSON',
            	data: {WorkflowModuleUID: WorkflowModuleUID},
            })
            .done(function(data) {
            	console.log("success");
            	// reset queueuid
            	$QueueUID.html('');
            	// append queues
            	$.each(data, function(key, value) {   
				     $QueueUID.append($("<option></option>").attr("value", value.QueueUID).text(value.QueueName)); 
				});            	
            })
            .fail(function() {
            	console.log("error");
            })
            .always(function() {
            	console.log("complete");
            });
            
                 
        });

	});

</script>