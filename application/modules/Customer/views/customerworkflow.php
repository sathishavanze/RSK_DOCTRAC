<form class="frmcustomerworkflow" action="#"> 
	<div class="col-md-12 borderright dynamicdiv"> 
		<div class="row" style="background:#f7f7f7;"> 
			<div class="col-md-2 dyndivhead">       
				<strong>Product / Subproduct</strong> 
			</div>
			<div class="col-md-4 dyndivhead">
				<strong>Workflows</strong>         
			</div>
			<div class="col-md-4 dyndivhead">
				<strong>Optional Workflow</strong>
			</div>
			<div class="col-md-2 dyndivhead">
				<strong>Action</strong>
			</div>
		</div>

		<?php foreach ($details as $key => $value) { 
			?>
			<div class="row WorkflowmoduleWrapper"> 
				<div class="col-md-2 dyndiv">       
					<p class="selectedproduct"><?php echo substr($value['ProductName'], 0, 1).'-'.$value['SubProductName'];  ?></p>
					<input type="hidden" name="ProductUID[<?php echo $key; ?>]" value="<?php echo $value['ProductUID']; ?>">
					<input type="hidden" name="SubProductUID[<?php echo $key; ?>]" value="<?php echo $value['SubProductUID']; ?>">
				</div>
				<div class="col-md-4 dyndiv">
					<div class="form-group">
						<select class="select2picker WorkflowModuleUID" id="WorkflowModuleUID_<?php echo $key; ?>" name="WorkflowModuleUID[<?php echo $key; ?>][]" multiple="multiple" >  
							<option value=""></option>

							<?php foreach ($WorkflowDetaiils as $wkey => $Workflow) {

								$selected = 0;

								foreach ($value['Customer_Workflow'] as $cwkeys => $values) {

									if($Workflow->WorkflowModuleUID == $values['WorkflowModuleUID']){  ?>
										<option value="<?php echo $Workflow->WorkflowModuleUID; ?>" selected><?php echo $Workflow->WorkflowModuleName;?></option>
										<?php 
										$selected = 1; 
									}

								} if($selected == 0){ ?>

									<option value="<?php echo $Workflow->WorkflowModuleUID; ?>" ><?php echo $Workflow->WorkflowModuleName;?></option>

								<?php } ?>
							<?php } ?>
						</select>
					</div>   
				</div>
				<div class="col-md-4 dyndiv">
					<div class="form-group">
						<select class="select2picker OptionalWorkflowModuleUID" id="WorkflowModuleUID_<?php echo $key; ?>" name="OptionalWorkflowModuleUID[<?php echo $key; ?>][]" multiple >  
							<option value=""></option>

							<?php foreach ($WorkflowDetaiils as $okey => $Workflow) {

								$selected = 0;

								foreach ($value['Customer_optionalWorkflow'] as $keys => $values) {

									if($Workflow->WorkflowModuleUID == $values['WorkflowModuleUID']){  ?>
										<option value="<?php echo $Workflow->WorkflowModuleUID; ?>" selected><?php echo $Workflow->WorkflowModuleName;?></option>
										<?php 
										$selected = 1; 
									}

								} if($selected == 0){ ?>

									<option value="<?php echo $Workflow->WorkflowModuleUID; ?>" ><?php echo $Workflow->WorkflowModuleName;?></option>

								<?php } ?>
							<?php } ?>

						</select>
					</div>
				</div>

					<div class="col-md-2 dyndiv">
						<div class="mt-10">
							<button type="button" class="btn btn-xs btn-dribbble copyworkflow"><i class="icon-copy4"></i></button>
						</div>
					</div>
				</div>
			<?php } ?>
			<div class="updatediv text-right col-md-12 pd-0">
				<button type="submit" class="btn btn-fill btn-dribbble btn-wd" name="UpdateWorkflow" id="UpdateWorkflow"> Update</button>
			</div>
		</div>
	</form>
	<script type="text/javascript">
		$(function() {
			$(".selectpicker").selectpicker();
			$("select.select2picker").select2({
				//tags: false,
				theme: "bootstrap",
			});


			/*-----WORKFLOW TAB STARTS*/
			$('.frmcustomerworkflow').submit(function (e) {
				e.preventDefault();
				e.stopPropagation();
				var formdata=new FormData($(this)[0]);
				formdata.append('CustomerUID', $('#CustomerUID').val());
				var button = $('#UpdateWorkflow');
				var button_text = $('#UpdateWorkflow').html();

				$.ajax({
					type: "POST",
					url: '<?php echo base_url();?>Customer/CustomerWorkflowAdd',
					data: formdata, 
					processData:false,
					contentType:false,
					dataType:'json',
					beforeSend: function(){
							button.attr("disabled", true);
							button.html('Please Wait ...'); 
					},
					success: function(data)
					{
						button.html(button_text);
						if(data.validation_error == 0){
							$.notify(
							{
								icon:"icon-bell-check",
								message:data.message
							},
							{
								type:"success",
								delay:1000 
							})
						}else if(data.validation_error == 1){

							$.notify(
							{
								icon:"icon-bell-check",
								message:data.message
							},
							{
								type:"danger",
								delay:1000 
							})
						}
						button.removeAttr("disabled"); 

					},
					error: function(jqXHR){
						console.log(jqXHR);
					}
				});

			})
			/*-----WORKFLOW TAB ENDS*/

		});



	</script>