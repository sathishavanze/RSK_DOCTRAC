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
	.welcome_call{
		padding-top:10px;
		padding-bottom:25px;
		display:block;
	}
	.welcome_head{
		margin: 0;
		font-size: 14px;
		font-weight: 600;
		padding: 5px 10px;
		cursor: pointer;
		background: #eee;
		position:relative;
	}

	.click_expand:before{
		content: " click to collapse ";
		font-weight: bold;
		position: absolute;
		top: 5px;
		text-decoration: underline;
		font-size: 10px;
		right: 15px;    
		color: #5a5a5a;
	}

	.custom_icon:before{
		content: " click to expand ";
		font-weight: bold;
		position: absolute;
		top: 5px;
		text-decoration: underline;
		font-size: 10px;
		right: 15px;
		color: #5a5a5a;
	}
	.welcome_content{
		border: 1px solid #eee;
		padding: 10px 15px;
	}
	.welcome_content p{
		margin-bottom: 10px;
		line-height: 1.9;
		font-size: 11px;
	}
	.thank_p{
		font-weight: bold;
		font-size: 12px!important;
		margin-bottom:0!important;
	}
	.first_p{
		font-weight: bold;
		font-size: 12px!important;
	}

  .welcome_table thead tr th{
  padding: 5px 5px;
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

	.welcome_history_table>tbody>tr>td, .welcome_history_table>tfoot>tr>td{
		white-space: normal;
	}
	.welcome_history_table>thead>tr>th{
		white-space: normal;
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
	.welcome_head_h5{
		border-bottom: 1px solid #eee;
		padding-bottom: 3px;
		margin-top: 10px;
		margin-bottom: 1px;
		font-size: 15px;
		font-weight:bold;
	}
	.welcome_call_div{
		margin-top:50px;
	}
	.welcome_call_history{
		margin-top:50px;
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

<!-- 						<div class="welcome_call">
							<div class="row">
								<div class="col-md-12">
									<h3 class="welcome_head click_expand" title="click to expand">Welcome Call Script</h3>
									<div class="welcome_content">
										<p class="first_p">Welcome Call to Borrower:</p>
										<p>Good Morning/Afternoon, this is (Your First Last name) with (Company) calling on a recorded line. May I please speak to (Borrower First, Last name) ?</p>
										<p>Ask Borrower to please verify the last four digits of their social.</p>
										<p>Thank them.</p>
										<p>State reason for call.</p>
										<p>Verify names(s) on loan and title and confirm, if applicable, both will be present to sign documents at closing.</p>
										<p>Verify the location/address of subject property and type of residency (Primary, Investment or Second home)</p>
										<p>Go over loan comparison (existing verses proposed), state benefit(s) of new loan</p>
										<p>Go through title questionairre</p>
										<p>State your completion of reviewing all application documents and tell Borrower of any needed items. Be sure to convey urgency in returning docs to us. Confirm email address on file and tell Borrower you will send a follow up email once off the phone which contains itemized requests from Borrower. State all of your contact information is in the signature of the email should they have any questions or concerns in between the times you are contacting them.</p>
										<p>Go over process of what happens when all docs from Borrower are received and CTC is in. Tell them timeline after you send their file to Underwriting and what will happen upon Final Approval or conditions.</p>
										<p>Upon Final Approval: Scheduling Dept. will contact you to set up your closing. Once closing is scheduled, Closing Disclosure will be sent and must be acknowledge to move the closing timeline forward.</p>
										<p>Upon conditions(s): You will contact Borrower immediately to meet any conditions as quickly as possible to get their application back to underwriting and Final Approved.</p>
										<p>If applicable: Let Borrower know they will receive a customer satisfaction to let us know how we did. Please take a few minutes to complete, it is very appreciated.</p>
										<p>Tell them of the next day you would like to contact them and if that day is ok and what time is good to call?</p>
										<p>Ask if they have any other questions while you have them on the line today?</p>
										<p>Confirm when you are contacting them again..have a wonderful day.</p>
										<p class="thank_p">Thank you for choosing Freedom Mortgage.</p>


									</div>
								</div>
							</div>
						</div> -->

							<?php  $this->load->view('checklist/checklistheader'); ?>
							<tbody class="addChecklist">
								<?php $question_sno =0; foreach ($DocumentTypeNameDetails as $key => $DocTypeName) {  $question_sno += 1; ?>
									<?php
									$data['DocTypeName'] = $DocTypeName;
									$data['OrderUID'] = $OrderSummary->OrderUID;
									$data['question_sno'] = $question_sno;
									$this->load->view('checklist/checklist',$data);

									/*child checklist start*/
									$childchecklists = $this->Common_Model->get_childchecklists($OrderSummary->CustomerUID,$WorkflowModuleUID,$DocTypeName->DocumentTypeUID,$OrderSummary->LoanType);
									if(!empty($childchecklists)) {
										$childquestion_sno = 0;
										foreach ($childchecklists as $childchecklistkey => $childchecklistvalue) {
											$childquestion_sno += 1;
											$childata['DocTypeName'] = $childchecklistvalue;
											$childata['OrderUID'] = $OrderSummary->OrderUID;
											$childata['question_sno'] = $question_sno.'.'.$childquestion_sno;
											$this->load->view('checklist/checklist',$childata);
										}
									}
									/*child checklist ends*/

								}
								$data1['OrderUID'] = $OrderSummary->OrderUID; 
								$data1['question_sno'] = $question_sno;
								$data1['WorkflowUID'] = $WorkflowModuleUID;
								$this->load->view('checklist/otherchecklist',$data1); ?>
							</tbody>	
						</table>
					<div class="row">
							<div class="col-md-12">
						<p class="custom_add_icon pull-right addchecklistrow" data-count = '1'  data-moduleUID = '<?php echo $WorkflowModuleUID; ?>' style="margin-right:10px;" aria-hidden="true">Add New Checklist</p>
					</div>
				</div>

				<div class="row">
					

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
								
								<label for="Customer" class="bmd-label-floating" style=" margin-left: 8px; margin-bottom: 0px; ">Preferred Day</label>
								<select class="select2picker form-control PreferedDay meetingfield" id="PreferedDay" name="PreferedDay">
									<option value="">Select Day</option>
									<?php foreach ($days as $key => $day) { ?>

										<option value="<?php echo $key; ?>" 
										<?php echo !empty($tOrderMeeting->PreferedDay) && $tOrderMeeting->PreferedDay == $key ? 'selected' : ''; ?>
										>
											<?php echo $day;?>
												
										</option>';

									<?php } ?>

								</select>
								<span class="mdl-textfield__error form_error"></span>
							

						</div>

						<div class="col-md-2" id="clockpicker-container">
							<label for="PreferedTime" class="bmd-label-floating" style=" margin-bottom: 0px; ">Preferred Time</label>
							<input type="text" class="form-control preferedtimepicker meetingfield" id="PreferedTime" name="PreferedTime" value="<?php echo !empty($tOrderMeeting->PreferedTime) ? $tOrderMeeting->PreferedTime : ''; ?>">
						</div>


						<div class="col-md-2">
								
								<label for="Customer" class="bmd-label-floating" style=" margin-left: 8px; margin-bottom: 0px; ">Preferred Timezone</label>
								<select class="select2picker form-control PreferedTimeZone meetingfield" id="PreferedTimeZone" name="PreferedTimeZone">
									<option value="">Select Timezone</option>
									<?php foreach ($mTimeZones as $tkey => $zone) { ?>

										<option value="<?php echo $zone->TimeZoneUID; ?>" 
											
											<?php echo !empty($tOrderMeeting->PreferedTimeZone) && $tOrderMeeting->PreferedTimeZone == $zone->TimeZoneUID ? 'selected' : ''; ?>

											>
										<?php echo $zone->TimeZoneName;?>
											
										</option>';

									<?php } ?>
								</select>
								<span class="mdl-textfield__error form_error"></span>
							

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




						<div class="row">
							<div class="col-md-12">
								<div class="welcome_call_div">
									<h5 class="welcome_head_h5" style="">Welcome Call Script</h5>
										<table class="table table-striped display nowrap welcome_table" id=""  cellspacing="0" width="100%"  style="width:100%;margin:0;">
											<thead>
												<tr>
													<th style="width:50px;">SNo</th>
													<th>Doc File Name</th>
													<th>Doc Created Date</th>
													<th>Uploaded User</th>
													<th class="text-right">Action</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td>1</td>
													<td>welcome_script.pdf</td>
													<td>09/23/2019 06:00:14</td>
													<td>James</td>
													<td class="text-right">
														<span style="">
															<a href="<?php echo base_url();?>assets/pdf/welcome_script.pdf" target="_blank" class="btn btn-xs viewFile" style="background-color: #3b5998;color: #fff;padding: 3px 5px!important;">PDF</a>
														</span>

														<span style="">
															<a href="javascript:void(0);" class="btn btn-xs pdfdownload" style="background-color: green;color: #fff;margin-left: 1px;padding: 3px 5px!important;"><i class="icon-cloud-download"></i></a>
														</span>

														<span style="">
															<button type="button" id="bt" onclick="" class="btn btn-xs btn-primary" style="margin-left: 1px;padding: 3px 5px!important;"> <i class="icon-printer2"></i> </button>
														</span>

													</td>
												</tr>

											</tbody>
										</table>
								</div>
							</div>
						</div>




						<div class="row">
							<div class="col-md-12">
								<div class="welcome_call_history">
									<h5 class="welcome_head_h5">Callback Summary</h5>
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
								<select class="select2picker orderpriority_parkinghourselect" name="orderpriority_parking" style="width: 100%;height: 31px;padding: 0px;">
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

				<div>
					<?php 
					$data2['OrderUID'] = $OrderSummary->OrderUID; 
					$data2['WorkflowModuleUID'] = $WorkflowModuleUID;
					$this->load->view('commonCommands/commandTable',$data2); ?>
				</div>

						<div class="row">
						<div class="col-sm-12 form-group pull-right mt-20">
							<p class="text-right">
								<?php if ($OrderSummary->StatusUID != $this->config->item('keywords')['Cancelled']) {?>
									<button type="submit" class="btn btn-space btn-social btn-color btn-twitter checklist_update pull-right" value="1">Update</button>
								<?php }?>
								<?php $this->load->view('orderinfoheader/workflowbuttons'); ?>

							</p>
						</div>
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


	$(document).ready(function(){

		$(".welcome_content").show();

		$(".welcome_head").click(function(){
			$(".welcome_content").slideToggle();
			$(this).toggleClass("custom_icon");
		});


		$(".order_expand_div").hide();

		$(".order_expand").click(function(){
			$(".order_expand_div").slideToggle();
		});

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
	});

</script>






