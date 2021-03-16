
<input type="hidden" name="OrderUID" id="tNotes-OrderUID" value="<?php echo $OrderUID;?>">
<input type="hidden" name='WorkflowModuleUID' id='tNotes-WorkflowModuleUID' value="<?php echo $WorkflowModuleUID;?>">				
<div class="row">
	<div class="col-md-12 commandDetails">
		<!-- <img class="cmd_img" src="assets/img/profile.jpg"/> -->
		<div class="commandBox_div">
			<textarea placeholder="Write a Comment ..." class="commandBox" id="Commands" name="Commands" rows="1"></textarea>
			<i title="SendCommand" class="fa fa-paper-plane-o sendCommand" style="font-size: 20px; color: #e40a0a; margin-top: 10px;"></i>
		</div>
	</div>
</div>

<div class="row CommandsappendTable">
	
		<?php 
		$result = $this->Common_Model->GetCommandsDetails($OrderUID,$WorkflowModuleUID);
		foreach ($result as $key => $value) {
			$Avatar = file_exists($value->Avatar) ? $value->Avatar : 'assets/img/profile.jpg';
			?>
			<div class="col-md-12 cmd_sec_div">
				<img class="cmd_img" src="<?php echo $Avatar; ?>"/>
				<div class="cmd_sec_view">
					<p class="Uname"><?php echo $value->UserName ?> <span class="cm_date"><?php echo date('m/d/Y H:i A',strtotime($value->CreateDateTime)) ?></span><?php if($value->IsRead != 1 && $value->CreatedByUserUID != $this->loggedid) {  echo '<span class="badge badge-pill badge-success badge-unread">UNREAD</span>'; } ?></p>
					<p class="Comments"><?php echo nl2br($value->Description) ?></p>
				</div>
			</div>
		<?php } ?>
		

</div>


<script type="text/javascript">
	$(window).on('resize scroll', function() {
		markasreadcomments();
	});
</script>