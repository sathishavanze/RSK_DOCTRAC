<?php $OrderUID = $this->uri->segment(3); ?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/js/multiple-emails.css">
<style type="text/css">
	.highlight-invalid{
		border-color: red;
	}
	.multiple_emails-ul{
		font-size: 11px;
	}

	.multiple_emails-email {
		border: 1px #BBD8FB solid;
		border-radius: 3px;
		background: #F3F7FD;
		padding: 0px 0px 0px 5px;
	}


	input.multiple_emails-input::-moz-placeholder {
		font-style: italic;
	}


	input.multiple_emails-input::-webkit-input-placeholder{
		font-style: italic;

	}
	.margin-bottom{
		margin-bottom: 5px;
	}
	/* Popover */
	.popover {
		border: 1px solid #777;
		z-index: 99999999;
	}

	/* Popover Header */
	.popover-header {
	/*  background-color: #73AD21; 
	color: #FFFFFF; */
	font-size: 16px;
	text-align:center;
}

/* Popover Body */
.popover-body {
	/*  background-color: coral;
	color: #FFFFFF;*/
	padding: 25px;
}

/* Popover Arrow */
.arrow {
	border-right-color: red !important;
}

.select2-container--open {
	z-index: 9999999999991 !important;
}

/*.margin-top-12{
	margin-top: 12px;
}*/

#ParkingQueue .modal-content{
    overflow-y: unset!important;
        max-height: 800px;
}
#ParkingQueue .ps-container{
    overflow: unset!important;
}
#ParkingQueue .dropdown-menu.bootstrap-datetimepicker-widget.open{
	    height: auto;
}
#ParkingQueue {
    overflow: unset!important;
}

.checklisttitlefindings {
	background-color: #f9dddd !important;
	/*box-shadow: #f9dbdb 0px 0px 20px 17px inset;
    border: none;*/
}

/*.select2-container.modal-select{
	border-bottom: 1px solid #d2d2d2;
}*/

.ExceptionReasonsCont .select2-container-multi .select2-choices {
    border: 0;
    border-bottom: 1px solid #c1c1c1;
    background-image: none;
}
</style>
<?php 
$mReasons = $this->Common_Model->get_mreasons();
$data['mReasons'] = $mReasons;
$mQueueReasons = $this->Common_Model->get_mqueuesmreasons();
?>
<div id="exceptiononholdpopover-content" class="hide">
	<form class="form-inline" id="raiseexcetion">
		<select class="form-control" id="exceptiontype" name="exceptiontype" style="width: 244px;height: 31px;padding: 0px;" >
			<option value="">--Select--</option>
			<?php $mExceptions = $this->Common_Model->get('mExceptions');
			foreach ($mExceptions as $key => $value) { ?>
				<option value="<?php echo $value->ExceptionUID; ?>"><?php echo $value->ExceptionName; ?></option>
				<?php
			}
			?>

		</select>
		<br><br>
		<textarea  class="remarkstext"  placeholder="Enter Remarks Here..." name="remarks" style="width:244px;"></textarea>
		<div class="input-group">
			<br>
			<br>
			<br>
			<button class="btn btn-primary btnraiseexcetion" id="btnraiseexcetion" type="submit" style="height: 30px;" >Submit</button>
		</div>
	</form> 
</div>

<div id="bookmarkInstru-content" class="hide" >
	<form class="form-inline" id="bookmarkPopupForm">	
		<input type="hidden" name="OrderUID" id="OrderUID" value="<?php echo $OrderUID; ?>">				
		<textarea class="SpecifyInstru" id="SpecifyInstru" name="SpecifyInstru" placeholder="Enter Details Here..." style="width:244px;"></textarea>
		<div class="input-group">
			<br>							
			<button class="btn btn-primary btnbmpopups" id="btnbmpopups" style="height: 30px;" >Submit</button>
		</div>
	</form> 
</div>


<div id="clearexceptionpopover-content" class="hide">
	<form class="form-horizontal" id="frmpopoverclearexception" action="#" method="post">
		<div class="row">
			<div class="col-md-12">
				<input type="hidden" value="" name="OrderUID">
				<div class="col-md-12 mb-20">
					<textarea style="resize: none;" class="remarkstext form-control margin-bottom"  placeholder="Enter Remarks Here..." name="remarks"></textarea>
				</div>


<!-- 						<div class="col-md-12 mt-20">
							<label>Select Exception <span class="mandatory"></span></label>
							<select class="selectpopover" name="ExceptionTypeUID" style="width: 100%;height: 31px;padding: 0px;" >
								<option value="">--Select Exception Type--</option>
								<option value="1">Fatal Exception</option>
								<option value="2">Non-Fatal Exception</option>
							</select>

						</div> -->

						<div class="col-md-12 mt-20">
							<label>Select Reason <span class="mandatory"></span></label>
							<select class="selectpopover select2picker" name="Reason" style="width: 100%;height: 31px;padding: 0px;" >
								<option value="">--Select Reason--</option>
								
								<?php 
								foreach ($mReasons as $key => $value) { ?>
									<option value="<?php echo $value->ReasonUID; ?>" data-queueuid="<?php echo $value->QueueUID; ?>"><?php echo $value->ReasonName; ?></option>
									
									<?php

								}
								?>
							</select>

						</div>
						<div class="text-right  mt-20">
							<button class="btn btn-danger Reopen_workflow_submit btnclearexception" type="submit" id="btnclearexception"> Clear Exception</button>
							<button class="btn btn-danger Reopen_workflow_submit btnclearexception" type="submit" id="btnclearexception"> Clear Exception and Complete Stacking</button>
						</div>
					</div>
				</div>
			</form> 
		</div>


		<!-- CLEAR EXCEPTION POPUP CONTENT STARTS -->                

		<div class="modal fade" id="ClearException" tabindex="-1" role="dialog" aria-labelledby="ClearExceptionLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<form class="form-horizontal" id="frmclearexception" action="#" method="post">
						<div class="modal-header">
							<h5 class="modal-title" id="ClearExceptionLabel">Clear Exception</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-md-12">
									<div class="col-md-12 mb-20">
										<textarea style="resize: none;" class="remarkstext form-control margin-bottom"  placeholder="Enter Remarks Here..." name="remarks"></textarea>
									</div>

									<div class="col-md-12 mt-20">
										<label>Select Reason <span class="mandatory"></span></label>
										<select class="modal-select" name="Reason" id="ExceptionReason" style="width: 100%;height: 31px;padding: 0px;" >
											<option value="">--Select Reason--</option>
											
											<?php 
											foreach ($mReasons as $key => $value) { ?>
												<option value="<?php echo $value->ReasonUID; ?>" data-queueuid="<?php echo $value->QueueUID; ?>"><?php echo $value->ReasonName; ?></option>
												
												<?php

											}
											?>
										</select>

									</div>
									<div class="text-right  mt-20">
									</div>
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<button class="btn btn-pinterest Reopen_workflow_submit btnclearexception" name="submit" type="submit" id="btnclearexception" value="clearexception"> Clear Exception</button>

							<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
						</div>
					</form> 
				</div>
			</div>
		</div>
		<!-- CLEAR EXCEPTION POPUP CONTENT ENDS -->                


<!-- ORDER REVERSE POPUP CONTENT STARTS -->                
<?php $this->load->view('orderinfoheader/reverseworkflow'); ?>
<!-- ORDER REVERSE POPUP CONTENT ENDS -->                


<!-- STACKING COMPLETE POPUP CONTENT STARTS -->                
<div id="confirmModal" tabindex="-1" role="dialog" aria-hidden="true"  class="modal fade">
	<div class="modal-dialog" >
		<div class="modal-content" style="width: 450px;left: 30%;">
			<div class="modal-header" style="padding: 10px;">
				<button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"> <i class="icon-x"></i></button>
			</div>
			<div class="modal-body">
				<div class="text-center">
				<!-- <p id="msg" class="text-center" style="color:red;font-weight: bold"></p>
				-->
				<div class="text-primary" id="iconchg"><i style="font-size: 40px;" class="fa fa-info-circle fa-5x"></i></div>
				<span id="modal_msg" class="modal_spanheading">Do you want to complete the Stacking?</span>


				<div class="xs-mt-40">
					<button type="button" data-dismiss="modal" class="btn btn-default btn-space modal-close">Cancel</button>
					<button type="button" class="btn btn-primary btn-space workflow_complete">Proceed</button>
					<button type="button" class="btn btn-primary btn-space workflow_complete_final" style="display: none;">complete</button>
				</div>
			</div>
		</div>

	</div>
</div>
</div>
<!-- STACKING COMPLETE POPUP CONTENT STARTS -->   
<!-- STACKING COMPLETE POPUP CONTENT STARTS -->                
<div id="DocumentCheckInconfirmModal" tabindex="-1" role="dialog" aria-hidden="true"  class="modal fade">
	<div class="modal-dialog" >
		<div class="modal-content" style="width: 450px;left: 30%;">
			<div class="modal-header" style="padding: 10px;">
				<button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"> <i class="icon-x"></i></button>
			</div>
			<div class="modal-body">
				<div class="text-center">
				<!-- <p id="msg" class="text-center" style="color:red;font-weight: bold"></p>
				-->
				<div class="text-primary" id="iconchg"><i style="font-size: 40px;" class="fa fa-info-circle fa-5x"></i></div>
				<span id="modal_msg" class="modal_spanheading">Do you want to complete the Document CheckIn?</span>


				<div class="xs-mt-40">
					<button type="button" data-dismiss="modal" class="btn btn-default btn-space modal-close">Cancel</button>
					<button type="button" class="btn btn-primary btn-space workflow_complete">Proceed</button>
					<button type="button" class="btn btn-primary btn-space workflow_complete_final" style="display: none;">complete</button>
				</div>
			</div>
		</div>

	</div>
</div>
</div>
<!-- STACKING COMPLETE POPUP CONTENT STARTS -->

<!-- OnHold Order PopOver Begin  -->
<div id="OnHold-content" class="hide" style="max-width: 26% !important;">
	<form class="form-inline" id="OnHoldPopOverForm">
		<!-- <select class="form-control Onholdtype" id="Onholdtype" name="Onholdtype" style="width: 244px;height: 31px;padding: 0px;" required="true">
			<option value="">--Select--</option>
			<option value="DocumentLevel">Document Level</option>
			<option value="OrderLevel">Order Level</option>
		</select>
		<br> -->
		<textarea  class="remarkstext"  placeholder="Enter Remarks Here..." name="remarks" style="width:244px;" required="true"></textarea>
		<br>
		<br>
		<?php $CustomerEmail_result = $this->Common_Model->GetUserEmailFromReleaseonhold($OrderDetails->OrderUID);
		$RecepientEmails = '';
		if(!empty($CustomerEmail_result->CustomerEmail)){
			$RecepientEmails = '["'.$CustomerEmail_result->CustomerEmail.'"]';
		}

		?>
		<br>
		<div class="input-group">
			<input id="CustomerNotification" name="CustomerNotification" type="checkbox"> &nbsp; <span class="mdl-checkbox__label" >Notify Customer </span>
		</div>
		<br>
		<br>
		<input type='text' name='UserEmails' class='UserEmails' value='<?php echo $RecepientEmails; ?>' placeholder='Enter Mail Here' >

		<div class="input-group">
			<br>
			<br>
			<br>
			<button class="btn btn-primary BtnOnHold" id="BtnOnHold" type="button" style="height: 30px;" >Submit</button>
		</div>
	</form> 
</div>
<!-- OnHold Order PopOver End  -->

<!-- Release Order PopOver Begin  -->
<div id="OnHoldclear_popover_content" class="hide">
	<form class="form-inline" id="OnHold_clearform" action="#" method="post">
		<textarea  class="comments_text"  placeholder="Enter Comments/Notes Here..." name="comments" style="width:244px;"></textarea>
		<br>
		<br>
		<?php $CustomerEmail_result = $this->Common_Model->GetUserEmailFromReleaseonhold($OrderDetails->OrderUID);
		$RecepientEmails = '';
		if(!empty($CustomerEmail_result->CustomerEmail)){
			$RecepientEmails = '["'.$CustomerEmail_result->CustomerEmail.'"]';
		}

		?>
		<input id="CustomerNotification" name="CustomerNotification" type="checkbox"> &nbsp; <span class="mdl-checkbox__label" >Notify Customer </span>
		<input type='text' name='ClearUserEmails' class='form-control input-xs ClearUserEmails' value='<?php echo $RecepientEmails; ?>' placeholder='Enter Mail Here' >


		<br>
		<div class="col-sm-10 pull-right">
			<button class="btn btn-primary BtnReleaseOnHold" id="BtnReleaseOnHold" type="button" style="height: 30px;" >Submit</button>
		</div>

	</form> 
</div>
<!-- Release Order PopOver End  -->


<!-- Follow up pophover STARTS -->
<div id="Followup-content" class="hide">
	<!-- <a href="#" class="close" data-dismiss="alert">&times;</a> -->
	<form class="form-inline" id="FollowupPopOverForm">
		
		
		<textarea  class="remarkstext comments"  placeholder="Enter Remarks Here..." id='comments' name="comments"style="width:244px;" required="true"></textarea>
		<div class="input-group">
			<br>
			<br>
			<br>
			<button class="btn btn-primary BtnFollowup" id="BtnFollowup" type="button" style="height: 30px;" >Submit</button>
		</div>
	</form> 
</div>

<!-- Follow_complete up pophover STARTS -->
<div id="Followup_complete-content" class="hide">
	<!-- <a href="#" class="close" data-dismiss="alert">&times;</a> -->
	<form class="form-inline" id="Followup_completePopOverForm">
		<label for="other">Completed Type</label>
		<select class="form-control Followuptype" id="Followuptype" name="Followuptype" style="width: 244px;height: 31px;padding: 0px;" required="true">
			<option value="Call">Call</option>
			<option value="Email">Email</option>
		</select>
		<br>
		<textarea  class="completecomments"  placeholder="Enter Comments Here..." id='Complete_comments' name="comments"style="width:244px;" required="true"></textarea>
		<div class="input-group">
			<br>
			<br>
			<br>
			<button class="btn btn-primary BtnFollowupComplete" id="BtnFollowupComplete" type="button" style="height: 30px;" >Complete</button>
		</div>
	</form> 
</div>


<!-- ORDER DEPENDENT POPUP CONTENT STARTS -->                
<?php

?>


<div class="modal fade" id="modal-OrderAssign" tabindex="-1" role="dialog" aria-labelledby="OrderAssignLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form class="form-horizontal" id="frm_orderassign" action="#" method="post">
				<div class="modal-header">
					<h5 class="modal-title" id="OrderAssignLabel">Assign Workflows</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<div class="col-md-12 mb-20">
								<div class="col-md-12">
									<table class="table table-striped text-center table_modal_dependentworkflow">
										<thead>
											<tr>
												<th>SNo</th>
												<th>Workflow</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody class="modal_dependentworkflow">
											
										</tbody>
									</table>
								</div>
							</div>

						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-success pull-right btnWorkflowComplete" data-WorkflowModuleName = "<?php echo $mWorkflowModule->WorkflowModuleName; ?>" data-WorkflowModuleUID = "<?php echo $mWorkflowModule->WorkflowModuleUID; ?>" data-skipdependent="1"> Assign & <span class="completeworkflowname"></span> Complete</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>
			</form> 
		</div>
	</div>
</div>

<div class="modal fade" id="RaiseDocChase" tabindex="-1" role="dialog" aria-labelledby="RaiseDocChaseLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form class="form-horizontal" id="frmRaiseDocChase" action="#" method="post" novalidate>
				<div class="modal-header">
					<h5 class="modal-title" id="RaiseDocChaseLabel">Initiate Doc Chase</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<div class="col-md-12 mb-20">
								<textarea style="resize: none;" class="remarkstext form-control margin-bottom"  placeholder="Enter Remarks Here..." name="remarks" required=""></textarea>
							</div>

							<div class="col-md-12 mt-20">
								<label>Select Reason</label>
								<select class="modal-select" name="Reason" id="docchaseraiseReason" style="width: 100%;height: 31px;padding: 0px;" required="">
									<option value=""> Select Reason </option>

									<?php 
									foreach ($mReasons as $key => $value) { ?>
										<option value="<?php echo $value->ReasonUID; ?>" data-queueuid="<?php echo $value->QueueUID; ?>"><?php echo $value->ReasonName; ?></option>

										<?php

									}
									?>
								</select>

							</div>
		
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-pinterest Reopen_workflow_submit btnraisedocchase" name="submit" type="submit" id="btnraisedocchase" value="RaiseDocChase"> Initiate Doc Chase</button>

					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>
			</form> 
		</div>
	</div>
</div>

<div class="modal fade" id="ClearEsclation" tabindex="-1" role="dialog" aria-labelledby="ClearEsclationLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form class="form-horizontal" id="frmClearEsclation" action="#" method="post">
				<div class="modal-header">
					<h5 class="modal-title" id="ClearEsclationLabel">Clear Escalation</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<div class="col-md-12 mb-20">
								<textarea style="resize: none;" class="remarkstext form-control margin-bottom"  placeholder="Enter Remarks Here..." name="remarks" required=""></textarea>
							</div>

							<div class="col-md-12 mt-20">
								<label>Select Reason <span class="mandatory"></span></label>
								<select class="modal-select" name="Reason" id="clearescalationReason" style="width: 100%;height: 31px;padding: 0px;" required="">
									<option value=""> Select Reason </option>

									<?php 
									foreach ($mReasons as $key => $value) { ?>
										<option value="<?php echo $value->ReasonUID; ?>" data-queueuid="<?php echo $value->QueueUID; ?>"><?php echo $value->ReasonName; ?></option>

										<?php

									}
									?>
								</select>

							</div>
		
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-pinterest Reopen_workflow_submit btnClearEsclation" name="submit" type="submit" id="btnClearEsclation" value="btnClearEsclation">Clear Escalation</button>

					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>
			</form> 
		</div>
	</div>
</div>
<div class="modal fade" id="RaiseEsclation" tabindex="-1" role="dialog" aria-labelledby="RaiseEsclationLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form class="form-horizontal" id="frmRaiseEsclation" action="#" method="post">
				<div class="modal-header">
					<h5 class="modal-title" id="RaiseEsclationLabel">Initiate Escalation</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<div class="col-md-12 mb-20">
								<textarea style="resize: none;" class="remarkstext form-control margin-bottom"  placeholder="Enter Remarks Here..." name="remarks" required=""></textarea>
							</div>

							<div class="col-md-12 mt-20">
								<label>Select Reason <span class="mandatory"></span></label>
								<select class="modal-select" name="Reason" id="docchaseraiseReason" style="width: 100%;height: 31px;padding: 0px;" required="">
									<option value=""> Select Reason </option>

									<?php 
									foreach ($mReasons as $key => $value) { ?>
										<option value="<?php echo $value->ReasonUID; ?>" data-queueuid="<?php echo $value->QueueUID; ?>"><?php echo $value->ReasonName; ?></option>

										<?php

									}
									?>
								</select>

							</div>

							<div class="col-md-12 mb-20">
								<div class="form-group bmd-form-group">
									<label for="username" class="bmd-label-floating">Name <span class="mandatory"></span></label>
									<input type="text" class="form-control" id="RecipientEmail" name="RecipientEmail" />
								</div>
							</div>
		
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-pinterest Reopen_workflow_submit btnRaiseEsclation" name="submit" type="submit" id="btnRaiseEsclation" value="btnRaiseEsclation">Initiate Escalation</button>

					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>
			</form> 
		</div>
	</div>
</div>

<div class="modal fade" id="ClearDocChase" tabindex="-1" role="dialog" aria-labelledby="ClearDocChaseLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form class="form-horizontal" id="frmclearDocChase" action="#" method="post" novalidate>
				<div class="modal-header">
					<h5 class="modal-title" id="ClearDocChaseLabel">Clear Doc Chase</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
								<label>Select Reason</label>
								<select class="modal-select" name="Reason" id="docchaseclearReason" style="width: 100%;height: 31px;padding: 0px;" required="">
									<option value="">--Select Reason--</option>

									<?php 
									foreach ($mReasons as $key => $value) { ?>
										<option value="<?php echo $value->ReasonUID; ?>" data-queueuid="<?php echo $value->QueueUID; ?>"><?php echo $value->ReasonName; ?></option>

										<?php

									}
									?>
								</select>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-pinterest btncleardocchase" name="submit" type="submit" id="btncleardocchase" value="cleardocchase"> Clear Doc Chase</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>
			</form> 
		</div>
	</div>
</div>
<!-- ORDER REVERSE POPUP CONTENT ENDS --> 


<div class="modal fade" id="ParkingQueue" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form class="form-horizontal" id="frmRaiseParking" action="#" method="post" novalidate="">
				<div class="modal-header">
					<h5 class="modal-title" id="ParkingQueueLabel">Initiate Parking</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">

							<div class="col-md-12">
								<div class="form-group bmd-form-group is-filled">
									<label class="label-control">Remainder</label>
									<input type="text" class="form-control dateraideparking" value="" id="Remainder" name="Remainder" required="">
									<span class="material-input"></span>
									<span class="material-input"></span>
								</div>
							</div>

							<div class="row col-md-12">

								<div class="col-sm-6">
									<div class="">
										<label class="label-control">Reason</label>
										<select class="modal-select  form-control" name="Reason" id="parkingclearReason" required="">
											<option value=""> Select Reason </option>

											<?php 
											foreach ($mReasons as $key => $value) { ?>
												<option value="<?php echo $value->ReasonUID; ?>" data-queueuid="<?php echo $value->QueueUID; ?>"><?php echo $value->ReasonName; ?></option>

												<?php

											}
											?>
										</select>
									</div>
								</div>

								<div class="col-sm-6">
									<div class="form-group bmd-form-group">
										<label class="label-control">Remarks</label>
										<textarea style="resize: none;" class="remarkstext form-control margin-top-12" name="remarks" required=""></textarea>
									</div>
								</div>

							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-pinterest btnraiseparking" name="submit" type="submit" id="btnraiseparking" value="raiseparking"> Raise Parking</button>

					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>
			</form> 
		</div>
	</div>
</div>

<div class="modal fade" id="ClearParkingQueue" tabindex="-1" role="dialog" aria-labelledby="ClearParkingQueueLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form class="form-horizontal" id="frmclearParking" action="#" method="post" novalidate>
				<div class="modal-header">
					<h5 class="modal-title" id="ClearParkingQueueLabel">Clear Parking</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">

						<div class="row col-md-12">

							<div class="col-sm-6">
								<div class="">
									<label class="label-control">Reason</label>
									<select class="modal-select  form-control" name="Reason" id="parkingclearReason" style="width: 100%;height: 31px;padding: 0px;" required="">
										<option value=""> Select Reason </option>

										<?php 
										foreach ($mReasons as $key => $value) { ?>
											<option value="<?php echo $value->ReasonUID; ?>" data-queueuid="<?php echo $value->QueueUID; ?>"><?php echo $value->ReasonName; ?></option>

											<?php

										}
										?>
									</select>
								</div>
							</div>

							<div class="col-sm-6">
								<div class="form-group bmd-form-group">
									<label class="label-control">Remarks</label>
									<textarea style="resize: none;" class="remarkstext form-control margin-top-12" name="remarks" required=""></textarea>
								</div>
							</div>

						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-pinterest btnclearparking" name="submit" type="submit" id="btnclearparking" value="clearparking"> Clear Parking</button>

					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>
			</form> 
		</div>
	</div>
</div>

<div class="modal fade" id="RaiseWithdrawal" tabindex="-1" role="dialog" aria-labelledby="RaiseWithdrawalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form class="form-horizontal" id="frmraiseWithdrawal" action="#" method="post">
				<div class="modal-header">
					<h5 class="modal-title" id="RaiseWithdrawalLabel">Initiate Withdrawal</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">

						<div class="row col-md-12">

							<div class="col-sm-6">
								<div class="">
									<label class="label-control">Reason <span class="mandatory"></span></label>
									<select class="modal-select  form-control" name="Reason" id="WithdrawalraiseReason" style="width: 100%;height: 31px;padding: 0px;" required="">
										<option value=""> Select Reason </option>

										<?php 
										foreach ($mReasons as $key => $value) { ?>
											<option value="<?php echo $value->ReasonUID; ?>" data-queueuid="<?php echo $value->QueueUID; ?>"><?php echo $value->ReasonName; ?></option>

											<?php

										}
										?>
									</select>
								</div>
							</div>

							<div class="col-sm-6">
								<div class="form-group bmd-form-group">
									<label class="label-control">Remarks</label>
									<textarea style="resize: none;" class="remarkstext form-control margin-top-12" name="remarks" required=""></textarea>
								</div>
							</div>

						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-pinterest btnraiseWithdrawal" name="submit" type="submit" id="btnraiseWithdrawal" value="raiseWithdrawal"> Raise Withdrawal</button>

					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>
			</form> 
		</div>
	</div>
</div>

<div class="modal fade" id="ClearWithdrawal" tabindex="-1" role="dialog" aria-labelledby="ClearWithdrawalQueueLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form class="form-horizontal" id="frmclearWithdrawal" action="#" method="post">
				<div class="modal-header">
					<h5 class="modal-title" id="ClearWithdrawalQueueLabel">Clear Withdrawal</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">

						<div class="row col-md-12">

							<div class="col-sm-6">
								<div class="">
									<label class="label-control">Reason <span class="mandatory"></span></label>
									<select class="modal-select  form-control" name="Reason" id="WithdrawalclearReason" style="width: 100%;height: 31px;padding: 0px;" required="">
										<option value=""> Select Reason </option>

										<?php 
										foreach ($mReasons as $key => $value) { ?>
											<option value="<?php echo $value->ReasonUID; ?>" data-queueuid="<?php echo $value->QueueUID; ?>"><?php echo $value->ReasonName; ?></option>

											<?php

										}
										?>
									</select>
								</div>
							</div>

							<div class="col-sm-6">
								<div class="form-group bmd-form-group">
									<label class="label-control">Remarks</label>
									<textarea style="resize: none;" class="remarkstext form-control margin-top-12" name="remarks" required=""></textarea>
								</div>
							</div>

						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-pinterest btnclearWithdrawal" name="submit" type="submit" id="btnclearWithdrawal" value="clearWithdrawal"> Clear Withdrawal</button>

					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>
			</form> 
		</div>
	</div>
</div>


<div class="modal fade" id="modal-completemultipledocchase" tabindex="-1" role="dialog" aria-labelledby="completemultipledocchaseLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form class="form-horizontal" id="frm_completemultipledocchase" action="#" method="post" novalidate>
				<div class="modal-header">
					<h5 class="modal-title" id="completemultipledocchaseLabel">Doc Chase Initated Details</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<table class="table table-striped">
								<thead>
									<tr>
										<th>Workflow</th>
										<th>Findings</th>
										<th>Reason</th>
										<th>Remarks</th>
										<th>InitiatedBy</th>                    
										<th>Initiated On</th> 
										<th>Action</th> 
									</tr>
								</thead>
								<tbody id="completemultipledocchase_div">
									<?php  $multipledocchaseexists = $this->Common_Model->multipledocchaseexists($OrderUID); 
									foreach ($multipledocchaseexists as $key => $value) {

										$redirect = !empty(array_search($value->WorkflowModuleUID, $this->config->item('Order_WorkflowMenu'))) ? array_search($value->WorkflowModuleUID, $this->config->item('Order_WorkflowMenu')) : 'Ordersummary';
										
					
										$RaisedDateTime = ($value->RaisedDateTime != '0000-00-00 00:00:00' && $value->RaisedDateTime != '') ? date('m/d/Y',strtotime($value->RaisedDateTime)) : '-';
										echo '<tr>
										<td><a class="btn btn-scondary btn-xs" href="'.base_url($redirect.'/index/'.$value->OrderUID).'" class="ajaxload" target="_blank">'.$value->WorkflowModuleName.'</a></td>
										<td><a style="background-color:#4E8657;font-size:11px" class="btn  btn-xs ajaxload" href="'.base_url($redirect.'/index/'.$value->OrderUID).'" target="_blank">'.$value->QuestionCount.'</a></td>
										<td>'.$value->RaisedReasonName.'</td>
										<td>'.$value->Remarks.'</td>
										<td>'.$value->RaisedUserName.'</td>                    
										<td>'.$RaisedDateTime.'</td>                    
										<td>
										<div class="form-check">
										<label class="form-check-label">
										<input class="form-check-input WorkflowModuleUIDClearChase_box" type="checkbox" id="WorkflowModuleUIDClearChase_box'.$value->WorkflowModuleUID.'" data-WorkflowModuleUID="'.$value->WorkflowModuleUID.'" checked> 
										<span class="form-check-sign">
										<span class="check"></span>
										</span>
										</label>
										</div>
										</td>                    
										</tr>';
									}
									?>
								</tbody>
							</table>
						</div>
					</div>
					<div class="row col-md-12">

						<label class="label-control">Reason</label>
						<select class="modal-select  form-control" name="Reason" id="multipledocchaseclearReason" style="width: 100%;height: 31px;padding: 0px;" required="">
							<option value=""> Select Reason </option>

							<?php 
							foreach ($mReasons as $key => $value) { ?>
								<option value="<?php echo $value->ReasonUID; ?>" data-queueuid="<?php echo $value->QueueUID; ?>"><?php echo $value->ReasonName; ?></option>

								<?php

							}
							?>
						</select>

					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-twitter btnmultiplecleardocchase" name="submit" type="submit" id="btnmultiplecleardocchase"> Clear</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>
			</form> 
		</div>
	</div>
</div>


<!-- Exception Queue Railse Popup Start -->
<div class="modal fade" id="RaiseExceptionQueue" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form class="form-horizontal" id="frmRaiseExcepitonQueue" action="#" method="post" novalidate="">
				<input type="hidden" name="OrderUID" value="<?php echo $OrderUID; ?>">
				<input type="hidden" name="QueueUID" id="raise-queueuid" value="">
				<div class="modal-header">
					<h5 class="modal-title" id="RaiseExceptionQueueLabel">Initiate <span class="raiseexceptionqueue"></span></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">


							<div class="row col-md-12">

								<div class="col-sm-6">
									<div class="form-group bmd-form-group ExceptionReasonsCont">
										<label class="label-control">Reason</label>
										<select class="modal-select  form-control" name="Reason[]" id="ExceptionRaiseReason" multiple="multiple" required="">
											<!-- <option value=""> Select Reason </option> -->

											<?php 
											foreach ($mQueueReasons as $key => $value) { ?>
												<option value="<?php echo $value->ReasonUID; ?>" data-queueuid="<?php echo $value->QueueUID; ?>"><?php echo $value->ReasonName; ?></option>

												<?php

											}
											?>
										</select>
									</div>
								</div>

								<div class="col-sm-6">
									<div class="form-group bmd-form-group">
										<label class="label-control">Remarks</label>
										<textarea style="resize: none;" class="remarkstext form-control margin-top-12" name="remarks" id="remarks" required=""></textarea>
									</div>
								</div>

							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-pinterest btnFrmRaiseExceptionQueue" name="submit" type="submit" id="btnFrmRaiseExceptionQueue" value="raiseparking"> Initiate <span class="raiseexceptionqueue"></span></button>

					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>
			</form> 
		</div>
	</div>
</div>
<!-- Exception Queue Railse Popup Ends -->

<!-- Exception Queue Clear Popup Start -->
<div class="modal fade" id="ClearExceptionQueue" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form class="form-horizontal" id="frmClearExceptionQueue" action="#" method="post" novalidate="">
				<input type="hidden" name="OrderUID" value="<?php echo $OrderUID; ?>">
				<input type="hidden" name="QueueUID" id="clear-queueuid" value="">
				<div class="modal-header">
					<h5 class="modal-title" id="RaiseExceptionQueueLabel">Complete <span class="clearexceptionqueue"></span></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">


							<div class="row col-md-12">

								<div class="col-sm-6">
									<div class="ExceptionReasonsCont">
										<label class="label-control">Reason</label>
										<select class="modal-select  form-control" name="Reason[]" id="ExcecptionQueueClearReason" multiple="multiple" required="">
											<!-- <option value=""> Select Reason </option> -->

											<?php 
											foreach ($mQueueReasons as $key => $value) { ?>
												<option value="<?php echo $value->ReasonUID; ?>" data-queueuid="<?php echo $value->QueueUID; ?>"><?php echo $value->ReasonName; ?></option>

												<?php

											}
											?>
										</select>
									</div>
								</div>

								<div class="col-sm-6">
									<div class="form-group bmd-form-group">
										<label class="label-control">Remarks</label>
										<textarea style="resize: none;" class="remarkstext form-control margin-top-12" name="remarks" id="ExcecptionQueueClearRemarks" required=""></textarea>
									</div>
								</div>

							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-pinterest btnFormClearException btnCompleteExceptionAndWorkflow" name="submit" type="submit" value="1"> Complete <span class="clearexceptionqueue"></span> & Complete  <span class="clearexceptionworkflow">Title</span> </button>
					<button class="btn btn-pinterest btnFormClearException" name="submit" type="submit" value="0"> Complete <span class="clearexceptionqueue"></span></button>
					<button class="btn btn-pinterest btnFormExceptionReasonUpdate" name="submit" type="button" value="0"> Update </button>

					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>
			</form> 
		</div>
	</div>
</div>
<!-- Exception Queue Clear Popup Start -->

<!-- HOI Rework Quueue Raise Modal -->
<div class="modal fade" id="HOIRework-Modal" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form class="form-horizontal" id="frmEnableHOIRework" action="#" method="post" novalidate="">
				<input type="hidden" name="OrderUID" value="<?php echo $OrderUID; ?>">
				<input type="hidden" name="WorkflowModuleUID" value="<?php echo $WorkflowModuleUID; ?>">
				<div class="modal-header">
					<h5 class="modal-title">Enable Rework </h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">

							<div class="row col-md-12">

								<div class="col-sm-12">
									<div class="form-group bmd-form-group">
										<label class="label-control">Remarks</label>
										<textarea style="resize: none;" class="remarkstext form-control margin-top-12" name="Remarks" required=""></textarea>
									</div>
								</div>

							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-pinterest btnFrmEnableHOIReworkQueue" name="submit" type="submit" id="btnFrmEnableHOIReworkQueue" value=""> Enable Rework</button>

					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>
			</form> 
		</div>
	</div>
</div>
<!-- HOI Rework Quueue Raise Modal End -->

<!-- HOI Rework Quueue Complete Modal -->
<div class="modal fade" id="HOIReworkComplete-Modal" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form class="form-horizontal" id="frmCompleteHOIRework" action="#" method="post" novalidate="">
				<input type="hidden" name="OrderUID" value="<?php echo $OrderUID; ?>">
				<input type="hidden" name="WorkflowModuleUID" value="<?php echo $WorkflowModuleUID; ?>">
				<div class="modal-header">
					<h5 class="modal-title">Complete Rework </h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">

							<div class="row col-md-12">

								<div class="col-sm-12">
									<div class="form-group bmd-form-group">
										<label class="label-control">Remarks</label>
										<textarea style="resize: none;" class="remarkstext form-control margin-top-12" name="Remarks" required=""></textarea>
									</div>
								</div>

							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-pinterest btnFrmCompleteHOIReworkQueue" name="submit" type="submit" id="btnFrmCompleteHOIReworkQueue" value=""> Complete Rework</button>

					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>
			</form> 
		</div>
	</div>
</div>
<!-- HOI Rework Quueue Complete Modal End -->

<!--ENABLE  WORKUP POPUP CONTENT -->                
<?php //$this->load->view('orderinfoheader/workupmodal'); ?>






<script type="text/javascript" src="<?php echo base_url('assets/js/checklist.js');?>"></script>

<script type="text/javascript" src="<?php echo base_url('assets/js/multiple-emails.js');?>"></script>

<script type="text/javascript" src="<?php echo base_url('assets/js/export/xlsx.full.min.js');?>"></script>

<script type="text/javascript" src="<?php echo base_url('assets/js/export/jspdf.min.js');?>"></script>

<script type="text/javascript" src="<?php echo base_url('assets/js/export/jspdf.plugin.autotable.js');?>"></script>

<script>
	$(document).ready(function(){
          $('.dateraideparking').datetimepicker({
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
               },
   //             keepOpen: true,
			// debug:true,
            });          	

			$(document).off("change", "[title|='Findings']").on("change", "[title|='Findings']", function (e) {
				if ($(this).val() == 'Problem Identified') {
					// table border color
					$(this).closest("tr.questionlist").addClass('checklisttitlefindings');
				} else {
					$(this).closest("tr.questionlist").removeClass('checklisttitlefindings');
				}
			});
			// onchange event on onload
			$("[title|='Findings']").change();
        });

	$(function () {
		window.exportfilenamingstandard=function(){
			// <<LoanNumber>>_mm/dd/yy
			var fullDate = new Date();

			//convert month to 2 digits
			var twoDigitMonth = ((fullDate.getMonth().length+1) === 1)? (fullDate.getMonth()+1) : '0' + (fullDate.getMonth()+1);
			 
			var currentDate = twoDigitMonth + "/" + fullDate.getDate() + "/" + fullDate.getFullYear();
			return $('#LoanNumberNamingConv').val()+'_'+currentDate;
		};
		
		// checklist pdf export start
		$(document).off("click", ".btn-checklist-export-pdf").on("click", ".btn-checklist-export-pdf", function (event) {
			event.preventDefault();
			// get checklist table id
			var checklisttableid = $(this).data("checklisttableid");
			
			//export checklist table data to pdf
			checklisttabledatapdf('#'+checklisttableid);
		});

		// function get table data
		window.checklisttabledatapdf=function(table) {
			
			// initialize jspdf
			const doc = new jsPDF('p', 'pt', 'letter');

			//check_list_table
			var table = $(table);

			var count = 1;
			table.each(function(){
				// get checklist table id
				var checklisttableid = this.id; //Here this refers current table
				
				// get workflowpdftitle
				var workflowpdftitle = $(this).attr("data-workflowpdftitle");

				//Get table heading as array
				var tableheading = [];
				var $headers = $("#"+checklisttableid+" th");
				$headers.each(function(key, val) {
					tableheading[key] = $(this).text();
				});

				//get table data
				var records = [];
				var $headers = $("#"+checklisttableid+" th");
				var $rows = $("#"+checklisttableid+" tbody tr:visible").each(function(index) {
				  $cells = $(this).find("td:visible");
				  records[index] = [];
				  $cells.each(function(cellIndex) {	
				  	var tdval;
				  	if ($(this).find(".checklists").length) {
				  		var tagName = $(this).find(".checklists").prop("tagName").toLowerCase();
				  		if (tagName == 'div') {
				  			var findings = $.trim($(this).find(".checklists :selected").text());
						    if (findings != 'empty') {
						    	tdval = findings;
						    } else {
						    	tdval = '';
						    }
				  		} else if(tagName == 'textarea') {
				  			tdval = $.trim($(this).find(".checklists").text());
				  		} else if(tagName == 'input') {
				  			var inputattrname = $(this).find(".checklists").attr("type");
				  			if (inputattrname == "checkbox") {
				  				if($(this).find(".checklists").prop("checked") == true){
					                IsChecklist = "Yes";
					            }
					            else if($(this).find(".checklists").prop("checked") == false){
					                IsChecklist = "No";
					            }
							    tdval = IsChecklist;	
				  			} else if(inputattrname == "text") {
				  				tdval = $.trim($(this).find(".checklists").val());
				  			} else {
				  				tdval = $.trim($(this).find(".checklists").text());
				  			}			  			
				  		} else {
				  			tdval = $.trim($(this).find(".checklists").text());
				  		}
				  	} else {
				  		tdval = $.trim($(this).text());
				  	}
				    records[index][cellIndex] = tdval;
				  });    
				});
				var output_data = [];
				output_data = records;

				if (count != 1) {
					// Add new page		
					doc.addPage();
				}
				count++;

				doc.setFontType("bold");	
				doc.setFontSize(12);		
				doc.text(40, 30, workflowpdftitle);
				 
				// Or use javascript directly:
				doc.autoTable({
				  head: [tableheading],
				  body: output_data,
				});
				
			});
			 
			doc.save(exportfilenamingstandard()+'.pdf');
		}
		// pdf export end

		// excel export start
		// function get table data excel
		window.checklisttabledataexcel=function(table) {
			 
			 /* Import if no xlsx component is imported */
			if(typeof XLSX == 'undefined') XLSX = require('xlsx');
				 
			 /* Create a new empty workbook, then add the worksheet */
			var wb = XLSX.utils.book_new();

			//check_list_table
			var table = $(table);
			table.each(function(){
				// get checklist table id
				var checklisttableid = this.id; //Here this refers current table
				// get workflowtitle
				var sheet_name = $(this).attr("data-workflowtitle");

				//Get table heading as array
				var tableheading = [];
				$("#"+checklisttableid+" th:visible").each(function(index) {
			      tableheading[index] = $(this).text();
				});
			
				//get checklist table data
				var records = [];
				var $headers = $("#"+checklisttableid+" th");
				var $rows = $("#"+checklisttableid+" tbody tr:visible").each(function(index) {
				  $cells = $(this).find("td:visible");
				  records[index] = {};
				  $cells.each(function(cellIndex) {	
				  	var tdval;
				  	if ($(this).find(".checklists").length) {
				  		var tagName = $(this).find(".checklists").prop("tagName").toLowerCase();
				  		if (tagName == 'div') {
				  			var findings = $.trim($(this).find(".checklists :selected").text());
						    if (findings != 'empty') {
						    	tdval = findings;
						    } else {
						    	tdval = '';
						    }
				  		} else if(tagName == 'textarea') {
				  			tdval = $.trim($(this).find(".checklists").text());
				  		} else if(tagName == 'input') {
				  			var inputattrname = $(this).find(".checklists").attr("type");
				  			if (inputattrname == "checkbox") {
				  				if($(this).find(".checklists").prop("checked") == true){
					                IsChecklist = "Yes";
					            }
					            else if($(this).find(".checklists").prop("checked") == false){
					                IsChecklist = "No";
					            }
							    tdval = IsChecklist;	
				  			} else if(inputattrname == "text") {
				  				tdval = $.trim($(this).find(".checklists").val());
				  			} else {
				  				tdval = $.trim($(this).find(".checklists").text());
				  			}			  			
				  		} else {
				  			tdval = $.trim($(this).find(".checklists").text());
				  		}
				  	} else {
				  		tdval = $.trim($(this).text());
				  	}
				    records[index][$($headers[cellIndex]).text()] = tdval;
				  });    
				});
			 
				 /* Create a worksheet */
				var ws = XLSX.utils.json_to_sheet(records, {header:tableheading});
				 
				 /* add the worksheet */
				XLSX.utils.book_append_sheet(wb, ws, sheet_name);
			});
			 
			 /* Generate xlsx files */
			XLSX.writeFile(wb, exportfilenamingstandard()+".xlsx");
		}

		var $btnDLtoExcel = $('.btn-checklist-export-xlsx');
		$btnDLtoExcel.on('click', function (event) {
			event.preventDefault();
			// get checklist table id
			var checklisttableid = $(this).data("checklisttableid");
			
			//export checklist table data to excel
			checklisttabledataexcel('#'+checklisttableid);

		});
		// excel export end

		$('select.modal-select').select2({
			theme: "bootstrap",
		});
		$("[data-toggle=OnHold-popover]").popover({
			title : 'OnHold <a href="#" class="close" data-dismiss="alert">&times;</a>',
			html: true,
			placement: 'top', 
			content: function() {
				$('.popover').not(this).popover('hide');
				$('.multiple_emails-container').remove();
				return $('#OnHold-content').html();
			}
		});
		$("[data-toggle=Release-popover]").popover({
			title : 'Release <a href="#" class="close" data-dismiss="alert">&times;</a>',
			html: true, 
			placement: 'top',
			content: function() {
				$('.popover').not(this).popover('hide');
				$('.multiple_emails-container').remove();
				return $('#OnHoldclear_popover_content').html();
			}
		});

		$("[data-toggle=Followup-popover]").popover({
			title : 'Followup <a href="#" class="close" data-dismiss="alert">&times;</a>',
			html: true, 
			placement: 'top',
			content: function() {
				$('.popover').not(this).popover('hide');
				return $('#Followup-content').html();
			}
		});

		$("[data-toggle=Followup_complete-popover]").popover({
			title : 'FollowupComplete <a href="#" class="close" data-dismiss="alert">&times;</a>',
			html: true,
			placement: 'top', 
			content: function() {
				$('.popover').not(this).popover('hide');
				return $('#Followup_complete-content').html();
			}
		});

		$("[data-toggle=exception-popover]").popover({
			title : 'Exception <a href="#" class="close" data-dismiss="alert">&times;</a>',
			html: true, 
			placement: 'top',
			content: function() {
				$('.popover').not(this).popover('hide');
				return $('#exceptiononholdpopover-content').html();
			}
		}); 

		$("[data-toggle=docchase-popover]").popover({
			title : 'Doc Chase <a href="#" class="close" data-dismiss="alert">&times;</a>',
			html: true, 
			placement: 'top',
			content: function() {
				$('.popover').not(this).popover('hide');
				return $('#docchaseonholdpopover-content').html();
			}
		}); 

		$("[data-toggle=bookmark-popover]").popover({
			title : 'Specification Instruction <a href="#" class="close" data-dismiss="alert">&times;</a>',
			html: true,
			placement: 'top', 
			content: function() {
				$('.popover').not(this).popover('hide');
				return $('#bookmarkInstru-content').html();
			}
		}); 
		
		$(document).off("click",".popover .close").on("click", ".popover .close" , function(){
			$(this).parents(".popover").popover('hide');
			$('.multiple_emails-container').remove();
		});

		$("[data-toggle=clearexceptionpopover]").popover({
			html: true, 
			placement: 'top',
			content: function() {
				$('.popover').not(this).popover('hide');
				return $('#clearexceptionpopover-content').html();
			},
			container: 'body'
		}); 

		$("[data-toggle=cleardocchasepopover]").popover({
			html: true, 
			placement: 'top',
			content: function() {
				$('.popover').not(this).popover('hide');
				return $('#cleardocchasepopover-content').html();
			},
			container: 'body'
		}); 

		$('[data-toggle=clearexceptionpopover]').on('shown.bs.popover', function (e) {

		});



		$(document).keyup(function (event) {
			if (event.which === 27) {
				$("[data-toggle=clearexceptionpopover]").popover('hide');
				$("[data-toggle=exception-popover]").popover('hide');
				$("[data-toggle=cleardocchasepopover]").popover('hide');
				$("[data-toggle=docchase-popover]").popover('hide');
			}
		});
		$(document).off('shown.bs.popover').on('shown.bs.popover', function (e) {
			var popover = '.popover.in';
			var id=$('.form-inline').prop('id');
			var originalHeight = $(popover).height();
			$('.UserEmails').multiple_emails({position: "bottom"});
			$('.ClearUserEmails').multiple_emails({position: "bottom"});
			var newHeight = $(popover).height();
			var top = parseFloat($(popover).css('top'));
			var changeInHeight = newHeight - originalHeight;

    	//$(popover).css({ top: top - (changeInHeight ) });
    });  

	});
	/*Specification Instruction ajax starts here*/
	$(document).off('click', '#btnbmpopups').on('click', '#btnbmpopups', function (e) {
		e.preventDefault();
		var OrderUID = $('#OrderUID').val();
		var SpecifyInstru = $('.popover').find('#SpecifyInstru').val();
		button = $(this);
		button_val = $(this).val();
		button_text = $(this).html();
		$.ajax({
			url: "<?php echo base_url('CommonController/SaveSpecificationInstru'); ?>",
			type: "POST",
			dataType: "JSON",
			data: {"SpecifyInstru":SpecifyInstru,"OrderUID":OrderUID},
			beforeSend: function(){
				$('.spinnerclass').addClass("be-loading-active");
				button.attr("disabled", true);
				button.html('Loading ...'); 
			},                                 
			success: function (data) 
			{   
				if (data.validation_error == 0) {  
					button.html('Submit'); 
					button.removeAttr('disabled');
					disposepopover();
					jQuery('.close').trigger('click');
					$.notify(
					{
						icon:"icon-bell-check",
						message: data.message,
					},
					{
						type: data.type,
						delay:100,
						z_index: 999999,
					});

				} else {
					swal({
						title: "<i class='icon-close2 icondanger'></i>",
						html: "<p>" + data.message + "</p>",
						confirmButtonClass: "btn btn-success",
						allowOutsideClick: false,
						width: '300px',
						buttonsStyling: false
					}).catch(swal.noop);
				}
				button.html(button_text);
				button.attr("disabled", false);
			},
			error: function (jqXHR) {
				swal({
					title: "<i class='icon-close2 icondanger'></i>",
					html: "<p>Failed to Complete</p>",
					confirmButtonClass: "btn btn-success",
					allowOutsideClick: false,
					width: '300px',
					buttonsStyling: false
				}).catch(swal.noop);
				button.html(button_text);
				button.attr("disabled", false);
			}
		});
		e.stopImmediatePropagation();
	}); 
	/*Specification Instruction ajax ends here*/
	function disposepopover(e) {
		$("[data-toggle=bookmark-popover]").popover('dispose');
		$("[data-toggle=clearexceptionpopover]").popover('dispose');
		$("[data-toggle=OnHold-popover]").popover('dispose');
		$("[data-toggle=FollowupPopOverForm]").popover('dispose');
		$("[data-toggle=Release-popover]").popover('dispose');
		$("[data-toggle=exception-popover]").popover('dispose');
	};


</script>
