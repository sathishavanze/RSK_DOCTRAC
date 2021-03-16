<style type="text/css">
	.pd-btm-0{
		padding-bottom: 0px;
	}

	.margin-minus8{
		margin: -8px;
	}

	.mt--15{
		margin-top: -15px;
	}

	.bulk-notes
	{
		list-style-type: none
	}
	.bulk-notes li:before
	{
		content: "*";
		color: red;
		font-size: 15px;
	}

	.nowrap{
		white-space: nowrap
	}

	.table-format > thead > tr > th{
		font-size: 12px;
	}
</style>
<div class="card mt-40" >
	<div class="card-header card-header-danger card-header-icon">
		<div class="card-icon">GET NEXT ORDER </div>
	</div>
	<div class="card-body">
		<div class="col-md-12">


			<div id="GetNextOrder-div">
				<div class="card-header card-header-icon card-header-rose">         
				</div>

				<div class="row">
					<div class="col-md-4">
						<select class="form-control select2picker"  id="WorkflowModule" name="WorkflowModule">
							<option value="0">Please Select WorkFlow</option>
							<?php 
							//print_r($WorkflowModules);
							foreach($WorkflowModules as $Module){ ?>
								<option value="<?php echo $Module->WorkflowModuleUID;?>"><?php echo $Module->WorkflowModuleName;?></option>
							<?php }?>
						</select>
					</div>
					<div class="col-md-4 datadiv">
						<div class="bmd-form-group row mt-5">
							<div class="col-md-5 pd-0 inputprepand" >
								<p class="mt-5"> FHA Lock Expiration Date </p>
							</div>
							<div class=" col-md-5" style="padding-left: 0px;">
								<div class="datediv">
									<input type="text" id="FHA_LockExpirationDate" name="FHA_LockExpirationDate" class="form-control datepicker" value="<?php echo $FHA_LockExpirationDate; ?>" style="height: 34px;">
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-4 datadiv">
						<div class="bmd-form-group row mt-5">
							<div class="col-md-5 pd-0 inputprepand" >
								<p class="mt-5"> VA Lock Expiration Date </p>
							</div>
							<div class=" col-md-5" style="padding-left: 0px;">
								<div class="datediv">
									<input type="text" id="VA_LockExpirationDate" name="VA_LockExpirationDate" class="form-control datepicker" value="<?php echo $VA_LockExpirationDate; ?>" style="height: 34px;">
								</div>
							</div>
						</div>
					</div>
				</div>
						
				<div class="material-datatables">
					<div id="Workflow-Userslist">

					</div> 
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$('#WorkflowModule').change(function(event) {
		WorkflowModule = $(this).val();
		if(WorkflowModule != '0'){
			if (WorkflowModule == <?php echo $this->config->item('Workflows')['Workup']; ?>) {
				$('.datadiv').show();
			} else {
				$('.datadiv').hide();
			}
			$.ajax({
				type: "POST",
				url: '<?php echo base_url();?>GetNextOrder/GetUsersByWorkflow',
				data: ({'WorkflowModule':WorkflowModule}),
				dataType: 'JSON',
				beforeSend: function()
				{
					addcardspinner($('#GetNextOrder-div'));
				},
				success: function(data)
				{
					$('#Workflow-Userslist').html(data.WorkflowUserslist);

					$("#MaritalTableList").dataTable({
						processing: true,
						scrollX:  true,
						paging:true,
					});

					// Append Lock Expiration Date
					FHA_LockExpirationDate = $("#FHA_LockExpirationDate").val(data.FHALockExpirationDate);
					VA_LockExpirationDate = $("#VA_LockExpirationDate").val(data.VALockExpirationDate);

					removecardspinner($('GetNextOrder-div'));
				}
			});
		}
		else
		{

			// Append Lock Expiration Date
			$("#FHA_LockExpirationDate").val("");
			$("#VA_LockExpirationDate").val("");

			$.notify(
			{
				icon:"icon-bell-check",
				message:"Please Select the Workflow"
			},
			{
				type:'danger',
				delay:1000 
			});

		}
	});

	// Update loantype based on users
	$(document).off('click', '.loan_type').on('click', '.loan_type', function (e) {
		var CustomerUID = $('#CustomerUID').val();
		var workflowUID = $('#WorkflowModule').val();
		var UserUID = $(this).closest('tr').find('.assign').val();
		var loantype = [];
		$(this).closest('tr').find('.loan_type').each(function( index ) {
			if($(this).is(":checked")){
				loantype.push($(this).val());
			}
		});
		$.ajax({
			type:'POST',
			dataType: 'JSON',
			url:'<?php echo base_url();?>GetNextOrder/Updateloantype',
			data: {'CustomerUID':CustomerUID,'workflowUID':workflowUID,'UserUID':UserUID,'loantype':loantype},
			success: function(data)
			{
				console.log(data);
				$.notify({icon:"icon-bell-check",message:data.msg},{type:data.type,delay:1000 });
			}
		});
	});

	$(document).off('click', '.assign').on('click', '.assign', function (e) { 
		var assigned_UID=[];
		var UID=$(this).val();
		assigned_UID.push(UID); 

		var formData = ({'assigned_UID': assigned_UID,'WorkflowModule':$('#WorkflowModule').val()}); 
		//var assigned_UID=[];
		$.ajax({
			type: "POST",
			url: '<?php echo base_url();?>GetNextOrder/assign_next_order_setup',
			data: formData,
			dataType: 'JSON',
			beforeSend: function()
			{
				addcardspinner($('#GetNextOrder-div'));
			},
			success: function(data)
			{
				removecardspinner($('#GetNextOrder-div'));
				if(data.validation_error == 1)
				{
					$.notify(
					{
						icon:"icon-bell-check",
						message:data.message
					},
					{
						type:'danger',
						delay:1000 
					});


				}else{
					$.notify(
					{
						icon:"icon-bell-check",
						message:data.message
					},
					{
						type:'success',
						delay:1000 
					});
				}
			}
		});

	});

	// Update loantype based on Workflow
	$(document).off('focusout', '#FHA_LockExpirationDate').on('focusout', '#FHA_LockExpirationDate', function (e) {
		var FHA_LockExpirationDate = $(this).val();
		var WorkflowModuleUID = $('#WorkflowModule').val();

		// Check Mandatory Validation
		if(WorkflowModuleUID == '' || WorkflowModuleUID == 0)
		{

			$.notify({icon:"icon-bell-check", message:'Please select workflow is mandatory'}, {type:'danger', delay:1000 });
			return false;
		}

		$.ajax({
			type:'POST',
			dataType: 'JSON',
			url:'<?php echo base_url();?>GetNextOrder/Update_FHA_LockExpirationDate',
			data: {'FHA_LockExpirationDate':FHA_LockExpirationDate, 'WorkflowModuleUID':WorkflowModuleUID},
			success: function(data)
			{
				console.log(data);
				$.notify({icon:"icon-bell-check",message:data.msg},{type:data.type,delay:1000 });
			}
		});
	});

	// Update loantype based on Workflow
	$(document).off('focusout', '#VA_LockExpirationDate').on('focusout', '#VA_LockExpirationDate', function (e) {
		var VA_LockExpirationDate = $(this).val();
		var WorkflowModuleUID = $('#WorkflowModule').val();

		// Check Mandatory Validation
		if(WorkflowModuleUID == '' || WorkflowModuleUID == 0)
		{

			$.notify({icon:"icon-bell-check", message:'Please select workflow is mandatory'}, {type:'danger', delay:1000 });
			return false;
		}

		$.ajax({
			type:'POST',
			dataType: 'JSON',
			url:'<?php echo base_url();?>GetNextOrder/Update_VA_LockExpirationDate',
			data: {'VA_LockExpirationDate':VA_LockExpirationDate, 'WorkflowModuleUID':WorkflowModuleUID},
			success: function(data)
			{
				console.log(data);
				$.notify({icon:"icon-bell-check",message:data.msg},{type:data.type,delay:1000 });
			}
		});
	});
	
</script>