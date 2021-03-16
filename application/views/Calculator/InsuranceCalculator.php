<style type="text/css">
	.InsuranceCalculator>thead>tr>th {
		text-align: center; 
		font-size: 25px !important;  
		padding: 1px !important;

	}

	.InsuranceCalculator>tbody>tr>td {
		text-align: left; 
		font-weight: 700;
		font-size: 13px !important;
		padding: 1px !important;

	}
	.CenterClass
	{
		text-align: center!important;
	}
	.FinalOutPut{font-size: 20px !important;}
	/*.manual{background: #f0f5c8 !important}*/
	.auto{background: #e4e4e4  !important}
	.InsuranceAuditLog{background: #676262 !important}

</style>

<div class="tab-pane active" id="<?php echo isset($CalculatorPrefixId) ? $CalculatorPrefixId : ''; ?>InsuranceCalculator">
	<div class="col-md-12 col-xs-12 pd-0">

		<button type="button" class="btn btn-fill pull-right search InsuranceAuditLog">Audit Log</button>
		<button type="button" class="btn btn-fill pull-right BtnHolidaysList">Holiday List</button>
		<table class="table table-striped InsuranceAuditLogTable" style="display: none;">
			<thead>
				<tr>
					<th>S.NO</th>
					<th>AuditLog</th>
					<th>Date Time</th>
					<th>Changed BY</th>
				</tr>
			</thead>
			<tbody class="AuditLogTbody" >
				<tr>
					<td></td>
					<td></td>
					<td></td>
				</tr>
			</tbody>
		</table>
		<form  id="InsuranceForm" class="InsuranceForm">
			<h4 class="card-title calc_head">Insurance Calculation</h4>
			<table class="table table-striped InsuranceCalculator">
				<tbody>
				<tr class ='manual'>
					<td>Disbursement Date (Greenlight closing screen)</td>
					<td>
					<?php 
					$FundingDate = '';
					if(!empty($CalculatorData->FundingDate) && $CalculatorData->FundingDate != '0000-00-00 00:00:00')
					{
						$FundingDate = date('m/d/Y',strtotime($CalculatorData->FundingDate));
					}
					else if(!empty($OrderSummary->FundingDate) && $OrderSummary->FundingDate != '0000-00-00 00:00:00' && $loadexceldata)
					{
						$FundingDate = date('m/d/Y',strtotime($OrderSummary->FundingDate));
					}

					?>					
	  					<input type="text" title="Disbursement Date" name="FundingDate" class="form-control checklistdatepicker FundingDate CommonTrigger CommonTriggerClear" value="<?php echo $FundingDate; ?>">
					</td>
					<td></td>
					<td></td>
				</tr>

				<tr class ='manual'>
					<td>Premium Amount (From HOI Dec Page)</td>
					<td>$
						<?php 
						$PremiumAmount = '';
						if(!empty($CalculatorData->PremiumAmount))
						{
							$PremiumAmount = $CalculatorData->PremiumAmount;
						}
						else if(!empty($OrderSummary->PremiumAmount) && $loadexceldata)
						{
							$PremiumAmount = $OrderSummary->PremiumAmount;
						}
						
						?>	
						<input type="number" title="Premium Amount" name="PremiumAmount" class="form-control  PremiumAmount CommonTrigger CommonTriggerClear" value="<?php echo $PremiumAmount; ?>">
					</td>
					<td></td>
					<td></td>
				</tr>

				<tr class ='auto'>
					<td>Hazard Insurance (Monthly)</td>
					<td>$
						<?php 
						$HazardInsurance = '';
						if(!empty($CalculatorData->HazardInsurance))
						{
							$HazardInsurance = $CalculatorData->HazardInsurance;
						}

						?>	
						<span class="HazardInsurance"><?php echo $HazardInsurance; ?></span>
					</td>
					<td></td>
					<td></td>
				</tr>

				<tr class ='auto'>
					<td>Funding Date + 60 Days</td>
					<td>
						<?php 
						$FundingDateAdd = '';
						if(!empty($CalculatorData->FundingDateAdd) && $CalculatorData->FundingDateAdd != '0000-00-00 00:00:00')
						{
							$FundingDateAdd = date('m/d/Y',strtotime($CalculatorData->FundingDateAdd));
						}

						?>	
						<input type="text" title="Funding Date" name="FundingDateAdd" class="form-control checklistdatepicker FundingDateAdd CommonTrigger CommonTriggerClear" value="<?php echo $FundingDateAdd; ?>" readonly>
					</td>
					<td></td>
					<td></td>
				</tr>

				<tr class ='manual'>
					<td>Policy Expiration Date (From HOI Dec Page)</td>
					<td>
						<?php 
							$PolicyExpiration = '';
						if(!empty($CalculatorData->PolicyExpirationDate) && $CalculatorData->PolicyExpirationDate != '0000-00-00 00:00:00')
						{
							$PolicyExpiration = date('m/d/Y',strtotime($CalculatorData->PolicyExpirationDate));
						}
						else if(!empty($OrderSummary->PolicyExpDate) && $OrderSummary->PolicyExpDate != '0000-00-00 00:00:00' && $loadexceldata)
						{
							$PolicyExpiration = date('m/d/Y',strtotime($OrderSummary->PolicyExpDate));
						}

						?>		
						<input type="text" title="Policy Expiration" name="PolicyExpiration" class="form-control checklistdatepicker PolicyExpiration CommonTrigger CommonTriggerClear" value="<?php echo $PolicyExpiration; ?>">
					</td>
					<td><span class="FinalOutputPreload" style="display: none;"></span></td>
					<td></td>
				</tr>

				<tr class ='manual'>
					<td>Renewal Policy Available (Yes/No)</td>
					<td>
						<select class="select2picker RenewalPolicy CommonTrigger" id="RenewalPolicy"  name="RenewalPolicy" style="width:100%;">   
							<option value="YES" <?php if($CalculatorData->RenewalPolicy == 'YES'){ echo 'selected';} ?> >YES</option>
							<option value="NO" <?php if($CalculatorData->RenewalPolicy == 'NO'){ echo 'selected';} ?> >NO</option> 
						</select>
					</td>
					<td></td>
					<td></td>
				</tr>

				<tr class ='manual'>
					<td>First Payment Date (Green light closing screen)</td>
					<td>
						<?php
						$FirstPaymentDate = '';
						if(!empty($CalculatorData->FirstPaymentDate) && $CalculatorData->FirstPaymentDate != '0000-00-00 00:00:00')
						{
							$FirstPaymentDate = date('m/d/Y',strtotime($CalculatorData->FirstPaymentDate));
						}
						else if(!empty($OrderSummary->FirstPaymentDate) && $OrderSummary->FirstPaymentDate != '0000-00-00 00:00:00' && $loadexceldata)
						{
							$FirstPaymentDate = date('m/d/Y',strtotime($OrderSummary->FirstPaymentDate));
						}

						?>	
						<input type="text" title="Funding Date" name="FirstPaymentDate" class="form-control checklistdatepicker FirstPaymentDate CommonTrigger CommonTriggerClear" value="<?php echo $FirstPaymentDate; ?>">
					</td>
					<td></td>
					<td></td>
				</tr>

				<tr>
					<td colspan="4" class="CenterClass">Final Input for HOI:</td>
				</tr>

				<tr class ='auto calculatorhighlight-rows'>
					<td colspan="4" class="CenterClass"><span class="FinalOutPut"><?php echo $CalculatorData->FinalOutPut ?></span></td>
				</tr>

				<tr>
					<td class ='auto'>Next Due Date (Aggregate escrow account screen)</td>
					<td class ='auto'>
						<?php
						$InsuranceNextDueDate = '';
						if(!empty($CalculatorData->InsuranceNextDueDate) && $CalculatorData->InsuranceNextDueDate != '0000-00-00 00:00:00')
						{
							$InsuranceNextDueDate = date('m/d/Y',strtotime($CalculatorData->InsuranceNextDueDate));
						}
						else if(!empty($OrderSummary->InsuranceNextDueDate) && $OrderSummary->InsuranceNextDueDate != '0000-00-00 00:00:00' && $loadexceldata)
						{
							$InsuranceNextDueDate = date('m/d/Y',strtotime($OrderSummary->InsuranceNextDueDate));
						}

						?>	
						<input type="text" title="" name="InsuranceNextDueDate" class="form-control checklistdatepicker InsuranceNextDueDate CommonTrigger CommonTriggerClear" value="<?php echo $InsuranceNextDueDate; ?>" readonly>
					</td>
					<td class ='auto'></td>
					<td class ='auto'></td>
				</tr>

				<tr class ='auto calculatorhighlight-rows'>
					<td>State</td>
					<td><?php echo $OrderSummary->PropertyStateCode; ?></td>
					<td>Cushion</td>
					<td>
						<?php
						if(empty($OrderSummary->PropertyStateCode)) { $Value = '';}
						else if(strtoupper($OrderSummary->PropertyStateCode) == 'ND' || strtoupper($OrderSummary->PropertyStateCode) == 'NV') {	$Value = 0; } 
						else if(strtoupper($OrderSummary->PropertyStateCode) == 'VT') {$Value = 1;}
						else { $Value = 2;}
						echo $Value; ?>
					</td>
				</tr>
				</tbody>
			</table>
		</form>
		<button type="button" class="btn btn-fill btn-tumblr pull-right search InsurenceSave" >Save</button>
		<button type="button" class="btn btn-fill btn-facebook pull-right search InsurenceClear" >Clear All</button>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function()
	{ 
        // window.addEventListener('load', function() {
        // 	//InsuranceCalculator()
        // });

    // InsurenceClear
		$(document).off('click', '.InsurenceClear').on('click', '.InsurenceClear', function (e) 
		{
			$('.CommonTriggerClear').val('');
			$('.HazardInsurance').html('0.00');
			$('.FinalOutPut').html('-');
			$("#RenewalPolicy").select2("val", "");

		});
		

		function validate_insuranceinputs()
		{
			var FundingDate = $('.FundingDate').val();
			var PremiumAmount = $('.PremiumAmount').val();
			var PolicyExpiration = $('.PolicyExpiration').val();
			var RenewalPolicy = $('#RenewalPolicy').val();
			var FirstPaymentDate = $('.FirstPaymentDate').val();

			if(FundingDate == '' || FundingDate == 'undefined'  && FundingDate == null) 
			{
				$.notify({icon:"icon-bell-check",message:'Enter Disbursement Date!'},{type:"danger",delay:1000});
				return false;
			} else if (PremiumAmount == '') {
				$.notify({icon:"icon-bell-check",message:'Enter Premium Amount!'},{type:"danger",delay:1000});
				return false;
			} else if (PolicyExpiration == '') {
				$.notify({icon:"icon-bell-check",message:'Enter Policy Expiration Date!'},{type:"danger",delay:1000});
				return false;
			} else if (RenewalPolicy == '') {
				$.notify({icon:"icon-bell-check",message:'Select Renewal Policy!'},{type:"danger",delay:1000});
				return false;
			} else if (FirstPaymentDate == '') {
				$.notify({icon:"icon-bell-check",message:'Enter First Payment Date!'},{type:"danger",delay:1000});
				return false;
			}
			return true;
		}
		
		// CommonTrigger
		$(document).off('blur', '.CommonTrigger').on('blur', '.CommonTrigger', function (e) 
		{
			
			if(validate_insuranceinputs()) {
				insurance_calculations();
			}

		});

		$(document).off('click', '.InsurenceSave').on('click', '.InsurenceSave', function (e) 
		{
			if(validate_insuranceinputs()) {
				InsertCalcData();
			}
		});

		function insurance_calculations()
		{
			var FundingDate = $('.FundingDate').val();
			var FirstPaymentDate = $('.FirstPaymentDate').val();
			var PolicyExpiration = $('.PolicyExpiration').val();
			var RenewalPolicy = $('#RenewalPolicy').val();
			$.ajax({
				type:"POST",
				url : '<?php  echo base_url().'CommonController/insurance_calculations'?>',
				data:{'FundingDate':FundingDate,'FirstPaymentDate':FirstPaymentDate,'PolicyExpiration':PolicyExpiration,'RenewalPolicy':RenewalPolicy,'CalculateDays':60},
				cache: false,
				dataType: 'json',
				beforeSend: function () {
				},
				success :function(response)
				{
					$('.FundingDateAdd').val(response.FundingDateAdd);

						//change  HazardInsurance from PremiumAmount
						var PremiumAmount= $('.PremiumAmount').val();
						if(PremiumAmount != '')
						{
							PremiumAmount = (PremiumAmount/12).toFixed(5);
							PremiumAmount = calculate_roundup(PremiumAmount,2);
							$('.HazardInsurance').html(PremiumAmount);
						}
						else
						{
							$('.HazardInsurance').html('');
						}

						//final output text pre load
						var PolicyExpiration = $('.PolicyExpiration').val();
						var FundingDateAdd = $('.FundingDateAdd').val();
						if(PolicyExpiration <= FundingDateAdd)
						{
							$('.FinalOutputPreload').html('Collect the Insurance in Line 903 in 2015 itemization screen');
						}
						else
						{
							$('.FinalOutputPreload').html('Escrow it in Aggregate Escrow Account Screen');
						}

						//final output generation 
						FundingDate =$('.FundingDate').val();
						PolicyExpiration = $('.PolicyExpiration').val();
						var RenewalPolicy = $('#RenewalPolicy').val();
						var FinalOutputPreload = $('.FinalOutputPreload').html();
						if((FundingDate == 'undefined'  || FundingDate == null || FundingDate == '') || (PolicyExpiration == 'undefined'  || PolicyExpiration == null || PolicyExpiration == '') || (RenewalPolicy == 'undefined'  || RenewalPolicy == null || RenewalPolicy == ''))
						{
							$('.FinalOutPut').html('-');
						}
						else if(RenewalPolicy == 'YES')
						{
							$('.FinalOutPut').html(FinalOutputPreload);
						}
						else
						{
							$('.FinalOutPut').html('Escrow it in Aggregate Escrow Account Screen');
						}


					$('.InsuranceNextDueDate').val(response.InsuranceNextDueDate);


				}
			});
		}



		function InsertCalcData()
		{
			var WorkflowModuleUID =$('.CalculatorWorkflowModuleUID').val();
			var OrderUID =$('.CalculatorOrderUID').val();
			var FundingDate =$('.FundingDate').val();
			var FundingDateAdd =$('.FundingDateAdd').val();
			var PremiumAmount= $('.PremiumAmount').val();
			var PolicyExpiration = $('.PolicyExpiration').val();
			var FirstPaymentDate = $('.FirstPaymentDate').val();
			var RenewalPolicy = $('#RenewalPolicy').val();
			var FinalOutPut = $('.FinalOutPut').html();
			var HazardInsurance = $('.HazardInsurance').html();
			var InsuranceNextDueDate = $('.InsuranceNextDueDate').val();
			var formdata = {'WorkflowModuleUID':WorkflowModuleUID,'OrderUID':OrderUID,'FundingDate':FundingDate,'FundingDateAdd':FundingDateAdd,'PremiumAmount':PremiumAmount,'PolicyExpiration':PolicyExpiration,'RenewalPolicy':RenewalPolicy,'FinalOutPut':FinalOutPut,'HazardInsurance':HazardInsurance,'FirstPaymentDate':FirstPaymentDate,'InsuranceNextDueDate':InsuranceNextDueDate};
			$.ajax({
					type:"POST",
					url : '<?php  echo base_url().'CommonController/InsertCalcData'?>',
					data:formdata,
					cache: false,
					beforeSend: function () {
					},
					success :function(response)
					{
						console.log(response);
						$.notify({
							icon: "icon-bell-check",
							message: "Insurance Calculations Saved"
						}, {
							type: "success",
							delay: 1000
						});
					}
				});
		}

		
		$(document).off('click', '.InsuranceAuditLog').on('click', '.InsuranceAuditLog', function (e) 
		{
			var WorkflowModuleUID =$('.CalculatorWorkflowModuleUID').val();
			var OrderUID =$('.CalculatorOrderUID').val();
			$.ajax({
					type:"POST",
					url : '<?php  echo base_url().'CommonController/CalculatorAuditLogView'?>',
					data:{'OrderUID':OrderUID,'WorkflowModuleUID':WorkflowModuleUID,'CalculatorType':'InsuranceCalculator'},
					cache: false,
					beforeSend: function () {
					},
					success :function(response)
					{
						$('.AuditLogTbody').html(response);
					}
				});
			$('.InsuranceAuditLogTable').fadeToggle();
		});

		


	});
</script>