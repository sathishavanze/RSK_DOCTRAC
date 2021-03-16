
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

							<?php $this->load->view('checklist/checklistheader'); ?>
							<tbody class="addChecklist">
								<tr style = "background: #d2cc84"><td style="font-size: 14px;font-weight: 400;padding: 9px 10px !important;" colspan="5"><?php echo $OrderSummary->LoanType;?> CHECKLIST</td> </tr>
								<?php 
								$question_sno =0; 
								$data['Tmp_HeadingName'] = '';
								foreach ($DocumentTypeNameDetails as $key => $DocTypeName) {  $question_sno += 1; ?>
									<?php
									$data['DocTypeName'] = $DocTypeName;
									$data['OrderUID'] = $OrderSummary->OrderUID;
									$data['question_sno'] = $question_sno;

									if(isset($DocTypeName->GroupHeadingName) && !empty($DocTypeName->GroupHeadingName)) { 

										$data['Tmp_HeadingName'] = '';
									}

									if(isset($DocTypeName->HeadingName) && !empty($DocTypeName->HeadingName)) {

										$data['Tmp_HeadingName'] = $this->Common_Model->RemoveSpecialCharsAndSpaces($DocTypeName->HeadingName);
									}

									$this->load->view('checklist/checklist',$data);
								} 
								$data1['OrderUID'] = $OrderSummary->OrderUID; 
								$data1['question_sno'] = $question_sno;
								$data1['WorkflowUID'] = $this->config->item('Workflows')['GateKeeping'];
								$this->load->view('checklist/otherchecklist',$data1);
								?>
							</tbody>	
						</table>
						<p class="custom_add_icon pull-right addchecklistrow" data-count = '1'  data-moduleUID = '<?php echo $this->config->item('Workflows')['GateKeeping']; ?>' style="margin-right:10px;" aria-hidden="true">Add New Checklist</p>
						<br>

						<!-- Checklist Group Headings with the issues checklist and their comments to be showed in gatekeeping as table (row-wise) - Gatekeeping -->
						<h5 style="font-weight: bold;">Problem Identified Checklists</h5>
						<table class="table checklisttable" cellspacing="0" style="width:100% !important;margin:0;">
							<tr>
								<th>Checklist</th>
								<th>Comments</th>
							</tr>
							<tbody>
								<?php 
								$question_sno = 1; 
								foreach ($DocumentTypeNameDetails as $key => $DocTypeName) {   

									if(isset($DocTypeName->GroupHeadingName) && !empty($DocTypeName->GroupHeadingName)) {

										$Temp_GroupHeading = $DocTypeName->GroupHeadingName;	
									}

									if(isset($DocTypeName->HeadingName) && !empty($DocTypeName->HeadingName)) {

										$Temp_ChecklistHeading = $DocTypeName->HeadingName;	
									}
									
									// Get checklist Answer
									$CheckListAnswers = $this->Common_Model->getCheckListAnswers($DocTypeName->DocumentTypeUID, $OrderSummary->OrderUID, $DocTypeName->WorkflowModuleUID); 

									// Print Problem Identified Checklist
									if ($CheckListAnswers->Answer == 'Problem Identified') { ?>
										
										<!-- GROUP HEADING -->
										<?php if(isset($Temp_GroupHeading) && !empty($Temp_GroupHeading)) { ?>

											<tr><td class="checklist-groupheading" colspan="2"><?php echo $Temp_GroupHeading; ?></td> </tr>

										<?php $Temp_GroupHeading = ''; } ?>
										<!-- HEADING -->
										<?php if(isset($Temp_ChecklistHeading) && !empty($Temp_ChecklistHeading)) { ?>

											<tr class="no-border"><td class="checklist-heading" colspan="2"><?php echo $Temp_ChecklistHeading; ?></td> </tr>

										<?php $Temp_ChecklistHeading = ''; } ?>

										<tr>
											<td class="mb-0">
												<?php echo $question_sno; ?>
												<?php echo "." ?> 
												<?php echo nl2br($DocTypeName->DocumentTypeName); ?> 
												<?php if ($DocTypeName->ScreenCode) {
													echo '<span style="font-size:9px; background-color: #26A69A; color:#ffffff; padding: 0px 4px; text-align: right; float: right;">' . $DocTypeName->ScreenCode . '</span></td>';
												} ?>
											</td>
											<td>
												<?php echo $CheckListAnswers->Comments; ?>
											</td>
										</tr>											

									<?php $question_sno++; }

								} ?>
							</tbody>
								
						</table>

						<div>
							<?php 
							$data2['OrderUID'] = $OrderSummary->OrderUID; 
							$data2['WorkflowModuleUID'] = $this->config->item('Workflows')['GateKeeping'];
							$this->load->view('commonCommands/commandTable',$data2); ?>
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
	$(document).ready(function(){
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
  
  		// hide document for Document / Approval Expiration Date checklist
		$( ".AutoPopulateDocumentDateHide" ).css("display", "none");

		$(document).off('blur', '.AutoPopulateChecklist').on('blur', '.AutoPopulateChecklist', function (e) {

			var ChecklistDocumentExpiryDate=[];
			$('.AutoPopulateDocumentExpiryDate').each(function(index, value) {
			  if (this.value) { ChecklistDocumentExpiryDate.push(new Date(this.value)); }
			});

			var AutoPopulateExpiryDate = $( ".MinimumExpiryDate" );

			if (ChecklistDocumentExpiryDate.length === 0)  {
				AutoPopulateExpiryDate.val('');
				$('.MinimumExpiryDate').trigger("blur");
			} else {

				var DocumentExpiryMinDate=formatDate(Math.min.apply(null,ChecklistDocumentExpiryDate));

				AutoPopulateExpiryDate.val(DocumentExpiryMinDate);
				$('.MinimumExpiryDate').trigger("blur");
			}
		});

		function formatDate(date) {
			var d = new Date(date),
			month = '' + (d.getMonth() + 1),
			day = '' + d.getDate(),
			year = d.getFullYear();

			if (month.length < 2) month = '0' + month;
			if (day.length < 2) day = '0' + day;

			return [month, day, year].join('/');
		}
	});
</script>