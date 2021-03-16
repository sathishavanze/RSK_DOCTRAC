<style type="text/css">
	 .boldtext
  {
   font-weight : bold;
   color : #000 !important;
  }
</style>
<table class="table table-hover table-striped listtable">
	<thead>

		<tr>
			<th></th>
			<?php
			foreach ($ProcessUsers as $value) 
				{ ?>
					<th colspan="<?php echo count($Queues)+1 ?>"><?php echo $value->UserName;?></th>
				<?php } ?>
			</tr>

			
		

		<tr>
			<th>Date</th>
			<?php
			foreach ($ProcessUsers as $value) 
				{ ?>
			<th><?php echo $WorkflowName; ?></th>
			<?php foreach ($Queues as $key => $Queue) { ?>
				<th><?php echo $Queue->QueueName; ?></th>
			<?php } }?>
		</tr>
	</thead>

	<tbody>


			<?php foreach ($CycleTimeReportrows as $cycletimeCount) { ?>
		<tr>
					<td><?php echo $cycletimeCount->date ; ?></td>

			<?php foreach ($ProcessUsers as $key => $Process) {
				if($cycletimeCount->{'FundedAvg'.$Process->UserUID} != '')
				{
					$WorkflowFundedAvg = round($cycletimeCount->{'FundedAvg'.$Process->UserUID}/$cycletimeCount->{'FundedOrderCount'.$Process->UserUID});
				}
				else
				{
					$WorkflowFundedAvg = 0;
				} ?>
				<td><?php  echo $WorkflowFundedAvg ;?></td>
			
				 <?php foreach ($Queues as $key => $Queue) 
				 { 
				 	if($cycletimeCount->{'FundedAvg'.$Queue->QueueUID.$Process->UserUID} != '')
				 	{
				 		$FundedAvg = round($cycletimeCount->{'FundedAvg'.$Queue->QueueUID.$Process->UserUID}/$cycletimeCount->{'FundedOrderCount'.$Queue->QueueUID.$Process->UserUID});
				 	}
				 	else
				 	{
				 		$FundedAvg = 0;
				 	}
				 	?>	
				 </td>				
				 <td><?php echo $FundedAvg ?></td>				
				
			<?php }
			 } ?>
				</tr>
			<?php }?>

			


	</tbody>  
</table>