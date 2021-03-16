<style type="text/css">
	.PayOffCalculator>thead>tr>th {
		text-align: center; 
		font-size: 25px !important;  
	}

	.PayOffCalculator>tbody>tr>td {
		text-align: left; 
		font-weight: 700;
		font-size: 13px !important;
	}
	.CenterClass
	{
		text-align: center!important;
	}
	.FinalOutPut{font-size: 20px !important;}
	/*.BtnHolidaysList{background: #676262 !important}*/
</style>

<div class="tab-pane" id="<?php echo isset($CalculatorPrefixId) ? $CalculatorPrefixId : ''; ?>PayOffCalculator">
	<div class="col-md-12 col-xs-12 pd-0">


		<button type="button" class="btn btn-fill pull-right search PayOffAuditLog">Audit Log</button>
		<button type="button" class="btn btn-fill pull-right BtnHolidaysList">Holiday List</button>
		<table class="table table-striped PayOffAuditLogTable" style="display: none;">
			<thead>
				<tr>
					<th>S.NO</th>
					<th>AuditLog</th>
					<th>Date Time</th>
					<th>Changed BY</th>
				</tr>
			</thead>
			<tbody class="PayOffAuditLogTbody" >
				<tr>
					<td></td>
					<td></td>
					<td></td>
				</tr>
			</tbody>
		</table>
		<form  id="PayOffForm" class="PayOffForm">
			<h4 class="card-title calc_head">PayOff Calculator</h4>
			<table class="table table-striped PayOffCalculator">

				<tbody>
					<tr> </tr>
					<tr class ='manual'>
						<td>Payment due date as shown in payoff statement</td>
						<td>
							<?php 
							$PaymentDueDatePayoff = '';
							if(!empty($CalculatorData->PaymentDueDatePayoff) && $CalculatorData->PaymentDueDatePayoff != '0000-00-00 00:00:00')
							{
								$PaymentDueDatePayoff = date('m/d/Y',strtotime($CalculatorData->PaymentDueDatePayoff));
							}
							else if(!empty($OrderSummary->PaymentDueDatePayoff) && $OrderSummary->PaymentDueDatePayoff != '0000-00-00 00:00:00' && $loadexceldata)
							{
								$PaymentDueDatePayoff = date('m/d/Y',strtotime($OrderSummary->PaymentDueDatePayoff));
							}
					
							?>						
							<input type="text" title="PaymentDueDatePayoff" name="PaymentDueDatePayoff" class="form-control checklistdatepicker PaymentDueDatePayoff PayOffTrigger" value="<?php echo $PaymentDueDatePayoff; ?>">
						</td>				
					</tr>

					<tr class ='manual'>
						<td>Disbursement Date as per in greenlight closing screen</td>
						<td>
							<?php 
							$FundingDatePayoff = '';
							if(!empty($CalculatorData->FundingDatePayoff) && $CalculatorData->FundingDatePayoff != '0000-00-00 00:00:00')
							{
								$FundingDatePayoff = date('m/d/Y',strtotime($CalculatorData->FundingDatePayoff));
							}
							else if(!empty($OrderSummary->FundingDate) && $OrderSummary->FundingDate != '0000-00-00 00:00:00' && $loadexceldata)
							{
								$FundingDatePayoff = date('m/d/Y',strtotime($OrderSummary->FundingDate));
							}
						
							?>		
							<input type="text" title="Disbursement Date" name="FundingDatePayoff" class="form-control checklistdatepicker FundingDatePayoff PayOffTrigger" value="<?php echo $FundingDatePayoff; ?>">
						</td>
					</tr>

					<tr class ='manual'>
						<td>Good through Date as per Payoff</td>
						<td>
							<?php 
							$GoodThroughDatePayoff = '';
							if(!empty($CalculatorData->GoodThroughDatePayoff) && $CalculatorData->GoodThroughDatePayoff != '0000-00-00 00:00:00')
							{
								$GoodThroughDatePayoff = date('m/d/Y',strtotime($CalculatorData->GoodThroughDatePayoff));
							}
							else if(!empty($OrderSummary->GoodThroughDatePayoff) && $OrderSummary->GoodThroughDatePayoff != '0000-00-00 00:00:00' && $loadexceldata)
							{
								$GoodThroughDatePayoff = date('m/d/Y',strtotime($OrderSummary->GoodThroughDatePayoff));
							}
							
							?>		
							<input type="text" title="GoodThroughDatePayoff" name="GoodThroughDatePayoff" class="form-control checklistdatepicker GoodThroughDatePayoff PayOffTrigger" value="<?php echo $GoodThroughDatePayoff; ?>">
						</td>
					</tr>

					<tr class ='auto'>
						<td>Good through Date needed as per actual disbursement date</td>
						<td>
							<?php 
							if(!empty($CalculatorData->GoodThroughDate) && $CalculatorData->GoodThroughDate != '0000-00-00 00:00:00')
							{
								$GoodThroughDate = date('m/d/Y',strtotime($CalculatorData->GoodThroughDate));
							}
							else
							{
								$GoodThroughDate = '';
							}
							?>	
							<input type="text" title="GoodThroughDate" name="GoodThroughDate" class="form-control checklistdatepicker GoodThroughDate PayOffTrigger" value="<?php echo $GoodThroughDate; ?>" readonly>
						</td>
					</tr>

					<tr class ='manual'>
						<td>Total Amount on the payoff statement</td>
						<td>$
							<?php 
							if(!empty($CalculatorData->TotalAmountPayoff))
							{
								$TotalAmountPayoff = $CalculatorData->TotalAmountPayoff;
							}
							else
							{
								$TotalAmountPayoff = '';
							}
							?>	
						<input type="number" title="Total Amount" name="TotalAmountPayoff" class="form-control  TotalAmountPayoff PayOffTrigger" value="<?php echo $TotalAmountPayoff; ?>">
						</td>
					</tr>

					<tr class ='manual'>
						<td>Daily Interest (Per diem) as shown on payoff statement</td>
						<td>$
							<?php 
							if(!empty($CalculatorData->DailyInterestPayoff))
							{
								$DailyInterestPayoff = $CalculatorData->DailyInterestPayoff;
							}
							else
							{
								$DailyInterestPayoff = '';
							}
							?>
						<input type="number" title="Daily Interest" name="DailyInterestPayoff" class="form-control  DailyInterestPayoff PayOffTrigger" value="<?php echo $DailyInterestPayoff ?>">
						</td>
					</tr>

					<tr class ='manual'>
						<td>Late Charge as shown on the payoff statement</td>
						<td>$
							<?php 
							if(!empty($CalculatorData->LateCharge))
							{
								$LateCharge = $CalculatorData->LateCharge;
							}
							else
							{
								$LateCharge = '';
							}
							?>
						<input type="number" title="Late Charge" name="LateCharge" class="form-control  LateCharge PayOffTrigger" value="<?php echo $LateCharge; ?>">
						</td>
					</tr>

					<tr> </tr>

					<tr class ='auto'>
						<td>Total Interest</td>
						<td>$
						<span class="TotalInterest"><?php echo $CalculatorData->TotalInterest; ?></span>
						</td>
					</tr>

					<tr> </tr>

					<tr class ='auto calculatorhighlight-rows'>
						<td>Use this Payoff</td>
						<td>$
						<span class="UseThisPayoff"><?php echo $CalculatorData->UseThisPayoff; ?></span>
						</td>
					</tr>
				</tbody>
			</table>
		</form>
		<button type="button" class="btn btn-fill btn-tumblr pull-right search PayOffSave" >Save</button>
		<button type="button" class="btn btn-fill btn-facebook pull-right search PayOffClear" >Clear All</button>
	</div>
</div>
<script type="text/javascript">

	function validate_payoffinputs()
	{
		var PaymentDueDatePayoff  = $('.PaymentDueDatePayoff ').val();
		var FundingDatePayoff = $('.FundingDatePayoff').val();

		if(PaymentDueDatePayoff == '' || PaymentDueDatePayoff == 'undefined'  && PaymentDueDatePayoff == null) 
		{
			$.notify({icon:"icon-bell-check",message:'Enter Payment Due Date!'},{type:"danger",delay:1000});
			return false;
		} else if (FundingDatePayoff == '') {
			$.notify({icon:"icon-bell-check",message:'Enter Disbursement Date!'},{type:"danger",delay:1000});
			return false;
		} 
		return true;
	}

	$(document).ready(function()
	{ 

		// PayOffClear
		$(document).off('click', '.PayOffClear').on('click', '.PayOffClear', function (e) 
		{
			$('.PayOffTrigger').val('');
			$('.UseThisPayoff').html('0.00');
			$('.TotalInterest').html('0.00');
		});
		
		// PayOffTrigger
		$(document).off('focusout', '.PayOffTrigger').on('focusout', '.PayOffTrigger', function (e) 
		{
			if(validate_payoffinputs()) {
				PayOffCalculator(); 
			}

		});

		$(document).off('click', '.PayOffSave').on('click', '.PayOffSave', function (e) 
		{

			if(validate_payoffinputs()) {
				InsertPayOffCalcData();
			}
		});

		function calculate_payoff()
		{
			var FundingDatePayoff =$('.FundingDatePayoff').val();
			var PaymentDueDatePayoff = $('.PaymentDueDatePayoff').val();
			var GoodThroughDatePayoff = $('.GoodThroughDatePayoff').val();
			var GoodThroughDate = $('.GoodThroughDate').val();
			var TotalAmountPayoff = $('.TotalAmountPayoff').val();
			var DailyInterestPayoff = $('.DailyInterestPayoff').val();
			var LateCharge = $('.LateCharge').val();
			$.ajax({
				type:"POST",
				url : '<?php  echo base_url().'CommonController/CalculatePayOff'?>',
				data:{'PaymentDueDatePayoff':PaymentDueDatePayoff,'GoodThroughDatePayoff':GoodThroughDatePayoff,'GoodThroughDate' :GoodThroughDate,'DailyInterestPayoff': DailyInterestPayoff ,'FundingDatePayoff':FundingDatePayoff,'LateCharge':LateCharge,'TotalAmountPayoff':TotalAmountPayoff},
				cache: false,
				beforeSend: function () {
				},
				success :function(response)
				{
					$('.UseThisPayoff').html(response);
					calculate_totalinterest();
				}
			});		
		}

		function calculate_totalinterest()
		{
			var FundingDatePayoff =$('.FundingDatePayoff').val();
			var PaymentDueDatePayoff = $('.PaymentDueDatePayoff').val();
			var GoodThroughDatePayoff = $('.GoodThroughDatePayoff').val();
			var GoodThroughDate = $('.GoodThroughDate').val();
			var TotalAmountPayoff = $('.TotalAmountPayoff').val();
			var DailyInterestPayoff = $('.DailyInterestPayoff').val();
			var LateCharge = $('.LateCharge').val();
			$.ajax({
				type:"POST",
				url : '<?php  echo base_url().'CommonController/CalculateTotalIntrest'?>',
				data:{'PaymentDueDatePayoff':PaymentDueDatePayoff,'GoodThroughDatePayoff':GoodThroughDatePayoff,'GoodThroughDate' :GoodThroughDate,'DailyInterestPayoff': DailyInterestPayoff ,'FundingDatePayoff':FundingDatePayoff,'LateCharge':LateCharge},
				cache: false,
				beforeSend: function () {
				},
				success :function(response)
				{
					$('.TotalInterest').html(response);
				}
			});	
		}

		function PayOffCalculator()
		{

			// change FundingDatePayoffAdd value from FundingDatePayoff
			var FundingDatePayoff =$('.FundingDatePayoff').val();
			if(FundingDatePayoff !== 'undefined'  && FundingDatePayoff != null && FundingDatePayoff != '')
			{
				$.ajax({
					type:"POST",
					url : '<?php  echo base_url().'CommonController/ExcludeHoliday'?>',
					data:{'StartDate':FundingDatePayoff,'CalculateDays':3},
					cache: false,
					beforeSend: function () {
					},
					success :function(response)
					{
						$('.GoodThroughDate').val(response);
						calculate_payoff();
					}
				});
			}
			else
			{
				$('.GoodThroughDate').val('');
				calculate_payoff();
			}			

		}

		function InsertPayOffCalcData()
		{
			var WorkflowModuleUID =$('.CalculatorWorkflowModuleUID').val();
			var OrderUID =$('.CalculatorOrderUID').val();
			var FundingDatePayoff =$('.FundingDatePayoff').val();
			var GoodThroughDate =$('.GoodThroughDate').val();
			var PaymentDueDatePayoff= $('.PaymentDueDatePayoff').val();
			var GoodThroughDatePayoff = $('.GoodThroughDatePayoff').val();
			var TotalAmountPayoff = $('.TotalAmountPayoff').val();
			var DailyInterestPayoff = $('.DailyInterestPayoff').val();
			var LateCharge = $('.LateCharge').val();
			var TotalInterest = $('.TotalInterest').html();
			var UseThisPayoff = $('.UseThisPayoff').html();
			var formdata = {'WorkflowModuleUID':WorkflowModuleUID,'OrderUID':OrderUID,'FundingDatePayoff':FundingDatePayoff,'GoodThroughDate':GoodThroughDate,'PaymentDueDatePayoff':PaymentDueDatePayoff,'GoodThroughDatePayoff':GoodThroughDatePayoff,'TotalAmountPayoff':TotalAmountPayoff,'DailyInterestPayoff':DailyInterestPayoff,'LateCharge':LateCharge,'TotalInterest':TotalInterest,'UseThisPayoff':UseThisPayoff};

			$.ajax({
					type:"POST",
					url : '<?php  echo base_url().'CommonController/InsertPayOffCalcData'?>',
					data:formdata,
					cache: false,
					beforeSend: function () {
					},
					success :function(response)
					{
						console.log(response);
						$.notify({
							icon: "icon-bell-check",
							message: "PayOff Calculator Saved"
						}, {
							type: "success",
							delay: 1000
						});
					}
				});
		}

		$(document).off('click', '.PayOffAuditLog').on('click', '.PayOffAuditLog', function (e) 
		{
			var WorkflowModuleUID =$('.CalculatorWorkflowModuleUID').val();
			var OrderUID =$('.CalculatorOrderUID').val();
			$.ajax({
					type:"POST",
					url : '<?php  echo base_url().'CommonController/CalculatorAuditLogView'?>',
					data:{'OrderUID':OrderUID,'WorkflowModuleUID':WorkflowModuleUID,'CalculatorType':'PayOffCalculator'},
					cache: false,
					beforeSend: function () {
					},
					success :function(response)
					{
						$('.PayOffAuditLogTbody').html(response);
					}
				});
			$('.PayOffAuditLogTable').fadeToggle();
		});

	});
</script>