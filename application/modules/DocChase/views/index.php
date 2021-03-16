<script src="<?php echo base_url(); ?>assets/js/plugins/bootstrap-notify.js"></script>
<script src="assets/js/plugins/bootstrap-notify.js"  type="text/javascript"></script>
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
		content: "*  ";
		color: red;
		font-size: 15px;
	}

	.nowrap{
		white-space: nowrap
	}

	.table-format > thead > tr > th{
		font-size: 12px;
	}


	.white-box {
		background: #ffffff;
		padding: 25px;
		margin-bottom: 15px;
	}
	.btn-outline {
		color: #fb9678;
		background-color: transparent;
	}
	.btn-danger.disabled {
		background: #fb9678;
		border: 1px solid #fb9678;
	}
	.exception{
		border: 1px dotted black;
		border-radius: 5px;
		padding-top: 10px;
		padding-bottom: 10px;
	}
	#iconc{
		float:right;
		margin-top:-19px;
	}
	.panel-heading-divider>p{
		border-bottom: 1px solid #d9d9d9;
		margin-left: 11px;
		padding-left: 0px;
		padding-right: 0;

		font-weight: 500px;

	}
	.panel-heading-divider{
		width: 100%;

	}
	.productadd_button{
		margin-top: -40px;
		font-size: 23px;
	}
	#Deleterow{
		margin-top: -40px; 
		font-size: 23px;
	}
	#fromdate{
		margin-top: -28px;
	}
	#ToDate{
		margin-top: -28px;
	}
	.Borrower
	{
		width: 100%;
	}
	.bmd-form-group .bmd-label-floating, .bmd-form-group .bmd-label-placeholder {
		top: -18px;
	}
	input {
		margin-bottom: 5px;
	}

	.questionparagraph{
		margin:0;
		padding:0px;
		padding-left: 2px;
		font-size:1.0em;
	}
	.questiondiv .form-control{
		font-size: 0.8em ;
	}
	.mt-0{
		margin-top:0px !important;
	}
	.questions tr select{
		height : 25px !important;
	}
	.questions .form-group{
		margin: 0px !important;
		padding-bottom: 0px !important;
	}
	.questions textarea{
		line-height: 10px !important;
		font-size: 10px !important;
	}
	.questions>tbody>tr>td, .questions>tbody>tr>th, .questions>tfoot>tr>td, .questions>thead>tr>td{
		padding: 0px 2px !important;
	}
	.questions textarea::-webkit-input-placeholder {
		font-size: 10px !important;
	}
	.questions textarea:-moz-placeholder { /* Firefox 18- */
		font-size: 10px !important;  
	}
	.questions textarea::-moz-placeholder {  /* Firefox 19+ */
		font-size: 10px !important;
	}
	.questions textarea:-ms-input-placeholder {
		font-size: 10px !important;
	}
	.main-panel>.content{
		margin-top:40px ;
	}
	.mb-5{
		margin-bottom:5px !important;
	}
	.tab-pane .card-category{
		font-size: 12px !important;
		font-weight: 500 !important;
		color: #403f3f !important;
	}
	table.questions td:nth-child(4){
		width:8% !important;
	}
	table.questions td:nth-child(5){
		width:29% !important;
	}
	table.questions td:nth-child(2){
		width:50% !important;
	}
	.mb-0{
		margin-bottom: 0px !important;
	}
	.ma-0{
		margin : 0px !important;
	}
	.questionlist label{
		font-size : 10px !important;
		padding-left: 15px !important;
	}

	.questionlist .form-check .form-check-label .circle {
		height: 11px;
		width: 11px; 
	}
	.questionlist  .form-check .form-check-label .circle .check{
		height: 11px;
		width: 11px;
	}
	.questionlist p{
		font-size: 11px;
		font-weight: 500;
		padding-top: 5px;
		padding-bottom: 5px;
		line-height: 17px;
	}
	.questionlist textarea::placeholder {
		font-size  : 10px !important;
	}


	.InputDocTypeQuestions#InputDocTypeQuestions{
		display: inline-block !important;
	}

	.expand_icon{
		position:relative;
	}
	.expand_icon i{
		font-size: 25px;
		right: 20px;
		top: -6px;
		cursor: pointer;
		position: absolute;
	}
	.questionlist{
		border-bottom:1px solid #ddd;
		vertical-align:middle;
	}

	.checklisttable .form-control:read-only {
		background-image:none;
	}
	.bmd-form-group{
		padding-bottom:0px;
	}

	.form-control:focus{
		border-color: #f44336;
	}
	.check_list_table>tbody>tr>td, .check_list_table>tbody>tr>th, .check_list_table>tfoot>tr>td, .check_list_table>thead>tr>td{
		white-space: normal;
		padding:0 10px!important;
	}	
	.questionlist  select.form-control:not([size]):not([multiple]) {
		height: 25px;
		padding: 0;
		font-size: 10px;
		width: 95% !important;
	}
	.icon_minus_td .bmd-form-group{
		float:left;
		width:92%;
	}


	.check_list_table thead tr th{
		padding: 4px 10px!important;
	}
	.welcome_head_h5{
		border-bottom: 1px solid #eee;
		padding-bottom: 3px;
		margin-top: 10px;
		margin-bottom: 1px;
		font-size: 15px;
		font-weight:bold;
	}
	.custom_add_icon{
		font-weight: 600;
		text-decoration: underline;
		font-size: 11px;
		text-align: right;
		margin: 0;
		display: block;
		cursor: pointer;
		color: #1A73E8;
	}

</style>
<input type="hidden" name="url" id="url" value="<?php echo $this->uri->segment(1);?>">
<div class="col-md-12 pd-0" >
	<div class="card mt-0">
		<div class="card-header tabheader" id="">
			<div class="col-md-12 pd-0">
				<div id="headers" style="color: #ffffff;">
					<!-- Order Info Header View -->  
					<?php $this->load->view('orderinfoheader/orderinfo'); ?>

				</div>
			</div>
		</div>
		<div class="card-body pd-0">
			<!-- Workflow Header View -->  
			<?php $this->load->view('orderinfoheader/workflowheader'); ?>
			<div class="expand_icon">
				<i class="fa fa-chevron-circle-down order_expand" aria-hidden="true"></i>
			</div>
			<div class="tab-content tab-space">
				<div class="tab-pane active" id="summary">
					<form action="#"  name="orderform" id="frmordersummary">
						<input type="hidden" name="OrderUID" id="OrderUID" value="<?php echo $OrderSummary->OrderUID;?>">
						<input type="hidden" name="OrderNumber" id="OrderNumber" value="<?php echo $OrderSummary->OrderNumber;?>">
						<input type="hidden" name="OnHoldUID" id="OnHoldUID"  value="<?php echo $OrderSummary->OnHoldUID;?>">

						<?php $this->load->view('Ordersummary/Orderdetails'); ?>


						<?php 
						foreach ($Workflows as $key => $workflow) { ?>
							<div class="show_workflowdiv" style="display: none;">
							<?php
							if($workflow->WorkflowModuleUID == 4)
							{
								$DocumentTypeNameDetails = $this->Common_Model->getfhacategorylist($OrderSummary->OrderUID,$workflow->WorkflowModuleUID,$OrderSummary->LoanType);
							}
							else
							{
								$DocumentTypeNameDetails = $this->Common_Model->getcategorylist($OrderSummary->OrderUID,$workflow->WorkflowModuleUID,$OrderSummary->LoanType);

							}


							$question_sno =0;
							$workflowtitle = $workflow->WorkflowModuleName;
							if($workflow->WorkflowModuleUID == 4)
							{
								if($OrderSummary->LoanType != '' && $OrderSummary->LoanType != 'FHA/VA')
								{
									if($OrderSummary->LoanType == 'VA')
									{
										$workflowtitle = 'VA CASE';
									}
									else if($OrderSummary->LoanType == 'FHA')
									{
										$workflowtitle = 'FHA CASE';
									}
								}
								else
								{
									$workflowtitle = 'FHA/VA CASE';
								}
							} ?>
							<strong><h6 style="padding: 7px 10px;font-size: 14px;margin: 0;font-weight: bold;background: #eee;color: #000;"><?php echo $workflowtitle ?></h6></strong>
							<?php
							$this->load->view('checklist/checklistheader');
							echo '<tbody class="addChecklist'.$workflow->WorkflowModuleUID.'">';

							foreach ($DocumentTypeNameDetails as $key => $DocTypeName) {  $question_sno += 1;
								//$CheckListAnswers = $this->Common_Model->getCheckListAnswers($DocTypeName->DocumentTypeUID,$OrderSummary->OrderUID,$DocTypeName->WorkflowModuleUID); 
								$data['DocTypeName'] = $DocTypeName;
								$data['OrderUID'] = $OrderSummary->OrderUID;
								$data['question_sno'] = $question_sno;
								$this->load->view('checklist/checklist',$data);
							}
							$data1['OrderUID'] = $OrderSummary->OrderUID; 
							$data1['question_sno'] = $question_sno;
							$data1['WorkflowUID'] = $workflow->WorkflowModuleUID;
							$this->load->view('checklist/otherchecklist',$data1);?>
						</tbody>
						<tr><td colspan="5"> <p class="custom_add_icon pull-right addchecklistrowPreScreen" data-div = '<?php echo "addChecklist".$question_sno; ?>'  data-count = '<?php echo $question_sno; ?>'  data-moduleUID = '<?php echo $workflow->WorkflowModuleUID; ?>' aria-hidden="true">Add New Checklist</p></td> </tr>
					</table>
					</div>
				<?php } ?>
				<div>
					<?php 
					$data2['OrderUID'] = $OrderSummary->OrderUID; 
					$data2['WorkflowModuleUID'] = $this->config->item('Workflows')['DocChase'];
					$this->load->view('commonCommands/commandTable',$data2); ?>
				</div>



				<div class="row" style="margin-top: 10px;">
					<input type="hidden" name="WorkflowModuleUID" id="WorkflowModuleUID" value="<?php echo $WorkflowModuleUID; ?>">

						<?php 
							$days = [
								'Sunday'=>'Sunday',
								'Monday'=>'Monday',
								'Tuesday'=>'Tuesday',
								'Wednesday'=>'Wednesday',
								'Thursday'=>'Thursday',
								'Friday'=>'Friday',
								'Saturday'=>'Saturday',
							];
						?>
						<div class="col-md-12" style="margin-top: 50px;"><h5 class="welcome_head_h5" style="">Best Time to Call</h5></div>
						<div class="col-md-2">
								
								<label for="PreferedDay" class="bmd-label-floating" style=" margin-left: 8px; margin-bottom: 0px; ">Preferred Day</label>
								<select class="select2picker form-control PreferedDay meetingfield" id="PreferedDay" name="PreferedDay">
									<option value="">Select Day</option>
									<?php foreach ($days as $key => $day) { ?>

										<option value="<?php echo $key; ?>" 
										
										<?php echo !empty($tOrderMeeting->PreferedDay) && $tOrderMeeting->PreferedDay == $key ? 'selected' : ''; ?>><?php echo $day;?>
												
										</option>

									<?php } ?>

								</select>
								<span class="mdl-textfield__error form_error"></span>
							

						</div>

						<div class="col-md-2" id="clockpicker-container">
							<label for="PreferedTime" class="bmd-label-floating" style=" margin-bottom: 0px; ">Preferred Time</label>
							<input type="text" class="form-control preferedtimepicker meetingfield" id="PreferedTime" name="PreferedTime" value="<?php echo !empty($tOrderMeeting->PreferedTime) ? $tOrderMeeting->PreferedTime : ''; ?>">
						</div>


						<div class="col-md-2">

								<label for="PreferedTimeZone" class="bmd-label-floating" style=" margin-left: 8px; margin-bottom: 0px; ">Preferred Timezone</label>
								<select class="select2picker form-control PreferedTimeZone meetingfield" id="PreferedTimeZone" name="PreferedTimeZone">
										<option value="">Select Timezone</option>
									<?php foreach ($mTimeZones as $tkey => $zone) { ?>

										<option value="<?php echo $zone->TimeZoneUID; ?>"<?php echo !empty($tOrderMeeting->PreferedTimeZone) && $tOrderMeeting->PreferedTimeZone == $zone->TimeZoneUID ? 'selected' : ''; ?> ><?php echo $zone->TimeZoneName;?></option>

									<?php } ?>
								</select>
								<span class="mdl-textfield__error form_error"></span>
							

						</div>

					
				</div>

				<h5 class="welcome_head_h5" style="margin-top: 50px;">Callback Summary</h5>
					<div class="row">
							<div class="col-md-12">
								<div class="welcome_call_history">
										<table class="table table-striped welcome_history_table" id=""  cellspacing="0" width="100%"  style="width:100%;margin:0;">
											<thead>
												<tr>
													<th>Remarks</th>
													<th style="width:200px;">Callback DateTime</th>
													<th style="width:100px;">User</th>
													<th style="width:180px;">Raised DateTime</th>
												</tr>
											</thead>
											<tbody>
												<?php  foreach ($Callbacks as $Callbackkey => $Callbacksvalue) { ?>
													
												<tr>
													<td><?php echo $Callbacksvalue->Remarks; ?></td>
													<td><?php echo $Callbacksvalue->Remainder; ?></td>
													<td><?php echo $Callbacksvalue->RaisedBy; ?></td>
													<td><?php echo $Callbacksvalue->RaisedDateTime; ?></td>
												</tr>
												<?php } ?>

											</tbody>
										</table>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-4 mt-20">
								<select class="select2picker form-control orderpriority_parkinghourselect" name="orderpriority_parking" style="width: 100%;height: 31px;padding: 0px;">
									<option value="" data-tat="">-- Select Option --</option>

									<?php
									$mParkingType = $this->Common_Model->get_mParkingType();
									 foreach ($mParkingType as $key => $parkingtype) { ?>
										<option value="<?php echo $parkingtype->ParkingTypeUID; ?>" data-tat="<?php echo $parkingtype->TAT; ?>"><?php echo $parkingtype->ParkingTypeName; ?> <?php echo !empty($parkingtype->TAT) ? '('.$parkingtype->TAT.' Hours)' : ' (Custom Hours)'; ?></option>
									<?php } ?>
								</select>
							</div>

							<div class="col-md-4 mt-20 orderpriority_parkinghourdiv" style="display: none;">
								<div class="form-group bmd-form-group">
									<label for="orderpriority_parkinghour" class="bmd-label-floating">Parking Hours</label>
									<input type="number" name="orderpriority_parkinghour" class="form-control input-xs" value="0" placeholder="Enter Parking Hours Here..." >
								</div>
							</div>
						</div>

			<div class="field_add">
				<div class="row mt-10">
					<div class="col-md-12">
						<div class="row">
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-12 form-group pull-right mt-20">
				<p class="text-right">
					<?php if ($OrderSummary->StatusUID != $this->config->item('keywords')['Cancelled']) {?>
						<button type="submit" class="btn btn-space btn-social btn-color btn-twitter checklist_update pull-right" value="1">Update</button>
					<?php }?>
					<?php $this->load->view('orderinfoheader/workflowbuttons'); ?>

				</p>
			</div>
		</form>
	</div>
</div>
</div>
</div>

<?php if (!empty($ExceptionList)) { ?>


	<div class="card mt-10">
		<div class="card-body">
			<div class="row">
				<div class="col-md-12">
					<h4 class="text-danger">Exception List</h4>
				</div>
				<div class="col-md-12 white-box perfectscrollbar" id="exception_list" style="max-height: 330px;overflow-y:scroll;">
					<?php foreach ($ExceptionList as $key => $value) { ?>

						<div class="btn-outline row col-md-12 m-b-10 m-t-30 mb-20 exception"> 
							<div class="col-md-6"> 

								<p align="left" style="margin-bottom: 0px;" class="col-md-12 Exception_label">Exception 
									<span class="Exception_span">
										<span class="fa fa-exclamation-circle"> </span>
									</span> 
								</p>

								<p align="left" style="margin-bottom: 0px;" class="col-md-12">Exception Name: 
									<span class="Exception_Name_span"><?php echo $value->ExceptionName; ?> </span>
								</p>

								<p align="left" style="margin-bottom: 0px;" class="col-md-12">Remarks: 
									<span class="Exception_Remark_span"><?php echo $value->ExceptionRemarks; ?></span>
								</p>
							</div>

							<div class="col-md-6"> 
								<p align="right" style="margin-bottom: 0px;" class="col-md-12">Date: 
									<span class="Exception_Date_span">: <?php echo $value->ExceptionRaisedDateTime; ?></span>                  
								</p>

								<p align="right" style="margin-bottom: 0px;" class="col-md-12">Exception Status: 
									<span class="Exception_Name_span"><?php echo $value->IsExceptionCleared == 1 ? 'Cleared' : 'Pending'; ?> </span>
								</p>
							</div>

						</div>

						<?php 
					} ?>
				</div>
			</div>
		</div>
	</div>
<?php } ?>
</div>

<?php $this->load->view('orderinfoheader/workflowpopover'); ?>


<script type="text/javascript">

	$(document).ready(function() {

		$(".order_expand_div").hide();

		$(".order_expand").click(function(){
			$(".order_expand_div").slideToggle();
		});

		$('.main-panel').scrollTop = 0;

		$('.perfectScrollbar').perfectScrollbar('update');

		$('.exception').hover(function(){ $(this).addClass('btn-danger') },function(){ $(this).removeClass('btn-danger') });
		
		$('select.pre_select').select2()
		.one('select2-focus', OpenSelect2)
		.on("select2-blur", function (e) {
			$(this).one('select2-focus', OpenSelect2)
		})

		function OpenSelect2() {
			var $select2 = $(this).data('select2')
			setTimeout(function() {
				if (!$select2.opened()) { $select2.open(); }
			}, 0);  
		}

		//display block when 
		var $selectedelement = $('.checklists option[value="YES"]:selected');
		$selectedelement.closest('.show_workflowdiv').show();
		$selectedelement.closest('tr').show();
		$selectedelement.closest('table').show();

		//display custom timer
		$('.orderpriority_parkinghourselect').change(function(event) {
			/* Act on the event */
			var TAT = $('.orderpriority_parkinghourselect option:selected').attr('data-tat');
			$('.orderpriority_parkinghourdiv').hide();
			if(TAT == "0") {
				$('.orderpriority_parkinghourdiv').show();
			}
		});

		$('.preferedtimepicker').datetimepicker({
			format: 'hh:mm A', 
			icons: {
				time: "fa fa-clock-o",
				date: "fa fa-calendar",
				up: "fa fa-chevron-up",
				down: "fa fa-chevron-down",
				previous: 'fa fa-chevron-left',
				next: 'fa fa-chevron-right',
				today: 'fa fa-screenshot',
				clear: 'fa fa-trash',
				close: 'fa fa-remove'
			}
		}).on('dp.change', function (e) {  
			$('.meetingfield:first').trigger('change');
		});


		$(document).off('click', '.addchecklistrowPreScreen').on('click', '.addchecklistrowPreScreen', function (e)
		{
			var workflowUId = $(this).attr('data-moduleUID');
			var count = Number($(this).attr('data-count'))+1;
			console.log(workflowUId);
			$(this).attr('data-count',count);
			$('.addChecklist'+workflowUId).append(`
				<tr class=" questionlist removeRow">
				
				
				<td>
				<textarea placeholder="Checklist" class="form-control checklists" id="Comments" name="checklist[`+workflowUId+`][OtherChecklist][N`+count+`][question]" rows="1"></textarea>
				</td>
				<td>
				<select name="checklist[`+workflowUId+`][OtherChecklist][N`+count+`][Answer]" title="Findings"  class="form-control form-check-input1 checklists pre_select" >
				<option value="empty"></option>
				<option value="Completed">Completed</option>
				<option value="Problem Identified">Issue</option>
				<option value="NA">NA</option>
				</select>
				</td>

				<td class="form-check dynamicCheckedList" style="text-align: center;border: 0!important;padding-top: 7px!important;">
				<label class="form-check-label Dashboardlable" for="[`+workflowUId+`][OtherChecklist`+count+`][IsChaseSend]"  style="color: teal">
				<input class="form-check-input checklists allworkflow " id = "[`+workflowUId+`][OtherChecklist`+count+`][IsChaseSend]" type="checkbox"  name="checklist[`+workflowUId+`][OtherChecklist][N`+count+`][FileUploaded]"> 
				<span class="form-check-sign">
				<span class="check"></span>
				</span>
				</label>
				</td>

				<td>
				<select name="checklist[`+workflowUId+`][OtherChecklist][N`+count+`][IsChaseSend]" title="
				Send to Chase"  class="form-control form-check-input1 checklists pre_select" >
				<option value="empty"></option>
				<option value="NA">NA</option>
				<option value="YES">YES</option>
				<option value="CANCELLED">CANCELLED</option>
				<option value="COMPLETED">COMPLETED</option>
				</select>
				</td>

				<td class="icon_minus_td">
				<textarea placeholder="Comments" class="form-control checklists" id="Comments" name="checklist[`+workflowUId+`][OtherChecklist][N`+count+`][Comments]" rows="1"></textarea>
				<a style="width:8%;float:right;"><i class="fa fa-minus-circle removechecklist pull-right" aria-hidden="true" style='font-size: 20px;margin-top: 10px;;'></i></a>
				</td>
				</tr>`);
			$('select.pre_select').select2()
  .one('select2-focus', OpenSelect2)
  .on("select2-blur", function (e) {
    $(this).one('select2-focus', OpenSelect2)
  })

function OpenSelect2() {
  var $select2 = $(this).data('select2');
  setTimeout(function() {
    if (!$select2.opened()) { $select2.open(); }
  }, 0);  
}
		
    });

	});





</script>
