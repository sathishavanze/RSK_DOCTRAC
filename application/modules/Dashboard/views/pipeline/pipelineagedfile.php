
<table  class="display dataTable table table-striped table-hover table-actions-bar queue-aging" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
	<thead>
		<tr>
			<th></th>
			<?php $DashboardAgingBucketHeader = $this->config->item('DashboardAgingBucketHeader1');

			foreach ($DashboardAgingBucketHeader as $AgingHeaderkey => $AgingHeadervalue) { ?>
				<th><?php echo $AgingHeadervalue; ?></th>
			<?php } ?>
		</tr>
	</thead>

	<tbody>
		<?php 
		$res = 0;
		foreach ($ReportCounts as $row) 
		{ 
			$fivetotendays = ($row['fivetotendays'] != '') ? count(explode(',', $row['fivetotendays'])) : NULL;
			$tentofifteendays = ($row['tentofifteendays'] != '') ? count(explode(',', $row['tentofifteendays'])) : NULL;
			$fifteentotwentydays = ($row['fifteentotwentydays'] != '') ? count(explode(',', $row['fifteentotwentydays'])) : NULL;
			$twentyfivetothirtydays = ($row['twentyfivetothirtydays'] != '') ? count(explode(',', $row['twentyfivetothirtydays'])) : NULL;
			$pastdue = ($row['pastdue'] != '') ? count(explode(',', $row['pastdue'])) : NULL;
			$duetoday = ($row['duetoday'] != '') ? count(explode(',', $row['duetoday'])) : NULL;
			?>
			<tr>
				<td><b><?php echo $row["WorkflowModuleName"]; ?></b></td>

				<td><a href="javascript:;" title="View Loans" data-heading="GateKeeping PipeLine File Aging Report - <?php echo $row["WorkflowModuleName"]; ?>" class="listorders" data-orderid="<?php echo $row['fivetotendays']; ?>"><?php echo $fivetotendays; ?></a></td>

				<td><a href="javascript:;" title="View Loans" data-heading="GateKeeping PipeLine File Aging Report - <?php echo $row["WorkflowModuleName"]; ?>" class="listorders" data-orderid="<?php echo $row['tentofifteendays']; ?>"><?php echo $tentofifteendays; ?></a></td>

				<td><a href="javascript:;" title="View Loans" data-heading="GateKeeping PipeLine File Aging Report - <?php echo $row["WorkflowModuleName"]; ?>" class="listorders" data-orderid="<?php echo $row['fifteentotwentydays']; ?>"><?php echo $fifteentotwentydays; ?></a></td>

				<td><a href="javascript:;" title="View Loans" data-heading="GateKeeping PipeLine File Aging Report - <?php echo $row["WorkflowModuleName"]; ?>" class="listorders" data-orderid="<?php echo $row['twentyfivetothirtydays']; ?>"><?php echo $twentyfivetothirtydays; ?></a></td>

				<td><a href="javascript:;" title="View Loans" data-heading="GateKeeping PipeLine File Aging Report - <?php echo $row["WorkflowModuleName"]; ?>" class="listorders" data-orderid="<?php echo $row['pastdue']; ?>"><?php echo $pastdue; ?></a></td>
				
				<td><a href="javascript:;" title="View Loans" data-heading="GateKeeping PipeLine File Aging Report - <?php echo $row["WorkflowModuleName"]; ?>" class="listorders" data-orderid="<?php echo $row['duetoday']; ?>"><?php echo $duetoday; ?></a></td>
			</tr>        
			<?php 
		}
		?>

		<!-- <tr>
			<td><span class="text-bold">Total</span></td>
			<?php foreach ($DashboardAgingBucketHeader as $AgingHeaderkey => $AgingHeadervalue) { ?>
				<td><?php echo isset($finalnoofvalues[$AgingHeaderkey]) ? $finalnoofvalues[$AgingHeaderkey] : 0; ?></td>
			<?php } ?>
			<td><span class="text-bold"><?php echo !empty($tot_complete) && !empty($tot_flow) ? percent($tot_complete,$tot_flow) : 0; ?>%</span></td>
		</tr> --> 

	</tbody>  


</table>



