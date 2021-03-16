<?php

$is_orderreverse_enabled = $is_cancel_enabled = $OrderIsOnHold = $ReleaseOnHold = $is_raise_docchase_enabled = $is_raise_esclation_enabled = $is_clear_docchase_enabled = $is_clear_esclation_enabled = $is_parkingqueue_enabled = $is_clearparkingqueue_enabled = $is_raise_withdrawal_enabled = $is_clear_withdrawal_enable = $is_clearparkingqueue_enabled = $is_reverse_enabled = 0;
$is_enableworkup = 0;
$OrderUID = $this->uri->segment(3);
$OrderDetails = $this->Common_Model->getOrderDetails($OrderUID); 
$Page = $this->uri->segment(1);

//workflow pages
$Page_WorkflowModuleUID = "";
$mResource = $this->Common_Model->getWorkflowByPage($Page);
if (!empty($mResource->WorkflowModuleUID)) {
	$Page_WorkflowModuleUID = $mResource->WorkflowModuleUID;
}
// Check is workflow is available
// $IsWorkflowButtonEnabled = $this->Common_Model->Is_orderworkflow_buttonavailable($OrderUID, $Page_WorkflowModuleUID);

$is_clear_withdrawal_enabled = $this->Common_Model->check_clearwithdrawal_enabled($OrderUID);
//$is_raise_withdrawal_enabled = $this->Common_Model->check_raisewithdrawal_enabled($OrderUID);

$IsWorkflowEnabled = $this->Common_Model->Is_given_workflow_available($OrderUID, $Page_WorkflowModuleUID);

// Order Reverse Enable
if (!in_array($OrderDetails->StatusUID, $this->config->item('CancelledOrders_Milestones')) && !in_array($OrderDetails->MilestoneUID, $this->config->item('Workflows_EliminatedMilestones')) && !empty($this->Common_Model->OrderReverseWorkflowStatus($OrderDetails->OrderUID))  && (isset($this->UserPermissions->IsReverseEnabled)) && $this->UserPermissions->IsReverseEnabled == 1) {
	$is_orderreverse_enabled = 1;
}



/* OrderCheckIsHoldQuery Begin*/
$this->db->select('IsOnHold,StatusUID');
$this->db->from('tOrders');
$this->db->where('tOrders.OrderUID',$OrderUID);
$IsOnHoldCheck = $this->db->get()->row();
if($IsOnHoldCheck->IsOnHold == 1){
	$ReleaseOnHold = 1; $StatusID = $IsOnHoldCheck->StatusUID;
} else {
	$OrderIsOnHold = 1; $StatusID = $IsOnHoldCheck->StatusUID;
}


/* OrderCheckIsHoldQuery End*/

if(in_array($this->RoleType, $this->config->item('Internal Roles'))){ ?>



<?php  if($is_cancel_enabled && $OrderIsOnHold) { ?>
	
	<button type="button" style="background:#182842;"  class="btn btn-danger pull-left discard" id="discard">Cancel Order</button>
<?php
}


if ($Page == "Ordersummary") { 
	

	$OrderSummaryWorkflows = $this->Common_Model->getPageLessWorkflows($OrderUID);

	foreach ($OrderSummaryWorkflows as $key => $WorkflowModuleUID) {

		$IsWorkflowAvailable = $this->Common_Model->Is_orderworkflow_buttonavailable($OrderUID, $WorkflowModuleUID); 
		$mWorkflowModule = $this->Common_Model->get_row('mWorkFlowModules', ['WorkflowModuleUID'=>$WorkflowModuleUID]);
		if ($IsWorkflowAvailable && $OrderIsOnHold && !$is_clear_withdrawal_enabled) { ?>
			<button type="button" class="btn btn-success pull-right btnWorkflowComplete tocompleteworkflow" data-WorkflowModuleName = "<?php echo $mWorkflowModule->WorkflowModuleName; ?>" data-WorkflowModuleUID = "<?php echo $mWorkflowModule->WorkflowModuleUID; ?>"> <?php echo $mWorkflowModule->WorkflowModuleName; ?> Complete</button>
		<?php }
	}

	//check escalation and hide clear docchase button
	if($is_clear_docchase_enabled) {
		if($this->Common_Model->check_clearesclation_enabled($OrderUID, $this->config->item('Workflows')['DocChase'])) {
			$is_clear_docchase_enabled = 0;
		}
	}

}else if ($Page == "DocChase") { 

	$is_clear_docchase_enabled = $this->Common_Model->check_atleastonecleardocchase_enabled($OrderUID);
	if($is_clear_docchase_enabled) {
		$is_parkingqueue_enabled = 1;
	}

	//check escalation and hide clear docchase button
	$is_clear_esclation_enabled = $this->Common_Model->check_docchaseesclation_enabled($OrderUID);

	if($is_clear_docchase_enabled) {
		$is_raise_esclation_enabled = 1;
		if($is_clear_esclation_enabled) {
			$is_raise_esclation_enabled = $is_clear_docchase_enabled = 0;
		}
	}

	if($is_clear_docchase_enabled && !empty($this->Common_Model->check_docchaseparking_enabled($OrderUID))) {
		$is_parkingqueue_enabled = $is_raise_docchase_enabled = $is_clear_docchase_enabled = $is_raise_esclation_enabled = $is_clear_esclation_enabled  = 0;
		$is_clearparkingqueue_enabled = 1;
	}

}else if ($Page == 'History') { 

	$is_clear_docchase_enabled = $this->Common_Model->check_atleastonecleardocchase_enabled($OrderUID);

} else {
	//workflow pages
	$WorkflowModuleUID = "";
	$mResource = $this->Common_Model->getWorkflowByPage($Page);
	if (!empty($mResource)) {
		$WorkflowModuleUID = $mResource->WorkflowModuleUID;
	}

	if (!empty($WorkflowModuleUID)) {
		$mCustomerWorkflows = $this->Common_Model->get_row('mCustomerWorkflowModules', ['CustomerUID'=>$OrderDetails->CustomerUID, 'WorkflowModuleUID'=>$WorkflowModuleUID]);

		$tOrder_ParkingQueue = $this->Common_Model->is_workflow_in_parkingqueue($OrderUID, $WorkflowModuleUID);
		$is_parkingqueue_enabled = !empty($this->Common_Model->is_parking_enabledforworkflow($WorkflowModuleUID)) ? 1 : 0;

		$is_clear_esclation_enabled = $this->Common_Model->check_clearesclation_enabled($OrderUID, $WorkflowModuleUID);

		/*--DOC CHASE PAGE --*/
		if ($this->config->item('Workflows')['DocChase'] == $WorkflowModuleUID && $this->Common_Model->display_docchasemenu($OrderUID) ) {
			$is_raise_esclation_enabled = 1;
			$is_raise_docchase_enabled = 0;

			if(!empty($tOrder_ParkingQueue) && $is_parkingqueue_enabled) {
				$is_parkingqueue_enabled = $is_raise_docchase_enabled = $is_clear_docchase_enabled = 0;
				$is_clearparkingqueue_enabled = 1;
			}

			if($is_clear_esclation_enabled){
				$is_parkingqueue_enabled = $is_raise_docchase_enabled = $is_clear_docchase_enabled = $is_clearparkingqueue_enabled =  $is_raise_esclation_enabled = 0;
			}

			//check escalation and hide clear docchase button
			if($is_clear_docchase_enabled) {
				$is_raise_esclation_enabled = 1;
				if($this->Common_Model->check_clearesclation_enabled($OrderUID, $this->config->item('Workflows')['DocChase'])) {
					$is_clear_docchase_enabled = 0;
				}
			}

		} else {

			// OTHER WORKFLOW PAGES
			$workflowcompleted = $this->Common_Model->IsWorkflowCompleted($OrderUID, $WorkflowModuleUID);
			if ($workflowcompleted || empty( $mCustomerWorkflows->IsDocChaseRequire ) ) {
				$is_raise_docchase_enabled = 0;
				$is_clear_docchase_enabled = 0;
			} else {
				$is_raise_docchase_enabled = $this->Common_Model->check_raisedocchase_enabled($OrderUID, $WorkflowModuleUID);
				$is_clear_docchase_enabled = $this->Common_Model->check_cleardocchase_enabled($OrderUID, $WorkflowModuleUID);

			}


			if($is_clear_docchase_enabled) {
				$is_raise_esclation_enabled = 1;
			}

			if(!empty($tOrder_ParkingQueue) && $is_parkingqueue_enabled) {
				$is_parkingqueue_enabled = 0;
				$is_clearparkingqueue_enabled = 1;
			}

			if($is_clear_esclation_enabled){
				$is_parkingqueue_enabled = $is_raise_docchase_enabled = $is_clear_docchase_enabled = $is_clearparkingqueue_enabled =  $is_raise_esclation_enabled = 0;
			}


			if ( empty($mCustomerWorkflows->IsEscalationRequire) ) {
				$is_clear_esclation_enabled = 0;
				$is_raise_esclation_enabled = 0;
			}

		}

		// check is Reverse enabled
		if(($WorkflowModuleUID == $this->config->item('Workflows')['PreScreen']) || ($WorkflowModuleUID == $this->config->item('Workflows')['TitleTeam']) || ($WorkflowModuleUID == $this->config->item('Workflows')['FHAVACaseTeam']) || ($WorkflowModuleUID == $this->config->item('Workflows')['HOI']))
		{	
			$is_reverse_enabled_Data = $this->db->select('*')->from('tOrderWorkflows')->where(array('OrderUID'=>$OrderUID,'WorkflowModuleUID'=>$WorkflowModuleUID,'IsReversed'=>1))->get()->row();
			if(!empty($is_reverse_enabled_Data))
			{
				$is_reverse_enabled = 1;
			}
		}



		if (!$is_clear_docchase_enabled && !$tOrder_ParkingQueue && !$is_clear_esclation_enabled ) {	
			$IsWorkflowAvailable = $this->Common_Model->Is_orderworkflow_buttonavailable($OrderUID, $WorkflowModuleUID); 
			if ($IsWorkflowAvailable && $OrderIsOnHold && !$is_clear_withdrawal_enabled) {
				
				$mWorkflowModule = $this->Common_Model->get_row('mWorkFlowModules', ['WorkflowModuleUID'=>$WorkflowModuleUID]); ?>
				<button type="button" class="btn btn-success pull-right btnWorkflowComplete tocompleteworkflow" data-WorkflowModuleName = "<?php echo $mWorkflowModule->WorkflowModuleName; ?>" data-WorkflowModuleUID = "<?php echo $mWorkflowModule->WorkflowModuleUID; ?>" data-skipdependent=""> <?php echo $mWorkflowModule->WorkflowModuleName; ?> Complete</button>
				<?php 
			} else if ($IsWorkflowEnabled && $WorkflowModuleUID == $this->config->item('Workflows')['Workup'] && !$this->Common_Model->IsWorkflowCompleted($OrderUID,$this->config->item('Workflows')['Workup']) && $this->Common_Model->isworkflow_forceenabled($OrderUID,$this->config->item('Workflows')['Workup'])) {
				$mWorkflowModule = $this->Common_Model->get_row('mWorkFlowModules', ['WorkflowModuleUID'=>$WorkflowModuleUID]); ?>

				<button type="button" class="btn btn-success pull-right btnWorkflowComplete tocompleteworkflow" data-WorkflowModuleName = "<?php echo $mWorkflowModule->WorkflowModuleName; ?>" data-WorkflowModuleUID = "<?php echo $mWorkflowModule->WorkflowModuleUID; ?>" data-skipdependent=""> <?php echo $mWorkflowModule->WorkflowModuleName; ?> Complete</button>

			<?php }
			else if($IsWorkflowEnabled && $WorkflowModuleUID == $this->config->item('Workflows')['GateKeeping'] && !$this->Common_Model->IsWorkflowCompleted($OrderUID,$this->config->item('Workflows')['GateKeeping']) && $this->Common_Model->isworkflow_forceenabled($OrderUID,$this->config->item('Workflows')['GateKeeping']))
			{ $mWorkflowModule = $this->Common_Model->get_row('mWorkFlowModules', ['WorkflowModuleUID'=>$WorkflowModuleUID]); ?>
				
				<button type="button" class="btn btn-success pull-right btnWorkflowComplete tocompleteworkflow" data-WorkflowModuleName = "<?php echo $mWorkflowModule->WorkflowModuleName; ?>" data-WorkflowModuleUID = "<?php echo $mWorkflowModule->WorkflowModuleUID; ?>" data-skipdependent=""> <?php echo $mWorkflowModule->WorkflowModuleName; ?> Complete</button>
			<?php }
		}

	}
}


?>

<?php 
$workflow = $this->uri->segment(1);
if($workflow == 'FHA_VA_CaseTeam')
{
	$workflow = 'FHAVACaseTeam';
}
$workflowUID = $this->config->item('Workflows')[$workflow];
if($workflowUID)
{
	$OrderAssignDetails = $this->Common_Model->getOrderAssignDetails($OrderUID,$workflowUID);
}

if(!empty($OrderAssignDetails)) { ?>
	<span class="workflow-complete-detail pull-right"><b> <?php echo $OrderAssignDetails->UserName; ?></b> has been completed <b><?php echo $OrderAssignDetails->WorkflowModuleName; ?></b> workflow on <b><?php echo  date('m/d/Y',strtotime($OrderAssignDetails->CompleteDateTime)); ?></b></span>
<?php } ?>



<!-- Order Exception Queue  -->
<?php 
if ($IsWorkflowEnabled) {
	$this->load->view('orderinfoheader/queuebuttons', ["OrderDetails"=>$OrderDetails, 'WorkflowModuleUID'=>$WorkflowModuleUID, 'OrderAssignDetails'=>$OrderAssignDetails,'is_reverse_enabled'=> $is_reverse_enabled,'tOrder_ParkingQueue'=> $tOrder_ParkingQueue]);
}
?>
<!-- Order Exception Queue  -->



<?php 
if ($is_orderreverse_enabled && $OrderIsOnHold) {
?>
<button title="Order Reverse" type="button" data-toggle="modal" data-target="#modal-OrderReverse" id="btnOrderReverse" class="btn pull-left btn-success "><i class="icon-tab pr-1"></i> &nbsp;Reverse&nbsp;</button>  
<?php 
} 
?>

<!-- HOI Rework Enable Buttone -->
<?php 
/* $is_rework_enabled = $this->Common_Model->get_row('tOrderReWork', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=> $WorkflowModuleUID,'IsReWorkEnabled'=>STATUS_ONE]);
if ($WorkflowModuleUID == $this->config->item('Workflows')['HOI'] && empty($is_rework_enabled)) {
?>
	<button title="Enable Rework" type="button" data-toggle="modal" data-target="#HOIRework-Modal" id="btn-hoirework" class="btn pull-left btn-twitter">Enable Rework&nbsp;</button>  
<?php 
} elseif($WorkflowModuleUID == $this->config->item('Workflows')['HOI'] && !empty($is_rework_enabled)) { ?>
	<button title="Complete Rework" type="button" data-toggle="modal" data-target="#HOIReworkComplete-Modal" id="btn-hoirework" class="btn pull-left btn-success">Complete Rework&nbsp;</button>  
<?php } */
?>

<?php 
if ($OrderIsOnHold) {
?>

<?php
if ($ReleaseOnHold) {
?>
<button data-toggle="Release-popover" Class="btn pull-left btn-warning pull-left ReleaseOnHold" type="button" id="ReleaseOnHold_Btn"><i class="icon-checkmark-circle2"></i> &nbsp;Release On Hold&nbsp;</button>  
<?php 
}
?>

<?php 
}
?>


<?php 
if ($is_raise_withdrawal_enabled && $OrderIsOnHold && !$is_clear_esclation_enabled) {
?>
<!-- <button title="Raise Withdrawal" type="button" data-toggle="modal" data-target="#RaiseWithdrawal"  data-html="true"  id="raisewithdrawalpopup" class="btn pull-left" style="background-color: #E53935;"><i class="fa fa-sign-out" aria-hidden="true"></i> &nbsp; Withdrawal&nbsp;</button>  --> 
<?php 
} 
?>

<?php 
if ($is_clear_withdrawal_enabled && $OrderIsOnHold) {
?>
<button title="Clear Withdrawal" type="button" data-toggle="modal" data-target="#ClearWithdrawal"  data-html="true"  id="clearwithdrawalpopup" class="btn pull-left" style="background-color: #E53935;"><i class="fa fa-sign-out" aria-hidden="true"></i> &nbsp; Clear Withdrawal&nbsp;</button>  
<?php 
} 
?>


<?php 
if ($is_raise_docchase_enabled && $OrderIsOnHold && !$is_clear_esclation_enabled) {
?>
<button title="Initiate Doc Chase" type="button" data-toggle="modal" data-target="#RaiseDocChase"  data-html="true"  id="raisedocchasepopup" class="btn pull-left btn-primary "><i class="icon-stats-growth2 pr-1"></i> &nbsp; Initiate Doc Chase&nbsp;</button> 
<?php 
} 
?>

<?php 
if ($is_clear_esclation_enabled && $OrderIsOnHold ) {
?>
<button title="Clear Esclation" type="button" data-toggle="modal" data-target="#ClearEsclation"  data-html="true"  id="clearClearEsclation" class="btn pull-left btn-primary "><i class="icon-stats-growth2 pr-1"></i> &nbsp; Clear Escalation&nbsp;</button> 
<?php 
} 
?>

<?php if($is_raise_esclation_enabled) { ?>
	<button title="Initiate Esclation" type="button" data-toggle="modal" data-target="#RaiseEsclation"  data-html="true"  id="RaiseEsclationpopup" class="btn pull-left btn-info "><i class="icon-stats-growth2 pr-1"></i> &nbsp; Initiate Escalation&nbsp;</button>
<?php } ?>


<?php 
if($is_clear_docchase_enabled && $OrderIsOnHold && ($Page == 'History' || $Page == 'DocChase')) {  ?>

	<button title="Clear Doc Chase" type="button" data-toggle="modal" data-target="#modal-completemultipledocchase"  data-html="true"  id="clearmultipledocchasepopup" class="btn pull-left btn-danger "><i class="icon-stats-growth2 pr-1"></i> &nbsp; Clear Doc Chase&nbsp;</button>  

<?php } elseif ($is_clear_docchase_enabled && $OrderIsOnHold) {	
	?>

	<button title="Clear Doc Chase" type="button" data-toggle="modal" data-target="#ClearDocChase"  data-html="true"  id="cleardocchasepopup" class="btn pull-left btn-danger "><i class="icon-stats-growth2 pr-1"></i> &nbsp; Clear Doc Chase&nbsp;</button>  
	<?php 
} 
?>

<!-- Parking -->
<?php if ($IsWorkflowEnabled && $is_parkingqueue_enabled && $OrderIsOnHold && !$is_clear_esclation_enabled && isset($workflowcompleted) && !$workflowcompleted && !$is_reverse_enabled) { ?>
	<button style="padding: 7px 10px;" title="Initiate Parking" type="button" data-toggle="modal" data-target="#ParkingQueue"  data-html="true"  id="parkingqueuepopup" class="btn pull-left btn-facebook "><i class="fa fa-product-hunt" aria-hidden="true"></i></i> &nbsp; Parking&nbsp;</button>  
<?php } ?>

<?php 
if ($IsWorkflowEnabled && $is_clearparkingqueue_enabled && $OrderIsOnHold) {
?>
<button title="Clear Parking" type="button" data-toggle="modal" data-target="#ClearParkingQueue"  data-html="true"  id="clearparkingqueuepopup" class="btn pull-left btn-danger "><i class="fa fa-product-hunt" aria-hidden="true"></i> &nbsp; Clear Parking&nbsp;</button>  
<?php 
} 
?>

<!-- Re-Work Queue Button -->
<?php if ($IsWorkflowEnabled && $this->Common_Model->IsWorkflowCompleted($OrderUID,$WorkflowModuleUID) && $this->Common_Model->isworkflow_forceenabled($OrderUID,$WorkflowModuleUID) && $this->Common_Model->IsReWorkEnabled($OrderUID,$WorkflowModuleUID)) { ?>
	
	<button title="Re-Work Complete" type="button" id="btnReWorkComplete" class="btn pull-left btn-warning" data-workflowmoduleuid="<?php echo $WorkflowModuleUID; ?>">&nbsp;Re-Work Complete&nbsp;</button>

<?php } ?>

<!-- Expiry Checklist Orders Complete -->
<?php if($IsWorkflowEnabled && $this->Common_Model->IsChecklistExpiryOrders($OrderUID, $WorkflowModuleUID)) { ?>
	<button title="Expiry Complete" type="button" id="btnExpiryComplete" class="btn pull-left btn-success" data-workflowmoduleuid="<?php echo $WorkflowModuleUID; ?>">&nbsp;Expiry Complete&nbsp;</button>
<?php } ?>
<!-- Expiry Checklist Orders Complete End -->

<!-- Initiate Pending -->
<?php 
if ($IsWorkflowEnabled && $WorkflowModuleUID == $this->config->item('Workflows')['CD'] && empty($OrderAssignDetails)) {
	if($this->Common_Model->CheckOrderExistInSubQueues($OrderUID, $WorkflowModuleUID)) { ?>
		<button title="Initiate Pending" type="button" id="btnInitiateSubQueuePending" class="btn pull-left" data-workflowmoduleuid="<?php echo $WorkflowModuleUID; ?>">&nbsp;Initiate Pending&nbsp;</button>
	<?php } else { ?>
		<button title="Complete Pending" type="button" id="btnCompleteSubPending" class="btn pull-left btn-success" data-workflowmoduleuid="<?php echo $WorkflowModuleUID; ?>">&nbsp;Complete Pending&nbsp;</button>
	<?php } 
}
?>

<!-- Initiate Pending End -->

<!-- KickBack Queue Button -->
<?php if ($IsWorkflowEnabled && $WorkflowModuleUID == $this->config->item('Workflows')['Workup'] && !$this->Common_Model->IsWorkflowCompleted($OrderUID,$WorkflowModuleUID) && $this->Common_Model->CheckIsKickBackOrder($OrderUID,$WorkflowModuleUID) == 0) { ?>
	
	<button title="KickBack" type="button" id="btnMovetoKickBack" class="btn pull-left btn-warning" data-workflowmoduleuid="<?php echo $WorkflowModuleUID; ?>">&nbsp;KickBack&nbsp;</button>

<?php } ?>
<!-- KickBack Queue Button -->

<?php 
}
?>
<script>

var options = '';
<?php $mReasons = $this->Common_Model->get_mreasons();?>
	options += '<?php foreach ($mReasons as $key => $value) { echo '<option value="' . $value->ReasonUID .'" data-queueuid="'.$value->QueueUID.'">'. $value->ReasonName .'</option>';}	?>'
			

//docchase
$(document).off('click', '#cleardocchasepopover').on('click', '#cleardocchasepopover', function (e) { 

	
	swal({
		title: 'Clear Doc Chase',
		html: '<form class="form-horizontal" id="frmcleardocchase" action="#" method="post">' +
		'<div class="row">'+
		'<div class="col-md-12">'+
		'	<input type="hidden" value="" name="OrderUID">'+
		'	<div class="col-md-12 mb-20">'+
		'	<textarea style="resize: none;" class="remarkstext form-control margin-bottom"  placeholder="Enter Remarks Here..." name="remarks"></textarea>'+
		'	</div>'+


		'	<div class="col-md-12 mt-20">'+
		'    <div class="form-group bmd-form-group">' +
		'		<label  class="bmd-label-floating">Select Reason <span class="mandatory"></span></label>'+
		'		<select class="selectpopover selectreason" name="Reason" style="width: 100%;height: 31px;padding: 0px;" >'+
		'			<option value="">--Select Reason--</option>'+
		options +
		'</select>'+

		'</div>'+
		'</div>'+
		'<div class="text-right  mt-20">'+
		'<button class="btn btn-danger Reopen_workflow_submit btncleardocchase" type="submit" id="btncleardocchase"> Clear Doc Chase</button>'+
		'</div>'+
		'</div>'+
		'</div>'+
		'</form> ',
		showCancelButton: true,
		confirmButtonClass: 'btn btn-success',
		cancelButtonClass: 'btn btn-danger',
		buttonsStyling: false,
		onOpen: function () {
			$('.selectreason').select2({
				theme: "bootstrap",
			});
		},
	}).then(function(result) {
		swal({
			type: 'success',
			html: 'You entered: <strong>' +
			$('#input-field').val() +
			'</strong>',
			confirmButtonClass: 'btn btn-success',
			buttonsStyling: false
			
		})
	}).catch(swal.noop)

	callselect2();
})

</script>

