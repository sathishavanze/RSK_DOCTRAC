<?php $data['OrderSummary'] = $OrderSummary; ?>
<style type="text/css">
	.calc_head{
		font-weight: 600;
		font-size: 20px;
	}
	.calctab_cnt .form-control{
		margin: 0;
		height: 22px;
	}
	.calctab_cnt{
		padding-left:0!important;
		padding-right:0!important;
		padding-top:0!important;
	}
	.tab-space {
		padding: 0px !important;
	}
	#CalculatorInfoLabel {
		font-size: 21px;
		font-weight: bold;
		margin-bottom: -10px;
		margin-top: 5px;
	}
</style>
<!-- ORDER REVERSE POPUP CONTENT STARTS -->                

<div class="fulltopmodal modal fade" id="modal-CalculatorInfo" tabindex="-1" role="dialog" aria-labelledby="CalculatorInfoLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<span class="modal-title" id="CalculatorInfoLabel">Calculator Info - <?php echo $OrderSummary->LoanNumber; ?></span>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>

			<div class="modal-body">

				<ul class="nav nav-pills nav-pills-rose customtab" role="tablist">
					<?php
					$ShowFirstTab = "active show";
					?>

					<?php if (isset($WorkUpCalculatorShow) && $WorkUpCalculatorShow == 1) { ?>
						<li class="nav-item">
							<a class="nav-link <?php echo $ShowFirstTab; $ShowFirstTab = ''; ?>" data-toggle="tab" href="#WorkupCalculatorTab" role="tablist">
								Work Up

							</a>
						</li>
					<?php } ?>
						
					<?php if (isset($DocsOutCalculatorShow) && $DocsOutCalculatorShow == 1) { ?>
						<li class="nav-item">
							<a class="nav-link <?php echo $ShowFirstTab; $ShowFirstTab = ''; ?>" data-toggle="tab" href="#DocsOutCalculatorTab" role="tablist">
								Docs Out

							</a>
						</li>
					<?php } ?>
					
				</ul>

				<div class="tab-content tab-space customtabpane">
					<!-- Workup calculator -->
					<?php
					$ShowFirstTabCont = "active show";
					?>
					<?php if (isset($WorkUpCalculatorShow) && $WorkUpCalculatorShow == 1) { ?>
					<div class="tab-pane <?php echo $ShowFirstTabCont; $ShowFirstTabCont = ''; ?>" id="WorkupCalculatorTab">

						<!-- Calculator  -->
						<ul class="nav nav-pills nav-pills-rose customtab" role="tablist">

							<!-- InsuranceCalculator -->
							<li class="nav-item">
								<a class="nav-link active" data-toggle="tab" href="#Workup_InsuranceCalculator" role="tablist">
									Insurance Calculator
								</a>
							</li>
							<!-- InsuranceCalculator -->

							<!-- Escrow Calculator -->
							<li class="nav-item">
								<a class="nav-link " data-toggle="tab" href="#Workup_EscrowCalculator" role="tablist">
									Escrow Calculator
								</a>
							</li>
							<!-- Escrow Calculator -->

							<!-- Pay Off Calculator -->
							<li class="nav-item">
								<a class="nav-link " data-toggle="tab" href="#Workup_PayOffCalculator" role="tablist">
									Pay Off Calculator
								</a>
							</li>
							<!-- Pay Off Calculator -->
							
						</ul>

						<div class="tab-content tab-space calctab_cnt">

							<!-- Fetch workup calculator data -->
							<?php  
							$data['CalculatorData'] = $this->Common_Model->GetCalculatorData($OrderUID,$this->config->item('Workflows')['Workup']);
							$data['CalculatorPrefixId'] = "Workup_";
							?>

							<!-- InsuranceCalculator -->
							<?php $this->load->view('Calculator/InsuranceCalculator', $data); ?>
							<!-- InsuranceCalculator -->							

							<!-- EscrowCalculator -->
							<?php $this->load->view('Calculator/EscrowCalculator', $data); ?>
							<!-- EscrowCalculator -->							

							<!-- PayOffCalculator -->
							<?php $this->load->view('Calculator/PayOffCalculator', $data); ?>
							<!-- PayOffCalculator -->
							
						</div>
						<!-- Calculator  -->
					</div>
					<?php } ?>
					<!-- Workup calculator End -->
					<?php if (isset($DocsOutCalculatorShow) && $DocsOutCalculatorShow == 1) { ?>
					<div class="tab-pane <?php echo $ShowFirstTabCont; $ShowFirstTabCont = ''; ?>" id="DocsOutCalculatorTab">
						
						<!-- Calculator  -->
						<ul class="nav nav-pills nav-pills-rose customtab" role="tablist">

							<!-- InsuranceCalculator -->
							<li class="nav-item">
								<a class="nav-link active" data-toggle="tab" href="#DocsOut_InsuranceCalculator" role="tablist">
									Insurance Calculator
								</a>
							</li>
							<!-- InsuranceCalculator -->

							<!-- Escrow Calculator -->
							<li class="nav-item">
								<a class="nav-link " data-toggle="tab" href="#DocsOut_EscrowCalculator" role="tablist">
									Escrow Calculator
								</a>
							</li>
							<!-- Escrow Calculator -->

							<!-- Pay Off Calculator -->
							<li class="nav-item">
								<a class="nav-link " data-toggle="tab" href="#DocsOut_PayOffCalculator" role="tablist">
									Pay Off Calculator
								</a>
							</li>
							<!-- Pay Off Calculator -->
							
						</ul>

						<div class="tab-content tab-space calctab_cnt">

							<!-- Fetch workup calculator data -->
							<?php  
							$data['CalculatorData'] = $this->Common_Model->GetCalculatorData($OrderUID,$this->config->item('Workflows')['DocsOut']);
							$data['CalculatorPrefixId'] = "DocsOut_";
							?>

							<!-- InsuranceCalculator -->
							<?php $this->load->view('Calculator/InsuranceCalculator', $data); ?>
							<!-- InsuranceCalculator -->							

							<!-- EscrowCalculator -->
							<?php $this->load->view('Calculator/EscrowCalculator', $data); ?>
							<!-- EscrowCalculator -->							

							<!-- PayOffCalculator -->
							<?php $this->load->view('Calculator/PayOffCalculator', $data); ?>
							<!-- PayOffCalculator -->
							
						</div>
						<!-- Calculator  -->

					</div>
					<?php } ?>
				</div>					

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<!-- ORDER REVERSE POPUP CONTENT ENDS -->  

<script type="text/javascript">
	// Initialize select2
	$("select.select2picker").select2({
		//tags: false,
		theme: "bootstrap",
	});

	// All form element readonly
	$('#EscrowForm input, #InsuranceForm input, #PayOffForm input').prop('disabled', true);
	$("#EscrowForm select, #InsuranceForm select, #PayOffForm select").prop('disabled', true);
	$('.BtnHolidaysList, .InsuranceAuditLog, .InsurenceSave, .InsurenceClear, .EscrowAuditLog, .escrowClear, .escrowSave, .PayOffAuditLog, .PayOffSave, .PayOffClear').remove();

	$(document).ready(function() {
		$('.SemiAnnual, .Quarterly').show();
	});
</script>