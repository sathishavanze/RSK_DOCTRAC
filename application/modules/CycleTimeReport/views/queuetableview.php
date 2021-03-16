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
			<th><?php echo $WorkflowName; ?></th>
			<?php foreach ($Queues as $key => $Queue) { ?>
				<th><?php echo $Queue->QueueName; ?></th>
			<?php } ?>
		</tr>	
		

	</thead>

	<tbody>
		<tr>
			<td class="boldtext">Date</td>
			<td class="boldtext">Cycle Time</td>
			<?php foreach ($Queues as $key => $Queue) { ?>
				<!-- <td class="boldtext"><b>Funded Loans</b></td> -->
				<td class="boldtext"><b>Cycle Time</b></td>
			<?php } ?>

		</tr>

		
			<?php foreach ($CycleTimeReportrows as $row) { 
				if($row->FundedAvg != '')
				 	{
				 		$WorkflowAVG = round($row->FundedAvg/$row->FundedOrderCount);
				 	}
				 	else
				 	{
				 		$WorkflowAVG = 0;
				 	}?>
				<tr>
					<td><?php echo $row->date ; ?></td>
					<td><?php echo $WorkflowAVG ; ?></td>
					<!-- <td><a href="javascript:void(0);" class="text-primary listorders" data-orderid="<?php echo $row->FundedOrderUID; ?>" data-title="CycleTime SubQueue List" ><?php  echo $row->FundedOrderCount;?></a></td> -->
				 <?php foreach ($Queues as $key => $Queue) 
				 { 
				 	if($row->{'FundedAvg'.$Queue->QueueUID} != '')
				 	{
				 		$FundedAvg = round($row->{'FundedAvg'.$Queue->QueueUID}/$row->{'FundedOrderCount'.$Queue->QueueUID});
				 	}
				 	else
				 	{
				 		$FundedAvg = 0;
				 	}
				 	?>	
				 <!-- <td><a href="javascript:void(0);" class="text-primary listorders" data-orderid="<?php echo $row->{'FundedOrderUID'.$Queue->QueueUID}; ?>" data-title="CycleTime SubQueue List" ><?php  echo $row->{'FundedOrderCount'.$Queue->QueueUID};?></a> -->
				 </td>				
				 <td><?php echo $FundedAvg ?></td>				
				
			<?php } }?>

				</tr>
	</tbody>  
</table>