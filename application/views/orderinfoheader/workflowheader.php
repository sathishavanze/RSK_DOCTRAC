<style type="text/css">
	.OrderProcessStatus {
		float: right;
		position: absolute;
		margin-top: 26px;
		right: 116px;
		padding: 8px;
		font-size: large;
		font-weight: 500;
	}
	.OrderProcessStatus.Success {
		background: #d3efd3;
	}
	.OrderProcessStatus.Failure {
		background: #ffc8c8;
	}
	.OrderProcessStatus.processing {
		background: #fffbc5;
	}
	.OrderProcessStatus .Success i {
		width: 30px;
		text-align: center;
		color: green !important;
	}
	.OrderProcessStatus .Failure i {
		width: 30px;
		text-align: center;
		color: red !important;
	}	
	.OrderProcessStatus .processing i {
		width: 30px;
		text-align: center;
		color: #FFCC00 !important;
	}	
</style>
<?php 
$OrderUID = $this->uri->segment(3);
$OrderDetails = $this->Common_Model->getOrderDetails($OrderUID); 
if(empty($OrderDetails)) {
	redirect(base_url());
}
$Page = $this->uri->segment(1);

?>
<div class="col-md-12 navmenu">
	<div class="row">
		<ul class="nav nav-pills nav-pills-link txt-white" role="tablist">
			<li class=" nav-item">
				<a class="nav-link tablist ajaxload <?php if ($this->uri->segment(1) == "Ordersummary") {echo "active";}?>" role="tablist" href="<?php echo base_url() . 'Ordersummary/index/' . $OrderDetails->OrderUID . '/' ?>" data-orderuid = "<?php echo $OrderDetails->OrderUID; ?>">Order Info</a>
			</li>
			<?php //if( $this->parameters['DefaultClientUID'] == $this->config->item('HOI_Customer') ){ ?>
				<!-- <li class=" nav-item">
					<a class="nav-link tablist <?php if ($this->uri->segment(1) == "OrderLoanFile") {echo "active";}?>" role="tablist" href="<?php echo base_url() . 'OrderLoanFile/index/' . $OrderDetails->OrderUID . '/' ?>" data-orderuid = "<?php echo $OrderDetails->OrderUID; ?>">Loan File</a>
				</li> --> 
			<!-- <li class=" nav-item">
				<a class="nav-link tablist <?php if ($this->uri->segment(1) == "SFTP_Files") {echo "active";}?>" role="tablist" href="<?php echo base_url() . 'SFTP_Files/index/' . $OrderDetails->OrderUID . '/' ?>" data-orderuid = "<?php echo $OrderDetails->OrderUID; ?>">SFTP Files</a>
			</li> --> 
			<?php //}
			$OrderWorkflows = $this->Common_Model->get_definedWorkflowMenu_options(['ORDERWORKFLOW']);
			
			foreach ($OrderWorkflows as $key => $workflow) {
				/*if ( empty( $workflow->WorkflowModuleUID ) || ( $this->Common_Model->Is_given_orderworkflow_available( $OrderUID, $workflow->WorkflowModuleUID ) ) ) { */
					?>
					<li class=" nav-item">
						<a class="nav-link tablist <?php if ($this->uri->segment(1) == $workflow->controller) {echo "active";}?>" role="tablist" href="<?php echo base_url() . $workflow->controller.'/index/' . $OrderDetails->OrderUID . '/' ?>" data-orderuid = "<?php echo $OrderDetails->OrderUID; ?>"><?php echo $workflow->FieldName; ?></a>
					</li>

				<?php /*}*/

			} ?>

		</ul>
	</div>
</div>
<?php 
/*if( ($Page == 'HOI' || $Page == 'OrderLoanFile' )){
	$checkStatus = $this->Common_Model->CheckAutoStatus($OrderUID); 
	
	if($checkStatus->AutoExportStatus == '' || $checkStatus->AutoExportStatus != 2){
		echo '<div class="OrderProcessStatus processing"><span class="StatusText processing"> <i class="fa fa-file-o" aria-hidden="true"></i> Waiting For Loan File </span></div>';
	}else{
		?>
		<div class="OrderProcessStatus <?php if($checkStatus->IsOCREnabled == 1){ echo 'processing'; }else{ echo $checkStatus->AutomationStatus; } ?>">
			<?php if($checkStatus->IsOCREnabled == 1){ ?>
				<span class="StatusText Processing"> <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> OCR Processing </span>
			<?php }else if($checkStatus->AutomationStatus != ''){ 
				if($checkStatus->AutomationStatus == 'Success'){
					if($checkStatus->AutomationType == 'Email Sent'){
						$msg = 'Email Sent';
					}else if($checkStatus->AutomationType == 'eFax Receive'){
						$msg = 'Fax Received';
					}else if($checkStatus->AutomationType == 'Email Receive'){
						$msg = 'Email Received';
					}else if($checkStatus->AutomationType == 'eFax Sent'){
						$msg = 'Fax Sent';
					}else if($checkStatus->AutomationType == 'Bot Receive'){
						$msg = 'Bot Received';
					}else if($checkStatus->AutomationType == 'Bot Sent'){
						$msg = 'Bot Sent';
					}else {
						$msg = $checkStatus->AutomationType.' '.$checkStatus->AutomationStatus;
					}
				}else{
					$msg = $checkStatus->AutomationType.' '.$checkStatus->AutomationStatus;
				}
				?>
				<span class="StatusText <?php echo $checkStatus->AutomationStatus; ?>">			
					<?php if($checkStatus->AutomationType == 'Email Sent' || $checkStatus->AutomationType == 'Email Receive'){
						echo '<i class="fa fa-envelope" aria-hidden="true"></i>';
					}else if($checkStatus->AutomationType == 'eFax Sent' || $checkStatus->AutomationType == 'eFax Receive'){
						echo '<i class="fa fa-fax" aria-hidden="true"></i>';
					}else if($checkStatus->AutomationType == 'Bot Sent' || $checkStatus->AutomationType == 'Bot Receive'){
						echo '<i class="fa fa-file" aria-hidden="true"></i>';
					}else if($checkStatus->AutomationType == 'OCR'){
						echo '<i class="fa fa-file-text" aria-hidden="true"></i>';
					}

					echo $msg; ?>
				</span>	
			<?php } ?> </div> <?php
		} 
	} */?>

<?php 
//workflow pages
$Page_WorkflowModuleUID = "";
$mResource = $this->Common_Model->getWorkflowByPage($Page);
if (!empty($mResource)) {
	$Page_WorkflowModuleUID = $mResource->WorkflowModuleUID;
}
?>
<script type="text/javascript">
	var Const_Page = '<?php  echo $Page;  ?>';
	var Page_WorkflowModuleUID = '<?php echo $Page_WorkflowModuleUID; ?>';
</script>
