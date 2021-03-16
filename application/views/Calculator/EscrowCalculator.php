<style type="text/css">
	.EscrowCalculator>thead>tr>th {
		text-align: center; 
		font-size: 25px !important;  
		padding: 1px !important;
	}

	.EscrowCalculator{
		border-top:1px solid #eee;
	}
	.EscrowCalculator>tbody>tr>td {
		text-align: left; 
		font-weight: 700;
		font-size: 12px !important;
		padding: 1px !important;
	}
	.EscrowTax .select2-choice > .select2-chosen {
		font-size: 18px;
		font-weight: 600;
	}
	.autofield{background: #e4e4e4  !important}

	.EscrowFinalOutPut{
		text-align: center; 
	}
	.select_term .form-control{
		height: 25px!important;
		padding: 0;
	}

	.calculatorhighlight-rows, .calculatorhighlight-rows > td, .calculatorhighlight-rows input{
		background-color: #FFFFCC !important;
		color: black;
		font-weight: 700 !important;
	}
</style>
<div class="tab-pane " id="<?php echo isset($CalculatorPrefixId) ? $CalculatorPrefixId : ''; ?>EscrowCalculator">
		<div class="col-md-12 col-xs-12 pd-0">

			<button type="button" class="btn btn-fill pull-right search EscrowAuditLog">Audit Log</button>
			<button type="button" class="btn btn-fill pull-right BtnHolidaysList">Holiday List</button>
			<table class="table table-striped EscrowAuditLogTable" style="display: none;">
				<thead>
					<tr>
						<th>S.NO</th>
						<th>AuditLog</th>
						<th>Date Time</th>
						<th>Changed BY</th>
					</tr>
				</thead>
				<tbody class="EscrowAuditLogTbody" >
					<tr>
						<td></td>
						<td></td>
						<td></td>
					</tr>
				</tbody>
			</table>

			<form  id="EscrowForm" class="EscrowForm">
				<input type="hidden" name="CalculatorWorkflowModuleUID" value="<?php echo $WorkflowModuleUID; ?>" class="CalculatorWorkflowModuleUID">
				<input type="hidden" name="CalculatorOrderUID" value="<?php echo $OrderSummary->OrderUID; ?>" class="CalculatorOrderUID">

			<h4 class="card-title calc_head">Escrow Calculator</h4>
			<table class="table EscrowCalculator" id="EscrowCalculator">
				<thead>
					<tr>
						<th></th>
						<th>

							<div class="row">
								<div class="col-md-12 select_term">
									<div class="form-group bmd-form-group">
										<select class="select2picker form-control EscrowTax" id="EscrowTax"  name="EscrowTax"> 
											<option value="Annual" <?php echo ($CalculatorData->EscrowTax == 'Annual') ? 'selected' : '' ?> >Tax-Annual</option>
											<option value="SemiAnnual" <?php echo ($CalculatorData->EscrowTax == 'SemiAnnual') ? 'selected' : '' ?> >Tax-SemiAnnual</option> 
											<option value="Quarterly" <?php echo ($CalculatorData->EscrowTax == 'Quarterly') ? 'selected' : '' ?> >Tax-Quarterly</option> 
										</select>
									</div> 
								</div>
							</div>
						</th>
						<th></th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Disbursement Date (Greenlight closing screen)</td>
						<td >
							<?php  
							$EscrowFundingDate = '';
							if(!empty($CalculatorData->EscrowFundingDate) && $CalculatorData->EscrowFundingDate != '0000-00-00 00:00:00')
							{
								$EscrowFundingDate = date('m/d/Y',strtotime($CalculatorData->EscrowFundingDate));
							}
							else if(!empty($OrderSummary->FundingDate) && $OrderSummary->FundingDate != '0000-00-00 00:00:00' && $loadexceldata)
							{
								$EscrowFundingDate = date('m/d/Y',strtotime($OrderSummary->FundingDate));
							}
							?>					
							<input type="text" title="" name="EscrowFundingDate" class="form-control checklistdatepicker EscrowFundingDate EscrowCommonTrigger" value="<?php echo $EscrowFundingDate; ?>">
						</td>
						<td></td>
						<td></td>
					</tr>

					<tr>
						<td>TAX amount from the TAX CERT</td>
						<td >$
							<?php 
							$TaxAmount = '';
							if(!empty($CalculatorData->TaxAmount))
							{
								$TaxAmount = $CalculatorData->TaxAmount;
							}
							else if(!empty($OrderSummary->TaxAmount) && $loadexceldata)
							{
								$TaxAmount = $OrderSummary->TaxAmount;
							}
							?>	
							<input type="number" title="" name="TaxAmount" class="form-control  TaxAmount EscrowCommonTrigger" value="<?php echo $TaxAmount; ?>">
						</td>
						<td></td>
						<td></td>
					</tr>

					<tr class ='autofield'>
						<td>TAXES (Monthly)</td>
						<td >$
							<?php 
							$TAXESmonthly = '';
							if(!empty($CalculatorData->TAXESmonthly))
							{
								$TAXESmonthly = $CalculatorData->TAXESmonthly;
							}
							else if(!empty($OrderSummary->TAXESmonthly) && $loadexceldata)
							{
								$TAXESmonthly = $OrderSummary->TAXESmonthly;
							}

							?>	
							<input type="text" title="" name="TAXESmonthly" class="form-control TAXESmonthly EscrowCommonTrigger" value="<?php echo $TAXESmonthly; ?>" readonly>
						</td>
						<td></td>
						<td></td>
					</tr>

					<tr class ='autofield'>
						<td>Disbursement Date + 60 Days</td>
						<td >
							<?php 
							$EscrowFundingDateAdd = '';
							if(!empty($CalculatorData->EscrowFundingDateAdd) && $CalculatorData->EscrowFundingDateAdd != '0000-00-00 00:00:00')
							{
								$EscrowFundingDateAdd = date('m/d/Y',strtotime($CalculatorData->EscrowFundingDateAdd));
							}

							?>					
							<input type="text" title="" name="EscrowFundingDateAdd" class="form-control checklistdatepicker EscrowFundingDateAdd EscrowCommonTrigger" value="<?php echo $EscrowFundingDateAdd; ?>" readonly>
						</td>
						<td></td>
						<td></td>
					</tr>

					<!-- TAX Due date 1 -->
					<tr class="TaxSelect">
						<td>TAX Due date 1 from INFONET (Vendor search)</td>
						<td>
							<?php 
							$EscrowTaxDueDate1 = '';
							if(!empty($CalculatorData->EscrowTaxDueDate1) && $CalculatorData->EscrowTaxDueDate1 != '0000-00-00 00:00:00')
							{
								$EscrowTaxDueDate1 = date('m/d/Y',strtotime($CalculatorData->EscrowTaxDueDate1));
							}
							else if(!empty($OrderSummary->EscrowTaxDueDate1) && $OrderSummary->EscrowTaxDueDate1 != '0000-00-00 00:00:00' && $loadexceldata)
							{
								$EscrowTaxDueDate1 = date('m/d/Y',strtotime($OrderSummary->EscrowTaxDueDate1));
							}

							?>					
							<input type="text" title="" name="EscrowTaxDueDate1" class="form-control checklistdatepicker EscrowTaxDueDate1 EscrowCommonTrigger" value="<?php echo $EscrowTaxDueDate1; ?>">
						</td>
						<td>$
							<?php 
							$EscrowTaxDueDate1Amount = '';
							if(!empty($CalculatorData->EscrowTaxDueDate1Amount))
							{
								$EscrowTaxDueDate1Amount = $CalculatorData->EscrowTaxDueDate1Amount;
							}
							else if(!empty($OrderSummary->EscrowTaxDueDate1Amount) && $loadexceldata)
							{
								$EscrowTaxDueDate1Amount = $OrderSummary->EscrowTaxDueDate1Amount;
							}

							?>	
							<input type="number" title="" name="EscrowTaxDueDate1Amount" class="form-control  EscrowTaxDueDate1Amount EscrowCommonTrigger" placeholder="Enter TAX Due date 1 Amount" value="<?php echo $EscrowTaxDueDate1Amount; ?>">
						</td>
						<td></td>
					</tr>


					<!-- TAX Due date 2 -->
					<tr class="SemiAnnual TaxSelect">
						<td>TAX Due date 2 from INFONET (Vendor search)</td>
						<td>
							<?php 
							$EscrowTaxDueDate2 = '';
							if(!empty($CalculatorData->EscrowTaxDueDate2) && $CalculatorData->EscrowTaxDueDate2 != '0000-00-00 00:00:00')
							{
								$EscrowTaxDueDate2 = date('m/d/Y',strtotime($CalculatorData->EscrowTaxDueDate2));
							}
							else if(!empty($OrderSummary->EscrowTaxDueDate2) && $OrderSummary->EscrowTaxDueDate2 != '0000-00-00 00:00:00' && $loadexceldata)
							{
								$EscrowTaxDueDate2 = date('m/d/Y',strtotime($OrderSummary->EscrowTaxDueDate2));
							}
						
							?>					
							<input type="text" title="" name="EscrowTaxDueDate2" class="form-control checklistdatepicker EscrowTaxDueDate2 EscrowCommonTrigger" value="<?php echo $EscrowTaxDueDate2; ?>">
						</td>
						<td>$
							<?php 
								$EscrowTaxDueDate2Amount = '';
							if(!empty($CalculatorData->EscrowTaxDueDate2Amount))
							{
								$EscrowTaxDueDate2Amount = $CalculatorData->EscrowTaxDueDate2Amount;
							}
							else if(!empty($OrderSummary->EscrowTaxDueDate2Amount) && $loadexceldata)
							{
								$EscrowTaxDueDate2Amount = $OrderSummary->EscrowTaxDueDate2Amount;
							}

							?>	
							<input type="number" title="" name="EscrowTaxDueDate2Amount" class="form-control  EscrowTaxDueDate2Amount EscrowCommonTrigger" placeholder="Enter TAX Due date 2 Amount" value="<?php echo $EscrowTaxDueDate2Amount; ?>">
						</td>
						<td></td>
					</tr>

					<!-- TAX Due date 3 -->
					<tr class="Quarterly TaxSelect">
						<td>TAX Due date 3 from INFONET (Vendor search)</td>
						<td>
							<?php 
							$EscrowTaxDueDate3 = '';
							if(!empty($CalculatorData->EscrowTaxDueDate3) && $CalculatorData->EscrowTaxDueDate3 != '0000-00-00 00:00:00')
							{
								$EscrowTaxDueDate3 = date('m/d/Y',strtotime($CalculatorData->EscrowTaxDueDate3));
							}
							else if(!empty($OrderSummary->EscrowTaxDueDate3) && $OrderSummary->EscrowTaxDueDate3 != '0000-00-00 00:00:00' && $loadexceldata)
							{
								$EscrowTaxDueDate3 = date('m/d/Y',strtotime($OrderSummary->EscrowTaxDueDate3));
							}

							?>					
							<input type="text" title="" name="EscrowTaxDueDate3" class="form-control checklistdatepicker EscrowTaxDueDate3 EscrowCommonTrigger" value="<?php echo $EscrowTaxDueDate3; ?>">
						</td>
						<td>$
							<?php 
								$EscrowTaxDueDate3Amount = '';
							if(!empty($CalculatorData->EscrowTaxDueDate3Amount))
							{
								$EscrowTaxDueDate3Amount = $CalculatorData->EscrowTaxDueDate3Amount;
							}
							else if(!empty($OrderSummary->EscrowTaxDueDate3Amount) && $loadexceldata)
							{
								$EscrowTaxDueDate3Amount = $OrderSummary->EscrowTaxDueDate3Amount;
							}
	
							?>	
							<input type="number" title="" name="EscrowTaxDueDate3Amount" class="form-control  EscrowTaxDueDate3Amount EscrowCommonTrigger" placeholder="Enter TAX Due date 3 Amount" value="<?php echo $EscrowTaxDueDate3Amount; ?>">
						</td>
						<td></td>
					</tr>

					<!-- TAX Due date 4 -->
					<tr class="Quarterly TaxSelect">
						<td>TAX Due date 4 from INFONET (Vendor search)</td>
						<td>
							<?php 
							$EscrowTaxDueDate4 = '';
							if(!empty($CalculatorData->EscrowTaxDueDate4) && $CalculatorData->EscrowTaxDueDate4 != '0000-00-00 00:00:00')
							{
								$EscrowTaxDueDate4 = date('m/d/Y',strtotime($CalculatorData->EscrowTaxDueDate4));
							}
							else if(!empty($OrderSummary->EscrowTaxDueDate4) && $OrderSummary->EscrowTaxDueDate4 != '0000-00-00 00:00:00' && $loadexceldata)
							{
								$EscrowTaxDueDate4 = date('m/d/Y',strtotime($OrderSummary->EscrowTaxDueDate4));
							}
					
							?>					
							<input type="text" title="" name="EscrowTaxDueDate4" class="form-control checklistdatepicker EscrowTaxDueDate4 EscrowCommonTrigger" value="<?php echo $EscrowTaxDueDate4; ?>">
						</td>
						<td>$
							<?php 
							$EscrowTaxDueDate4Amount = '';
							if(!empty($CalculatorData->EscrowTaxDueDate4Amount))
							{
								$EscrowTaxDueDate4Amount = $CalculatorData->EscrowTaxDueDate4Amount;
							}
							else if(!empty($OrderSummary->EscrowTaxDueDate4Amount) && $loadexceldata)
							{
								$EscrowTaxDueDate4Amount = $OrderSummary->EscrowTaxDueDate4Amount;
							}
							
							?>	
							<input type="number" title="" name="EscrowTaxDueDate4Amount" class="form-control  EscrowTaxDueDate4Amount EscrowCommonTrigger" placeholder="Enter TAX Due date 4 Amount" value="<?php echo $EscrowTaxDueDate4Amount; ?>">
						</td>
						<td></td>
					</tr>

					<tr>
					  <td>Updated TAX bill Available (Yes/No)</td>
					  <td >
					   <select class="select2picker UpdatedTaxBill EscrowCommonTrigger" id="UpdatedTaxBill"  name="UpdatedTaxBill" style="width:100%;">  
					  <option value="YES" <?php if($CalculatorData->UpdatedTaxBill == 'YES') { echo 'selected'; } ?> >YES</option>
					  <option value="NO" <?php if($CalculatorData->UpdatedTaxBill == 'NO') { echo 'selected'; } ?> >NO</option> 
					  </select>
						</td>
						<td></td>
						<td></td>
					</tr>

					<tr>
						<td>First Payment Date (Green light closing screen)</td>
						<td >
							<?php
							$EscrowFirstPaymentDate = '';
							if(!empty($CalculatorData->EscrowFirstPaymentDate) && $CalculatorData->EscrowFirstPaymentDate != '0000-00-00 00:00:00')
							{
								$EscrowFirstPaymentDate = date('m/d/Y',strtotime($CalculatorData->EscrowFirstPaymentDate));
							}
							else if(!empty($OrderSummary->FirstPaymentDate) && $OrderSummary->FirstPaymentDate != '0000-00-00 00:00:00' && $loadexceldata)
							{
								$EscrowFirstPaymentDate = date('m/d/Y',strtotime($OrderSummary->FirstPaymentDate));
							}
						
							?>	
							<input type="text" title="" name="EscrowFirstPaymentDate" class="form-control checklistdatepicker EscrowFirstPaymentDate EscrowCommonTrigger" value="<?php echo $EscrowFirstPaymentDate; ?>">
						</td>
						<td></td>
						<td></td>
					</tr>

					<tr class ='autofield calculatorhighlight-rows'>
						<td colspan="4" class="CenterClass">
						<input type="text" title="" name="EscrowFinalOutPut" class="form-control EscrowFinalOutPut EscrowCommonTrigger" value="<?php echo $CalculatorData->EscrowFinalOutPut; ?>" readonly>
					</td>
					</tr>

					<tr class ='autofield'>
						<td rowspan="4">Next Due Date (Aggregate escrow account screen)</td>

						<td >
							<?php
							$EscrowNextDueDate1 = '';
							if(!empty($CalculatorData->EscrowNextDueDate1) && $CalculatorData->EscrowNextDueDate1 != '0000-00-00 00:00:00')
							{
								$EscrowNextDueDate1 = date('m/d/Y',strtotime($CalculatorData->EscrowNextDueDate1));
							}
							else if(!empty($OrderSummary->EscrowNextDueDate1) && $OrderSummary->EscrowNextDueDate1 != '0000-00-00 00:00:00' && $loadexceldata)
							{
								$EscrowNextDueDate1 = date('m/d/Y',strtotime($OrderSummary->EscrowNextDueDate1));
							}
					
							?>	
							<input type="text" title="" name="EscrowNextDueDate1" class="form-control checklistdatepicker EscrowNextDueDate1 EscrowCommonTrigger" value="<?php echo $EscrowNextDueDate1; ?>" readonly>
						</td>
						<td></td>
						<td></td>

					</tr>

					<tr class ='autofield'>
						<td >
							<?php
							$EscrowNextDueDate2 = '';
							if(!empty($CalculatorData->EscrowNextDueDate2) && $CalculatorData->EscrowNextDueDate2 != '0000-00-00 00:00:00')
							{
								$EscrowNextDueDate2 = date('m/d/Y',strtotime($CalculatorData->EscrowNextDueDate2));
							}
							else if(!empty($OrderSummary->EscrowNextDueDate2) && $OrderSummary->EscrowNextDueDate2 != '0000-00-00 00:00:00' && $loadexceldata)
							{
								$EscrowNextDueDate2 = date('m/d/Y',strtotime($OrderSummary->EscrowNextDueDate2));
							}
						
							?>	
							<input type="text" title="" name="EscrowNextDueDate2" class="form-control checklistdatepicker EscrowNextDueDate2 EscrowCommonTrigger" value="<?php echo $EscrowNextDueDate2; ?>" readonly>
						</td>
						<td></td>
						<td></td>
					</tr>

					<tr class ='autofield'>
						<td >
							<?php
							$EscrowNextDueDate3 = '';
							if(!empty($CalculatorData->EscrowNextDueDate3) && $CalculatorData->EscrowNextDueDate3 != '0000-00-00 00:00:00')
							{
								$EscrowNextDueDate3 = date('m/d/Y',strtotime($CalculatorData->EscrowNextDueDate3));
							}
							else if(!empty($OrderSummary->EscrowNextDueDate3) && $OrderSummary->EscrowNextDueDate3 != '0000-00-00 00:00:00' && $loadexceldata)
							{
								$EscrowNextDueDate3 = date('m/d/Y',strtotime($OrderSummary->EscrowNextDueDate3));
							}

							?>	
							<input type="text" title="" name="EscrowNextDueDate3" class="form-control checklistdatepicker EscrowNextDueDate3 EscrowCommonTrigger" value="<?php echo $EscrowNextDueDate3; ?>" readonly>
						</td>
						<td></td>
						<td></td>
					</tr>

					<tr class ='autofield'>
						<td >
							<?php
							$EscrowNextDueDate4 = '';
							if(!empty($CalculatorData->EscrowNextDueDate4) && $CalculatorData->EscrowNextDueDate4 != '0000-00-00 00:00:00')
							{
								$EscrowNextDueDate4 = date('m/d/Y',strtotime($CalculatorData->EscrowNextDueDate4));
							}
							else if(!empty($OrderSummary->EscrowNextDueDate4) && $OrderSummary->EscrowNextDueDate4 != '0000-00-00 00:00:00' && $loadexceldata)
							{
								$EscrowNextDueDate4 = date('m/d/Y',strtotime($OrderSummary->EscrowNextDueDate4));
							}
			
							?>	
							<input type="text" title="" name="EscrowNextDueDate4" class="form-control checklistdatepicker EscrowNextDueDate4 EscrowCommonTrigger" value="<?php echo $EscrowNextDueDate4; ?>" readonly>
						</td>
						<td></td>
						<td></td>
					</tr>

					<tr class ='autofield calculatorhighlight-rows'>
						<td>State</td>
						<td ><?php echo $OrderSummary->PropertyStateCode; ?></td>
						<td>Cushion</td>
						<td >
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
			<button type="button" class="btn btn-fill btn-facebook pull-right search escrowSave" >Save</button>
			<button type="button" class="btn btn-fill btn-tumblr pull-right search escrowClear" >Clear All</button>
		</div>
	</div>

	<script type="text/javascript">

		function validate_escrowinputs()
		{
			var EscrowFundingDate = $('.EscrowFundingDate').val();
			var TaxAmount = $('.TaxAmount').val();
			var TAXESmonthly = $('.TAXESmonthly').val();
			var UpdatedTaxBill = $('#UpdatedTaxBill').val();
			var EscrowFirstPaymentDate = $('.EscrowFirstPaymentDate').val();
			var EscrowTax = $('#EscrowTax').val();
			var EscrowTaxDueDate1 = $('.EscrowTaxDueDate1').val();
			var EscrowTaxDueDate2 = $('.EscrowTaxDueDate2').val();
			var EscrowTaxDueDate3 = $('.EscrowTaxDueDate3').val();
			var EscrowTaxDueDate4 = $('.EscrowTaxDueDate4').val();
			var EscrowTaxDueDate1Amount  = $('.EscrowTaxDueDate1Amount ').val();
			var EscrowTaxDueDate2Amount  = $('.EscrowTaxDueDate2Amount ').val();
			var EscrowTaxDueDate3Amount  = $('.EscrowTaxDueDate3Amount ').val();
			var EscrowTaxDueDate4Amount  = $('.EscrowTaxDueDate4Amount ').val();

			if(EscrowFundingDate == '' || EscrowFundingDate == 'undefined'  || EscrowFundingDate == null) 
			{
				$.notify({icon:"icon-bell-check",message:'Enter Disbursement Date!'},{type:"danger",delay:1000});
				return false;
			} else if (TaxAmount == '') {
				$.notify({icon:"icon-bell-check",message:'Enter TAX Amount!'},{type:"danger",delay:1000});
				return false;
			} else if ((EscrowTax == 'Annual' || EscrowTax == 'SemiAnnual' || EscrowTax == 'Quarterly') && EscrowTaxDueDate1 == '') {
				$.notify({icon:"icon-bell-check",message:'Enter TAX Due date 1!'},{type:"danger",delay:1000});
				return false;
			} else if ((EscrowTax == 'Annual' || EscrowTax == 'SemiAnnual' || EscrowTax == 'Quarterly') && EscrowTaxDueDate1Amount  == '') {
				$.notify({icon:"icon-bell-check",message:'Enter TAX Due date 1 Amount!'},{type:"danger",delay:1000});
				return false;
			} else if ((EscrowTax == 'SemiAnnual' || EscrowTax == 'Quarterly') && EscrowTaxDueDate2 == '') {
				$.notify({icon:"icon-bell-check",message:'Enter TAX Due date 2!'},{type:"danger",delay:1000});
				return false;
			} else if ((EscrowTax == 'SemiAnnual' || EscrowTax == 'Quarterly' || EscrowTax == 'Quarterly') && EscrowTaxDueDate2Amount  == '') {
				$.notify({icon:"icon-bell-check",message:'Enter TAX Due date 2 Amount!'},{type:"danger",delay:1000});
				return false;
			// } else if ((EscrowTax == 'Quarterly') && EscrowTaxDueDate3 == '') {
			// 	$.notify({icon:"icon-bell-check",message:'Enter TAX Due date 3!'},{type:"danger",delay:1000});
			// 	return false;
			} else if ((EscrowTax == 'Quarterly') && EscrowTaxDueDate3Amount == '') {
				$.notify({icon:"icon-bell-check",message:'Enter TAX Due date 3 Amount!'},{type:"danger",delay:1000});
				return false;
			// } else if ((EscrowTax == 'Quarterly') && EscrowTaxDueDate4 == '') {
			// 	$.notify({icon:"icon-bell-check",message:'Enter TAX Due date 4!'},{type:"danger",delay:1000});
			// 	return false;
			} else if ((EscrowTax == 'Quarterly') && EscrowTaxDueDate4Amount == '') {
				$.notify({icon:"icon-bell-check",message:'Enter TAX Due date 4 Amount!'},{type:"danger",delay:1000});
				return false;
			} else if (UpdatedTaxBill == '' || UpdatedTaxBill == null) {
				$.notify({icon:"icon-bell-check",message:'Select Updated TAX bill!'},{type:"danger",delay:1000});
				return false;
			} else if (EscrowFirstPaymentDate == '') {
				$.notify({icon:"icon-bell-check",message:'Enter First Payment Date!'},{type:"danger",delay:1000});
				return false;
			}
			return true;
		}

		function Insert_escrow_CalcData()
		{
			var CalculatorOrderUID =$('.CalculatorOrderUID').val();
			var CalculatorWorkflowModuleUID =$('.CalculatorWorkflowModuleUID').val();
			var EscrowTax = $('#EscrowTax').val();
			var EscrowFundingDate  = $('.EscrowFundingDate').val();
			var TaxAmount  = $('.TaxAmount').val();
			var TAXESmonthly  = $('.TAXESmonthly').val();
			var EscrowFundingDateAdd  = $('.EscrowFundingDateAdd').val();
			var EscrowTaxDueDate1  = $('.EscrowTaxDueDate1').val();
			var EscrowTaxDueDate1Amount  = $('.EscrowTaxDueDate1Amount').val();
			var EscrowTaxDueDate2  = $('.EscrowTaxDueDate2').val();
			var EscrowTaxDueDate2Amount  = $('.EscrowTaxDueDate2Amount').val();
			var EscrowTaxDueDate3  = $('.EscrowTaxDueDate3').val();
			var EscrowTaxDueDate3Amount  = $('.EscrowTaxDueDate3Amount').val();
			var EscrowTaxDueDate4  = $('.EscrowTaxDueDate4').val();
			var EscrowTaxDueDate4Amount  = $('.EscrowTaxDueDate4Amount').val();
			var UpdatedTaxBill  = $('.UpdatedTaxBill option:selected').val();
			var EscrowFirstPaymentDate  = $('.EscrowFirstPaymentDate').val();
			var EscrowFinalOutPut  = $('.EscrowFinalOutPut').val();
			var EscrowNextDueDate1  = $('.EscrowNextDueDate1').val();
			var EscrowNextDueDate2  = $('.EscrowNextDueDate2').val();
			var EscrowNextDueDate3  = $('.EscrowNextDueDate3').val();
			var EscrowNextDueDate4  = $('.EscrowNextDueDate4').val();


			var formdata = {'CalculatorWorkflowModuleUID':CalculatorWorkflowModuleUID,'CalculatorOrderUID':CalculatorOrderUID,'EscrowTax':EscrowTax,'EscrowFundingDate':EscrowFundingDate,'TaxAmount':TaxAmount,'TAXESmonthly':TAXESmonthly,'EscrowFundingDateAdd':EscrowFundingDateAdd,'EscrowTaxDueDate1':EscrowTaxDueDate1,'EscrowTaxDueDate1Amount':EscrowTaxDueDate1Amount,'EscrowTaxDueDate2':EscrowTaxDueDate2,'EscrowTaxDueDate2Amount':EscrowTaxDueDate2Amount,'EscrowTaxDueDate3':EscrowTaxDueDate3,'EscrowTaxDueDate3Amount':EscrowTaxDueDate3Amount,'EscrowTaxDueDate4':EscrowTaxDueDate4,'EscrowTaxDueDate4Amount':EscrowTaxDueDate4Amount,'UpdatedTaxBill':UpdatedTaxBill,'EscrowFirstPaymentDate':EscrowFirstPaymentDate,'EscrowFinalOutPut':EscrowFinalOutPut,'EscrowNextDueDate1':EscrowNextDueDate1,'EscrowNextDueDate2':EscrowNextDueDate2,'EscrowNextDueDate3':EscrowNextDueDate3,'EscrowNextDueDate4':EscrowNextDueDate4};
			$.ajax({
				type:"POST",
				url : '<?php  echo base_url().'CommonController/InsertEscrowCalcData'; ?>',
				data:formdata,
				cache: false,
				beforeSend: function () {
				},
				success :function(response)
				{
					console.log(response);
					$.notify({
						icon: "icon-bell-check",
						message: "Escrow Calculations Saved"
					}, {
						type: "success",
						delay: 1000
					});
				}
			});
		}

		function escrow_calculations()
		{
			var TaxAmount = $('.TaxAmount').val();
			if(TaxAmount != '')
			{	
				TaxAmount = (TaxAmount/12).toFixed(5);
				TaxAmount = calculate_roundup(TaxAmount,2);
				$('.TAXESmonthly').val(TaxAmount);
			}
			else
			{
				$('.TAXESmonthly').val('');
			}

			var formdata = new FormData($("#EscrowForm")[0]);
			console.log(formdata);
			$.ajax({
				type:"POST",
				url : '<?php  echo base_url().'CommonController/EscrowCalculator'; ?>',
				data: formdata,
				cache: false,
				dataType: 'json',
				processData: false,
				contentType: false,
				beforeSend: function () {
				},
				success :function(response)
				{
					console.log(response);

					$('.EscrowFinalOutPut').val(response.EscrowFinalOutPut);
					$('.EscrowFundingDateAdd').val(response.EscrowFundingDateAdd);
					$('.EscrowNextDueDate1').val(response.EscrowNextDueDate1);
					$('.EscrowNextDueDate2').val(response.EscrowNextDueDate2);
					$('.EscrowNextDueDate3').val(response.EscrowNextDueDate3);
					$('.EscrowNextDueDate4').val(response.EscrowNextDueDate4);

								var EscrowTaxDueDate3  = $('.EscrowTaxDueDate3 ').val();


					if( $('#EscrowTax').val() == 'Quarterly' &&  ($('.EscrowTaxDueDate3 ').val() == '')) {
						$('.EscrowTaxDueDate3 ').val(response.EscrowTaxDueDate3)
					}

					if( $('#EscrowTax').val() == 'Quarterly' &&  ($('.EscrowTaxDueDate4 ').val() == '')) {
						$('.EscrowTaxDueDate4 ').val(response.EscrowTaxDueDate4)
					}

				}
			});
		}

		$(document).ready(function()
		{

			<?php echo ( (isset($CalculatorData->EscrowTax) && ($CalculatorData->EscrowTax == 'Annual' || $CalculatorData->EscrowTax == '')) || empty($CalculatorData) ) ? "$('.SemiAnnual').hide();$('.Quarterly').hide();" : "" ?>
			<?php echo (isset($CalculatorData->EscrowTax) && $CalculatorData->EscrowTax == 'SemiAnnual') ? "$('.Quarterly').hide();" : '' ?>

			// CommonTrigger
			$(document).off('blur', '.EscrowCommonTrigger').on('blur', '.EscrowCommonTrigger', function (e) 
			{

				if(validate_escrowinputs()) {
					escrow_calculations();
				}

			});

			$(document).off('change', '.select2picker.EscrowCommonTrigger').on('change', '.select2picker.EscrowCommonTrigger', function (e) 
			{

				if(validate_escrowinputs()) {
					escrow_calculations();
				}

			});

			$(document).off('click', '.escrowClear').on('click', '.escrowClear', function (e) 
			{
				$('.EscrowCommonTrigger').val('');
				$("#UpdatedTaxBill").select2("val", "");
			});

			$(document).off('click', '.escrowSave').on('click', '.escrowSave', function (e) 
			{
				if(validate_escrowinputs()) {
					Insert_escrow_CalcData();
				}
			});


			$(document).off('change', '.EscrowTax').on('change', '.EscrowTax', function (e) 
			{
				$('.TaxSelect').show();
				var EscrowTax = $(this).val();
				if(EscrowTax == 'Annual')
				{
					$('.SemiAnnual').hide();
					$('.Quarterly').hide();
				}
				else if(EscrowTax == 'SemiAnnual')
				{
					$('.Quarterly').hide();
				}
				$('.escrowClear').trigger('click');
			});


			$(document).off('click', '.EscrowAuditLog').on('click', '.EscrowAuditLog', function (e) 
			{
				var WorkflowModuleUID =$('.CalculatorWorkflowModuleUID').val();
				var OrderUID =$('.CalculatorOrderUID').val();
				$.ajax({
					type:"POST",
					url : '<?php  echo base_url().'CommonController/CalculatorAuditLogView'?>',
					data:{'OrderUID':OrderUID,'WorkflowModuleUID':WorkflowModuleUID,'CalculatorType':'EscrowCalculator'},
					cache: false,
					beforeSend: function () {
					},
					success :function(response)
					{
						$('.EscrowAuditLogTbody').html(response);
					}
				});
				$('.EscrowAuditLogTable').fadeToggle();
			});



			
		});
	</script>